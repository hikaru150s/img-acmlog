<!DOCTYPE html>

<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <title>Image Encryption/Decryption | Processing ...</title>
    <link rel="stylesheet" type="text/css" href="modules/bootstrap/css/bootstrap.min.css" />
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Image Encryption/Decryption</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                </li>
            </ul>
        </div>
    </nav>
    <hr />
    <div class="container">
        <div class="jumbotron jumbotron-fluid">
            <div class="container">
                <h1 class="display-4">Status</h1>
                <pre>
<?php
$filename = null;
if ( isset($_POST['encrypt']) ) {
	// Import library
	require_once __DIR__ . '/libacm.php';
	
	// Prepare basic parameter
	$targetfile = $_FILES['target']['tmp_name'];
	$p = intval($_POST['p']);
	$q = intval($_POST['q']);
	$iteration = intval($_POST['iteration']);
	$debug = isset($_POST['debugMode']);
	$x0 = floatval($_POST['x0']);
	$r = intval($_POST['r']);
	$destroyImageOnCompleted = true;
		
	// Prepare advanced parameter
	$N = getimagesize($targetfile)[0];
	$imagetype = /*exif_imagetype($targetfile);*/ IMAGETYPE_PNG;
	$extension = image_type_to_extension($imagetype);
	
	if (!$debug) echo "Processing image ...\n";

	// Start Encryption
	$acm_encrypted_image = load_image($targetfile, $debug);
	acm_encrypt($acm_encrypted_image, $N, $p, $q, $iteration, $debug);
	logmap_apply($acm_encrypted_image, $N, $x0, $r, $debug);
	$filename = 'res/' . microtime(true) . '_' . $_FILES['target']['name'];
	write_image($acm_encrypted_image, $imagetype, $filename, $destroyImageOnCompleted, $debug);

} else if ( isset($_POST['decrypt']) ) {
	// Import library
	require_once __DIR__ . '/libacm.php';
	
	// Prepare basic parameter
	$targetfile = $_FILES['target']['tmp_name'];
	$p = $_POST['p'];
	$q = $_POST['q'];
	$iteration = $_POST['iteration'];
	$debug = isset($_POST['debugMode']);
	$x0 = $_POST['x0'];
	$r = $_POST['r'];
	$destroyImageOnCompleted = true;
	
	if (!$debug) echo "Processing image ...\n";
	
	// Prepare advanced parameter
	$N = getimagesize($targetfile)[0];
	$imagetype = /*exif_imagetype($targetfile);*/ IMAGETYPE_PNG;
	$extension = image_type_to_extension($imagetype);

	// Start testing: Decryption
	$acm_decrypted_image = load_image($targetfile, $debug);
	logmap_apply($acm_decrypted_image, $N, $x0, $r, $debug);
	acm_decrypt($acm_decrypted_image, $N, $p, $q, $iteration, $debug);
	$filename = 'res/' . microtime(true) . '_' . $_FILES['target']['name'];
	write_image($acm_decrypted_image, $imagetype, $filename, $destroyImageOnCompleted, $debug);
	
}
//header('Content-Type: text/html');
?>
                </pre>
            </div>
        </div>
		<hr/>
        <?php if (!is_null($filename)) { ?>
			<img src="<?php echo $filename; ?>" class="img-fluid" alt="Result Image">
		<?php } ?>
        <hr/>
        <a href="./">Back</a>
    </div>
    <script type="text/javascript" src="modules/jquery/js/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="modules/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript">
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
</body>
</html>