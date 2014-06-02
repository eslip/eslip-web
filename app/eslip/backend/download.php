<?php
	$fichero = 'i18n/es.ini';

	if (file_exists($fichero)) {
	    header('Content-Description: File Transfer');
	    header('Content-Type: application/octet-stream');
	    header('Content-Disposition: attachment; filename='.basename($fichero));
	    header('Content-Transfer-Encoding: binary');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
	    header('Content-Length: ' . filesize($fichero));
	    ob_clean();
	    flush();
	    readfile($fichero);
	    exit;
	}

?>