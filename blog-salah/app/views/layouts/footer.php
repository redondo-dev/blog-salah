    <footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="mb-3">À propos</h5>
                    <p>Un blog moderne pour partager vos pensées et vos expériences avec le monde.</p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="mb-3">Liens rapides</h5>
                    <ul class="list-unstyled">
                        <li><a href="/blog-salah/index.php" class="text-decoration-none">Accueil</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li><a href="/blog-salah/index.php?controller=post&action=create" class="text-decoration-none">Créer un article</a></li>
                            <li><a href="/blog-salah/index.php?controller=post&action=myPosts" class="text-decoration-none">Mes articles</a></li>
                        <?php else: ?>
                            <li><a href="/blog-salah/index.php?controller=auth&action=login" class="text-decoration-none">Connexion</a></li>
                            <li><a href="/blog-salah/index.php?controller=auth&action=register" class="text-decoration-none">Inscription</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">Suivez-nous</h5>
                    <div class="social-links">
                        <a href="#" class="text-decoration-none me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-decoration-none me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-decoration-none me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-decoration-none"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <p class="text-center mb-0">
                &copy; <?php echo date('Y'); ?> Mon Blog. Tous droits réservés.
            </p>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
<!-- Animation au défilement -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fadeElements = document.querySelectorAll('.fade-in');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        });

        fadeElements.forEach(element => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(20px)';
            element.style.transition = 'all 0.5s ease';
            observer.observe(element);
        });
    });
</script>
</body>
</html>
