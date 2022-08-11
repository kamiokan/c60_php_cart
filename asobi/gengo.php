<?php
require_once '../common/common.php';

$seireki = $_POST['seireki'];

$wareki = gengo($seireki);
echo $wareki;

