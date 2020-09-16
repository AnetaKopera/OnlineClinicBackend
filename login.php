<?php 

session_start();

$response = array(); 


$login = ltrim(rtrim(filter_input(INPUT_POST, "login", FILTER_SANITIZE_STRING)));
if (empty($login))
{
	$response["success"] = 0;
    $response["message"] = "Error in attribute login";
    echo json_encode($response);
	exit();
}

$password =  ltrim(rtrim(filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING)));  
if (empty($password))
{
	$response["success"] = 0;
    $response["message"] = "Error in attribute password";
    echo json_encode($response);
	exit();
}


require_once "configuration.php";


$dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbConnection->query('SET CHARSET utf8');


$query = "SELECT id, accountType, password  FROM `users`  WHERE email = :login AND accountType='client'";
$statement = $dbConnection->prepare($query);
$statement->bindParam(":login", $login, PDO::PARAM_STR);

try 
{
	$statement->execute();
} 
catch (Exception $th) 
{ 
    $response["success"] = 0;
    $response["message"] = "Error wrong login or password!";
    echo json_encode($response);
    exit();
} 

        $result = $statement->fetchAll(PDO::FETCH_OBJ);
        $passDataBase = null;

        foreach ($result as $row)
			{
			  $passDataBase = $row->password;
            }
            

        if(password_verify($password, $passDataBase) == false)
        {
            $response["success"] = 0;
            $response["message"] = "Bad email or password"  ;
        }
        else{
		  foreach ($result as $row)
			{
                $response["success"] = 1;
                $response["message"] = "Logged into account id"  ;
				$response["id"] = $row->id;
				$response["accountType"] = $row->accountType;
			}	
        }
        echo json_encode($response);
		

?>
