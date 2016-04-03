<?php
$route = '/support/';
$app->get($route, function ()  use ($app,$contentType,$githuborg,$githubrepo){

	$ReturnObject = array();
	//$ReturnObject["contentType"] = $contentType;

	if($contentType == 'application/apis+json')
		{
		$app->response()->header("Content-Type", "application/json");

		$apis_json_url = "http://" . $githuborg . ".github.io/" . $githubrepo . "/apis.json";
		$apis_json = file_get_contents($apis_json_url);
		echo stripslashes(format_json($apis_json));
		}
	else
		{

	 	$request = $app->request();
	 	$params = $request->params();

		if(isset($params['query'])){ $query = trim(mysql_real_escape_string($params['query'])); } else { $query = '';}
		if(isset($params['page'])){ $page = trim(mysql_real_escape_string($params['page'])); } else { $page = 0;}
		if(isset($params['count'])){ $count = trim(mysql_real_escape_string($params['count'])); } else { $count = 50;}
		if(isset($params['sort'])){ $sort = trim(mysql_real_escape_string($params['sort'])); } else { $sort = 'Title';}
		if(isset($params['order'])){ $order = trim(mysql_real_escape_string($params['order'])); } else { $order = 'ASC';}

		// Pull from MySQL
		if($query!='')
			{
			$Query = "SELECT * FROM support WHERE title LIKE '%" . $query . "%' OR header LIKE '%" . $query . "%' OR footer LIKE '%" . $query . "%'";
			}
		else
			{
			$Query = "SELECT * FROM support";
			}
			$Query .= " ORDER BY " . $sort . " " . $order . " LIMIT " . $page . "," . $count;
			//echo $Query . "<br />";
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

				$app->response()->header("Content-Type", "application/json");
				echo format_json(json_encode($ReturnObject));
			}
	});
?>
