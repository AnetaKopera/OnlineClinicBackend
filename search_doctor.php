<?php 

session_start();

$specialization = strtoupper(ltrim(rtrim(filter_input(INPUT_POST, "specialization", FILTER_SANITIZE_STRING))));
$doctor = strtoupper(ltrim(rtrim(filter_input(INPUT_POST, "doctor", FILTER_SANITIZE_STRING))));


if($specialization != ""){
	$specialization = '%' . $specialization . '%';
	
}

if($doctor != ""){
	$doctor = '%' . $doctor . '%';
}

require_once "configuration.php";


$dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbConnection->query('SET CHARSET utf8');

$query = "SELECT doctors.id, doctors.specialization, users.name, users.surname, users.email 
		FROM `doctors` JOIN users ON users.id = doctors.idUser 
		WHERE users.id = doctors.idUser 
		AND upper(specialization) LIKE :specialization OR upper(name) LIKE :doctor OR upper(surname) LIKE :doctor;";

$statement = $dbConnection->prepare($query);
$statement->bindParam(":specialization", $specialization, PDO::PARAM_STR);
$statement->bindParam(":doctor", $doctor, PDO::PARAM_STR);


try 
{
	$statement->execute();
} 
catch (Exception $th) 
{
	
	$response["success"] = 0;
	$response["message"] = "Error in searching doctor";

	echo json_encode($response);
	exit();
}
	
$response["success"] = 1;
$response["message"] = "Displayed doctors"  ;


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

		$indexidDoctor= strval($i)  ."_idDoctor" ;
		$indexSpecialization= strval($i)  ."_Specialization" ;
		$indexName = strval($i)  ."_Name" ;
		$indexSurname= strval($i)  ."_Surname" ;
		$indexEmail= strval($i)  ."_Email" ;
	

		$response[$indexidDoctor] = $row->id;
		$response[$indexSpecialization] = $row->specialization;
		$response[$indexName] = $row->name;
		$response[$indexSurname] = $row->surname;
		$response[$indexEmail] = $row->email;
		$i = $i + 1;
		  
	}

echo json_encode($response);


?> 