<?php

// Test script to check if everything is set up correctly

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;

$app = require_once __DIR__ . '/bootstrap/app.php';

echo "✅ Laravel application loaded\n";

// Test database connection
try {
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

    // Check if we can connect to MySQL database
    $host = 'localhost';
    $dbname = 'spa_fypv2';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        echo "✅ MySQL database connection successful\n";

        // Check if tables exist
        $result = $pdo->query("SHOW TABLES LIKE 'users'");
        if ($result && $result->fetch()) {
            echo "✅ Users table exists\n";
        } else {
            echo "❌ Users table does not exist - need to run migrations\n";
        }
    } catch (PDOException $e) {
        echo "❌ MySQL Database error: " . $e->getMessage() . "\n";
        echo "Please ensure MySQL is running and create the database 'spa_
        fypv2'\n";
    }

} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n🚀 Ready to test the API!\n";
echo "Frontend: http://localhost:3001\n";
echo "Backend API: http://localhost:8000/api\n";
echo "\nTest credentials from seeders:\n";
echo "Admin: admin@kapasbeautyspa.com / password123\n";
echo "Therapist: alicia@kapasbeautyspa.com / password123\n";
echo "Client: sarah.johnson@gmail.com / password123\n";
