<?php $title = 'Mon Profil'; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h1 class="h3 mb-0">Mon Profil</h1>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php 
                                echo $_SESSION['success'];
                                unset($_SESSION['success']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <i class="fas fa-user-circle fa-5x text-primary"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h2 class="h4 mb-3"><?php echo htmlspecialchars($user_info['username']); ?></h2>
                            <p class="text-muted mb-2">
                                <i class="fas fa-envelope me-2"></i>
                                <?php echo htmlspecialchars($user_info['email']); ?>
                            </p>
                            <p class="text-muted mb-3">
                                <i class="fas fa-user-tag me-2"></i>
                                Rôle: <?php echo ucfirst(htmlspecialchars($user_info['role'])); ?>
                            </p>
                            <a href="index.php?controller=profile&action=edit" class="btn btn-primary">
                                <i class="fas fa-edit me-2"></i>Modifier mon profil
                            </a>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h3 class="h5 mb-3">Actions rapides</h3>
                            <div class="d-grid gap-2 d-md-flex">
                                <a href="index.php?controller=post&action=myPosts" class="btn btn-outline-primary">
                                    <i class="fas fa-newspaper me-2"></i>Mes articles
                                </a>
                                <a href="index.php?controller=post&action=create" class="btn btn-outline-success">
                                    <i class="fas fa-plus me-2"></i>Nouvel article
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
