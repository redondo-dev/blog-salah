<?php
$title = 'Mon Profil';
ob_start();
?>

<div class="container">
    <div class="profile-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-4 text-center">
                <?php 
                $avatarPath = !empty($userData['avatar']) ? 'public/assets/images/' . $userData['avatar'] : 'public/assets/images/default-avatar.png';
                ?>
                <img src="<?php echo htmlspecialchars($avatarPath); ?>" 
                     alt="Profile Avatar" 
                     class="profile-avatar">
            </div>
            <div class="col-md-8">
                <h2 class="mb-3">
                    <i class="fas fa-user text-primary me-2"></i>
                    <?php echo htmlspecialchars($userData['username']); ?>
                </h2>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2">
                            <i class="fas fa-envelope text-muted me-2"></i>
                            <?php echo htmlspecialchars($userData['email']); ?>
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-calendar-alt text-muted me-2"></i>
                            Inscrit le <?php echo date('d/m/Y', strtotime($userData['created_at'])); ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="fas fa-edit me-1"></i> Modifier le profil
                        </button>
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <i class="fas fa-key me-1"></i> Changer le mot de passe
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h3 class="mb-3">
                <i class="fas fa-newspaper text-primary me-2"></i>
                Mes Articles (<?php echo count($posts); ?>)
            </h3>
        </div>
    </div>

    <?php if(empty($posts)): ?>
        <div class="alert alert-info text-center" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            Vous n'avez pas encore publié d'articles.
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach($posts as $post): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <?php 
                        $imagePath = !empty($post['image']) ? 'public/assets/images/' . $post['image'] : 'public/assets/images/default-post.jpg';
                        ?>
                        <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($post['title']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                            <p class="card-text text-muted">
                                <?php echo substr(strip_tags($post['content']), 0, 100) . '...'; ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="far fa-calendar me-1"></i>
                                    <?php echo date('d/m/Y', strtotime($post['created_at'])); ?>
                                </small>
                                <div class="btn-group">
                                    <a href="index.php?controller=post&action=view&id=<?php echo $post['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>Voir
                                    </a>
                                    <a href="index.php?controller=post&action=edit&id=<?php echo $post['id']; ?>" 
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit me-1"></i>Éditer
                                    </a>
                                    <a href="index.php?controller=post&action=delete&id=<?php echo $post['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">
                                        <i class="fas fa-trash me-1"></i>Supprimer
                                    </a>
                                </div>
                                <div class="reaction-container">
                                    <?php if (isset($_SESSION['user_id']) && $post['user_id'] != $_SESSION['user_id']): ?>
                                    <div class="reaction-wrapper">
                                        <div class="reaction-tooltip like-tooltip">
                                            <button 
                                                class="reaction-btn like-btn <?php echo $post['hasLiked'] ? 'active' : ''; ?>"
                                                onclick="toggleReaction(<?php echo $post['id']; ?>, 'like')">
                                                <i class="fas fa-thumbs-up"></i>
                                                <span class="reaction-count like-count"><?php echo $post['likeCount'] ?? 0; ?></span>
                                            </button>
                                            <span class="tooltip-text">J'aime</span>
                                        </div>
                                        <div class="reaction-tooltip dislike-tooltip">
                                            <button 
                                                class="reaction-btn dislike-btn <?php echo $post['hasDisliked'] ? 'active' : ''; ?>"
                                                onclick="toggleReaction(<?php echo $post['id']; ?>, 'dislike')">
                                                <i class="fas fa-thumbs-down"></i>
                                                <span class="reaction-count dislike-count"><?php echo $post['dislikeCount'] ?? 0; ?></span>
                                            </button>
                                            <span class="tooltip-text">Je n'aime pas</span>
                                        </div>
                                    </div>
                                    <?php else: ?>
                                    <div class="reaction-summary">
                                        <div class="like-summary">
                                            <i class="fas fa-thumbs-up"></i>
                                            <span><?php echo $post['likeCount'] ?? 0; ?></span>
                                        </div>
                                        <div class="dislike-summary">
                                            <i class="fas fa-thumbs-down"></i>
                                            <span><?php echo $post['dislikeCount'] ?? 0; ?></span>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
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
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
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

    .reaction-container {
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }

    .reaction-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px;
        border-radius: 8px;
        background-color: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .reaction-tooltip {
        position: relative;
    }

    .reaction-tooltip .tooltip-text {
        position: absolute;
        top: -30px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #333;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        visibility: hidden;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .reaction-tooltip:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }

    .reaction-btn {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 8px 16px;
        border: none;
        border-radius: 8px;
        background-color: transparent;
        color: white;
        cursor: pointer;
        position: relative;
        z-index: 1;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .reaction-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
        opacity: 0;
        z-index: -1;
        transition: opacity 0.3s ease;
    }

    .reaction-btn:hover::before {
        opacity: 1;
    }

    .reaction-btn i {
        font-size: 16px;
        transition: transform 0.2s ease;
    }

    .reaction-btn:hover i {
        transform: scale(1.1);
    }

    .like-btn {
        background: linear-gradient(135deg, rgba(0, 128, 0, 0.2), rgba(0, 128, 0, 0.1));
        border: 1px solid rgba(0, 128, 0, 0.3);
    }

    .like-btn.active {
        background: linear-gradient(135deg, rgba(0, 128, 0, 0.5), rgba(0, 128, 0, 0.4));
        box-shadow: 0 0 10px rgba(0, 128, 0, 0.3);
    }

    .dislike-btn {
        background: linear-gradient(135deg, rgba(128, 0, 0, 0.2), rgba(128, 0, 0, 0.1));
        border: 1px solid rgba(128, 0, 0, 0.3);
    }

    .dislike-btn.active {
        background: linear-gradient(135deg, rgba(128, 0, 0, 0.5), rgba(128, 0, 0, 0.4));
        box-shadow: 0 0 10px rgba(128, 0, 0, 0.3);
    }

    .reaction-btn.loading {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .reaction-btn.loading i {
        animation: pulse 1s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(0.9);
        }
    }

    .reaction-btn.success i {
        color: #4CAF50;
        animation: success 0.5s;
    }

    .reaction-btn.error i {
        color: #F44336;
        animation: error 0.5s;
    }

    @keyframes success {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.2) rotate(10deg); }
    }

    @keyframes error {
        0%, 100% { transform: scale(1) rotate(0deg); }
        25% { transform: scale(1.1) rotate(-5deg); }
        75% { transform: scale(1.1) rotate(5deg); }
    }

    .reaction-summary {
        display: flex;
        align-items: center;
        gap: 12px;
        background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
        border: 1px solid rgba(255,255,255,0.2);
        padding: 8px 12px;
        border-radius: 8px;
    }

    .like-summary, .dislike-summary {
        display: flex;
        align-items: center;
        gap: 6px;
        color: rgba(255,255,255,0.8);
    }

    .like-summary i, .dislike-summary i {
        font-size: 16px;
        opacity: 0.8;
    }

    .like-summary span, .dislike-summary span {
        font-size: 14px;
        font-weight: 500;
    }
