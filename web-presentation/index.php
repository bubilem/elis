<?php
$reg = json_decode(file_get_contents("content/register.json"), true);
$page = strtolower(filter_input(INPUT_GET, "p"));
if (empty($page)) {
    $page = "home";
}
if (!key_exists($page, $reg) || !file_exists("content/$page.html")) {
    $page = "404";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo $reg[$page]['title']; ?> | Projekt</title>
    <meta name="description" content="<?php echo $reg[$page]['description']; ?>">
    <meta name="author" content="3IT, VOŠ, SPŠ a SOŠ Varnsdorf">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="css/style.min.css" />
</head>

<body>
    <header>
        <div class="logo">
            <img src="img/logo.png" alt="logo" />
        </div>
        <nav>
            <ul>
                <?php
                foreach ($reg as $key => $val) {
                    if ($val["menu-item-name"]) {
                        echo '<li' . ($page == $key ? ' class="active"' : '') . '>';
                        echo '<a href="?p=' . $key . '">' . $val["menu-item-name"] . '</a>';
                        echo '</li>' . "\n";
                    }
                }
                ?>
            </ul>
        </nav>
    </header>
    <main>
        <?php include "content/$page.html"; ?>
    </main>
    <footer>
        <p>Lorem</p>
    </footer>
</body>

</html>