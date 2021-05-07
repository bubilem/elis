<?php
header("Content-type: text/xml");
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
$items = json_decode(file_get_contents("pages/register.json"), true);
foreach ($items as $key => $val) {
    if ($val["menu-item-name"]) {
        if ($key == 'home') {
            echo "<url><loc>https://www.jid-project.eu</loc></url>\n";
        } else {
            echo "<url><loc>https://www.jid-project.eu/?p=$key</loc></url>\n";
        }
    }
}
$items = json_decode(file_get_contents("blog/register.json"), true);
foreach ($items as $key => $val) {
    if ($val["menu-item-name"]) {
        echo "<url><loc>https://www.jid-project.eu/?a=$key</loc></url>\n";
    }
}
echo '</urlset>' . "\n";
