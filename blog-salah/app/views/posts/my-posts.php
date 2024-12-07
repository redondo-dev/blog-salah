<?php $title = 'Mes Articles'; ?>

<div class="container py-5">
    <!-- En-tête avec barre de recherche -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Mes Articles</h1>
        <div>
            <a href="index.php?controller=profile&action=index" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Retour au profil
            </a>
            <a href="index.php?controller=post&action=create" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvel article
            </a>
        </div>
    </div>

    <!-- Liste des articles -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php if (empty($posts)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    Vous n'avez pas encore publié d'articles.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($post['image'])): ?>
                            <img src="<?php echo htmlspecialchars($post['image']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($post['title']); ?>"
                                 style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                            <p class="card-text">
                                <?php echo substr(htmlspecialchars($post['content']), 0, 150) . '...'; ?>
                            </p>
                        </div>
                        <div class="card-footer bg-transparent border-top-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="far fa-clock me-1"></i>
                                    <?php echo date('d/m/Y', strtotime($post['created_at'])); ?>
                                </small>
                                <div class="btn-group">
                                    <a href="index.php?controller=post&action=view&id=<?php echo $post['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>Voir
                                    </a>
                                    <a href="index.php?controller=post&action=edit&id=<?php echo $post['id']; ?>" 
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit me-1"></i>Modifier
                                    </a>
                                    <a href="index.php?controller=post&action=delete&id=<?php echo $post['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">
                                        <i class="fas fa-trash-alt me-1"></i>Supprimer
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav aria-label="Navigation des pages" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="index.php?controller=post&action=myPosts&page=<?php echo $current_page - 1; ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $current_page == $i ? 'active' : ''; ?>">
                        <a class="page-link" href="index.php?controller=post&action=myPosts&page=<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="index.php?controller=post&action=myPosts&page=<?php echo $current_page + 1; ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
