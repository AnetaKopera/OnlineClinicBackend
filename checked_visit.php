<?php

session_start();

require_once "configuration.php";

$token = ltrim(rtrim(filter_input(INPUT_POST, "token", FILTER_SANITIZE_STRING)));

$dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbConnection->query('SET CHARSET utf8');

$query = "SELECT visits.id, visits.idService,
            visits.dateVisit, visits.hourVisit, visits.payInAdvance, visits.IDdOCTOR, services.typeOfService, 
            users.name, users.surname
            FROM visits 
            JOIN services ON visits.idService=services.id 
            JOIN clinics ON services.idClinic=clinics.id
            JOIN doctors ON visits.idDoctor=doctors.id
            JOIN users ON doctors.idUser = users.id
            WHERE clientQR=:token ORDER BY visits.dateVisit, visits.hourVisit";
$statement = $dbConnection->prepare($query);
$statement->bindParam(":token", $token, PDO::PARAM_STR);

try 
{
	$statement->execute();
} 
catch (Exception $th) 
{ 
    $response["success"] = 0;
    $response["message"] = "Error in displaying visit";
 	echo json_encode($response);
    exit();
} 

        $response["success"] = 1;
        $response["message"] = "Displayed checked visit"  ;
	
		
		
		$result = $statement->fetchAll(PDO::FETCH_OBJ);
		
			$numberQuery=0;
				 foreach ($result as $row)
				{
					$mystring= $row->dateVisit ." " .$row->hourVisit;
                    $checkDate =  strtotime($mystring); 
                    if($checkDate >= time())
                    {
                        $numberQuery = $numberQuery + 1;
                    }
					  
				}
            $response["query_amount"] = $numberQuery;
            if($numberQuery==0)
            {
                $response["success"] = 0;
                $response["message"] = "Bad token"  ;
	
            }
			
			
			  foreach ($result as $row)
				{

                    $mystring= $row->dateVisit ." " .$row->hourVisit;
                    $checkDate =  strtotime($mystring); 
                    if($checkDate >= time()) //tutaj stare sa kasowane
                    {
                        $response["typeOfService"] = $row->typeOfService;
                        $response["nameDoctor"] = $row->name;
                        $response["surnameDoctor"] = $row->surname;
                        $response["dateVisit"] = $row->dateVisit;
                        $response["hourVisit"] = $row->hourVisit;
                        $response["payInAdvance"] = $row->payInAdvance;
                    }
					  
				}
			
		echo json_encode($response);
		

?> 
