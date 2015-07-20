<?PHP

// https://github.com/ashbike/csv2kanboard
// IMPORTANT : Set the URL for task creation.
// Get it from http://<kanboard_url>/?controller=config&action=webhook. Copy the text against 'URL for task creation' label
// You must be able to call this URL from browser and get a FAILED message. If you see 'Not Authorized' then this is not correct.
$webhookurl = "http://kanboard.local/?controller=webhook&action=task&token=7f7b7e350f28283491cef025cd6929f4a8ec0eb33d18002b8925399f5952";

/*
 * A simple PHP script to parse CSV file and create tasks in Kanboard (http://www.kanboard.net).
 * The CSV should be structured this way:
 * - First line is assumed as header and skipped.
 * - Columns must be in this order: project_id, title, description, color_id, owner_id, column_id
 * - Mandatory field:
 * - project_id: If not provided, row will be skipped.
 * - title: If not provided, row will be skipped.
 */

$handle = fopen ( ".csv2kanboard", "r" );
if ($handle) {
	if (($line = fgets ( $handle )) !== false) {
		$webhookurl = trim ( $line );
		printf ( "\n  Using webhook URL from locally stored .csv2kanboard file...\n" );
		fclose ( $handle );
	}
} else {
	printf ( "\n  Using webhook URL from defined in script...\n" );
}

$filename = $argv [1];

if (empty ( $filename )) {
	printf ( "\n  SYNTAX: csv2kanboard.php <filename>. You need to pass a comma delimited filename as argument. For more information, see https://github.com/ashbike/csv2kanboard/blob/master/README.md\n\n" );
	exit ( 1 );
}

printf ( "\n  Opening file %s...\n", $filename );
$file_handle = fopen ( $filename, "r" );

if (! $file_handle) {
	printf ( "\n\n  Cound not open file %s. Exiting...\n\n", $filename );
	exit ( 1 );
}

$firstrow = true;
$rownum = 1;
$curl = curl_init ();

while ( ! feof ( $file_handle ) ) {
	// Skip first row which will have the headers.
	$row = fgetcsv ( $file_handle );
	if ($firstrow) {
		$firstrow = false;
		continue;
	}
	$rownum ++;

	$project_id = trim ( $row [0] );
	$title = trim ( $row [1] );
	printf ( "  Processing row [%'.4u]...    ", $rownum );
	
	if (! empty ( $project_id ) && ! empty ( $title )) {
		$url = $webhookurl;
		$url .= "&project_id=" . $project_id;
		$url .= "&title=" . urlencode ( $title );
		$url .= "&description=" . urlencode ( trim ( $row [2] ) );
		$url .= "&color_id=" . trim ( $row [3] );
		$url .= "&owner_id=" . trim ( $row [4] );
		$url .= "&column_id=" . trim ( $row [5] );
		$url .= "&category_id=" . trim ( $row [6] );
		
		curl_setopt ( $curl, CURLOPT_URL, $url );
		
		// Ignore SSL certificates
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, 0 );
		
		$output = curl_exec ( $curl );
		
		$httpcode = curl_getinfo ( $curl, CURLINFO_HTTP_CODE );
		if ($httpcode == '401') {
			printf ( "!!! Your webhook URL is not correct or token is no longer valid. Please check from browser. For more information, see https://github.com/ashbike/csv2kanboard/blob/master/README.md\n\n" );
			exit ( 2 );
		}
		printf ( "\n" );
	} else {
		printf ( "SKIPPED. Missing project_id or title.\n" );
	}
}

curl_close ( $curl );
fclose ( $file_handle );

printf ( "  Finished. \n\n" );
?>
