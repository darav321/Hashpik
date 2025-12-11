<?php
session_start();
if (isset($_GET['term'])) {
    $_SESSION['pending_search'] = $_GET['term'];
}
