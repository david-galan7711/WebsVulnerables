<?php
require_once __DIR__ . '/session.php';

mordor_start_session();
mordor_destroy_session();

header("Location: ./");
exit();
