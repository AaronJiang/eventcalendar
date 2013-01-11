<?php 
//Include necessary files
include_once '../sys/core/init.inc.php';

//Load the admin object
$obj = new Admin($dbo);

//Load a hash of the word test and output it
$hash1 = $obj->testSaltedHash("test");
echo "Hash 1 without a salt:<br>$hash1<br><br>";

//Load a hash of the word test and output it
$hash2 = $obj->testSaltedHash("test",$hash1);
echo "Hash 2 with a salt:<br>$hash2<br>";



