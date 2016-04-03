<?php
$route = '/support/:support_id/';
$app->delete($route, function ($support_id) use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$support_id = prepareIdIn($support_id,$host);
	$support_id = mysql_real_escape_string($support_id);

	$Add = 1;
	$ReturnObject = array();

 	$request = $app->request();
 	$_POST = $request->params();

	$query = "DELETE FROM support WHERE support_id = " . $support_id;
	//echo $query . "<br />";
	mysql_query($query) or die('Query failed: ' . mysql_error());

	});
?>
