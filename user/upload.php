<?php

/**
 * mostly copied from here:
 * https://css-tricks.com/drag-and-drop-file-uploading/
 */
require_once 'lib/auth.php';
require_once "../lib/app.php";

$accepted_types = [
    "jpg", "png", "jpeg",
    "avi", "mpg", "mpeg", "mp4", "mkv", "mov", "qt", "wmv",
    "pdf", "ppt", "odp", "keynote", "key"
];

$pid = sprintf("%03u", $_SESSION['uid']);
$target_dir = "../" . $UPLOADS_DIR . "/" . $pid . "/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, TRUE);
}

if (isset($_POST["op"])) {

    $op = isset($_POST["op"]) ? $_POST["op"] : "";
    $data = [];
    $data['success'] = TRUE;

    if ($op=="upload") {
#        print "upload";
        //print_r($_FILES);
        $data['files'] = [];
        $data['error'] = "";

        foreach ($_FILES['files']['name'] as $i => $v) {
            $name = $_FILES['files']['name'][$i];
            $type = $_FILES['files']['type'][$i];
            $tmp_name = $_FILES['files']['tmp_name'][$i];
            $size = $_FILES['files']['size'][$i];
            $error = $_FILES['files']['error'][$i];

            if (strlen($name)<=0) {continue;}
            $data['files'][$name] = "nok";

            $target_file = $target_dir . basename($name);
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, TRUE);
            }
            $filetype = pathinfo($target_file, PATHINFO_EXTENSION);

#            if(! in_array($filetype, $accepted_types)) {#
#                $data['files'][$name] = "nacc";
#                $data['success'] = FALSE;
#                $data['error'] .= $name . ": filetype not accepted <br>";
#                continue;
#            }

            $res = move_uploaded_file($tmp_name, $target_file);

            if (!$res) {
                $data['files'][$name] = "nmov";
                $data['success'] = FALSE;
                $data['error'] .= $name . ": could not move file<br>";
                continue;
            }

            $data['files'][$name] = "ok";
        }

        print json_encode($data);
    }
    exit();
}



require "lib/header.php";
require "lib/menu.php";


?>

<main>
<article>
    <h1>Upload Your Talk / Poster</h1>

    <p>
        Please upload a draft / testing version with your special fonts and videos
        by Friday, 2nd Sept. for testing purposes.
        <br>
        The final presentation to be shown can be uploaded up to 5min before your talk starts.
        <br>
        You can either embed media files, or upload them as separate files.
        Please note that subfolders are not supported.
        You will overwrite old files and cannot delete files.
        Only certain filetypes are allowed.
        If I forgot about yours, please let me know.
    </p>
    <p class="warning" style="font-size: 85%; color: #600;">
        By uploading your presentation, you agree that your slides and presention will be put online <b>during or after</b> the conference / your talk and that you agree to apear in a live stream.
        <br>
        To opt-out, please simply write an email to us, stating you don't wish to appear in the live stream and / or don't want your sildes to be published.
    </p>

    <form   method="post"
            action="upload.php"
            enctype="multipart/form-data"
            novalidate
            class="box">

        <div class="box__input">
            <svg class="box__icon" xmlns="http://www.w3.org/2000/svg" width="50" height="43" viewBox="0 0 50 43"><path d="M48.4 26.5c-.9 0-1.7.7-1.7 1.7v11.6h-43.3v-11.6c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v13.2c0 .9.7 1.7 1.7 1.7h46.7c.9 0 1.7-.7 1.7-1.7v-13.2c0-1-.7-1.7-1.7-1.7zm-24.5 6.1c.3.3.8.5 1.2.5.4 0 .9-.2 1.2-.5l10-11.6c.7-.7.7-1.7 0-2.4s-1.7-.7-2.4 0l-7.1 8.3v-25.3c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v25.3l-7.1-8.3c-.7-.7-1.7-.7-2.4 0s-.7 1.7 0 2.4l10 11.6z"/></svg>

            <input type="file" name="files[]" id="file" class="box__file" data-multiple-caption="{count} files selected" multiple />
            <label for="file">
                <strong>Choose a file</strong>
                <span class="box__dragndrop"> or drag it here</span>.
            </label>
            <button type="submit" class="box__button">Upload</button>
        </div>

		<div class="box__uploading">Uploading&hellip;</div>
		<div class="box__success">
            Done!
            <a href="." class="box__restart" role="button">Upload more?</a>
        </div>
		<div class="box__error">
            Error!<br><span style="font-size:80%;"></span><br><a href="." class="box__restart" role="button">Try again!</a>
        </div>

        <input type="hidden" name="op" value="upload"/>

    </form>

    <p>
        Uploaded files (press F5 to reload):
    </p>
    <ul>
<?php
$files = array_diff(scandir($target_dir), array('..', '.'));
rsort($files);

foreach ($files as $file) {
#    print "<li><a href='../uploads/".$pid."/".$file."'>" . $file . "</a></li>";
    print "<li>" . $file . "</li>";
}
?>
    </ul>


</article>
</main>

