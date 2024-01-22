<?php
/* Template Name: Reverse Proxy Template */

$URL = get_post_meta(get_the_ID(), 'URL', true);
$external_content = file_get_contents($URL);

// Replace URLs in the fetched content
$external_content = str_replace('href="/', 'href="' . $URL, $external_content);
$external_content = str_replace('src="/', 'src="' . $URL, $external_content);
$external_content = str_replace('../../', $URL, $external_content);

echo $external_content;

?>
