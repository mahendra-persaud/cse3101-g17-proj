<?php
require_once __DIR__ . '/models/School.php';
$school = new School();
$res = $school->createYear(['year_name' => '2099/2100']);
var_dump($res);
if (!$res) {
    echo "Creation failed!\n";
} else {
    echo "Year created with ID: $res\n";
}
