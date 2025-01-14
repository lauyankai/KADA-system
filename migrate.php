<?php
require_once 'app/config/Database.php';
require_once 'app/config/Migration.php';

$db = new Database();
$migration = new Migration($db->getConnection());
$migration->run();