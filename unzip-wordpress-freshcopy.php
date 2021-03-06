<?php
	error_reporting(0); // Set to -1 to show all
	mysqli_report(MYSQLI_REPORT_STRICT);


	// Get the action
	$action = (isset($_POST["action"])) ? $_POST["action"] : false;
	$zip_name = (isset($_POST["zip_name"])) ? $_POST["zip_name"] : false; 

	// Set the fiished variable
	$finished = false;

	// Check connection
	if($action == "install_starter_site") {

		// Variables
		$temp_zip = $zip_name.".zip";
		$wp_files = "wordpress";

		// If there is a zip of wp
		$has_wp_zip = (file_exists($temp_zip)) ? true : false;
		if($has_wp_zip) {
			if(file_exists($wp_files)) {
				rrmdir($wp_files);
			}

			// Now the zip id there, unzip it
			open_zip($temp_zip, $wp_files);

			// Now we can move the wp files out of the folder and into the correct place
			recurse_copy($wp_files."/".$wp_files, $wp_files."/../");

			// Clean up
			rrmdir($wp_files);
			unlink($temp_zip);

			// Set it so we know its all done
			$finished = true;
		}		
	}

	// FUNCTIONS
	function recurse_copy($src,$dst) { 
	    $dir = opendir($src); 
	    if($dst!==$wp_files."/../") {
	    	mkdir($dst); 
		}
	    while(false !== ( $file = readdir($dir)) ) { 
	        if (( $file != '.' ) && ( $file != '..' )) { 
	            if ( is_dir($src . '/' . $file) ) { 
	                recurse_copy($src . '/' . $file,$dst . '/' . $file); 
	            } 
	            else { 
	                copy($src . '/' . $file,$dst . '/' . $file); 
	            } 
	        } 
	    } 
	    closedir($dir); 
	} 

	function open_zip($open, $dest) {		
		$zip = new ZipArchive;
		$res = $zip->open($open);
		if ($res === TRUE) {
		    $zip->extractTo($dest);
		    $zip->close();
		} else {
		    echo 'failed, code:' . $res;
		}
	}

	function rrmdir($src) {
	    $dir = opendir($src);
	    while(false !== ( $file = readdir($dir)) ) {
	        if (( $file != '.' ) && ( $file != '..' )) {
	            $full = $src . '/' . $file;
	            if ( is_dir($full) ) {
	                rrmdir($full);
	            }
	            else {
	                unlink($full);
	            }
	        }
	    }
	    closedir($dir);
	    rmdir($src);
	}

?>
<!doctype html>
<html>
	<head>
		<title>Unzip Wordpress - Tech Whisperer</title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width">
		<meta name="robots" content="index, noarchive"/>
		<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,500|Open+Sans+Condensed:300,700" rel="stylesheet" type="text/css">
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
		<script>
			$(document).ready(function() {
				$('button').click(function(e) {
					// Stop the form submitting
					e.preventDefault();
					// Get the form to submit
					var to_submit = $(this).attr('data-to-submit');
					// Check they have filled in all the inputs
					var err=0;
					$('input').each(function() {
						if($(this).val()==="") {
							err++
							// Add err class
							$(this).addClass('err');
						} else {
							$(this).removeClass('err');
						}
					});

					if(err<1) {
						// Let them know something is happening and disable the button
						$(this).html('WORKING SOME MAGIC.... HOLD TIGHT.').attr('disabled','disabled');
						// Submit form
						$('#'+to_submit).submit();
					} else {
						$(this).html('Please fill in the details and click here again.');
					}
					
				});
			});
		</script>

		<!-- Make the page look good :) -->
		<style>
			.err {border:1px solid #A80000;background-color:rgba(168,0,0,0.1);}
			.err_txt {color:#A80000;}
			* {box-sizing: border-box;}
			body {margin:0;padding:0;}
			body, input, button {font-family: 'Source Sans Pro', sans-serif;color:#444;}
			button {padding:20px;border-radius: 3px;background-color: #F9461D;color:#FFF;border:0;width: 100%;}
			input {padding:6px;border:1px solid #CFD9E6;width:65%;float:right;display:block;}
			form {padding:20px;width:500px;margin:0 auto;background-color:rgba(207,217,230,0.2);overflow: hidden;}
			label {width:30%;float:left;display:block;line-height:30px;}
			h2 {line-height: 1em;margin-top:0;text-align: center;}
			p {overflow:hidden;}
			@media screen and (max-width:600px) {
				form, label, input {width:100%;}
				form {margin:0;padding:4%;}
			}
		</style>
	</head>

	<body>
		<?php
			// Check if the site has wordpress
			$has_wp = (file_exists("wp-config.php")) ? true : false;

			// If no wp then offer them the option of installing it
			if($finished) { ?>
				<h2>ALL DONE!</h2>
			<?php } else if(!$has_wp || !$finished) { ?>
				<form action="" method="post" id="install_starter_site">
					<input type="hidden" id="action" name="action" value="install_starter_site"/>
					<h2>UNZIP WORDPRESS</h2>
					<?php 
					if($connection_err) { ?>
						<p class="err_txt"><?php echo $connection_err; ?></p>
					<?php } ?>
					<p>
						<label>Zip File</label>
						<input type="text" id="zip_name" name="zip_name" placeholder="wordpress-4.6.1" value=""/>
					</p>
					<button data-to-submit="install_starter_site">UNZIP WORDPRESS</button>
				</form>
			<?php }
		?>		
	</body>

</html>
