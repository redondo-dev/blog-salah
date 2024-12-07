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
                                <img src="/blo-php-salah/public/assets/images/<?php echo htmlspecialchars($post['image']); ?>" 
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
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function confirmDelete(postId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) {
        window.location.href = `index.php?controller=post&action=delete&id=${postId}`;
    }
}
</script>
