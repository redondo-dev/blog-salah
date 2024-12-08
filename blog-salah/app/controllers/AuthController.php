<?php
require_once ROOT_PATH . 'config/database.php';
require_once APP_PATH . 'models/User.php';
require_once APP_PATH . 'models/Post.php';
require_once APP_PATH . 'services/EmailService.php';
require_once APP_PATH . 'models/Like.php';

class AuthController {
    private $db;
    private $user;
    private $emailService;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
        $this->emailService = new EmailService();
    }

    public function login() {
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $_SESSION['error'] = "Tous les champs sont obligatoires.";
                header("Location: index.php?controller=auth&action=login");
                exit();
            }

            $userData = $this->user->findByUsername($username);

            if ($userData && password_verify($password, $userData['password'])) {
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['username'] = $userData['username'];
                $_SESSION['role'] = $userData['role'];
                header("Location: index.php");
                exit();
            } else {
                $_SESSION['error'] = "Nom d'utilisateur ou mot de passe incorrect.";
                header("Location: index.php?controller=auth&action=login");
                exit();
            }
        }

        require_once APP_PATH . 'views/auth/login.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            // Validation
            if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
                $_SESSION['error'] = "Tous les champs sont obligatoires";
                require_once APP_PATH . 'views/auth/register.php';
                return;
            }

            if ($password !== $confirm_password) {
                $_SESSION['error'] = "Les mots de passe ne correspondent pas";
                require_once APP_PATH . 'views/auth/register.php';
                return;
            }

            if (strlen($password) < 8) {
                $_SESSION['error'] = "Le mot de passe doit contenir au moins 8 caractères";
                require_once APP_PATH . 'views/auth/register.php';
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "L'adresse email n'est pas valide";
                require_once APP_PATH . 'views/auth/register.php';
                return;
            }

            // Vérifier si l'utilisateur existe déjà
            if ($this->user->findByUsername($username)) {
                $_SESSION['error'] = "Ce nom d'utilisateur est déjà utilisé";
                require_once APP_PATH . 'views/auth/register.php';
                return;
            }

            if ($this->user->findByEmail($email)) {
                $_SESSION['error'] = "Cette adresse email est déjà utilisée";
                require_once APP_PATH . 'views/auth/register.php';
                return;
            }

            // Création de l'utilisateur
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            if ($this->user->create($username, $email, $hashedPassword)) {
                $_SESSION['success'] = "Compte créé avec succès. Vous pouvez maintenant vous connecter.";
                header('Location: index.php?controller=auth&action=login');
                exit();
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de la création du compte";
                require_once APP_PATH . 'views/auth/register.php';
                return;
            }
        }

        require_once APP_PATH . 'views/auth/register.php';
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?controller=auth&action=login");
        exit();
    }

    public function profile() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        $userData = $this->user->findById($_SESSION['user_id']);
        
        // Récupérer les posts de l'utilisateur
        $post = new Post($this->db);
        $like = new Like($this->db);
        $posts = $post->getUserPosts($_SESSION['user_id']);

        // Ajouter les informations de réaction à chaque post
        $postsWithReactions = [];
        foreach ($posts as &$postItem) {
            $postItem['likeCount'] = $like->getLikeCount($postItem['id']);
            $postItem['dislikeCount'] = $like->getDislikeCount($postItem['id']);
            $postItem['hasLiked'] = isset($_SESSION['user_id']) ? $like->hasUserLiked($_SESSION['user_id'], $postItem['id']) : false;
            $postItem['hasDisliked'] = isset($_SESSION['user_id']) ? $like->hasUserDisliked($_SESSION['user_id'], $postItem['id']) : false;
            $postsWithReactions[] = $postItem;
        }

        require_once APP_PATH . 'views/auth/profile.php';
    }

    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            
            if (empty($email)) {
                $_SESSION['error'] = "Veuillez entrer votre adresse email.";
                require_once APP_PATH . 'views/auth/forgot-password.php';
                return;
            }

            $userData = $this->user->findByEmail($email);
            
            if ($userData) {
                // Générer un token unique
                $token = bin2hex(random_bytes(32));
                $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Sauvegarder le token
                if ($this->user->saveResetToken($userData['id'], $token, $expiry)) {
                    // Préparer l'email
                    $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/index.php?controller=auth&action=resetPassword&token=" . $token;
                    
                    // Envoyer l'email avec le nouveau service
                    if ($this->emailService->sendPasswordReset($email, $resetLink)) {
                        $_SESSION['info'] = "Un email de réinitialisation a été envoyé à votre adresse email.";
                    } else {
                        $_SESSION['error'] = "Erreur lors de l'envoi de l'email. Veuillez réessayer plus tard.";
                    }
                } else {
                    $_SESSION['error'] = "Une erreur est survenue. Veuillez réessayer plus tard.";
                }
            }
            
            // Pour des raisons de sécurité, on affiche toujours le même message
            if (!isset($_SESSION['error'])) {
                $_SESSION['info'] = "Si votre email existe dans notre base de données, vous recevrez un lien de réinitialisation.";
            }
            header('Location: index.php?controller=auth&action=login');
            exit();
        }
        
        $title = 'Mot de passe oublié';
        require_once APP_PATH . 'views/auth/forgot-password.php';
    }

    public function resetPassword() {
        if (!isset($_GET['token'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        $token = $_GET['token'];
        $userId = $this->user->verifyResetToken($token);

        if (!$userId) {
            $_SESSION['error'] = "Le lien de réinitialisation est invalide ou a expiré.";
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            if ($password !== $confirm_password) {
                $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
            } else {
                if ($this->user->updatePassword($userId, $password)) {
                    $this->user->deleteResetToken($token);
                    $_SESSION['success'] = "Votre mot de passe a été réinitialisé avec succès.";
                    header("Location: index.php?controller=auth&action=login");
                    exit();
                } else {
                    $_SESSION['error'] = "Erreur lors de la réinitialisation du mot de passe.";
                }
            }
        }
        require_once APP_PATH . 'views/auth/reset_password.php';
    }
}
