<?php
require_once ROOT_PATH . 'config/database.php';
require_once APP_PATH . 'models/Post.php';
require_once APP_PATH . 'models/Like.php';

class PostController {
    private $db;
    private $post;
    private $like;

    protected function render($view, $data = []) {
        extract($data);
        ob_start();
        require_once APP_PATH . 'views/' . $view . '.php';
        $content = ob_get_clean();
        require_once APP_PATH . 'views/layouts/main.php';
    }

    public function __construct() {
        // Permettre aux utilisateurs non connectés de voir les articles
        $database = new Database();
        $this->db = $database->getConnection();
        $this->post = new Post($this->db);
        $this->like = new Like($this->db);
    }

    public function index() {
        // Paramètres de pagination
        $items_per_page = 6;
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($current_page - 1) * $items_per_page;

        // Récupérer le nombre total d'articles
        $total_items = $this->post->getTotalPosts();
        $total_pages = ceil($total_items / $items_per_page);

        // S'assurer que la page courante est valide
        if ($current_page < 1) {
            $current_page = 1;
        } elseif ($current_page > $total_pages) {
            $current_page = $total_pages;
        }

        // Récupérer les articles pour la page courante
        $result = $this->post->readPaginated($offset, $items_per_page);
        $posts = $result->fetchAll(PDO::FETCH_ASSOC);

        // Passer les variables à la vue
        $data = [
            'posts' => $posts,
            'total_pages' => $total_pages,
            'current_page' => $current_page,
            'is_admin' => isset($_SESSION['role']) && $_SESSION['role'] === 'admin',
            'user_id' => $_SESSION['user_id'] ?? null
        ];

        $this->render('posts/list', $data);
    }

    public function create() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->post->title = $_POST['title'];
            $this->post->content = $_POST['content'];
            $this->post->user_id = $_SESSION['user_id'];
            
            // Gestion de l'upload d'image
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = ROOT_PATH . 'public/uploads/';
                
                // Créer le dossier s'il n'existe pas
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Sécuriser le nom du fichier
                $fileName = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", basename($_FILES['image']['name']));
                $targetPath = $uploadDir . $fileName;

