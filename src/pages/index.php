<?php
    require_once "../css.php";
    require_once "../path.php";
    require_once "../cache.php";
    require_once "../../config.php";

    if ($_CONFIG["MEMCACHED"]) {
        $memcached = new Memcached;
        $memcached->addServer("127.0.0.1", 11211);
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $top_stories = get_from_inet_or_cache($curl, $memcached, "global-topst", "https://hacker-news.firebaseio.com/v0/topstories.json");
    $jobs = get_from_inet_or_cache($curl, $memcached, "global-topjob", "https://hacker-news.firebaseio.com/v0/jobstories.json");

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
    <?php require_css("/_static/css/news.css") ?>
    <title>Hacker N3ws</title>
</head>
<body>
    <header>
        <h1 class="hn--header-title">hackern3ws</h1>
        <nav>
            <ul>
                <li>
                    <a href="/news">
                        NEWS
                    </a>
                </li>
                <li>
                    <a href="/jobs">
                        JOBS
                    </a>
                </li>
            </ul>
        </nav>
    </header>
    <h2 class="hn--lt-title">Latest Jobs</h2>
    <?php for ($i = 0; $i < 3; $i++) : ?>
        <?php
            $job = get_from_inet_or_cache($curl, $memcached, "jobs-$jobs[$i]", "https://hacker-news.firebaseio.com/v0/item/$jobs[$i].json");
        ?>
        <div class="hn--container hn--jobs-container">
            <span class="hn--jobs-title">
                <a target="_blank" href="<?= $job->{"url"} ?>">
                    <?= $job->{"title"} ?>
                </a>
            </span>
            <br />
            <div class="hn--news-bottom">
                <small>
                    <?= $job->{"by"} ?>
                </small>
            </div>
        </div>
    <?php endfor ?>
    <hr />
    <h2 class="hn--tn-title">Top News</h2>
    <?php for ($i = $from; $i < $top_stories_len && $i < $to; $i++) : ?>
        <?php
            $story = get_from_inet_or_cache($curl, $memcached, "news-$top_stories[$i]", "https://hacker-news.firebaseio.com/v0/item/$top_stories[$i].json");
        ?>
        <div class="hn--container hn--news-container">
            <div>
                <span class="hn--news-title">
                    <a target="_blank" href="<?= $story->{"url"} ?>">
                        <?= $story->{"title"} ?>
                    </a>
                </span>
                <br />
                <div class="hn--news-bottom">
                    <small>
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 20 20" aria-hidden="true" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M10 8a3 3 0 100-6 3 3 0 000 6zM3.465 14.493a1.23 1.23 0 00.41 1.412A9.957 9.957 0 0010 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 00-13.074.003z"></path></svg>
                        <?= $story->{"by"} ?>
                    </small>
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
</body>
</html>
