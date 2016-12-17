<?php
require_once ('/home/wwwyoutube/lib/suggest/common.php');
require_once ('/home/wwwyoutube/lib/suggest/functions.php');

$indexes 	= 'songtext';

$arr =array();
$q = trim($_GET['term']);
$stmt = $ln_sph->prepare("SELECT * FROM $indexes WHERE MATCH(:match) LIMIT 0,10");


$aq = explode(' ',$q);
if(strlen($aq[count($aq)-1])<3){
	$query = $q;
}else{
	$query = $q.'*';
}
$stmt->bindValue(':match', $query,PDO::PARAM_STR);
$stmt->execute();

foreach($stmt->fetchAll() as $r){
	$arr[] = array('id' => utf8_encode($r['title']),'label' =>utf8_encode( $r['title']));
}

echo json_encode($arr);
exit();
