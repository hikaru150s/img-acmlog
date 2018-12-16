<?php
/*
	LibACM - A procedure-based library to perform Arnold's Cat Map image pixel
	reposition and Logistic Map image pixel encryption.
	This library provides functions to perform ACM image pixel reposition and
	apply Logistic Map into image pixel.
	Dependencies:
		(core)		PHP				>= 7.2.0
		(extension)	EXIF			>= 7.1.0 (Used to read image metadata - bundled with PHP)
		(extension)	GD				>= 2.1.0 (Used to manipulate image - bundled with PHP)
		(extension)	mbstring		>= 1.3.2 (Used by EXIF and GD - bundled with PHP)
		(library)	php-ai/php-ml	>= 0.7.0 (Used to handle most of Matrix operation - Install via composer)
	--- USE ONLY FOR DEVELOPMENT AND EXPERIMENT! ---
*/
require_once __DIR__ . '/vendor/autoload.php';
use Phpml\Math\Matrix;

/*
	Load image from specific file path.
	Parameter:
		$filepath	=> (string) path to image
		$debug		=> (bool) enable debug mode (verbose output)
	Return:
		$img		=> (resource) a GD resource image
	Exception:
		File inaccessible or unreadable
		File is not a valid image
		Cannot convert image to true color
		File must a square image (width = height)
*/
function load_image($filepath, $debug = false) {
	if ($debug) echo "Loading File\n";
	// Check if $filepath is accessible and readable
	if ( ($data = file_get_contents($filepath)) !== false ) {
		// Check and try read $filepath as image
		if ( ($img = imagecreatefromstring($data)) !== false )  {
			// Bugfix: convert $img to true color for best result processing
			if ( imagepalettetotruecolor($img) != false ) {
				// Check image size
				$imagesize = getimagesize($filepath);
				if ($imagesize[0] == $imagesize[1]) {
					// Return the $img
					return $img;
				} else {
					throw new Exception('File must a square image (width = height)');
				}
			} else {
				throw new Exception('Cannot convert image to true color');
			}
		} else {
			throw new Exception('File is not a valid image');
		}
	} else {
		throw new Exception('File inaccessible or unreadable');
	}
}

/*
	Apply a modulo by scalar operation into matrix.
	Parameter:
		$mat	=> (Matrix) Matrix to be applied
		$num	=> (int) divider
	Return:
		{}		=> (Matrix) Matrix of remainder
	Exception:
		
*/
function helper_mod(Matrix $mat, $num) {
	$flattened = $mat->toArray();
	foreach ($flattened as &$x) {
		foreach ($x as &$y) {
			$y = $y % $num;
		}
	}
	return new Matrix($flattened);
}

/*
	Perform ACM reposition encryption.
	Parameter:
		$image		=> (resource, pointer) GD resource image that will be applied with ACM
		$N			=> (int) image width or image height size
		$p			=> (int) ACM variable: p
		$q			=> (int) ACM variable: q
		$iteration	=> (int) iteration count on how much ACM will be performed
		$debug		=> (bool) enable debug mode (verbose output)
	Return:
		
	Exception:
		
*/
function acm_encrypt(&$image, $N, $p, $q, $iteration, $debug = false) {
	if ($debug) echo "Encryption mode\n";
	// Prepare environments for ACM encryption
	$M = new Matrix([
		[1, $p],
		[$q, $p * $q + 1]
	]);
	if ($debug) echo "Env ready, starting iteration\n";
	// Start the iteration
	for ($i = 0; $i < $iteration; $i++) {
		$currenttime = microtime(true);
		if ($debug) echo "Iteration $i ... ";
		// Generate $empty image as overlay
		$empty = imagecreatetruecolor($N, $N);
		for ($x = 0; $x < $N; $x++) {
			for ($y = 0; $y < $N; $y++) {
				// Calculate ACM
				$acm = helper_mod($M->multiply(new Matrix([[$x], [$y]])), $N);
				// Get ACM Values
				$temp = $acm->getColumnValues(0);
				// Reposition $image in $empty
				imagesetpixel($empty, $x, $y, imagecolorat($image, $temp[0], $temp[1]));
			}
		}
		// Copy $empty image into $image
		imagecopy($image, $empty, 0, 0, 0, 0, $N, $N);
		// Release all resource belongs to $empty
		imagedestroy($empty);
		if ($debug) echo "passed (" . (microtime(true) - $currenttime) . " s)\n";
	}
}

