<?php require_once APP_PATH . 'views/layouts/header.php'; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-12 mb-4">
            <h1 class="text-center mb-4">Articles récents</h1>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="text-center">
                    <a href="/blog-salah/index.php?controller=post&action=create" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Créer un nouvel article
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <?php foreach ($posts as $post): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="post-card fade-in">
                    <?php if (!empty($post['image'])): ?>
                        <img src="/blog-salah/public/uploads/<?php echo htmlspecialchars($post['image']); ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($post['title']); ?>">
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                        
                        <div class="post-meta mb-3">
                            <span class="me-3">
                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($post['username']); ?>
                            </span>
                            <span>
                                <i class="fas fa-calendar-alt me-1"></i>
                                <?php echo date('d/m/Y', strtotime($post['created_at'])); ?>
                            </span>
                        </div>

                        <p class="card-text">
                            <?php echo substr(htmlspecialchars($post['content']), 0, 150) . '...'; ?>
                        </p>

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="reaction-stats">
                                <span class="me-3" title="J'aime">
                                    <i class="fas fa-thumbs-up text-success"></i>
                                    <span class="ms-1"><?php echo $post['like_count'] ?? 0; ?></span>
                                </span>
                                <span title="Je n'aime pas">
                                    <i class="fas fa-thumbs-down text-danger"></i>
                                    <span class="ms-1"><?php echo $post['dislike_count'] ?? 0; ?></span>
                                </span>
                            </div>
                            <a href="/blog-salah/index.php?controller=post&action=view&id=<?php echo $post['id']; ?>" 
                               class="btn btn-outline-primary">
                                Lire la suite
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once APP_PATH . 'views/layouts/footer.php'; ?>
