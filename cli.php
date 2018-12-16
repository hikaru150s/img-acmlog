<?php
// Import library
require_once __DIR__ . '/libacm.php';

$helpmsg = "LibACM image encryption

Usage: cli.php -o [operation] -s [source] -d [destination] -p [p] -q [q] -i [iteration] -x [start value] -r [r] [--debug]
Description:
	operation: either \"encrypt\" or \"decrypt\"
	source: file path of image to be processed
	destination: file address to save processed image
	p: ACM parameter(p)
	q: ACM parameter(q)
	iteration: ACM parameter(iteration count)
	start value: Logistic Map start value, number between 0 and 1 but not both
	r: Logistic Map parameter(r)
	
Additional parameter:
	--debug: enter debug mode(verbose output)
	";

// Get options from argv
try {
	$vars = getopt('o:s:d:p:q:i:x:r:', ['debug::']);
} catch (Exception $e) {
	echo $helpmsg;
	exit(1);
}
$set = true;
$set = isset($vars['o']);
$set = isset($vars['s']);
$set = isset($vars['d']);
$set = isset($vars['p']);
$set = isset($vars['q']);
$set = isset($vars['i']);
$set = isset($vars['x']);
$set = isset($vars['r']);
if(!$set) {
	echo $helpmsg;
	exit(1);
}

// Clean the options
$test = true;
$test = $test && ( $vars['o'] == 'encrypt' || $vars['o'] == 'decrypt' );
$test = $test && ( intval($vars['i']) > 0 );
$test = $test && ( floatval($vars['x']) > 0 && floatval($vars['x']) < 1 );

if(!$test) {
	echo $helpmsg;
	exit(1);
}

// Prepare basic parameter
$operation = $vars['o'];
$source = $vars['s'];
$destination = $vars['d'];
$p = $vars['p'];
$q = $vars['q'];
$iteration = $vars['i'];
$debug = isset($vars['debug']);
$x0 = $vars['x'];
$r = $vars['r'];
$destroyImageOnCompleted = true;

// Prepare advanced parameter
$N = getimagesize($source)[0];
$imagetype = exif_imagetype($source);

try {
	if ($operation == 'encrypt') {
		// Start: Encryption
		$acm_encrypted_image = load_image($source, $debug);
		acm_encrypt($acm_encrypted_image, $N, $p, $q, $iteration, $debug);
		logmap_apply($acm_encrypted_image, $N, $x0, $r, $debug);
		write_image($acm_encrypted_image, $imagetype, $destination, $destroyImageOnCompleted, $debug);
	} else if ($operation == 'decrypt') {
	// Start: Decryption
		$acm_decrypted_image = load_image($source, $debug);
		logmap_apply($acm_decrypted_image, $N, $x0, $r, $debug);
		acm_decrypt($acm_decrypted_image, $N, $p, $q, $iteration, $debug);
		write_image($acm_decrypted_image, $imagetype, $destination, $destroyImageOnCompleted, $debug);
	}
} catch (Exception $e) {
	var_dump($e);
	exit(2);
}
