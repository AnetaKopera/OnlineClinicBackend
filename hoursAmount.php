<?php 

session_start();

$timeOfService = ltrim(rtrim(filter_input(INPUT_POST, "timeOfService", FILTER_VALIDATE_INT)));
$dateVisit = ltrim(rtrim(filter_input(INPUT_POST, "dateVisit", FILTER_SANITIZE_STRING)));
$idDoctor = ltrim(rtrim(filter_input(INPUT_POST, "idDoctor", FILTER_VALIDATE_INT)));

require_once "configuration.php";


$dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbConnection->query('SET CHARSET utf8');


$day = strtolower(date('l', strtotime($dateVisit)));

$startDay= $day ."Start";
$stopDay= $day ."Stop";


$query = "SELECT " .$startDay  .", ". $stopDay ." FROM timeofworking
        JOIN doctors ON doctors.idWorkSchedule=timeofworking.id
        WHERE doctors.id=:idDoctor;";
$statement = $dbConnection->prepare($query);
$statement->bindParam(":idDoctor", $idDoctor, PDO::PARAM_INT);


try 
{
	$statement->execute();
} 
catch (Exception $th) 
{
	
    $response["success"] = 0;
    $response["message"] = "Error in checking hours";
    echo json_encode($response);
	exit();
}


$result = $statement->fetchAll(PDO::FETCH_OBJ);
$startHour = null;
$stopHour = null;
   
  foreach ($result as $row)
    {
        $startHour = $row->$startDay;
        $stopHour = $row->$stopDay;
          
    }
 //echo  "start hour: ".$startHour . "  stop hour: " .  $stopHour ."<br>"; 

if($startHour == "00:00:00" && $stopHour == "00:00:00")
{
    $response["success"] = 0;
	$response["message"] = "Error doctor dont work in choosed day";
	$response["query_amount"] = 0;
    echo json_encode($response);
	exit();
}

 $query ="SELECT visits.hourVisit, services.timeOfService  FROM visits 
 JOIN services ON visits.idService = services.id 
 WHERE idDoctor=:idDoctor and dateVisit=:dateVisit ORDER BY TIME(hourVisit);";
$statement = $dbConnection->prepare($query);
$statement->bindParam(":idDoctor", $idDoctor, PDO::PARAM_INT);
$statement->bindParam(":dateVisit", $dateVisit, PDO::PARAM_STR);
try 
{
	$statement->execute();
} 
catch (Exception $th) 
{
	
    $response["success"] = 0;
    $response["message"] = "Error in checking visits";
    echo json_encode($response);
	exit();
}

$result = $statement->fetchAll(PDO::FETCH_OBJ);
$visits=0;

$array =[];

  foreach ($result as $row)
    {
        $visits++;
        
        $rowArray =[strtotime($row->hourVisit), ( strtotime($row->hourVisit) + intval($row->timeOfService) *60)];
        array_push($array,$rowArray);
          
    }
 //echo "visits amount: " .$visits ."<br>";
//echo "time of service: ".$timeOfService ."<br>"; 
//echo "List of visits: <br>"; 

 /*for($j=0; $j<$visits; $j++)
 {
    echo date('H:i:s',$array[$j][0]) ."  " .date('H:i:s',$array[$j][1]) . "<br>"; ////////////
 }*/

$response["success"] = 0;
$response["message"] = "No avaible termin in this day"  ;



    //echo $dateVisit ." " . date("Y-m-d", strtotime($dateVisit)) ." " .date("Y-m-d", time());
 if( date("Y-m-d", strtotime($dateVisit)) == date("Y-m-d", time()))
 {
    //echo "TEN SAM DZIEŃ";
    //echo date("H", time())+1;
    if(date("H", time())=="23")
    {
       exit();
    }
    else{
        $ssstring =date("H", time())+1 .":00:00";
       // echo date("H:i:s", strtotime( $ssstring ));
        $VisitHourStart = strtotime( $ssstring ); 
        $VisitHourStop =  $VisitHourStart + intval($timeOfService) *60;
    }
 }
 else
 {
    $VisitHourStart = strtotime($startHour);
    $VisitHourStop =  $VisitHourStart + intval($timeOfService) *60;
    
 }

$checkingvisit=0;
$index=0;
$atleastonehour=true;
$nomore = true;
while($VisitHourStop <= strtotime($stopHour))
{
    if($checkingvisit==$visits)
    {
		 while($VisitHourStart< strtotime($stopHour) && ( ($VisitHourStart + intval($timeOfService) *60)  <= strtotime($stopHour)))
			{
				$response["success"] = 1;
				$response["message"] = "Displayed all hours list to choose"  ;
				$atleastonehour = false;
				
			   //echo $index . "    "  .date('H:i:s',$VisitHourStart) , "<br>";
			  /////////// $namehour = $index ."_hour";
			  //////////// $response[$namehour] = date('H:i:s',$VisitHourStart);
			   $index++;
			   $VisitHourStart += 15*60;
			}
        break;
    }
    else
    {
        if($VisitHourStart < $array[$checkingvisit][0]) 
        {
            if($VisitHourStop <= $array[$checkingvisit][0]) 
            {
                 $response["success"] = 1;
                 $response["message"] = "Displayed all hours list to choose";
                   
                //echo  $index ."   " .  date('H:i:s',$VisitHourStart) , "<br>";
               //////////////////// $namehour = $index ."_hour";
               /////////////////// $response[$namehour] = date('H:i:s',$VisitHourStart);
                $index++;
                $VisitHourStart += 15*60; 
                $VisitHourStop =  $VisitHourStart + intval($timeOfService) *60;
            }
            else
            {
                $VisitHourStart = $array[$checkingvisit][1];
                $VisitHourStop =  $VisitHourStart + intval($timeOfService) *60;
                $checkingvisit++;
            }
        }
        else if($VisitHourStart == $array[$checkingvisit][0])
        {
            $VisitHourStart = $array[$checkingvisit][1];
            $VisitHourStop =  $VisitHourStart + intval($timeOfService) *60;
            $checkingvisit++;
        }
        else
        {
            if ($VisitHourStart > $array[$checkingvisit][1])
            {
                $checkingvisit++;
            }
            else
            {
                $VisitHourStart = $array[$checkingvisit][1];
                $VisitHourStop =  $VisitHourStart + intval($timeOfService) *60;
                $checkingvisit++;
            }
        }
    }


}

$response["query_amount"] = $index;

echo json_encode($response);



?>