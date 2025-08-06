<?php
$imagesFolder = '../Libraries/media/wallpaper';
$images = glob($imagesFolder . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
if ($images) {
    $randomImage = $images[array_rand($images)];
    echo json_encode(['image' => $randomImage]);
} else {
    echo json_encode(['error' => 'No images found in the specified folder.']);
}
