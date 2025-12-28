<?php
// image processing stuff

// function to create a smaller thumbnail from big image
// uses gd library
function make_thumb($src, $dest, $desired_width) {
    // check if gd is enabled on server
    if (!extension_loaded('gd')) return false;

    $info = getimagesize($src);
    if ($info === false) return false; // not an image
    
    $mime = $info['mime'];
    
    // open image based on type
    switch ($mime) {
        case 'image/jpeg': $source_image = imagecreatefromjpeg($src); break;
        case 'image/png':  $source_image = imagecreatefrompng($src); break;
        case 'image/gif':  $source_image = imagecreatefromgif($src); break;
        default: return false; // format not supported
    }
    
    // calc new height to keep aspect ratio
    $width = imagesx($source_image);
    $height = imagesy($source_image);
    $desired_height = floor($height * ($desired_width / $width));
    
    // create empty canvas
    $virtual_image = imagecreatetruecolor($desired_width, $desired_height);
    
    // keep transparency for png and gif
    if ($mime == 'image/png' || $mime == 'image/gif') {
        imagecolortransparent($virtual_image, imagecolorallocatealpha($virtual_image, 0, 0, 0, 127));
        imagealphablending($virtual_image, false);
        imagesavealpha($virtual_image, true);
    }
    
    // copy and resize
    imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
    
    // save file
    if ($mime == 'image/jpeg') imagejpeg($virtual_image, $dest, 85);
    elseif ($mime == 'image/png') imagepng($virtual_image, $dest);
    elseif ($mime == 'image/gif') imagegif($virtual_image, $dest);
    
    return true;
}
?>