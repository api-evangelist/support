<?php
$route = '/support/';
$app->post($route, function () use ($app){

 	$request = $app->request();
 	$params = $request->params();

	if(isset($params['title'])){ $title = mysql_real_escape_string($params['title']); } else { $title = date('Y-m-d H:i:s'); }
	if(isset($params['image'])){ $image = mysql_real_escape_string($params['image']); } else { $image = ''; }
	if(isset($params['header'])){ $header = mysql_real_escape_string($params['header']); } else { $header = ''; }
	if(isset($params['footer'])){ $footer = mysql_real_escape_string($params['footer']); } else { $footer = ''; }

  $Query = "SELECT * FROM support WHERE title = '" . $title . "'";
	//echo $Query . "<br />";
	$Database = mysql_query($Query) or die('Query failed: ' . mysql_error());

	if($Database && mysql_num_rows($Database))
		{
		$Thissupport = mysql_fetch_assoc($Database);
		$support_id = $Thissupport['ID'];
		}
	else
		{
		$Query = "INSERT INTO support(title,image,header,footer)";
		$Query .= " VALUES(";
		$Query .= "'" . mysql_real_escape_string($title) . "',";
		$Query .= "'" . mysql_real_escape_string($image) . "',";
		$Query .= "'" . mysql_real_escape_string($header) . "',";
		$Query .= "'" . mysql_real_escape_string($footer) . "'";
		$Query .= ")";
		//echo $Query . "<br />";
		mysql_query($Query) or die('Query failed: ' . mysql_error());
		$support_id = mysql_insert_id();
		}

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
