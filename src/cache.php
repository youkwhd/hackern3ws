<?php

session_start();

if (!isset($_SESSION["cache"])) {
    $_SESSION["cache"] = array();
}
