<?php 


$response = array();

$surname = ltrim(rtrim(filter_input(INPUT_POST, "surname", FILTER_SANITIZE_STRING)));
if (empty($surname)|| (!filter_var($surname, FILTER_SANITIZE_STRING)))
{
	$response["success"] = 0;
    $response["message"] = "Error in attribute surname";
    echo json_encode($response);
	exit();
}

$name = ltrim(rtrim(filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)));
if (empty($name)|| (!filter_var($name, FILTER_SANITIZE_STRING)))
{
	$response["success"] = 0;
    $response["message"] = "Errror in attribute name";
    echo json_encode($response);
	exit();
}


$account_type = "client"; 


$email = ltrim(rtrim(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));
if ((empty($email)) || (!filter_var($email, FILTER_SANITIZE_STRING))) 
{
	$response["success"] = 0;
    $response["message"] = "Error in attribute email";
    echo json_encode($response);
	exit();
}

$password = ltrim(rtrim(filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING)));
if ((empty($password)) || (!filter_var($password, FILTER_SANITIZE_STRING)))
{
	$response["success"] = 0;
    $response["message"] = "Error in attribute password";
    echo json_encode($response);
	exit();
}

$password = password_hash($password, PASSWORD_DEFAULT);

require_once "configuration.php";


$dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbConnection->query('SET CHARSET utf8');


$query = "INSERT INTO users (surname, name, accountType, email, password) VALUES(:surname, :name, :accountType, :email, :password)";
$statement = $dbConnection->prepare($query);
$statement->bindParam(":surname", $surname, PDO::PARAM_STR);
$statement->bindParam(":name", $name, PDO::PARAM_STR);
$statement->bindParam(":accountType", $account_type, PDO::PARAM_STR);
$statement->bindParam(":email", $email, PDO::PARAM_STR);
$statement->bindParam(":password", $password, PDO::PARAM_STR);

try 
{
    $statement->execute();
    $query = "SELECT id FROM users WHERE email = :email";
    $statement = $dbConnection->prepare($query);
    $statement->bindParam(":email", $email, PDO::PARAM_STR);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_OBJ);
    foreach ($result as $row)
			{
                $response["id"] = $row->id;
			}	
} 
catch (Exception $th) 
{ 
    $response["success"] = 0;
    $response["message"] = "Oops! An error occurred.";
    echo json_encode($response);
    exit();
} 
    $response["success"] = 1;
    $response["message"] = "User created.";
    echo json_encode($response);

?>