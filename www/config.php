<?php
// config.php - Version avec support des variables d'environnement

// Fonction helper pour récupérer les variables d'environnement
function env(string $key, $default = null) {
    $value = getenv($key);
    return $value !== false ? $value : $default;
}

// Configuration Base de données PostgreSQL
// Utilise les variables d'environnement si disponibles, sinon valeurs par défaut
define('DB_HOST', env('POSTGRES_HOST', 'db'));
define('DB_PORT', env('POSTGRES_PORT', '5432'));
define('DB_NAME', env('POSTGRES_DB', 'devdb'));
define('DB_USER', env('POSTGRES_USER', 'devuser'));
define('DB_PASS', env('POSTGRES_PASSWORD', 'devpass'));

// Configuration du site
define('SITE_URL', env('SITE_URL', 'http://localhost:8080'));
define('SITE_NAME', 'Mini WordPress');

// Configuration email
define('MAIL_HOST', env('MAIL_HOST', 'mailhog'));          // Serveur SMTP
define('MAIL_PORT', env('MAIL_PORT', '1025'));             // Port SMTP
define('MAIL_USERNAME', env('MAIL_USERNAME', ''));          // Username (vide pour MailHog)
define('MAIL_PASSWORD', env('MAIL_PASSWORD', ''));          // Password (vide pour MailHog)
define('MAIL_ENCRYPTION', env('MAIL_ENCRYPTION', ''));      // tls, ssl ou vide
define('MAIL_FROM', env('MAIL_FROM', 'noreply@miniwordpress.local'));
define('MAIL_FROM_NAME', env('MAIL_FROM_NAME', 'Mini WordPress'));
define('MAIL_CONTACT', env('MAIL_CONTACT', 'contact@miniwordpress.local'));

// Clé de sécurité (IMPORTANT: Change cette valeur en production !)
define('SECRET_KEY', env('SECRET_KEY', 'votre_cle_secrete_a_changer_123456'));

// Activation du mode debug
define('DEBUG_MODE', env('DEBUG_MODE', true));

// Afficher les erreurs en mode debug
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}