/*
	Perform ACM reposition decryption.
	Parameter:
		$image		=> (resource, pointer) GD resource image that will be applied with ACM
		$N			=> (int) image width or image height size
		$p			=> (int) ACM variable: p
		$q			=> (int) ACM variable: q
		$iteration	=> (int) iteration count on how much ACM will be performed
		$debug		=> (bool) enable debug mode (verbose output)
	Return:
		
	Exception:
		
*/
function acm_decrypt(&$image, $N, $p, $q, $iteration, $debug = false) {
	if ($debug) echo "Decryption mode\n";
	// Prepare environments for ACM decryption
	$M = new Matrix([
		[1, $p],
		[$q, $p * $q + 1]
	]);
	if ($debug) echo "Env ready, starting iteration\n";
	// Start the iteration
	for ($i = 0; $i < $iteration; $i++) {
		$currenttime = microtime(true);
		if ($debug) echo "Iteration $i ... ";
		// Generate $empty image as overlay
		$empty = imagecreatetruecolor($N, $N);
		for ($x = 0; $x < $N; $x++) {
			for ($y = 0; $y < $N; $y++) {
				// Calculate ACM
				$acm = helper_mod($M->multiply(new Matrix([[$x], [$y]])), $N);
				// Get ACM Values
				$temp = $acm->getColumnValues(0);
				// Reposition $image in $empty
				imagesetpixel($empty, $temp[0], $temp[1], imagecolorat($image, $x, $y));
			}
		}
		// Copy $empty image into $image
		imagecopy($image, $empty, 0, 0, 0, 0, $N, $N);
		// Release all resource belongs to $empty
		imagedestroy($empty);
		if ($debug) echo "passed (" . (microtime(true) - $currenttime) . " s)\n";
	}
}

/*
	Apply Logistic Map into image.
	Parameter:
		$image		=> (resource, pointer) GD resource image that will be applied with ACM
		$N			=> (int) image width or image height size
		$startnumber=> (int) Logistic Map variable: x0
		$r			=> (int) Logistic Map variable: r
		$debug		=> (bool) enable debug mode (verbose output)
	Return:
		
	Exception:
		
*/
function logmap_apply(&$image, $N, $startnumber = 0.456, $r = 4, $debug = false) {
	if ($debug) echo "Preparing Logistic Mapper\n";
	$x = array($startnumber);
	$a = 10 ** 3;
	$imagesize = $N ** 2;
	$i = 0;
	if ($debug) echo "Logistic Mapper ready, starting map\n";
	for ($shifter = 16; $shifter >= 0; $shifter -= 8)  {
		$channeltime = microtime(true);
		if ($debug) echo "----- Mapping Channel: " . ($shifter == 16 ? 'RED' : ($shifter == 8 ? 'GREEN' : 'BLUE')) . " -----\n";
		for ($row = 0; $row < $N; $row++) {
			$currenttime = microtime(true);
			if ($debug) echo "Row $row of $N ... ";
			for ($col = 0; $col < $N; $col++) {
				if ($i < $imagesize) {
					// 1. Generate keystream from Logistic Map
					$x[$i + 1] = $r * $x[$i] * (1 - $x[$i]);
					// 2. Extract 3 digits integer starting from 2 digits after coma
					$key = ($x[$i + 1] * (10 ** 5)) % (10 ** 3);
					// 3. Keep $key in bytes range by apply modulo
					$key = $key % 256;
					// 4. Apply $key to specific color and channel using XOR
					// 4.1. Create $encryptedColor
					$color = imagecolorat($image, $row, $col); // Get color, result in 0xRRGGBB hex format
					$shiftedKey = $key << $shifter; // Shifting $shifter bits, resulted (hex): 0xKK0000 in RED($shifter = 16), 0x00KK00 in GREEN($shifter = 8), or 0x0000KK in BLUE($shifter = 0)
					$encryptedColor = $color ^ $shiftedKey; // Just XOR, other channel will remain untouched
					// 4.2. Decompose $encryptedColor
					$red = ($encryptedColor >> 16) & 0xFF;
					$green = ($encryptedColor >> 8) & 0xFF;
					$blue = ($encryptedColor) & 0xFF;
					// 4.3. Assign $encryptedColor
					$encryptedColorId = imagecolorexact($image, $red, $green, $blue); // Check if the color already allocated
					if ($encryptedColorId == -1) {
						$encryptedColorId = imagecolorallocate($image, $red, $green, $blue); // Allocate if color not exist
					}
					imagesetpixel($image, $row, $col, $encryptedColorId);
					$i += 1;
				}
			}
			if ($debug) echo "Completed (" . (microtime(true) - $currenttime) . " s)\n";
		}
		if ($debug) echo "----- Channel Mapping Completed (" . (microtime(true) - $channeltime) . " s) -----\n";
	}
}

