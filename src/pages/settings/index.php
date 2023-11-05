<?php
    require_once "../../css.php";

    if ($_POST["news-per-page"]) {
        $_COOKIE["news-per-page"] = $_POST["news-per-page"];
    }
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
    <form>
        <label>
            News per page
        </label>
        <input type="number" value=10 min=5 max=20 />
        <button type="submit">
            Save
        </button>
    </form>
</body>
</html>
