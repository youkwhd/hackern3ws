<?php
    require_once "../cookie.php";

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
    <title>Hacker N3ws</title>
</head>
<body>
    <h1>
        Hacker N3ws
    </h1>
    <?php for ($i = $from; $i < count($top_stories) && $i < $to; $i++) : ?>
        <?php
            curl_setopt($curl, CURLOPT_URL, "https://hacker-news.firebaseio.com/v0/item/$top_stories[$i].json");
            $story = json_decode(curl_exec($curl));
        ?>
        <div>
            <span>
                <?= $story->{"title"} ?>
            </span>
        </div>
    <?php endfor ?>
</body>
</html>
