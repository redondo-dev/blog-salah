<?php
$title = 'Inscription';
ob_start();
?>

<div class="auth-container bg-register">
    <div class="auth-box">
        <h1 class="text-center mb-4">
            <i class="fas fa-user-plus text-primary mb-3"></i><br>
            Inscription
        </h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="index.php?controller=auth&action=register" method="post" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="invalid-feedback">
                    Veuillez choisir un nom d'utilisateur.
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="invalid-feedback">
                    Veuillez entrer une adresse email valide.
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" class="form-control" id="password" name="password" 
                           required pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', 'toggleIcon1')">
                        <i class="fas fa-eye" id="toggleIcon1"></i>
                    </button>
                </div>
                <div class="form-text">
                    Le mot de passe doit contenir au moins 8 caractères, dont des lettres et des chiffres.
                </div>
            </div>

            <div class="mb-4">
                <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password', 'toggleIcon2')">
                        <i class="fas fa-eye" id="toggleIcon2"></i>
                    </button>
                </div>
                <div class="invalid-feedback">
                    Les mots de passe ne correspondent pas.
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-user-plus me-2"></i>S'inscrire
                </button>
            </div>
        </form>

        <div class="text-center mt-4">
            <p class="mb-0">Déjà un compte ?</p>
            <a href="index.php?controller=auth&action=login" class="text-primary text-decoration-none">
                <i class="fas fa-sign-in-alt me-1"></i>Se connecter
            </a>
        </div>
    </div>
</div>

<script>
// Validation du formulaire
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            
            // Vérification des mots de passe
            const password = document.getElementById('password')
            const confirm = document.getElementById('confirm_password')
            if (password.value !== confirm.value) {
                confirm.setCustomValidity('Les mots de passe ne correspondent pas')
                event.preventDefault()
                event.stopPropagation()
            } else {
                confirm.setCustomValidity('')
            }
            
            form.classList.add('was-validated')
        }, false)
    })
})()

// Toggle password visibility
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<?php
$content = ob_get_clean();
require_once APP_PATH . 'views/layouts/auth.php';
?>
