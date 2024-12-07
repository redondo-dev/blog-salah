<?php

require_once ROOT_PATH . 'config/database.php';
require_once APP_PATH . 'models/Post.php';

class HomeController {
    private $db;
    private $post;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->post = new Post($this->db);
    }

    public function index() {
        // Si l'utilisateur est connecté, rediriger vers la liste des articles
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=post&action=index');
            exit();
        }

        // Récupérer les 3 articles les plus récents
        $recent_posts = $this->post->getRecentPosts(3);
        
        // Charger la vue de la page d'accueil
        $title = 'Accueil';
        require_once APP_PATH . 'views/home/index.php';
    }
}
