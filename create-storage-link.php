<?php
/**
 * Create storage symbolic link for Laravel
 * Run this file once to create the symbolic link needed for file serving
 */

$publicPath = __DIR__ . '/public';
$storagePath = __DIR__ . '/storage/app/public';

// Create the public/storage directory if it doesn't exist
$linkPath = $publicPath . '/storage';

// Remove existing link/directory if it exists
if (file_exists($linkPath)) {
    if (is_link($linkPath)) {
        unlink($linkPath);
        echo "Removed existing symbolic link.\n";
    } elseif (is_dir($linkPath)) {
        rmdir($linkPath);
        echo "Removed existing directory.\n";
    }
}

// Create the symbolic link
if (symlink($storagePath, $linkPath)) {
    echo "✅ Storage symbolic link created successfully!\n";
    echo "Link: $linkPath\n";
    echo "Target: $storagePath\n";
} else {
    echo "❌ Failed to create symbolic link.\n";
    echo "You may need to run this with administrator privileges or use Laravel's artisan command.\n";
}

// Create the profile_images directory in storage if it doesn't exist
$profileImagesPath = $storagePath . '/profile_images';
if (!file_exists($profileImagesPath)) {
    mkdir($profileImagesPath, 0755, true);
    echo "✅ Created profile_images directory in storage.\n";
}

echo "\nDone! Your images should now be accessible via web URLs.\n";
?>


