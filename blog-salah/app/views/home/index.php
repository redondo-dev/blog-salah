<?php require_once APP_PATH . 'views/layouts/header.php'; ?>

<div class="container mt-5">
    <div class="jumbotron">
        <h1 class="display-4">Bienvenue sur notre Blog</h1>
        <p class="lead">Découvrez nos derniers articles et partagez vos pensées.</p>
        <hr class="my-4">
        <p>Connectez-vous pour commencer à publier des articles.</p>
        <a class="btn btn-primary btn-lg" href="index.php?controller=auth&action=login" role="button">Se connecter</a>
        <a class="btn btn-secondary btn-lg" href="index.php?controller=auth&action=register" role="button">S'inscrire</a>
    </div>

    <?php if (!empty($recent_posts)): ?>
        <h2 class="mb-4">Articles récents</h2>
        <div class="row">
            <?php foreach ($recent_posts as $post): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                            <p class="card-text"><?php echo substr(htmlspecialchars($post['content']), 0, 150) . '...'; ?></p>
                            <a href="index.php?controller=post&action=view&id=<?php echo $post['id']; ?>" class="btn btn-primary">Lire plus</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once APP_PATH . 'views/layouts/footer.php'; ?>
