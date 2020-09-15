<?php 

session_start();

$idDoctor = ltrim(rtrim(filter_input(INPUT_POST, "idDoctor", FILTER_SANITIZE_STRING)));


require_once "configuration.php";


$dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbConnection->query('SET CHARSET utf8');

$query = "SELECT services.id, services.typeOfService, services.description, services.price,
		services.timeOfService, services.idClinic
 		FROM services  JOIN tenure ON services.id = tenure.idService
		WHERE services.id = tenure.idService
		AND tenure.idDoctor = :idDoctor;";

$statement = $dbConnection->prepare($query);
$statement->bindParam(":idDoctor", $idDoctor, PDO::PARAM_STR);


try 
{
	$statement->execute();
} 
catch (Exception $th) 
{
	
	$response["success"] = 0;
	$response["message"] = "Error in searching services";

	echo json_encode($response);
	exit();
}
	
$response["success"] = 1;
$response["message"] = "Displayed services" ;


$result = $statement->fetchAll(PDO::FETCH_OBJ);

		
$numberQuery=0;
	 foreach ($result as $row)
	{
		
		$numberQuery = $numberQuery + 1;
	
	}
$response["query_amount"] = $numberQuery;


$i = 0 ;
  foreach ($result as $row)
	{

		$indexidServices= strval($i)  ."_idServices" ;
		$indexTypeOfService= strval($i)  ."_typeOfService" ;
		$indexDescription= strval($i)  ."_description" ;
		$indexPrice = strval($i)  ."_price" ;
		$indexTimeOfService= strval($i)  ."_timeOfService" ;
		$indexidClinic= strval($i)  ."_clinic" ;
	

		$response[$indexidServices] = $row->id;
		$response[$indexTypeOfService] = $row->typeOfService;
		$response[$indexDescription] = $row->description;
		$response[$indexPrice] = $row->price;
		$response[$indexTimeOfService] = $row->timeOfService;
		$response[$indexidClinic] = $row->idClinic;
		$i = $i + 1;
		  
	}

echo json_encode($response);


?> 