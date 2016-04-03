<?php
$route = '/support/:support_id/';
$app->get($route, function ($support_id) use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$support_id = prepareIdIn($support_id,$host);
	$support_id = mysql_real_escape_string($support_id);

	$ReturnObject = array();
	$Query = "SELECT * FROM support WHERE support_id = " . $support_id;
	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());

	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{

		$support_id = $Database['support_id'];
		$title = $Database['title'];
		$image = $Database['image'];
		$header = $Database['header'];
		$footer = $Database['footer'];

		$resourcesQuery = "SELECT * from resources r";
		$resourcesQuery .= " WHERE support_id = " . $support_id;
		$resourcesQuery .= " ORDER BY title ASC";
		$resourcesResults = mysql_query($resourcesQuery) or die('Query failed: ' . mysql_error());

		$support_id = prepareIdOut($support_id,$host);

		$F = array();
		$F['support_id'] = $support_id;
		$F['title'] = $title;
		$F['image'] = $image;
		$F['header'] = $header;
		$F['footer'] = $footer;

		// resources
		$F['resources'] = array();
		while ($resources = mysql_fetch_assoc($resourcesResults))
			{
			$title = $resources['title'];
			$description = $resources['description'];
			$image = $resources['image'];
			$url = $resources['url'];
			$K = array();
			$K['title'] = $title;
			$K['description'] = $description;
			$K['image'] = $image;
			$K['url'] = $url;
			array_push($F['resources'], $K);
			}

		$ReturnObject = $F;
		}

		$app->response()->header("Content-Type", "application/json");
		echo format_json(json_encode($ReturnObject));
	});
?>
