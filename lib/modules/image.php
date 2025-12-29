<?php
/**
 * Image module.
 * Functions for resizing images.
 */

/**
 * Create a small thumbnail image.
 * Uses GD library.
 *
 * @param string $src Path to original image
 * @param string $dest Path to save new image
 * @param int $desired_width Width in pixels
 * @return bool True if success
 */
function make_thumb($src, $dest, $desired_width) {
    // check if gd is installed
    if (!extension_loaded('gd')) return false;

    $info = getimagesize($src);
    if ($info === false) return false; 
    
    $mime = $info['mime'];
    
    // open file based on type
    switch ($mime) {
        case 'image/jpeg': $source_image = imagecreatefromjpeg($src); break;
        case 'image/png':  $source_image = imagecreatefrompng($src); break;
        case 'image/gif':  $source_image = imagecreatefromgif($src); break;
        default: return false; // bad format
    }
    
    // calculate size
    $width = imagesx($source_image);
    $height = imagesy($source_image);
    $desired_height = floor($height * ($desired_width / $width));
    
    // make new empty image
    $virtual_image = imagecreatetruecolor($desired_width, $desired_height);
    
    // fix transparency for png/gif
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