</style>

<script>
function toggleReaction(postId, reaction) {
    const reactionWrapper = document.querySelector(`.reaction-wrapper button[onclick="toggleReaction(${postId}, '${reaction}')"]`);
    
    // Désactiver le bouton pendant la requête
    reactionWrapper.disabled = true;
    reactionWrapper.classList.add('loading');

    fetch(`index.php?controller=post&action=toggleReaction&id=${postId}&reaction=${reaction}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                try {
                    const errorData = JSON.parse(text);
                    throw new Error(errorData.message || 'Erreur de réaction');
                } catch (e) {
                    throw new Error(text || 'Erreur réseau');
                }
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const likeBtn = document.querySelector(`button[onclick="toggleReaction(${postId}, 'like')"]`);
            const dislikeBtn = document.querySelector(`button[onclick="toggleReaction(${postId}, 'dislike')"]`);
            
            // Mise à jour des compteurs
            likeBtn.querySelector('.reaction-count').textContent = data.likeCount;
            dislikeBtn.querySelector('.reaction-count').textContent = data.dislikeCount;
            
            // Gestion des états des boutons
            likeBtn.classList.toggle('active', data.liked);
            dislikeBtn.classList.toggle('active', data.disliked);

            // Animation de feedback
            reactionWrapper.classList.add('success');
            setTimeout(() => {
                reactionWrapper.classList.remove('success');
            }, 500);
        } else {
            // Animation d'erreur
            reactionWrapper.classList.add('error');
            setTimeout(() => {
                reactionWrapper.classList.remove('error');
            }, 500);
            
            alert(data.message || 'Une erreur est survenue');
        }
    })
    .catch(error => {
        console.error('Erreur de réaction:', error);
        
        // Animation d'erreur
        reactionWrapper.classList.add('error');
        setTimeout(() => {
            reactionWrapper.classList.remove('error');
        }, 500);

        alert(error.message || 'Une erreur est survenue lors de la réaction');
    })
    .finally(() => {
        // Réactiver le bouton
        reactionWrapper.disabled = false;
        reactionWrapper.classList.remove('loading');
    });
}
</script>

<?php
$content = ob_get_clean();
require_once APP_PATH . 'views/layouts/main.php';
?>
