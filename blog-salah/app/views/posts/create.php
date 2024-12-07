<?php require_once APP_PATH . 'views/layouts/header.php'; ?>

<div class="create-post-bg">
    <div class="container py-5">
        <div class="glass-container-post fade-in">
            <h1 class="text-center text-white mb-4">Créer un nouvel article</h1>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <form action="index.php?controller=post&action=create" method="POST" enctype="multipart/form-data" class="post-form">
                <div class="mb-3">
                    <label for="title" class="form-label">Titre</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Contenu</label>
                    <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Image (optionnelle)</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-4">Publier</button>
                    <a href="index.php?controller=post&action=index" class="btn btn-secondary ms-2">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once APP_PATH . 'views/layouts/footer.php'; ?>
