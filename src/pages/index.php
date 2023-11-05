<?php
    require_once "../css.php";
    require_once "../path.php";
    require_once "../../config.php";

    if ($_CONFIG["MEMCACHED"]) {
        $memcached = new Memcached;
        $memcached->addServer("127.0.0.1", 11211);
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    if ($_CONFIG["MEMCACHED"] && $memcached->get("global-topst")) {
        $top_stories = $memcached->get("global-topst");
    } else {
        curl_setopt($curl, CURLOPT_URL, "https://hacker-news.firebaseio.com/v0/topstories.json");
        $top_stories = json_decode(curl_exec($curl)) ?: [];

        if ($_CONFIG["MEMCACHED"]) {
            // cache for 2 minutes
            $memcached->set("global-topst", $top_stories, 60 * 2);
        }
    }

    if ($_CONFIG["MEMCACHED"] && $memcached->get("global-topjob")) {
        $jobs = $memcached->get("global-topjob"); 
    } else {
        curl_setopt($curl, CURLOPT_URL, "https://hacker-news.firebaseio.com/v0/jobstories.json");
        $jobs = json_decode(curl_exec($curl)) ?: [];

        if ($_CONFIG["MEMCACHED"]) {
            // cache for 2 minutes 
            $memcached->set("global-topjob", $jobs, 60 * 2);
        }
    }

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
            if ($_CONFIG["MEMCACHED"] && $memcached->get("jobs-$jobs[$i]")) {
                $job = $memcached->get("jobs-$jobs[$i]");
            } else {
                curl_setopt($curl, CURLOPT_URL, "https://hacker-news.firebaseio.com/v0/item/$jobs[$i].json");
                $job = json_decode(curl_exec($curl));

                if ($_CONFIG["MEMCACHED"]) {
                    $memcached->set("jobs-$jobs[$i]", $job);
                }
            }
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
            if ($_CONFIG["MEMCACHED"] && $memcached->get("news-$top_stories[$i]")) {
                $story = $memcached->get("news-$top_stories[$i]");
            } else {
                curl_setopt($curl, CURLOPT_URL, "https://hacker-news.firebaseio.com/v0/item/$top_stories[$i].json");
                $story = json_decode(curl_exec($curl));

                if ($_CONFIG["MEMCACHED"]) {
                    $memcached->set("news-$top_stories[$i]", $story);
                }
            }
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
