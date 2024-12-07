<?php
class User {
    private $db;
    private $table = 'users';
    public $id;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($username, $email, $password) {
        try {
            $query = "INSERT INTO {$this->table} (username, email, password, role, created_at) 
                     VALUES (:username, :email, :password, 'user', NOW())";
            
            $stmt = $this->db->prepare($query);
            
            return $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => $password  // Le mot de passe est déjà haché dans le contrôleur
            ]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la création de l'utilisateur : " . $e->getMessage());
            return false;
        }
    }

    public function findByUsername($username) {
        try {
            $query = "SELECT * FROM {$this->table} WHERE username = :username LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':username' => $username]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la recherche de l'utilisateur par username : " . $e->getMessage());
            return false;
        }
    }

    public function findByEmail($email) {
        try {
            $query = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':email' => $email]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la recherche de l'utilisateur par email : " . $e->getMessage());
            return false;
        }
    }

    public function findById($id) {
        try {
            $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la recherche de l'utilisateur par ID : " . $e->getMessage());
            return false;
        }
    }

    public function updateProfile($id, $username, $email) {
        try {
            $query = "UPDATE {$this->table} 
                     SET username = :username, email = :email, updated_at = NOW() 
                     WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                ':id' => $id,
                ':username' => $username,
                ':email' => $email
            ]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du profil : " . $e->getMessage());
            return false;
        }
    }

    public function updatePassword($id, $password) {
        try {
            $query = "UPDATE {$this->table} 
                     SET password = :password, updated_at = NOW() 
                     WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                ':id' => $id,
                ':password' => password_hash($password, PASSWORD_DEFAULT)
            ]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du mot de passe : " . $e->getMessage());
            return false;
        }
    }

    public function getUserPosts($userId) {
        try {
            $query = "SELECT p.*, u.username 
                     FROM posts p 
                     JOIN users u ON p.user_id = u.id 
                     WHERE p.user_id = :user_id 
                     ORDER BY p.created_at DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([':user_id' => $userId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des posts de l'utilisateur : " . $e->getMessage());
            return [];
        }
    }

    public function delete($id) {
        try {
            // Supprimer d'abord les posts de l'utilisateur
            $query = "DELETE FROM posts WHERE user_id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);

            // Ensuite supprimer l'utilisateur
            $query = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression de l'utilisateur : " . $e->getMessage());
            return false;
        }
    }

    public function createPasswordReset($userId) {
        try {
            // Generate a unique token
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $query = "UPDATE {$this->table} 
                     SET reset_token = :token, reset_token_expiry = :expiry 
                     WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            $success = $stmt->execute([
                ':token' => $token,
                ':expiry' => $expiry,
                ':id' => $userId
            ]);
            
            return $success ? $token : false;
        } catch (PDOException $e) {
            error_log("Erreur lors de la création du token de réinitialisation : " . $e->getMessage());
            return false;
        }
    }

    public function verifyResetToken($token) {
        try {
            $query = "SELECT id FROM {$this->table} 
                     WHERE reset_token = :token 
                     AND reset_token_expiry > NOW() 
                     AND reset_token IS NOT NULL";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([':token' => $token]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['id'] : false;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification du token : " . $e->getMessage());
            return false;
        }
    }

    public function clearResetToken($userId) {
        try {
            $query = "UPDATE {$this->table} 
                     SET reset_token = NULL, reset_token_expiry = NULL 
                     WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            return $stmt->execute([':id' => $userId]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression du token : " . $e->getMessage());
            return false;
        }
    }

    public function createPasswordResetOld($id) {
        try {
            // Generate a unique token
            $token = bin2hex(random_bytes(32));
            
            // Set token expiration to 1 hour from now
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Insert token into password_resets table
            $query = "INSERT INTO password_resets (user_id, token, expires_at) VALUES (:user_id, :token, :expires)";
            $stmt = $this->db->prepare($query);
            
            $result = $stmt->execute([
                ':user_id' => $id,
                ':token' => $token,
                ':expires' => $expires
            ]);
            
            return $result ? $token : false;
        } catch (PDOException $e) {
            error_log("Erreur lors de la création du token de réinitialisation de mot de passe : " . $e->getMessage());
            return false;
        }
    }

    public function deleteResetToken($token) {
        try {
            $query = "UPDATE {$this->table} 
                     SET reset_token = NULL, reset_token_expires = NULL 
                     WHERE reset_token = :token";
            
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                ':token' => $token
            ]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression du token de réinitialisation : " . $e->getMessage());
            return false;
        }
    }

    public function verifyResetTokenOld($token) {
        try {
            $query = "SELECT user_id FROM password_resets WHERE token = :token AND expires_at > NOW()";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':token' => $token]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ? $result['user_id'] : false;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification du token de réinitialisation : " . $e->getMessage());
            return false;
        }
    }

    public function saveResetToken($userId, $token, $expiry) {
        try {
            $query = "UPDATE {$this->table} 
                     SET reset_token = :token, 
                         reset_token_expiry = :expiry 
                     WHERE id = :userId";
            
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                ':userId' => $userId,
                ':token' => $token,
                ':expiry' => $expiry
            ]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la sauvegarde du token de réinitialisation : " . $e->getMessage());
            return false;
        }
    }

    public function verifyResetTokenNew($token) {
        try {
            $query = "SELECT id FROM {$this->table} 
                     WHERE reset_token = :token 
                     AND reset_token_expiry > NOW() 
                     LIMIT 1";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([':token' => $token]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['id'] : false;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification du token : " . $e->getMessage());
            return false;
        }
    }

    public function deleteResetTokenNew($token) {
        try {
            $query = "UPDATE {$this->table} 
                     SET reset_token = NULL, 
                         reset_token_expiry = NULL 
                     WHERE reset_token = :token";
            
            $stmt = $this->db->prepare($query);
            return $stmt->execute([':token' => $token]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression du token : " . $e->getMessage());
            return false;
        }
    }
}
