<?php
    require_once "../css.php";
    require_once "../cache.php";
    require_once "../cookie.php";
    require_once "../../config.php";

    if (isset($_CONFIG["USE_MEMCACHED"]) && $_CONFIG["USE_MEMCACHED"]) {
        $memcached = new Memcached;
        $memcached->addServer("127.0.0.1", 11211);
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://hacker-news.firebaseio.com/v0/topstories.json");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $page = isset($_GET["p"]) && $_GET["p"] > 0 ? $_GET["p"] : 1;
    $top_stories = json_decode(curl_exec($curl));

    $to = $_COOKIE["NEWS_PER_PAGE"] * $page;
    $from = $to - $_COOKIE["NEWS_PER_PAGE"];
?>
    

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_css("_static/css/news.css") ?>
    <title>Hacker N3ws</title>
</head>
<body>
    <h1>
        Hacker N3ws
    </h1>
    <?php for ($i = $from; $i < count($top_stories) && $i < $to; $i++) : ?>
        <?php
            if ((isset($_CONFIG["USE_MEMCACHED"]) && $_CONFIG["USE_MEMCACHED"]) && $memcached->get($top_stories[$i])) {
                $story = $memcached->get($top_stories[$i]);
            } else {
                curl_setopt($curl, CURLOPT_URL, "https://hacker-news.firebaseio.com/v0/item/$top_stories[$i].json");
                $story = json_decode(curl_exec($curl));

                if (isset($_CONFIG["USE_MEMCACHED"]) && $_CONFIG["USE_MEMCACHED"]) {
                    $memcached->set($top_stories[$i], $story);
                }

                echo "CACHED";
            }
        ?>
        <div class="hn--news-containter">
            <span class="hn--news-title">
                <?= $story->{"title"} ?>
            </span>
        </div>
    <?php endfor ?>
    <footer class="hn--footer-root">
        <div class="hn--footer-left">
        </div>
        <div class="hn--footer-right">
            <?php
                $prev = $page - 1;
                $next = $page + 1;

                if ($prev <= 0) {
                    $prev = 1;
                }
            ?>
            <a href="/?p=<?= $prev ?>">Prev</a>
            <a href="/?p=<?= $next ?>">Next</a>
        </div>
    </footer>
</body>
</html>
