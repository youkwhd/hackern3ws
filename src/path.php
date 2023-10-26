<?php
    list($expected_path, $_) = explode("index.php", $_SERVER["SCRIPT_NAME"]);

    // Assuming to be always http://
    $url = "http://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    $path = parse_url($url, PHP_URL_PATH);

    if (!str_ends_with($path, "/")) {
        $path .= "/";
    }

    if ($path != $expected_path) {
        require_once "pages/404.php";
        die();
    }
?>
