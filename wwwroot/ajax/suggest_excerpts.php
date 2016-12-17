<?php

require_once ('/home/wwwyoutube/lib/suggest/common.php');
require_once ('/home/wwwyoutube/lib/suggest/functions.php');

$indexes 	= 'songtext';
$arr 		= array();
$q 			= trim($_GET['term']);
$stmt 		= $ln_sph->prepare("SELECT * FROM $indexes WHERE MATCH(:match) LIMIT 0,10 OPTION ranker=sph04");


$aq = explode(' ',$q);
if(strlen($aq[count($aq)-1])<3){
	$query = $q;
}else{
	$query = $q.'*';
}
$stmt->bindValue(':match', $query,PDO::PARAM_STR);
$stmt->execute();

$docs = array();
$title = "";
$stmsnp = $ln_sph->prepare("CALL SNIPPETS(:doc,$indexes,:query)");
$stmsnp->bindValue(':query',$query,PDO::PARAM_STR);
$stmsnp->bindParam(':doc',$title,PDO::PARAM_STR);

foreach($stmt->fetchAll() as $r){
	$title = $r['title'];
	$stmsnp->execute();
	$r = $stmsnp->fetch();
	$arr[] = array('id' => utf8_encode($r['snippet']),'label' =>utf8_encode( $r['snippet']));
}


echo json_encode($arr);
exit();