<script>

	'use strict';

	;( function ( document, window, index )
	{
		// feature detection for drag&drop upload
		var isAdvancedUpload = function()
			{
				var div = document.createElement( 'div' );
				return ( ( 'draggable' in div ) || ( 'ondragstart' in div && 'ondrop' in div ) ) && 'FormData' in window && 'FileReader' in window;
			}();


		// applying the effect for every form
		var forms = document.querySelectorAll( '.box' );
		Array.prototype.forEach.call( forms, function( form )
		{
			var input		 = form.querySelector( 'input[type="file"]' ),
				label		 = form.querySelector( 'label' ),
				errorMsg	 = form.querySelector( '.box__error span' ),
				restart		 = form.querySelectorAll( '.box__restart' ),
				droppedFiles = false,
				showFiles	 = function( files )
				{
					label.textContent = files.length > 1 ? ( input.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', files.length ) : files[ 0 ].name;
				},
				triggerFormSubmit = function()
				{
					var event = document.createEvent( 'HTMLEvents' );
					event.initEvent( 'submit', true, false );
					form.dispatchEvent( event );
				};

			// letting the server side to know we are going to make an Ajax request
			var ajaxFlag = document.createElement( 'input' );
			ajaxFlag.setAttribute( 'type', 'hidden' );
			ajaxFlag.setAttribute( 'name', 'ajax' );
			ajaxFlag.setAttribute( 'value', 1 );
			form.appendChild( ajaxFlag );

			// automatically submit the form on file select
			input.addEventListener( 'change', function( e )
			{
				showFiles( e.target.files );


			});

			// drag&drop files if the feature is available
			if( isAdvancedUpload )
			{
				form.classList.add( 'has-advanced-upload' ); // letting the CSS part to know drag&drop is supported by the browser

				[ 'drag', 'dragstart', 'dragend', 'dragover', 'dragenter', 'dragleave', 'drop' ].forEach( function( event )
				{
					form.addEventListener( event, function( e )
					{
						// preventing the unwanted behaviours
						e.preventDefault();
						e.stopPropagation();
					});
				});
				[ 'dragover', 'dragenter' ].forEach( function( event )
				{
					form.addEventListener( event, function()
					{
						form.classList.add( 'is-dragover' );
					});
				});
				[ 'dragleave', 'dragend', 'drop' ].forEach( function( event )
				{
					form.addEventListener( event, function()
					{
						form.classList.remove( 'is-dragover' );
					});
				});
				form.addEventListener( 'drop', function( e )
				{
					droppedFiles = e.dataTransfer.files; // the files that were dropped
					showFiles( droppedFiles );

									});
			}


			// if the form was submitted
			form.addEventListener( 'submit', function( e )
			{
				// preventing the duplicate submissions if the current one is in progress
				if( form.classList.contains( 'is-uploading' ) ) return false;

				form.classList.add( 'is-uploading' );
				form.classList.remove( 'is-error' );

				if( isAdvancedUpload ) // ajax file upload for modern browsers
				{
					e.preventDefault();

					// gathering the form data
					var ajaxData = new FormData( form );
					if( droppedFiles )
					{
						Array.prototype.forEach.call( droppedFiles, function( file )
						{
							ajaxData.append( input.getAttribute( 'name' ), file );
						});
					}

					// ajax request
					var ajax = new XMLHttpRequest();
					ajax.open( form.getAttribute( 'method' ), form.getAttribute( 'action' ), true );

					ajax.onload = function()
					{
						form.classList.remove( 'is-uploading' );
						if( ajax.status >= 200 && ajax.status < 400 )
						{
							var data = JSON.parse( ajax.responseText );
							form.classList.add( data.success == true ? 'is-success' : 'is-error' );
							if( !data.success ) errorMsg.innerHTML = data.error;
						}
						else alert( 'Error. Please, contact the webmaster!' );
					};

					ajax.onerror = function()
					{
						form.classList.remove( 'is-uploading' );
						alert( 'Error. Please, try again!' );
					};

					ajax.send( ajaxData );
				}
				else // fallback Ajax solution upload for older browsers
				{
					var iframeName	= 'uploadiframe' + new Date().getTime(),
						iframe		= document.createElement( 'iframe' );

						$iframe		= $( '<iframe name="' + iframeName + '" style="display: none;"></iframe>' );

					iframe.setAttribute( 'name', iframeName );
					iframe.style.display = 'none';

					document.body.appendChild( iframe );
					form.setAttribute( 'target', iframeName );

					iframe.addEventListener( 'load', function()
					{
						var data = JSON.parse( iframe.contentDocument.body.innerHTML );
						form.classList.remove( 'is-uploading' )
						form.classList.add( data.success == true ? 'is-success' : 'is-error' )
						form.removeAttribute( 'target' );
						if( !data.success ) errorMsg.textContent = data.error;
						iframe.parentNode.removeChild( iframe );
					});
				}
			});


			// restart the form if has a state of error/success
			Array.prototype.forEach.call( restart, function( entry )
			{
				entry.addEventListener( 'click', function( e )
				{
					e.preventDefault();
					form.classList.remove( 'is-error', 'is-success' );
					input.click();
				});
			});

			// Firefox focus bug fix for file input
			input.addEventListener( 'focus', function(){ input.classList.add( 'has-focus' ); });
			input.addEventListener( 'blur', function(){ input.classList.remove( 'has-focus' ); });

		});
	}( document, window, 0 ));

</script>


<?php
require "lib/footer.php";
?>
