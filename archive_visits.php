<?php

session_start();

require_once "configuration.php";

$id = ltrim(rtrim(filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT))); 

$dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbConnection->query('SET CHARSET utf8');

$query = "SELECT visits.id, visits.idService,
            visits.dateVisit, visits.hourVisit, visits.payInAdvance, visits.idDoctor, services.typeOfService, 
            users.name, users.surname
            FROM visits 
            JOIN services ON visits.idService=services.id 
            JOIN clinics ON services.idClinic=clinics.id
            JOIN doctors ON visits.idDoctor=doctors.id
            JOIN users ON doctors.idUser = users.id
            WHERE visits.idUser=:id ORDER BY visits.dateVisit DESC, visits.hourVisit ASC ";
$statement = $dbConnection->prepare($query);
$statement->bindParam(":id", $id, PDO::PARAM_INT);

try 
{
	$statement->execute();
} 
catch (Exception $th) 
{ 
    $response["success"] = 0;
    $response["message"] = "Error in displaying visits";
 	echo json_encode($response);
    exit();
} 

        $response["success"] = 1;
        $response["message"] = "Displayed archive visits"  ;
	
		
		
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
		   
			$response["query_amount"] = 0;
            if($numberQuery==0)
            {
                $response["success"] = 0;
                $response["message"] = "No visits"  ;
	
            }
			
			
			$i = 0 ;
			  foreach ($result as $row)
				{

                    $mystring= $row->dateVisit ." " .$row->hourVisit;
                    $checkDate =  strtotime($mystring); 
                    if($checkDate < time()) //tutaj stare sa kasowane
                    {
                        //$index_id= "id_" .strval($i);
                        //$response[$index_id] = $row->id;

                        //$index_idService= "idService_" .strval($i);
                        //$response[$index_idService] = $row->idService;

                        $index_nameService= "typeOfService_" .strval($i);
                        $response[$index_nameService] = $row->typeOfService;

                       	$index_name= "name_" .strval($i);
                        $response[$index_name] = $row->name;

                        $index_surname= "surname_" .strval($i);
                        $response[$index_surname] = $row->surname;

                        $index_dateVisit= "dateVisit_" .strval($i);
                        $response[$index_dateVisit] = $row->dateVisit;

                        $index_hourVisit= "hourVisit_" .strval($i);
                        $response[$index_hourVisit] = $row->hourVisit;

                        $index_payInAdvance= "payInAdvance_" .strval($i);
                        $response[$index_payInAdvance] = $row->payInAdvance;

                        //$index_nameOfCompany= "nameOfCompany_" .strval($i);
                       // $response[$index_nameOfCompany] = $row->nameOfCompany;
                        
                        //$index_city= "city_" .strval($i);
                        //$response[$index_city] = $row->city;
                        
                        //$index_street= "street_" .strval($i);
                        //$response[$index_street] = $row->street;

                        //$index_idWorker= "idWorker_" .strval($i);
                        //$response[$index_idWorker] = $row->idWorker;
                        
                        $i = $i + 1;
                    }
					  
				}
		$response["query_amount"] = $i;
		echo json_encode($response);
		

?> 
