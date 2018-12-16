<!DOCTYPE html>

<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <title>Image Encryption/Decryption</title>
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
        <div class="alert alert-info" role="alert">
            <h4 class="alert-heading">Notes!</h4>
            <p>
				This system will encrypt/decrypt <abbr data-toggle="tooltip" data-placement="top" title="An image that have width = height e.g. 32 x 32">squared images</abbr> using <abbr data-toggle="tooltip" data-placement="top" title="Arnold's Cat Map">ACM</abbr> and Logistic Map.
				Please note that the ACM was taking time to running depending on Iteration and image size, so be patient.
				On a <abbr data-toggle="tooltip" data-placement="top" title="Intel(R) Core(TM) i5-3337U CPU with 4 GB RAM and Solid-State Disk">test device</abbr>, the average amount of <abbr data-toggle="tooltip" data-placement="top" title="In a normal usage, not in an isolated system (so another processes may affect the time)">required time</abbr> to do each iteration shown <abbr data-toggle="tooltip" data-placement="top" title="Repeat: Just ACM! This time is not combined with Logistic Map.">below</abbr>.
			</p>
			<ul>
				<li>PNG 32 x 32 pixels require about 0.08 seconds</li>
				<li>PNG 64 x 64 pixels require about 0.29 seconds</li>
				<li>PNG 128 x 128 pixels require about 1.53 seconds</li>
				<li>PNG 256 x 256 pixels require about 4.64 seconds</li>
				<li>PNG 512 x 512 pixels require about 16.12 seconds</li>
			</ul>
			<p>The result may vary on <abbr data-toggle="tooltip" data-placement="top" title="including OS, server handler, and maybe image types">different environment</abbr>.</p>
            <hr>
            <p class="mb-0">This system just for experiment and development only.</p>
        </div>
        <form action="process.php" enctype="multipart/form-data" method="post">
            <div class="form-group">
                <label for="imageTarget">Pick image</label>
                <input type="file" class="form-control-file" id="imageTarget" name="target" required="required">
            </div>
            <div class="form-group row">
                <label for="param_p" class="col-sm-2 col-form-label">p</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" id="param_p" placeholder="p" required="required" name="p" min="0" step="1" value="10">
                </div>
            </div>
            <div class="form-group row">
                <label for="param_q" class="col-sm-2 col-form-label">q</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" id="param_q" placeholder="q" required="required" name="q" min="0" step="1" value="10">
                </div>
            </div>
            <div class="form-group row">
                <label for="param_i" class="col-sm-2 col-form-label">Iteration</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" id="param_i" placeholder="Iteration" required="required" name="iteration" min="1" step="1" value="10">
                </div>
            </div>
            <div class="form-group row">
                <label for="param_x0" class="col-sm-2 col-form-label">x<sub>0</sub></label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" id="param_x0" placeholder="Start value" required="required" name="x0" min="0.000000000001" step="0.000000000001" max="0.999999999999" value="0.456">
                </div>
            </div>
            <div class="form-group row">
                <label for="param_r" class="col-sm-2 col-form-label">r</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" id="param_r" placeholder="r" required="required" name="r" min="1" step="1" value="4">
                </div>
            </div>
            <hr />
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="devMode" name="debugMode">
                <label class="form-check-label" for="devMode">
                    Debug Mode
                </label>
            </div>
            <hr />
            <div class="form-row">
                <div class="col">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" name="encrypt" value="e">Encrypt</button>
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-secondary btn-lg btn-block" name="decrypt" value="d">Decrypt</button>
                </div>
            </div>
        </form>
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