                // Vérifier le type de fichier
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $_FILES['image']['tmp_name']);
                finfo_close($finfo);

                if (!in_array($mimeType, $allowedTypes)) {
                    $_SESSION['error'] = "Type de fichier non autorisé. Seules les images JPG, PNG et GIF sont acceptées.";
                    $this->render('posts/create');
                    return;
                }

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $this->post->image = $fileName;
                }
            }

            if ($this->post->create()) {
                $_SESSION['success'] = "Article créé avec succès.";
                header("Location: index.php?controller=post&action=index");
                exit();
            } else {
                $_SESSION['error'] = "Erreur lors de la création de l'article.";
            }
        }
        
        $this->render('posts/create');
    }

    public function edit() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        if (!isset($_GET['id'])) {
            header("Location: index.php?controller=post&action=index");
            exit();
        }

        $this->post->id = $_GET['id'];
        $post = $this->post->readOne();

        // Vérifier si l'article existe
        if (!$post) {
            $_SESSION['error'] = "Article introuvable.";
            header("Location: index.php?controller=post&action=index");
            exit();
        }

        // Vérifier si l'utilisateur a le droit de modifier cet article
        if ($_SESSION['role'] !== 'admin' && $post['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = "Vous n'avez pas la permission de modifier cet article.";
            header("Location: index.php?controller=post&action=index");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->post->title = $_POST['title'];
            $this->post->content = $_POST['content'];
            $this->post->user_id = $post['user_id']; // Garder l'utilisateur original
            $this->post->image = $post['image']; // Garder l'image existante par défaut

            // Gestion de l'upload d'une nouvelle image
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = ROOT_PATH . 'public/uploads/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Supprimer l'ancienne image si elle existe
                if (!empty($post['image'])) {
                    $oldImagePath = $uploadDir . $post['image'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $fileName = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", basename($_FILES['image']['name']));
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $this->post->image = $fileName;
                }
            }

            if ($this->post->update()) {
                $_SESSION['success'] = "Article mis à jour avec succès.";
                header("Location: index.php?controller=post&action=view&id=" . $this->post->id);
                exit();
            } else {
                $_SESSION['error'] = "Erreur lors de la mise à jour de l'article.";
            }
        }

        $this->render('posts/edit', ['post' => $post]);
    }

    public function delete() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        if (!isset($_GET['id'])) {
            header("Location: index.php?controller=post&action=index");
            exit();
        }

        $id = $_GET['id'];
        $this->post->id = $id;
        $post = $this->post->readOne();

        // Vérifier si l'article existe
        if (!$post) {
            $_SESSION['error'] = "Article introuvable.";
            header("Location: index.php?controller=post&action=index");
            exit();
        }

        // Vérifier si l'utilisateur a le droit de supprimer cet article
        if ($_SESSION['role'] !== 'admin' && $post['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = "Vous n'avez pas la permission de supprimer cet article.";
            header("Location: index.php?controller=post&action=index");
            exit();
        }

        // Set the user_id for non-admin users
        if ($_SESSION['role'] !== 'admin') {
            $this->post->user_id = $_SESSION['user_id'];
        }

        try {
            // Start transaction
            $this->db->beginTransaction();

            // Try to delete likes if the table exists
            try {
                $query = "DELETE FROM likes WHERE post_id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$id]);
            } catch (PDOException $e) {
                // Ignore error if likes table doesn't exist
                if ($e->getCode() !== '42S02') { // SQL State for table not found
                    throw $e;
                }
            }

            // Try to delete the post
            if ($this->post->delete()) {
                // Delete the associated image if it exists
                if (!empty($post['image'])) {
                    $imagePath = ROOT_PATH . 'public/uploads/' . $post['image'];
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                $this->db->commit();
                $_SESSION['success'] = "Article supprimé avec succès.";
            } else {
                throw new Exception("Erreur lors de la suppression de l'article.");
            }
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = "Erreur lors de la suppression de l'article: " . $e->getMessage();
        }

        header("Location: index.php?controller=post&action=index");
        exit();
    }

    public function view() {
        if (!isset($_GET['id'])) {
            header('Location: index.php?controller=post&action=index');
            exit();
        }

        $id = $_GET['id'];
        $post = $this->post->getById($id);
        
        if (!$post) {
            header('Location: index.php?controller=post&action=index');
            exit();
        }

        // Get reaction information
        $likeCount = $this->like->getLikeCount($id);
        $dislikeCount = $this->like->getDislikeCount($id);
        $hasLiked = isset($_SESSION['user_id']) ? $this->like->hasUserLiked($_SESSION['user_id'], $id) : false;
        $hasDisliked = isset($_SESSION['user_id']) ? $this->like->hasUserDisliked($_SESSION['user_id'], $id) : false;

        $title = $post['title'];
        require_once APP_PATH . 'views/posts/view.php';
    }

    public function toggleReaction() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'User not logged in']);
            exit();
        }

        if (!isset($_GET['id']) || !isset($_GET['reaction'])) {
            echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            exit();
        }

        $postId = $_GET['id'];
        $userId = $_SESSION['user_id'];
        $reaction = $_GET['reaction'];

        if ($reaction === 'like') {
            $result = $this->like->toggleLike($userId, $postId);
        } else if ($reaction === 'dislike') {
            $result = $this->like->toggleDislike($userId, $postId);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid reaction type']);
            exit();
        }

        $response = [
            'success' => true,
            'likeCount' => $this->like->getLikeCount($postId),
            'dislikeCount' => $this->like->getDislikeCount($postId),
            'liked' => $this->like->hasUserLiked($userId, $postId),
            'disliked' => $this->like->hasUserDisliked($userId, $postId)
        ];

        echo json_encode($response);
        exit();
    }

    public function myPosts() {
        // Paramètres de pagination
        $items_per_page = 6;
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($current_page - 1) * $items_per_page;

        // Récupérer les articles de l'utilisateur
        $user_id = $_SESSION['user_id'];
        $posts = $this->post->getUserPosts($user_id, $offset, $items_per_page);
        $total_items = $this->post->getUserPostsCount($user_id);
        $total_pages = ceil($total_items / $items_per_page);

        // S'assurer que la page courante est valide
        if ($current_page < 1) {
            $current_page = 1;
        } elseif ($current_page > $total_pages && $total_pages > 0) {
            $current_page = $total_pages;
        }

        // Charger la vue
        ob_start();
        require_once APP_PATH . 'views/posts/my-posts.php';
        $content = ob_get_clean();
        require_once APP_PATH . 'views/layout.php';
    }
}
