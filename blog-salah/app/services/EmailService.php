<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $mailer;
    
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        
        // Load mail configuration
        $config = require_once ROOT_PATH . 'config/mail.php';
        
        // Configuration du serveur
        $this->mailer->isSMTP();
        $this->mailer->Host = $config['smtp_host'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $config['smtp_username'];
        $this->mailer->Password = $config['smtp_password'];
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = $config['smtp_port'];
        
        // Configuration de l'expéditeur
        $this->mailer->setFrom($config['from_email'], $config['from_name']);
        $this->mailer->CharSet = 'UTF-8';
    }
    
    public function sendPasswordReset($to, $resetLink) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);
            
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Réinitialisation de votre mot de passe';
            
            // Corps du message en HTML
            $this->mailer->Body = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        .container { padding: 20px; }
                        .button {
                            display: inline-block;
                            padding: 10px 20px;
                            background-color: #4CAF50;
                            color: white;
                            text-decoration: none;
                            border-radius: 5px;
                            margin: 20px 0;
                        }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <h2>Réinitialisation de votre mot de passe</h2>
                        <p>Vous avez demandé la réinitialisation de votre mot de passe.</p>
                        <p>Cliquez sur le bouton ci-dessous pour réinitialiser votre mot de passe :</p>
                        <a href='{$resetLink}' class='button'>Réinitialiser mon mot de passe</a>
                        <p>Ce lien expirera dans 1 heure.</p>
                        <p>Si vous n'avez pas demandé cette réinitialisation, vous pouvez ignorer cet email.</p>
                    </div>
                </body>
                </html>";
            
            // Version texte pour les clients qui ne supportent pas le HTML
            $this->mailer->AltBody = "
                Réinitialisation de votre mot de passe
                
                Vous avez demandé la réinitialisation de votre mot de passe.
                
                Cliquez sur le lien suivant pour réinitialiser votre mot de passe :
                {$resetLink}
                
                Ce lien expirera dans 1 heure.
                
                Si vous n'avez pas demandé cette réinitialisation, vous pouvez ignorer cet email.";
            
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Erreur lors de l'envoi de l'email : " . $e->getMessage());
            return false;
        }
    }
}
