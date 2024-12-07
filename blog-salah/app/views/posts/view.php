<?php require_once APP_PATH . 'views/layouts/header.php'; ?>

<div class="view-post-bg">
    <div class="container py-5">
        <div class="glass-container-post fade-in">
            <div class="post-content">
                <h1 class="text-center"><?php echo htmlspecialchars($post['title']); ?></h1>
                
                <div class="post-meta text-center">
                    <span class="me-3">
                        <i class="fas fa-user me-1"></i>
                        <?php echo htmlspecialchars($post['username']); ?>
                    </span>
                    <span class="me-3">
                        <i class="fas fa-calendar-alt me-1"></i>
                        <?php echo date('d/m/Y', strtotime($post['created_at'])); ?>
                    </span>
                </div>

                <?php if (!empty($post['image'])): ?>
                    <div class="text-center mb-4">
                        <img src="/blog-salah/public/uploads/<?php echo htmlspecialchars($post['image']); ?>" 
                             alt="<?php echo htmlspecialchars($post['title']); ?>"
                             class="img-fluid rounded">
                    </div>
                <?php endif; ?>

                <div class="post-body">
                    <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                </div>

                <div class="reaction-buttons">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <button class="btn-reaction btn-like <?php echo $hasLiked ? 'active' : ''; ?>" 
                                onclick="toggleReaction(<?php echo $post['id']; ?>, 'like')" 
                                id="likeBtn-<?php echo $post['id']; ?>">
                            <i class="fas fa-thumbs-up"></i>
                            <span class="reaction-count" id="likeCount-<?php echo $post['id']; ?>">
                                <?php echo $likeCount; ?>
                            </span>
                        </button>
                        <button class="btn-reaction btn-dislike <?php echo $hasDisliked ? 'active' : ''; ?>" 
                                onclick="toggleReaction(<?php echo $post['id']; ?>, 'dislike')" 
                                id="dislikeBtn-<?php echo $post['id']; ?>">
                            <i class="fas fa-thumbs-down"></i>
                            <span class="reaction-count" id="dislikeCount-<?php echo $post['id']; ?>">
                                <?php echo $dislikeCount ?? 0; ?>
                            </span>
                        </button>
                    <?php else: ?>
                        <div class="text-muted">
                            <i class="fas fa-thumbs-up"></i>
                            <span><?php echo $likeCount; ?></span>
                            <i class="fas fa-thumbs-down ms-3"></i>
                            <span><?php echo $dislikeCount ?? 0; ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $post['user_id'] || $_SESSION['role'] == 'admin')): ?>
                    <div class="mt-4 text-center">
                        <a href="index.php?controller=post&action=edit&id=<?php echo $post['id']; ?>" 
                           class="btn btn-primary me-2">
                            <i class="fas fa-edit me-1"></i>Modifier
                        </a>
                        <button type="button" class="btn btn-danger" 
                                onclick="confirmDelete(<?php echo $post['id']; ?>)">
                            <i class="fas fa-trash-alt me-1"></i>Supprimer
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.btn-reaction {
    background: none;
    border: none;
    color: #6c757d;
    padding: 0;
    font-size: 1.1em;
    transition: all 0.3s ease;
}

.btn-reaction:hover {
    transform: scale(1.1);
}

.btn-reaction.active {
    color: #dc3545;
}

.btn-like {
    margin-right: 10px;
}

.reaction-count {
    margin-left: 5px;
}

.reaction-buttons {
    margin-top: 20px;
    text-align: center;
}
</style>

<script>
function confirmDelete(postId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) {
        window.location.href = 'index.php?controller=post&action=delete&id=' + postId;
    }
}

function toggleReaction(postId, reaction) {
    fetch('index.php?controller=post&action=toggleReaction&id=' + postId + '&reaction=' + reaction, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        const likeBtn = document.getElementById('likeBtn-' + postId);
        const dislikeBtn = document.getElementById('dislikeBtn-' + postId);
        const likeCount = document.getElementById('likeCount-' + postId);
        const dislikeCount = document.getElementById('dislikeCount-' + postId);
        
        if (data.success) {
            if (reaction === 'like') {
                likeBtn.classList.toggle('active');
                likeCount.textContent = data.likeCount;
                if (data.disliked) {
                    dislikeBtn.classList.remove('active');
                    dislikeCount.textContent = data.dislikeCount;
                }
            } else {
                dislikeBtn.classList.toggle('active');
                dislikeCount.textContent = data.dislikeCount;
                if (data.liked) {
                    likeBtn.classList.remove('active');
                    likeCount.textContent = data.likeCount;
                }
            }
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

<?php require_once APP_PATH . 'views/layouts/footer.php'; ?>
