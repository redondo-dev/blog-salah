<?php
$title = 'Connexion';
ob_start();
?>

<div class="auth-container bg-login">
    <div class="auth-box">
        <h1 class="text-center mb-3">
            <i class="fas fa-sign-in-alt text-primary mb-3"></i><br>
            Connexion
        </h1>
        
        <div class="welcome-message">
            <p>Bienvenue sur notre plateforme ! 👋</p>
            <p class="small text-muted">Connectez-vous pour accéder à votre espace personnel</p>
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

        <form id="loginForm" action="index.php?controller=auth&action=login" method="post" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" class="form-control" id="username" name="username" value="" required>
                </div>
                <div class="invalid-feedback">
                    Veuillez entrer votre nom d'utilisateur.
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" class="form-control" id="password" name="password" value="" required>
                </div>
                <div class="invalid-feedback">
                    Veuillez entrer votre mot de passe.
                </div>
                <div class="mt-2 text-end">
                    <a href="index.php?controller=auth&action=forgotPassword" class="text-primary text-decoration-none small">Mot de passe oublié ?</a>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                </button>
            </div>

            <div class="mt-4 text-center">
                <p class="mb-0">
                    <a href="index.php?controller=auth&action=register" class="text-primary text-decoration-none">
                        <i class="fas fa-user-plus me-1"></i>Créer un compte
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>

<script>
// Validation du formulaire
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
})()

// Réinitialiser le formulaire après la soumission
document.getElementById('loginForm').addEventListener('submit', function() {
    setTimeout(() => {
        this.reset();
        this.classList.remove('was-validated');
    }, 100);
});
</script>

<?php
$content = ob_get_clean();
require_once APP_PATH . 'views/layouts/auth.php';
?>