/*
	Write image.
	Parameter:
		$image		=> (resource, pointer) GD resource image that will be applied with ACM
		$imagetype	=> (int) image type defined in EXIF (Currently support: IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WBMP, IMAGETYPE_WEBP, IMAGETYPE_XBM, IMAGETYPE_BMP)
		$destination=> (string) Pah to file where image will be written
		$destroy	=> (bool) indicates whether destroy the image resource on completion of image writing or not
		$debug		=> (bool) enable debug mode (verbose output)
	Return:
		
	Exception:
		
*/
function write_image(&$image, $imagetype, $destination = null, $destroy = true, $debug = false) {
	if ($debug) echo "Writing ...\n";
	switch ($imagetype) {
		case IMAGETYPE_GIF: {
			if (is_null($destination)) {
				//header('Content-Type: ' . image_type_to_mime_type(IMAGETYPE_GIF));
				imagegif($image);
			} else {
				imagegif($image, $destination);
			}
			break;
		}
		case IMAGETYPE_JPEG: {
			if (is_null($destination)) {
				//header('Content-Type: ' . image_type_to_mime_type(IMAGETYPE_JPEG));
				imagejpeg($image);
			} else {
				imagejpeg($image, $destination);
			}
			break;
		}
		case IMAGETYPE_PNG: {
			if (is_null($destination)) {
				//header('Content-Type: ' . image_type_to_mime_type(IMAGETYPE_PNG));
				imagepng($image);
			} else {
				imagepng($image, $destination);
			}
			break;
		}
		case IMAGETYPE_WBMP: {
			if (is_null($destination)) {
				//header('Content-Type: ' . image_type_to_mime_type(IMAGETYPE_WBMP));
				imagewbmp($image);
			} else {
				imagewbmp($image, $destination);
			}
			break;
		}
		case IMAGETYPE_WEBP: {
			if (is_null($destination)) {
				//header('Content-Type: ' . image_type_to_mime_type(IMAGETYPE_WEBP));
				imagewebp($image);
			} else {
				imagewebp($image, $destination);
			}
			break;
		}
		case IMAGETYPE_XBM: {
			if (is_null($destination)) {
				//header('Content-Type: ' . image_type_to_mime_type(IMAGETYPE_XBM));
				imagexbm($image);
			} else {
				imagexbm($image, $destination);
			}
			break;
		}
		default: {
			if (is_null($destination)) {
				//header('Content-Type: ' . image_type_to_mime_type(IMAGETYPE_BMP));
				imagebmp($image);
			} else {
				imagebmp($image, $destination);
			}
			break;
		}
	}
	if ($destroy) {
		if ($debug) echo "Cleaning ...";
		imagedestroy($image);
		if ($debug) echo " Done\n";
	}
}
