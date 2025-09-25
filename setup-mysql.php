<?php

// MySQL Database Setup Script for Kapas Beauty Spa

echo "🏥 Setting up MySQL Database for Kapas Beauty Spa\n\n";

$host = 'localhost';
$username = 'root';
$password = '';  // Change this if you have a MySQL root password
$dbname = 'kapas_beauty_spa';

try {
    // First, connect without database to create it
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Connected to MySQL server\n";

    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $pdo->exec($sql);
    echo "✅ Database '$dbname' created/verified\n";

    // Test connection to the specific database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    echo "✅ Connected to database '$dbname'\n";

    echo "\n📝 Environment Configuration:\n";
    echo "Please ensure your .env file has these settings:\n\n";
    echo "DB_CONNECTION=mysql\n";
    echo "DB_HOST=127.0.0.1\n";
    echo "DB_PORT=3306\n";
    echo "DB_DATABASE=$dbname\n";
    echo "DB_USERNAME=$username\n";
    echo "DB_PASSWORD=$password\n\n";

    echo "🚀 Ready to run migrations!\n";
    echo "Run: php artisan migrate:fresh --seed\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
    echo "Please ensure:\n";
    echo "1. MySQL server is running\n";
    echo "2. Root user credentials are correct\n";
    echo "3. MySQL is installed and accessible\n";
}




