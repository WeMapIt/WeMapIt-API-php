<?php
header ("Content-type: application/json");
header ("Access-Control-Allow-Origin: *");


try {
	$bdd = new PDO('mysql:host=localhost:3306;dbname=wemapit;charset=utf8', 'root', 'root');
}
catch (Exception $e){
	die('Erreur : ' . $e->getMessage());
}
$reponse = $bdd->prepare('SELECT places FROM maps WHERE id = :id');
$reponse->execute(array(
	'id' => $_GET['id']
));

$m = $reponse->fetchAll(PDO::FETCH_ASSOC); 

$request = array(
	"api" => array(
		"timestamp" => $_SERVER['REQUEST_TIME'],
		"version" => 1.0 ),

	"stats" => array(
		"total" => count($m) ),

	"places" => json_decode($m[0]['places'], true)
);

echo json_encode($request, JSON_PRETTY_PRINT);

$reponse->closeCursor(); ?>