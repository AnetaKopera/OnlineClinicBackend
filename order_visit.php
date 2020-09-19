<?php 


$response = array();

$idService =  ltrim(rtrim(filter_input(INPUT_POST, "idService", FILTER_SANITIZE_STRING)));
$dateVisit = ltrim(rtrim(filter_input(INPUT_POST, "dateVisit", FILTER_SANITIZE_STRING)));
$hourVisit = ltrim(rtrim(filter_input(INPUT_POST, "hourVisit", FILTER_SANITIZE_STRING)));
$payInAdvance = ltrim(rtrim(filter_input(INPUT_POST, "payInAdvance", FILTER_SANITIZE_STRING)));
$idDoctor = ltrim(rtrim(filter_input(INPUT_POST, "idDoctor", FILTER_SANITIZE_STRING)));
$idClient = ltrim(rtrim(filter_input(INPUT_POST, "idClient", FILTER_SANITIZE_STRING)));
$clientToken = ltrim(rtrim(filter_input(INPUT_POST, "clientToken", FILTER_SANITIZE_STRING)));

require_once "configuration.php";

$dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbConnection->query('SET CHARSET utf8');

$query = "INSERT INTO visits (idService, dateVisit, hourVisit, payInAdvance, idDoctor, idUser, clientQR ) VALUES(:idService, :dateVisit, :hourVisit, :payInAdvance, :idDoctor, :idClient, :clientToken )";
$statement = $dbConnection->prepare($query);
$statement->bindParam(":idService", $idService, PDO::PARAM_INT);
$statement->bindParam(":dateVisit", $dateVisit, PDO::PARAM_STR);
$statement->bindParam(":hourVisit", $hourVisit, PDO::PARAM_STR);
$statement->bindParam(":payInAdvance", $payInAdvance, PDO::PARAM_STR);
$statement->bindParam(":idDoctor", $idDoctor, PDO::PARAM_INT);
$statement->bindParam(":idClient", $idClient, PDO::PARAM_INT);
$statement->bindParam(":clientToken", $clientToken, PDO::PARAM_STR);

try 
{
	$statement->execute();
} 
catch (Exception $th) 
{
    
    $response["success"] = 0;
    $response["message"] = "Error in order visit";
    echo json_encode($response);
    exit();
} 

$response["success"] = 1;
$response["message"] = "Visit ordered.";
echo json_encode($response);

?>