<?php
require_once dirname(__DIR__, 3) . '/legacy/legacy_auth.php';

logoutUser();

header('Location: index.php');
exit;
