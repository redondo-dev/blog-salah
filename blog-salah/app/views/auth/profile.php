<?php
$title = 'Mon Profil';
ob_start();
?>

<div class="bg-profile">
    <div class="container">
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <h2 class="mb-1"><?php echo htmlspecialchars($userData['username']); ?></h2>
            <p class="mb-0"><?php echo ucfirst($userData['role']); ?></p>
            
            <div class="profile-stats">
                <div class="row">
                    <div class="col-4 profile-stats-item">
                        <div class="profile-stats-value"><?php echo count($posts); ?></div>
                        <div class="profile-stats-label">Articles</div>
                    </div>
                    <div class="col-4 profile-stats-item">
                        <div class="profile-stats-value">0</div>
                        <div class="profile-stats-label">Commentaires</div>
                    </div>
                    <div class="col-4 profile-stats-item">
                        <div class="profile-stats-value"><?php echo date('d/m/Y', strtotime($userData['created_at'])); ?></div>
                        <div class="profile-stats-label">Membre depuis</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-4">
    <div class="row">
        <!-- Profile Info Card -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">Informations du profil</h5>
                    <div class="mb-3">
                        <p class="mb-2">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            <?php echo htmlspecialchars($userData['email']); ?>
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-calendar text-primary me-2"></i>
                            Inscrit le <?php echo date('d/m/Y', strtotime($userData['created_at'])); ?>
                        </p>
                    </div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="fas fa-edit me-2"></i>Modifier le profil
                        </button>
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <i class="fas fa-key me-2"></i>Changer le mot de passe
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Posts Card -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">Mes articles</h5>
                        <a href="index.php?controller=post&action=create" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-2"></i>Nouvel article
                        </a>
                    </div>

                    <?php if (empty($posts)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Vous n'avez pas encore publié d'articles.</p>
                            <a href="index.php?controller=post&action=create" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Créer mon premier article
                            </a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                            <div class="card mb-3 border-0 shadow-sm">
                                <div class="row g-0">
                                    <div class="col-md-4">
                                        <img src="<?php echo htmlspecialchars($post['image']); ?>" 
                                             class="img-fluid rounded-start h-100" 
                                             style="object-fit: cover;" 
                                             alt="<?php echo htmlspecialchars($post['title']); ?>">
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body">
                                            <h5 class="card-title mb-2">
                                                <?php echo htmlspecialchars($post['title']); ?>
                                            </h5>
                                            <p class="card-text text-muted small mb-2">
                                                <i class="fas fa-calendar me-2"></i>
                                                <?php echo date('d/m/Y à H:i', strtotime($post['created_at'])); ?>
                                            </p>
                                            <p class="card-text mb-3">
                                                <?php echo substr(htmlspecialchars($post['content']), 0, 100) . '...'; ?>
                                            </p>
                                            <div class="btn-group">
                                                <a href="index.php?controller=post&action=view&id=<?php echo $post['id']; ?>" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i>Voir
                                                </a>
                                                <a href="index.php?controller=post&action=edit&id=<?php echo $post['id']; ?>" 
                                                   class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-edit me-1"></i>Modifier
                                                </a>
                                                <a href="index.php?controller=post&action=delete&id=<?php echo $post['id']; ?>" 
                                                   class="btn btn-outline-danger btn-sm"
                                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">
                                                    <i class="fas fa-trash me-1"></i>Supprimer
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier le profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="index.php?controller=auth&action=updateProfile">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nom d'utilisateur</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo htmlspecialchars($userData['username']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Changer le mot de passe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="index.php?controller=auth&action=updatePassword">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mot de passe actuel</label>
                        <input type="password" class="form-control" id="current_password" 
                               name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" class="form-control" id="new_password" 
                               name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmer le nouveau mot de passe</label>
                        <input type="password" class="form-control" id="confirm_password" 
                               name="confirm_password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-key me-2"></i>Changer le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.bg-profile {
    background-image: linear-gradient(to bottom, #f7f7f7, #ffffff);
    background-size: 100% 300px;
    background-position: 0% 100%;
    height: 300px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.profile-header {
    text-align: center;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    margin: 0 auto;
    color: var(--primary-color);
    font-size: 60px;
}

.profile-stats {
    margin-top: 20px;
}

.profile-stats-item {
    margin-bottom: 20px;
}

.profile-stats-value {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 5px;
}

.profile-stats-label {
    font-size: 16px;
    color: #666;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-5px);
}

.btn-group {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
}

.btn-group .btn {
    border: none;
    padding: 8px 16px;
}

.modal-content {
    border: none;
    border-radius: 15px;
}

.modal-header {
    background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
    color: white;
    border-bottom: none;
}

.modal-header .btn-close {
    color: white;
}
</style>

<?php
$content = ob_get_clean();
require_once APP_PATH . 'views/layouts/main.php';
?>
