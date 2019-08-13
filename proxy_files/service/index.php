<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$controller = 'Standard';
$action = 'index';
$script = '';
$isAPI = false;

$load_array = explode('?',$_SERVER["REQUEST_URI"]);

$ctrlaction = explode('/',$load_array[0]);
if (count($ctrlaction) > 2) {
    if (isset($ctrlaction[2]) && isset($ctrlaction[3])) {
        if ($ctrlaction[2] == 'Classes' && $ctrlaction[3] == 'API') {
            $isAPI = true;
            if (isset($ctrlaction[4])) {
                $script = $ctrlaction[4];
            }
        }
        $controller = $ctrlaction[2];
        $action = $ctrlaction[3];
    } elseif (isset($ctrlaction[2]) && !isset($ctrlaction[3])) {
        $action = $ctrlaction[2];
    }
}

if($_SERVER["REQUEST_METHOD"]=="POST") 
{

        $opts = array('http' =>
                array(
                        'method'  => 'POST',
                        'header'  => 'Content-type: application/x-www-form-urlencoded',
                        'content' => http_build_query($_POST),
                        'header'  => 'Cookie: ' . $_SERVER['HTTP_COOKIE']."\r\n",
                        'header'  => "Cookie: ".session_name()."=".session_id()."\r\n"
                )
        );

        $context  = stream_context_create($opts);
        if ($isAPI == true) {
            session_write_close();
            $content = file_get_contents("http://www.server.com/<URI_PATH_to_service>/".$controller."/".$action."/".$script."?".$load_array[1], false, $context);
            session_start();
        } else {
            session_write_close();
            $content = file_get_contents("http://www.server.com/<URI_PATH_to_service>/".$controller."/".$action."?".$load_array[1], false, $context);
            session_start();
        }
} else {
    
        $opts = array('http' =>
                array(
                        'header'  => 'Cookie: ' . $_SERVER['HTTP_COOKIE']."\r\n",
                        'header'  => "Cookie: ".session_name()."=".session_id()."\r\n"
                )
        );
        
        $context  = stream_context_create($opts);
    
    if ($isAPI == true) {
            session_write_close();
            $content = file_get_contents("http://www.server.com/<URI_PATH_to_service>/".$controller."/".$action."/".$script."?".$load_array[1], false, $context);
            session_start();
    } else {
        session_write_close();
        $content = file_get_contents("http://www.server.com/<URI_PATH_to_service>/".$controller."/".$action."?".$load_array[1], false, $context);
        session_start();
    }
}

foreach($http_response_header as $value) {
        
        if(strpos($value, "Location")!==false) {
                
                $newLocation = str_replace("/<BASE_URI_of_PROXY>", "", $value);
                header($newLocation);
                exit;
        }

}

echo $content;