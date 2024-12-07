<?php

class Like {
    private $conn;
    private $table_name = "likes";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function toggleLike($userId, $postId) {
        // First check if user has disliked the post
        if ($this->hasUserDisliked($userId, $postId)) {
            $this->removeDislike($userId, $postId);
        }

        // Then handle the like
        if ($this->hasUserLiked($userId, $postId)) {
            return $this->removeLike($userId, $postId);
        } else {
            return $this->addLike($userId, $postId);
        }
    }

    public function toggleDislike($userId, $postId) {
        // First check if user has liked the post
        if ($this->hasUserLiked($userId, $postId)) {
            $this->removeLike($userId, $postId);
        }

        // Then handle the dislike
        if ($this->hasUserDisliked($userId, $postId)) {
            return $this->removeDislike($userId, $postId);
        } else {
            return $this->addDislike($userId, $postId);
        }
    }

    private function addLike($userId, $postId) {
        $query = "INSERT INTO " . $this->table_name . " (user_id, post_id, reaction_type) VALUES (:user_id, :post_id, 'like')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":post_id", $postId);
        return $stmt->execute();
    }

    private function removeLike($userId, $postId) {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = :user_id AND post_id = :post_id AND reaction_type = 'like'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":post_id", $postId);
        return $stmt->execute();
    }

    private function addDislike($userId, $postId) {
        $query = "INSERT INTO " . $this->table_name . " (user_id, post_id, reaction_type) VALUES (:user_id, :post_id, 'dislike')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":post_id", $postId);
        return $stmt->execute();
    }

    private function removeDislike($userId, $postId) {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = :user_id AND post_id = :post_id AND reaction_type = 'dislike'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":post_id", $postId);
        return $stmt->execute();
    }

    public function getLikeCount($postId) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE post_id = :post_id AND reaction_type = 'like'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":post_id", $postId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }

    public function getDislikeCount($postId) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE post_id = :post_id AND reaction_type = 'dislike'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":post_id", $postId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }

    public function hasUserLiked($userId, $postId) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                 WHERE user_id = :user_id AND post_id = :post_id AND reaction_type = 'like'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":post_id", $postId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }

    public function hasUserDisliked($userId, $postId) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                 WHERE user_id = :user_id AND post_id = :post_id AND reaction_type = 'dislike'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":post_id", $postId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }
}
