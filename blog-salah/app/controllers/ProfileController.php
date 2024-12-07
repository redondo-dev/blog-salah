<?php
require_once ROOT_PATH . 'config/database.php';
require_once APP_PATH . 'models/User.php';

class ProfileController {
    private $db;
    private $user;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    public function index() {
        // Récupérer les informations de l'utilisateur
        $user_id = $_SESSION['user_id'];
        $user_info = $this->user->findById($user_id);
        
        // Charger la vue du profil
        ob_start();
        require_once APP_PATH . 'views/profile/index.php';
        $content = ob_get_clean();
        require_once APP_PATH . 'views/layout.php';
    }

    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            
            // Mettre à jour le profil
            if ($this->user->updateProfile($user_id, $username, $email)) {
                $_SESSION['success'] = "Profil mis à jour avec succès.";
                header("Location: index.php?controller=profile&action=index");
                exit();
            } else {
                $_SESSION['error'] = "Erreur lors de la mise à jour du profil.";
            }
        }
        
        $user_id = $_SESSION['user_id'];
        $user_info = $this->user->findById($user_id);
        
        ob_start();
        require_once APP_PATH . 'views/profile/edit.php';
        $content = ob_get_clean();
        require_once APP_PATH . 'views/layout.php';
    }
}
