<?php
$route = '/support/:support_id/';
$app->put($route, function ($support_id) use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$support_id = prepareIdIn($support_id,$host);
	$support_id = mysql_real_escape_string($support_id);

	$ReturnObject = array();

 	$request = $app->request();
 	$params = $request->params();

	if(isset($params['title'])){ $title = mysql_real_escape_string($params['title']); } else { $title = date('Y-m-d H:i:s'); }
	if(isset($params['image'])){ $image = mysql_real_escape_string($params['image']); } else { $image = ''; }
	if(isset($params['header'])){ $header = mysql_real_escape_string($params['header']); } else { $header = ''; }
	if(isset($params['footer'])){ $footer = mysql_real_escape_string($params['footer']); } else { $footer = ''; }

  $Query = "SELECT * FROM support WHERE ID = " . $support_id;
	//echo $Query . "<br />";
	$Database = mysql_query($Query) or die('Query failed: ' . mysql_error());

	if($Database && mysql_num_rows($Database))
		{
		$query = "UPDATE support SET ";
		$query .= "title = '" . mysql_real_escape_string($title) . "'";
		$query .= ", image = '" . mysql_real_escape_string($image) . "'";
		$query .= ", header = '" . mysql_real_escape_string($header) . "'";
		$query .= ", footer = '" . mysql_real_escape_string($footer) . "'";
		$query .= " WHERE support_id = " . $support_id;
		//echo $query . "<br />";
		mysql_query($query) or die('Query failed: ' . mysql_error());
		}

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
	echo stripslashes(format_json(json_encode($ReturnObject)));

	});
?>
