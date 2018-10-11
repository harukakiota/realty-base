<?php
if(!isset($_COOKIE['realtor_uid'])) {
    header("HTTP/1.1 403 Forbidden");
    exit();
} else {
    return($_COOKIE['realtor_uid']);
}
?>