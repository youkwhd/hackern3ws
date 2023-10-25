<?php

function cookie_reset($name, $value){
    if (!isset($_COOKIE[$name])) {
        $_COOKIE[$name] = $value;
    }
}

cookie_reset("NEWS_PER_PAGE", 10);
