<?php /* Template Name: Reverse Proxy Template */
    $uri = $_SERVER['REQUEST_URI'];
    $forward_uri = explode("forward_uri=", $uri);
    if (count($forward_uri) == 1){
        $external_url = "https://council.nyc.gov/";
    } else {
        $external_url = $forward_uri[1];
    }
 
    $external_content = file_get_contents($external_url);
    echo $external_content;
?>