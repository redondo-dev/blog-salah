<?php
$title = 'Réinitialisation du mot de passe';
ob_start();
?>

<div class="auth-container bg-login">
    <div class="auth-box">
        <h1 class="text-center mb-3">
            <i class="fas fa-lock-open text-primary mb-3"></i><br>
            Nouveau mot de passe
        </h1>
        
        <div class="welcome-message mb-4">
            <p>Presque terminé ! 🔒</p>
            <p class="small text-muted">Veuillez choisir votre nouveau mot de passe.</p>
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

        <form id="resetPasswordForm" action="index.php?controller=auth&action=resetPassword" method="post" class="needs-validation" novalidate>
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
            
            <div class="mb-3">
                <label for="new_password" class="form-label">Nouveau mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" class="form-control" id="new_password" name="new_password" required 
                           minlength="8" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password', 'toggleIcon1')">
                        <i class="fas fa-eye" id="toggleIcon1"></i>
                    </button>
                </div>
                <div class="invalid-feedback">
                    Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre.
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
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Enregistrer le nouveau mot de passe
                </button>
                <a href="index.php?controller=auth&action=login" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour à la connexion
                </a>
            </div>
        </form>
    </div>
</div>

<script>
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

// Password validation
document.getElementById('resetPasswordForm').addEventListener('submit', function(event) {
    const password = document.getElementById('new_password');
    const confirm = document.getElementById('confirm_password');
    
    if (password.value !== confirm.value) {
        confirm.setCustomValidity('Les mots de passe ne correspondent pas');
        event.preventDefault();
    } else {
        confirm.setCustomValidity('');
    }
});

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
