<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="card-title mb-4">Modifier l'article</h2>
                
                <form method="POST" action="index.php?controller=post&action=edit&id=<?php echo $post['id']; ?>" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Titre</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?php echo htmlspecialchars($post['title']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Contenu</label>
                        <textarea class="form-control" id="content" name="content" 
                                  rows="10" required><?php echo htmlspecialchars($post['content']); ?></textarea>
                    </div>
                    
                    <?php if ($post['image']): ?>
                    <div class="mb-3">
                        <label class="form-label">Image actuelle</label>
                        <div class="position-relative">
                            <img src="public/assets/images/<?php echo htmlspecialchars($post['image']); ?>" 
                                 class="img-fluid rounded" style="max-height: 200px;" alt="Image actuelle">
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-4">
                        <label for="image" class="form-label">Nouvelle image (optionnel)</label>
                        <div class="input-group">
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <label class="input-group-text" for="image">
                                <i class="fas fa-upload"></i>
                            </label>
                        </div>
                        <div class="form-text">Formats acceptés : JPG, PNG, GIF. Taille maximale : 5MB</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="index.php?controller=post&action=index" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                        <div>
                            <a href="index.php?controller=post&action=delete&id=<?php echo $post['id']; ?>" 
                               class="btn btn-outline-danger me-2"
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');">
                                <i class="fas fa-trash me-2"></i>Supprimer
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Prévisualisation de l'image
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        if (file.size > 5 * 1024 * 1024) { // 5MB
            alert('L\'image est trop volumineuse. Taille maximale : 5MB');
            this.value = '';
            return;
        }
        
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            alert('Format d\'image non supporté. Utilisez JPG, PNG ou GIF.');
            this.value = '';
            return;
        }
    }
});
</script>
