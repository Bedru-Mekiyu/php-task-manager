<?php

function check_auth()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION["user_id"])) {
        header("Location: index.php?action=login");
        exit;
    }
}
