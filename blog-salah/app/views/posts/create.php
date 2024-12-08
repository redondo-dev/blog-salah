<?php ?>

<div class="create-article-background">
    <div class="container">
        <div class="create-article-form-container">
            <h2 class="text-center mb-4 text-white">
                <i class="fas fa-pencil-alt text-white me-2"></i>Créer un Nouvel Article
            </h2>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="index.php?controller=post&action=create" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Titre de l'article</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 text-white">
                            <i class="fas fa-heading"></i>
                        </span>
                        <input type="text" class="form-control" id="title" name="title" 
                               placeholder="Entrez le titre de votre article" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="content" class="form-label">Contenu</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 text-white">
                            <i class="fas fa-paragraph"></i>
                        </span>
                        <textarea class="form-control" id="content" name="content" 
                                  placeholder="Écrivez votre article ici..." rows="6" required></textarea>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="image" class="form-label">Image de couverture</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 text-white">
                            <i class="fas fa-image"></i>
                        </span>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    </div>
                    <small class="text-white-50">Formats acceptés : JPG, PNG (max 5 Mo)</small>
                </div>
                
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save me-2"></i>Publier l'article
                    </button>
                    <a href="index.php?controller=post&action=index" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php ?>
