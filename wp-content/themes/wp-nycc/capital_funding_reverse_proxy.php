<?php
/* Template Name: Reverse Proxy Template */

$external_url = "https://datateam.council.nyc.gov:8377/capital_funding/"
$external_content = file_get_contents($external_url);

// Replace URLs in the fetched content
$external_content = str_replace('href="/', 'href="' . $external_url, $external_content);
$external_content = str_replace('src="/', 'src="' . $external_url, $external_content);
$external_content = str_replace('../../', 'https://datateam.council.nyc.gov:8377/expense_funding/', $external_content);

echo $external_content;

?>
