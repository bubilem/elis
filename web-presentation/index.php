<?php
$pages = json_decode(file_get_contents("pages/register.json"), true);
$articles = json_decode(file_get_contents("blog/register.json"), true);
$page = strtolower(filter_input(INPUT_GET, "p"));
$article = strtolower(filter_input(INPUT_GET, "a"));
if (empty($page)) {
    $page = "home";
}
if (!key_exists($page, $pages) || ($article  != null && !key_exists($article, $articles))) {
    $page = "404";
    $article = null;
}
if ($article) {
    $page = "blog";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    echo '<base href="' . (in_array($_SERVER['REMOTE_ADDR'], [
        '127.0.0.1',
        '::1'
    ]) ? 'http://projects/elis/web-presentation/' : 'https://www.jid-project.eu/') . '">' . "\n";
    echo '<title>'
        . ($article  != null ? $articles[$article]['menu-item-name'] . ' | Blog' : $pages[$page]['title'])
        . ' | JID project</title>';
    ?>
    <meta charset="utf-8" />
    <meta name="robots" content="index, follow" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="author" content="3IT, VOŠ, SPŠ a SOŠ Varnsdorf" />
    <link href="css/style.min.css" rel="stylesheet" />
</head>

<body>
    <header>
        <div class="content">
            <div>
                <a class="logo" href="">
                    <img src="img/jid-project-logo.png" alt="JID project logo" />
                </a>
                <nav>
                    <ul>
                        <?php
                        foreach ($pages as $key => $val) {
                            if ($val["menu-item-name"]) {
                                echo '<li><a' .
                                    ($key == $page ? ' class="actual"' : '')
                                    . ' href="'
                                    . ($key == 'home' ? '' : '?p=' . $key) . '">'
                                    . $val["menu-item-name"]
                                    . '</a></li>' . "\n";
                            }
                        }
                        ?>
                    </ul>
                </nav>
                <div class="caption">
                    <?php
                    echo empty($article) ? '<h1>' . strtoupper($page) . '</h1>' : '<h2>BLOG</h2>';
                    ?>
                </div>
            </div>
        </div>
    </header>
    <main>
        <div class="content">
            <div>
                <?php
                if ($page == "blog") {
                    if (empty($article)) {
                        echo '<div class="blog">';
                        foreach ($articles as $key => $val) {
                            if ($val["menu-item-name"]) {
                                echo '<article>';
                                echo '<h2><a href="?a=' . $key . '">' . $val["menu-item-name"] . '</a></h2>';
                                echo '<p class="date">' . $val["date"] . '</p>';
                                echo '<p>' . $val["intro"] . '</p>';
                                echo '</article>';
                            }
                        }
                        echo '</div>';
                    } else {
                        echo '<div class="article">';
                        echo '<article>';
                        echo '<h1>' . $articles[$article]['title'] . '</h1>';
                        echo '<p class="date">' . $articles[$article]["date"] . '</p>';
                        include "blog/$article/_.html";
                        echo '</article>';
                        echo '<nav>';
                        echo '<ul>';
                        foreach ($articles as $key => $val) {
                            echo '<li>';
                            if ($key != $article) {
                                echo '<a href="?a=' . $key . '">' . $val["menu-item-name"]  . '</a>';
                            } else {
                                echo $val["menu-item-name"];
                            }
                            echo '</li>';
                        }
                        echo '</ul>';
                        echo '</nav>';
                        echo '</div>';
                    }
                } else {
                    include "pages/$page.html";
                }
                ?>
            </div>
        </div>
    </main>
    <footer>
        <div class="content">
            <div>
                <nav>
                    <ul>
                        <?php
                        foreach ($pages as $key => $val) {
                            if ($val["menu-item-name"]) {
                                echo '<li>';
                                echo '<a href="' . ($key == 'home' ? '' : '?p=' . $key) . '">' . $val["menu-item-name"] . '</a>';
                                echo '</li>' . "\n";
                            }
                        }
                        ?>
                    </ul>
                </nav>
                <div class="c1">VOŠ, SPŠ a SOŠ Varnsdorf, p. o., Bratislavská 2166, 407 47 Varnsdorf, ČR</div>
                <div class="c2">Zespół Szkół Zawodowych im. Sandora Petöfi w Ostródzie</div>
            </div>
        </div>
    </footer>
    <div class="under-footer">
        <div class="content">
            <div>
                <a href="http://www.skolavdf.cz">
                    <img src="img/skolavdf-logo.png" alt="logo VOŠ, SPŠ a SOŠ Varnsdorf">
                </a>
                <a href="https://ekonomik.webd.pl/ekonomikwp">
                    <img src="img/skolaostroda-logo.png" alt="logo ZESPOL SZKOL ZAWODOWYCH IM. SANDORA PETOFI W OSTRODZIE">
                </a>
                <a href="https://ec.europa.eu/programmes/erasmus-plus/node_en">
                    <img src="img/erasmus-logo.png" alt="logo Erasmus+">
                </a>
            </div>
        </div>
    </div>
</body>

</html>