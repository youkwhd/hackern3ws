<?php
    require_once "../css.php";
    require_once "../path.php";
    require_once "../cache.php";
    require_once "../../config.php";

    require_once "../components/header.php";

    $memcached = NULL;

    if ($_CONFIG["MEMCACHED"]) {
        $memcached = new Memcached;
        $memcached->addServer("127.0.0.1", 11211);
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $top_stories = get_from_inet_or_cache($curl, $memcached, "global-topst", "https://hacker-news.firebaseio.com/v0/topstories.json");
    $top_stories_len = count($top_stories);

    $page = isset($_GET["p"]) && $_GET["p"] > 0 ? $_GET["p"] : 1;
    $to = $_CONFIG["NEWS_PER_PAGE"] * $page;
    $from = $to - $_CONFIG["NEWS_PER_PAGE"];
?>
    
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:ital,wght@0,300;0,400;0,500;1,300;1,400;1,500&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
    <?php require_css("/_static/css/news.css") ?>
    <title>Hacker N3ws</title>
</head>
<body>
    <main>
        <?= render_header() ?>
        <?php for ($i = $from; $i < $top_stories_len && $i < $to; $i++) : ?>
            <?php
                $story = get_from_inet_or_cache($curl, $memcached, "news-$top_stories[$i]", "https://hacker-news.firebaseio.com/v0/item/$top_stories[$i].json");

                $__url = isset($story->{"url"}) ? sprintf("%s://%s", parse_url($story->{"url"}, PHP_URL_SCHEME), parse_url($story->{"url"}, PHP_URL_HOST)) : "";
                $__url_text = isset($story->{"url"}) ? parse_url($story->{"url"}, PHP_URL_HOST) : "";
            ?>
            <div class="hn--container hn--news-container">
                <div>
                    <span class="hn--news-title">
                        <a target="_blank" href="<?= $story->{"url"} ?>">
                            <?= htmlspecialchars($story->{"title"}) ?>
                        </a>
                    </span>
                    <br />
                    <small class="hn--news-host-container">
                    <?= $story->{"by"} ?> — (<a class="hn--news-host" target="_blank" href="<?= $__url ?>"><?= $__url_text ?></a>)
                    </small>
                </div>
                <div>
                    <div class="hn--news-bottom">
                        <div>
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="100%" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M277.375 427V167.296l119.702 119.702L427 256 256 85 85 256l29.924 29.922 119.701-118.626V427h42.75z"></path></svg>
                            <span>
                                <?= $story->{"score"} ?>
                            </span>
                        </div>
                        <div>
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="100%" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M144 208c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm112 0c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm112 0c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zM256 32C114.6 32 0 125.1 0 240c0 47.6 19.9 91.2 52.9 126.3C38 405.7 7 439.1 6.5 439.5c-6.6 7-8.4 17.2-4.6 26S14.4 480 24 480c61.5 0 110-25.7 139.1-46.3C192 442.8 223.2 448 256 448c141.4 0 256-93.1 256-208S397.4 32 256 32zm0 368c-26.7 0-53.1-4.1-78.4-12.1l-22.7-7.2-19.5 13.8c-14.3 10.1-33.9 21.4-57.5 29 7.3-12.1 14.4-25.7 19.9-40.2l10.6-28.1-20.6-21.8C69.7 314.1 48 282.2 48 240c0-88.2 93.3-160 208-160s208 71.8 208 160-93.3 160-208 160z"></path></svg>
                            <span>
                                <?= isset($story->{"descendants"}) ? $story->{"descendants"} : "?" ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endfor ?>
        <footer class="hn--footer-root">
            <?php
                $prev = $page - 1;
                $next = $page + 1;

                $max_page = (int)($top_stories_len / $_CONFIG["NEWS_PER_PAGE"]);

                if ($prev <= 0 || $prev > $max_page) {
                    $prev = $max_page;
                }

                if ($next > $max_page) {
                    $next = 1;
                }
            ?>
            <div class="hn--footer-left">
                <a href="/?p=<?= $prev ?>">←</a>
            </div>
            <div class="hn--footer-right">
                <a href="/?p=<?= $next ?>">→</a>
            </div>
        </footer>
    <main>
</body>
</html>
