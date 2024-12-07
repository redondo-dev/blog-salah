<?php
$uploadDir = __DIR__ . '/public/assets/images/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
    echo "Dossier images créé avec succès!\n";
} else {
    echo "Le dossier images existe déjà.\n";
}
