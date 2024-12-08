<?php $title = 'Liste des articles'; ?>

<div class="container py-5">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Articles du blog</h1>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="index.php?controller=post&action=create" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Nouvel article
            </a>
        <?php endif; ?>
    </div>

    <?php if (empty($posts)): ?>
        <div class="text-center py-5">
            <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
            <h3>Aucun article pour le moment</h3>
            <p class="text-muted">Soyez le premier à publier un article !</p>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 posts-grid">
            <?php foreach ($posts as $post): ?>
                <div class="col">
                    <article class="card h-100">
                        <?php if (!empty($post['image'])): ?>
                            <div class="card-img-container">
                                <img src="public/assets/images/<?php echo htmlspecialchars($post['image']); ?>" 
                                     class="card-img-top" alt="<?php echo htmlspecialchars($post['title']); ?>"
                                     loading="lazy">
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="index.php?controller=post&action=view&id=<?php echo $post['id']; ?>" 
                                   class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </h5>
                            <p class="card-text text-muted">
                                <?php echo substr(htmlspecialchars($post['content']), 0, 150) . '...'; ?>
                            </p>
                        </div>
                        
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="post-meta">
                                    <i class="fas fa-user-circle me-1"></i>
                                    <span class="post-author"><?php echo htmlspecialchars($post['username']); ?></span>
                                    <br>
                                    <small class="text-muted">
                                        <i class="far fa-clock me-1"></i>
                                        <?php echo date('d/m/Y', strtotime($post['created_at'])); ?>
                                    </small>
                                </div>
                                
                                <div class="d-flex align-items-center">
                                    <div class="reaction-buttons d-flex align-items-center">
                                        <?php if (isset($_SESSION['user_id']) && $post['user_id'] != $_SESSION['user_id']): ?>
                                        <button 
                                            class="btn btn-outline-primary btn-sm me-2 d-flex align-items-center <?php echo $post['hasLiked'] ? 'active' : ''; ?>"
                                            onclick="toggleReaction(<?php echo $post['id']; ?>, 'like')">
                                            <i class="fas fa-thumbs-up me-1"></i>
                                            <span class="like-count"><?php echo $post['likeCount'] ?? 0; ?></span>
                                        </button>
                                        <button 
                                            class="btn btn-outline-danger btn-sm d-flex align-items-center <?php echo $post['hasDisliked'] ? 'active' : ''; ?>"
                                            onclick="toggleReaction(<?php echo $post['id']; ?>, 'dislike')">
                                            <i class="fas fa-thumbs-down me-1"></i>
                                            <span class="dislike-count"><?php echo $post['dislikeCount'] ?? 0; ?></span>
                                        </button>
                                        <?php else: ?>
                                        <div class="text-muted">
                                            <i class="fas fa-thumbs-up me-1"></i> <?php echo $post['likeCount'] ?? 0; ?>
                                            <i class="fas fa-thumbs-down ms-2 me-1"></i> <?php echo $post['dislikeCount'] ?? 0; ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if (isset($_SESSION['user_id']) && 
                                            ($post['user_id'] == $_SESSION['user_id'] || $_SESSION['role'] == 'admin')): ?>
                                        <div class="btn-group">
                                            <a href="index.php?controller=post&action=edit&id=<?php echo $post['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDelete(<?php echo $post['id']; ?>)">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    .reaction-buttons .btn-sm {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .reaction-buttons .btn-sm i {
        margin-right: 0.25rem;
    }
    
    .reaction-buttons .btn-sm.active.btn-outline-primary {
        background-color: rgba(13, 110, 253, 0.2);
        color: #0d6efd;
    }
    
    .reaction-buttons .btn-sm.active.btn-outline-danger {
        background-color: rgba(220, 53, 69, 0.2);
        color: #dc3545;
    }
</style>

<script>
function confirmDelete(postId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) {
        window.location.href = `index.php?controller=post&action=delete&id=${postId}`;
    }
}

function toggleReaction(postId, reaction) {
    fetch(`index.php?controller=post&action=toggleReaction&id=${postId}&reaction=${reaction}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Réponse réseau incorrecte');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const likeBtn = document.querySelector(`button[onclick="toggleReaction(${postId}, 'like')"]`);
            const dislikeBtn = document.querySelector(`button[onclick="toggleReaction(${postId}, 'dislike')"]`);
            
            // Mise à jour des compteurs
            likeBtn.querySelector('.like-count').textContent = data.likeCount;
            dislikeBtn.querySelector('.dislike-count').textContent = data.dislikeCount;
            
            // Gestion des états des boutons
            likeBtn.classList.toggle('active', data.liked);
            dislikeBtn.classList.toggle('active', data.disliked);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue lors de la réaction.');
    });
}
</script>
