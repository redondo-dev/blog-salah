# Blog PHP MVC

Un blog moderne développé en PHP avec une architecture MVC (Model-View-Controller) et une interface utilisateur responsive.

## Fonctionnalités

- 👤 Système d'authentification complet (inscription, connexion, déconnexion)
- 👑 Gestion des rôles (administrateur et utilisateur)
- 📝 Gestion complète des articles (CRUD)
- 🖼️ Upload d'images pour les articles
- 🔒 Sécurité renforcée
- 📱 Interface responsive avec Bootstrap 5

## Prérequis

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Apache avec mod_rewrite activé
- Extensions PHP requises :
  - PDO
  - PDO_MySQL
  - GD (pour les images)

## Installation

1. Clonez ce dépôt dans votre répertoire web :
```bash
git clone https://github.com/votre-username/blog-php.git
```

2. Créez une base de données MySQL et importez le schéma :
```bash
mysql -u root -p < config/schema.sql
```

3. Configurez la base de données dans `config/database.php` :
```php
private $host = "localhost";
private $db_name = "blog_php";
private $username = "votre_username";
private $password = "votre_password";
```

4. Assurez-vous que les permissions des dossiers sont correctes :
```bash
chmod 755 -R public/
chmod 777 -R public/assets/images/
```

5. Créez un compte administrateur en modifiant le mot de passe dans `config/schema.sql` avant l'importation.

## Structure du projet

```
blog-php/
├── app/
│   ├── controllers/    # Contrôleurs
│   ├── models/        # Modèles
│   └── views/         # Vues
├── config/           # Configuration
├── public/           # Fichiers publics
│   ├── assets/       # CSS, JS, images
│   └── index.php     # Point d'entrée
└── .htaccess        # Configuration Apache
```

## Sécurité

- Protection contre les injections SQL avec PDO
- Hashage des mots de passe avec password_hash()
- Protection XSS
- Protection CSRF
- Validation des entrées utilisateur
- Gestion sécurisée des uploads de fichiers

## Utilisation

1. Accédez à la page d'accueil : `http://votre-domaine/blog-php/`
2. Créez un compte utilisateur ou connectez-vous
3. Pour l'accès administrateur :
   - Email : admin@example.com
   - Mot de passe : admin123 (à changer en production)

## Contribution

Les contributions sont les bienvenues ! N'hésitez pas à :

1. Fork le projet
2. Créer une branche pour votre fonctionnalité
3. Commit vos changements
4. Push sur votre fork
5. Créer une Pull Request

## Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.
