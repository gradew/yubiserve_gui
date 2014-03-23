<?php
session_start();

include "config.php";

$is_nickname=isset($_POST['nickname']);
$is_publicid=isset($_POST['publicid']);
$is_privateid=isset($_POST['privateid']);
$is_aeskey=isset($_POST['aeskey']);
$is_serno=isset($_POST['serno']);

if(!($is_nickname&&$is_publicid&&$is_privateid&&$is_aeskey&&$is_serno)){
    die("MISSING PARAMETERS");
}
$nickname=preg_replace('/\s+/', '', $_POST['nickname']);
$publicid=preg_replace('/\s+/', '', $_POST['publicid']);
$privateid=preg_replace('/\s+/', '', $_POST['privateid']);
$aeskey=preg_replace('/\s+/', '', $_POST['aeskey']);
$serno=preg_replace('/\s+/', '', $_POST['serno']);

if(!preg_match('/^[a-zA-Z\-_\.0-9@]+$/', $nickname)) { die("Malformed field (nickname)!"); }
if(!preg_match('/^[cbdefghijklnrtuv]{12}$/', $publicid)) { die("Malformed field (public ID)!"); }
if(!preg_match('/^[0-9a-f]{12}$/', $privateid)) { die("Malformed field (private ID)!"); }
if(!preg_match('/^[0-9a-f]{32}$/', $aeskey)) { die("Malformed field (AES key)!"); }
if(!preg_match('/^[0-9 ]+$/', $serno)) { die("Malformed field (serial)!"); }

$command="$yubiserve_root/dbconf.py -ya $nickname $publicid $privateid $aeskey";
$last_line=exec($command, $output, $return_val);

if($return_val==0) {
    echo "OK";
}else{
    echo "$last_line";
}

?>
