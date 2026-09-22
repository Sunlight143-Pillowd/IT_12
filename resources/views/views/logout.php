<?php
require_once dirname(__DIR__, 2) . '/config/auth.php';

logoutUser();

header('Location: index.php');
exit;
