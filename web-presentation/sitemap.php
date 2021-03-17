<?php
header("Content-type: text/xml");
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
$reg = json_decode(file_get_contents("content/register.json"), true);
foreach ($reg as $key => $val) {
    if ($val["menu-item-name"]) {
        echo '<url><loc>';
        echo 'http://' . $_SERVER['HTTP_HOST'] . "/elis/web-presentation/index.php?p=" . $key;
        echo '</loc></url>' . "\n";
    }
}
echo '</urlset>' . "\n";
