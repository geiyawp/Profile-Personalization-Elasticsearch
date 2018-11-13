<?php

require_once 'app/init.php';

session_start();

if (!isset($_SESSION['access_token'])) {
	header('Location: google_login.php');
	exit();
}

if(isset($_GET['q'])) {

	$q = $_GET['q'];
}

elseif (empty($_GET['q'])) {
	$q = '#';
}

$params = [
	    'index' => 'users',
	    'type' => 'user_mapping',
	    'id' => $_SESSION['id'],
	];

	if (isset($_SESSION['id'])) {
		$user = $client->get($params);
	}
  
  echo($_SESSION['user_status']);

	if(!empty($user)) {
      $prev_location = $user['_source']['prev_location']; 
	    $school = $user['_source']['school'];
	    $department = $user['_source']['department'];
      
 }
	else {
		echo("Elasticsearch does not return user");
 }
 
 $query1 = array();
	if(empty($query1)) {
	$query1 = (array) $client->search([
				'index' => 'fix_news',
		        'type' => 'fix_news_mapping',
		        'from' => '0', 
		        'size' => '10',
		        'explain' => 'true',
				'body' => [						
					'query' => [
						'bool' => [
							'must' => [
								'multi_match' => [
								'query' => "$q",
								'type' => 'most_fields',
								'fields' => ['title','title.indo','title.search','description','description.indo','description.search','content','content.indo', 'content.search', 'keywords','keywords.indo','keywords.search'],
								'operator' => 'and'
								//'boost' => 1
								]
							],							
							'should' => [
								['match' => ['title' => $prev_location]],
								['match' => ['title.indo' => $prev_location]],
								['match' => ['title.search' => $prev_location]],

								['match' => ['description' => $prev_location]],
								['match' => ['description.indo' => $prev_location]],
								['match' => ['description.search' => $prev_location]],

								['match_phrase' => ['content' => $prev_location]],
								['match_phrase' => ['content.indo' => $prev_location]],
								['match_phrase' => ['content.search' => $prev_location]],

								['match' => ['keywords' => $prev_location]],
								['match' => ['keywords.indo' => $prev_location]],
								['match' => ['keywords.search' => $prev_location]],

								['match_phrase' => ['title' => $school]],
								['match_phrase' => ['title.indo' => $school]],
								['match_phrase' => ['title.search' => $school]],

								['match_phrase' => ['description' => $school]],
								['match_phrase' => ['description.indo' => $school]],
								['match_phrase' => ['description.search' => $school]],

								['match_phrase' => ['content' => $school]],
								['match_phrase' => ['content.indo' => $school]],
								['match_phrase' => ['content.search' => $school]],

								['match_phrase' => ['keywords' => $school]],
								['match_phrase' => ['keywords.indo' => $school]],
								['match_phrase' => ['keywords.search' => $school]],

								['match_phrase' => ['title' => $department]],
								['match_phrase' => ['title.indo' => $department]],
								['match_phrase' => ['title.search' => $department]],

								['match_phrase' => ['description' => $department]],
								['match_phrase' => ['description.indo' => $department]],
								['match_phrase' => ['description.search' => $department]],

								['match_phrase' => ['content' => $department]],
								['match_phrase' => ['content.indo' => $department]],
								['match_phrase' => ['content.search' => $department]],

								['match_phrase' => ['keywords' => $department]],
								['match_phrase' => ['keywords.indo' => $department]],
								['match_phrase' => ['keywords.search' => $department]]

							],
								'minimum_should_match' =>1,
								'boost' => 1.0
						]
					]
				]
			]);
	  }
  $query = $query1;	
	if(empty($query)) {
		echo("Elasticsearch search return empty or not available");
	}
	elseif(!empty($query)) {
		
		$milliseconds =  $query['took'];
		$total_hits =  $query['hits']['total'];

		if($query['hits']['total'] >=1) {
		$results = $query['hits']['hits'];
		}
	}
  ?>
  
  <!-- ---------------------------- User Interface -------------------------------->

<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Search | ES </title>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		
	  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	  	<link rel="stylesheet" type="text/css" href="style.css"> 
	</head>	
	<body>
	<div class="user-name"><a href="profile.php"><?php echo "Hello, ", $_SESSION['given_name']; ?></a></div>
	<div class="register"><a href="logout.php">Log out</a></div>
	<div class="container" style="margin-top: 100px">
		<div class="row justify-content-center">
			<div class="col-md-6 col-offset-3" align="center">
          <img src="img/berita.png"><br><br>

          <form action="index.php" method="get">
			<input class="form-control" placeholder="Search something..." type="text" name="q"><br>
			<button type="submit" class="btn btn-default">
      		<span class="glyphicon glyphicon-search"></span> Search
    		</button>
          </form>  
        </div>
    </div>
    <div class="row justify-content-center">
    <?php echo "Total $total_hits documents found"; ?><br>
    <?php echo "Took $milliseconds ms"; ?>
	</div> <br>
         <?php
          if(isset($results)) {
          	foreach ($results as $r) {
          	?>          	
          		<div class="row justify-content-center">
          		<div class="result col-md-6 col-offset-3">          			
					   <div class="score">score: <?php echo $r['_score']; ?></div> 
					<div class="score">id: <?php echo $r['_source']['id']; ?></div>
          			<h5><a href="<?php echo $r['_source']['link']; ?>"><?php echo $r['_source']['title']; ?></a></h5>
          			<div class="result-keywords"><?php echo $r['_source']['description']; ?></div>
          			<div class="source">source: <?php echo $r['_source']['source']; ?></div>          			
          			<br>
          		</div>
          		</div>
          	<?php
          	}
          }
          ?> 
	</body>
</html>

  
    
