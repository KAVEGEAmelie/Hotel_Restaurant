<?php
// Script de diagnostic pour la production
// Accessible à /debug.php

echo "<h1>🔍 Diagnostic Hotel Restaurant - Production</h1>";
echo "<hr>";

// 1. Vérifier la version PHP
echo "<h2>📋 Informations système</h2>";
echo "<strong>PHP Version:</strong> " . phpversion() . "<br>";
echo "<strong>Date/Heure:</strong> " . date('Y-m-d H:i:s') . "<br>";
echo "<strong>Répertoire de travail:</strong> " . getcwd() . "<br>";

// 2. Vérifier les fichiers Laravel
echo "<h2>📁 Fichiers Laravel</h2>";
$files_to_check = [
    '.env' => '.env',
    'bootstrap/app.php' => 'bootstrap/app.php',
    'config/app.php' => 'config/app.php',
    'public/index.php' => 'public/index.php'
];

foreach ($files_to_check as $name => $path) {
    if (file_exists($path)) {
        echo "✅ <strong>$name:</strong> Existe<br>";
    } else {
        echo "❌ <strong>$name:</strong> MANQUANT<br>";
    }
}

// 3. Vérifier le fichier .env
echo "<h2>🔧 Configuration .env</h2>";
if (file_exists('.env')) {
    $env_content = file_get_contents('.env');
    $lines = explode("\n", $env_content);
    foreach ($lines as $line) {
        if (strpos($line, 'APP_') === 0 || strpos($line, 'DB_') === 0) {
            // Masquer les mots de passe
            if (strpos($line, 'PASSWORD') !== false || strpos($line, 'KEY') !== false) {
                $parts = explode('=', $line, 2);
                if (count($parts) == 2) {
                    echo "<strong>{$parts[0]}:</strong> [MASQUÉ]<br>";
                }
            } else {
                echo "<strong>$line</strong><br>";
            }
        }
    }
} else {
    echo "❌ Fichier .env MANQUANT<br>";
}

// 4. Vérifier les extensions PHP
echo "<h2>🔌 Extensions PHP</h2>";
$required_extensions = ['pdo', 'pdo_pgsql', 'mbstring', 'openssl', 'json', 'gd', 'zip'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ <strong>$ext:</strong> Activé<br>";
    } else {
        echo "❌ <strong>$ext:</strong> MANQUANT<br>";
    }
}

// 5. Vérifier les répertoires de storage
echo "<h2>📂 Répertoires Storage</h2>";
$storage_dirs = [
    'storage/app/public' => 'storage/app/public',
    'storage/framework/cache' => 'storage/framework/cache',
    'storage/framework/sessions' => 'storage/framework/sessions',
    'storage/framework/views' => 'storage/framework/views',
    'bootstrap/cache' => 'bootstrap/cache',
    'public/storage' => 'public/storage'
];

foreach ($storage_dirs as $name => $path) {
    if (is_dir($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        $writable = is_writable($path) ? '✅ Écriture OK' : '❌ Pas d\'écriture';
        echo "<strong>$name:</strong> Existe (perms: $perms) $writable<br>";
    } else {
        echo "❌ <strong>$name:</strong> MANQUANT<br>";
    }
}

// 6. Test de connexion à la base de données
echo "<h2>🗄️ Connexion Base de Données</h2>";
try {
    // Lire les variables d'environnement
    $database_url = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL');
    
    if ($database_url) {
        echo "<strong>DATABASE_URL:</strong> Présent<br>";
        
        // Parser l'URL
        $url_parts = parse_url($database_url);
        $host = $url_parts['host'];
        $port = $url_parts['port'];
        $dbname = ltrim($url_parts['path'], '/');
        $username = $url_parts['user'];
        $password = $url_parts['pass'];
        
        echo "<strong>Host:</strong> $host<br>";
        echo "<strong>Port:</strong> $port<br>";
        echo "<strong>Database:</strong> $dbname<br>";
        echo "<strong>Username:</strong> $username<br>";
        
        // Test de connexion
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        $pdo = new PDO($dsn, $username, $password);
        echo "✅ <strong>Connexion PostgreSQL:</strong> RÉUSSIE<br>";
        
        // Test d'une requête simple
        $stmt = $pdo->query("SELECT version()");
        $version = $stmt->fetchColumn();
        echo "<strong>Version PostgreSQL:</strong> $version<br>";
        
    } else {
        echo "❌ <strong>DATABASE_URL:</strong> NON DÉFINIE<br>";
    }
    
} catch (Exception $e) {
    echo "❌ <strong>Erreur BDD:</strong> " . $e->getMessage() . "<br>";
}

// 7. Variables d'environnement importantes
echo "<h2>🌍 Variables d'environnement</h2>";
$env_vars = ['APP_ENV', 'APP_DEBUG', 'APP_URL', 'DATABASE_URL', 'PORT'];
foreach ($env_vars as $var) {
    $value = $_ENV[$var] ?? getenv($var) ?? 'NON DÉFINIE';
    if ($var === 'DATABASE_URL') {
        $value = $value !== 'NON DÉFINIE' ? '[MASQUÉ]' : $value;
    }
    echo "<strong>$var:</strong> $value<br>";
}

echo "<hr>";
echo "<p><strong>🕐 Diagnostic généré le:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>