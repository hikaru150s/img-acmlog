<?php
// Import library
require_once __DIR__ . '/libacm.php';

// Prepare basic parameter
$plainfile = __DIR__ . '/plainimage/lenapng128x128.png';
$encryptedfile = __DIR__ . '/temp';
$decryptedfile = __DIR__ . '/test';
$p = 10;
$q = 10;
$iteration = 10;
$debug = true;
$x0 = 0.456;
$r = 4;
$destroyImageOnCompleted = true;

// Prepare advanced parameter
$N = getimagesize($plainfile)[0];
$imagetype = exif_imagetype($plainfile);
$extension = image_type_to_extension($imagetype);

// Start testing: Encryption
$acm_encrypted_image = load_image($plainfile, $debug);
acm_encrypt($acm_encrypted_image, $N, $p, $q, $iteration, $debug);
logmap_apply($acm_encrypted_image, $N, $x0, $r, $debug);
write_image($acm_encrypted_image, $imagetype, $encryptedfile, $destroyImageOnCompleted, $debug);

// Start testing: Decryption
$acm_decrypted_image = load_image($encryptedfile . $extension, $debug);
logmap_apply($acm_decrypted_image, $N, $x0, $r, $debug);
acm_decrypt($acm_decrypted_image, $N, $p, $q, $iteration, $debug);
write_image($acm_decrypted_image, $imagetype, $decryptedfile, $destroyImageOnCompleted, $debug);
