<?php
header ("Content-type: application/json");
header ("Access-Control-Allow-Origin: *");


try {
	$bdd = new PDO('mysql:host=localhost:3306;dbname=wemapit;charset=utf8', 'root', 'root');
}
catch (Exception $e){
	die('Erreur : ' . $e->getMessage());
}
$reponse = $bdd->query('SELECT `id`, `name`, `description` FROM maps');

$m = $reponse->fetchAll(PDO::FETCH_ASSOC); 

$request = array(
	"api" => array(
		"timestamp" => $_SERVER['REQUEST_TIME'],
		"version" => 1.0 ),

	"stats" => array(
		"total" => count($m) ),

	"maps" => $m
);

echo json_encode($request, JSON_PRETTY_PRINT);

$reponse->closeCursor(); ?>