<?php


class Post {
    private $conn;
    private $table_name = "posts";

    public $id;
    public $user_id;
    public $title;
    public $content;
    public $image;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (user_id, title, content, image, created_at)
                VALUES
                (:user_id, :title, :content, :image, :created_at)";

        $stmt = $this->conn->prepare($query);

        // Nettoyer les données
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->content = htmlspecialchars(strip_tags($this->content));
        $this->created_at = date('Y-m-d H:i:s');

        // Bind des paramètres
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":created_at", $this->created_at);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function read() {
        $query = "SELECT p.*, u.username 
                FROM " . $this->table_name . " p
                LEFT JOIN users u ON p.user_id = u.id
                ORDER BY p.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function readOne($id = null) {
        // Si aucun ID n'est passé, utiliser l'ID de l'instance
        $postId = $id ?? $this->id;
        
        $query = "SELECT p.*, u.username 
                  FROM " . $this->table_name . " p
                  LEFT JOIN users u ON p.user_id = u.id
                  WHERE p.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $postId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update() {
        // Vérifier si l'utilisateur est admin ou propriétaire du post
        if ($_SESSION['role'] === 'admin') {
            $query = "UPDATE " . $this->table_name . "
                    SET title = :title,
                        content = :content,
                        image = :image,
                        updated_at = :updated_at
                    WHERE id = :id";
        } else {
            // Les utilisateurs normaux ne peuvent modifier que leurs propres posts
            $query = "UPDATE " . $this->table_name . "
                    SET title = :title,
                        content = :content,
                        image = :image,
                        updated_at = :updated_at
                    WHERE id = :id AND user_id = :user_id";
        }

        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->content = htmlspecialchars(strip_tags($this->content));
        $this->updated_at = date('Y-m-d H:i:s');

        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":updated_at", $this->updated_at);
        $stmt->bindParam(":id", $this->id);
        
        if ($_SESSION['role'] !== 'admin') {
            $stmt->bindParam(":user_id", $this->user_id);
        }

        return $stmt->execute();
    }

    public function delete() {
        // Si c'est l'admin, il peut supprimer n'importe quel post
        if ($_SESSION['role'] === 'admin') {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $this->id);
        } else {
            // Les utilisateurs normaux ne peuvent supprimer que leurs propres posts
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $this->id);
            $stmt->bindParam(":user_id", $this->user_id);
        }
        
        return $stmt->execute();
    }

    public function getUserPosts($user_id, $offset = 0, $items_per_page = 6) {
        $query = "SELECT p.*, u.username 
                 FROM " . $this->table_name . " p
                 LEFT JOIN users u ON p.user_id = u.id
                 WHERE p.user_id = :user_id 
                 ORDER BY p.created_at DESC
                 LIMIT :offset, :items_per_page";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':items_per_page', $items_per_page, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalPosts() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function readPaginated($offset, $items_per_page) {
        $query = "SELECT p.*, u.username 
                 FROM " . $this->table_name . " p
                 LEFT JOIN users u ON p.user_id = u.id
                 ORDER BY p.created_at DESC
                 LIMIT :offset, :items_per_page";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':items_per_page', $items_per_page, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserPostsCount($user_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['total'];
    }

    public function isOwner($user_id) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE id = :id AND user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    public function getRecentPosts($limit = 3) {
        $query = "SELECT p.*, u.username 
                  FROM " . $this->table_name . " p
                  LEFT JOIN users u ON p.user_id = u.id
                  ORDER BY p.created_at DESC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT p.*, u.username 
                  FROM " . $this->table_name . " p
                  LEFT JOIN users u ON p.user_id = u.id
                  WHERE p.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
