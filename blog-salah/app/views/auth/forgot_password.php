<?php
$title = 'Mot de passe oublié';
ob_start();
?>

<div class="auth-container bg-login">
    <div class="auth-box">
        <h1 class="text-center mb-3">
            <i class="fas fa-key text-primary mb-3"></i><br>
            Mot de passe oublié
        </h1>
        
        <div class="welcome-message mb-4">
            <p>Pas de panique ! 🔐</p>
            <p class="small text-muted">Entrez votre adresse e-mail et nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form id="forgotPasswordForm" action="index.php?controller=auth&action=forgotPassword" method="post" class="needs-validation" novalidate>
            <div class="mb-4">
                <label for="email" class="form-label">Adresse e-mail</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="invalid-feedback">
                    Veuillez entrer une adresse e-mail valide.
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i>Envoyer le lien
                </button>
                <a href="index.php?controller=auth&action=login" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour à la connexion
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Form validation
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php
$content = ob_get_clean();
require_once APP_PATH . 'views/layouts/main.php';
?>
