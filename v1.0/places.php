<?php
header ("Content-type: application/json");
header ("Access-Control-Allow-Origin: *");

try {
	$bdd = new PDO('mysql:host=localhost:3306;dbname=wemapit;charset=utf8', 'root', 'root');
}
catch (Exception $e){
	die('Erreur : ' . $e->getMessage());
}

$token = "bite";

/* Functions */

function getPlaces($bdd, $map_id) {
	$reponse = $bdd->prepare('SELECT `id`, `name`, `link`, `description`, `longitude`, `latitude` FROM places WHERE map_id = :map_id');
	$reponse->execute(array(
		'map_id' => $map_id
	));

	$m = $reponse->fetchAll(PDO::FETCH_ASSOC); 

	foreach ($m as $key => $value) {
		$m[$key]['coordinates'] = array($m[$key]['latitude'], $m[$key]['longitude']);
		unset($m[$key]['latitude']);
		unset($m[$key]['longitude']);
	}

	$request = array(
		"api" => array(
			"timestamp" => $_SERVER['REQUEST_TIME'],
			"version" => 1.0 ),

		"stats" => array(
			"total" => count($m) ),

		"places" => $m
	);
	return $request;
}

function addPlace($bdd, $map_id) {
	$data = json_decode(file_get_contents('php://input'), true);
	$place = array(
		'latitude' => $data['latitude'],
		'longitude' => $data['longitude'],
		'name' => $data['name'],
		'link' => $data['link'],
		'description' => $data['description'],
		'map_id' => $map_id
	);
	unset($data['coordinates']);

	if (is_string($place['link']) && is_string($place['name']) && is_string($place['description']) && is_string($place['link']) && is_numeric($place['latitude']) && is_numeric($place['longitude'])) {
		$reponse = $bdd->prepare('INSERT INTO `places` (`map_id`, `name`, `link`, `description`, `latitude`, `longitude`) VALUES (:map_id, :name, :link, :description, :latitude, :longitude)');
		$reponse->execute($place);
		echo json_encode($place, JSON_PRETTY_PRINT);
	}
}

function editPlace($bdd, $map_id, $place_id) {

}

function deletePlace($bdd, $map_id, $place_id) {

}

function errorAPI($code, $message) {
	$request = array(
		"api" => array(
			"timestamp" => $_SERVER['REQUEST_TIME'],
			"version" => 1.0 ),

		"error" => array(
			"code" => $code,
			"message" => $message
		)
	);
	echo json_encode($request, JSON_PRETTY_PRINT);
}

/* Détection de l'action */

if (isset($_GET['map_id']) && $_GET['map_id'] != "" && is_numeric($_GET['map_id'])) {
	switch($_SERVER['REQUEST_METHOD']){
		case 'GET':
			echo json_encode(getPlaces($bdd, $_GET['map_id']), JSON_PRETTY_PRINT);
			break;
		case 'POST':
			addPlace($bdd, $_GET['map_id']);
			break;
		case 'PUT':
			editPlace($bdd, $_GET['map_id'], $_GET['place_id']);
			break;
		case 'DELETE':
			deletePlace($bdd, $_GET['map_id'], $_GET['place_id']);
			break;
		default:
			errorAPI(405, "Method not allowed");
			break;
	}
}
else {
	errorAPI(400, "Bad Request");
}