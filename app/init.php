<?php

require_once 'vendor/autoload.php';

use Elasticsearch\ClientBuilder;

$hosts = ['127.0.0.1:9200'];              // Replace with your host
$client = ClientBuilder::create()           // Instantiate a new ClientBuilder
	->setHosts($hosts)      // Set the hosts
    ->build();              // Build the client object

// $response = $client->search([...]);  // Execute a search with the new client


// $es = new Elasticsearch\Client([
// 	'hosts' => ['127.0.0.1:9200']
// ]);

?>