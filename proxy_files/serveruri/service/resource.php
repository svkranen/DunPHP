<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$server = 'www.server.com';
$public_resources = '/<URI_PATH_to_service>/Resources/Public/';

$fp = fopen("http://".$server.$public_resources.$file.$_SERVER["QUERY_STRING"], "r");

array_shift($http_response_header);
foreach($http_response_header as $h) {
    header($h);
}

fpassthru($fp);
fclose($fp);

?>