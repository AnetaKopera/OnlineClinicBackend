<?php 

$id = ltrim(rtrim(filter_input(INPUT_POST, "id", FILTER_SANITIZE_STRING)));


$surname = ltrim(rtrim(filter_input(INPUT_POST, "surname", FILTER_SANITIZE_STRING)));
if (empty($surname) || (!filter_var($surname, FILTER_SANITIZE_STRING)) )
{
	$response["success"] = 0;
    $response["message"] = "Errror in attribute surname";
    echo json_encode($response);
	exit();
}

$name = ltrim(rtrim(filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)));
if (empty($name) || (!filter_var($name, FILTER_SANITIZE_STRING)) )
{
	$response["success"] = 0;
    $response["message"] = "Errror in attribute name";
    echo json_encode($response);
	exit();
}


$accountType = "client";

$email = ltrim(rtrim(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));
if ((empty($email)) || (!filter_var($email, FILTER_SANITIZE_EMAIL))) 
{
	$response["success"] = 0;
    $response["message"] = "Errror in attribute email";
    echo json_encode($response);
	exit();
}

require_once "configuration.php";


$dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbConnection->query('SET CHARSET utf8');


$query = "UPDATE  `users` SET 
        surname=:surname, name=:name, 
        accountType=:accountType, email=:email
        WHERE id=:id";
$statement = $dbConnection->prepare($query);
$statement->bindParam(":surname", $surname, PDO::PARAM_STR);
$statement->bindParam(":name", $name, PDO::PARAM_STR);
$statement->bindParam(":accountType", $accountType, PDO::PARAM_STR);
$statement->bindParam(":email", $email, PDO::PARAM_STR);
$statement->bindParam(":id", $id, PDO::PARAM_INT);

try 
{
	$statement->execute();
} 
catch (Exception $th) 
{
    $response["success"] = 0;
    $response["message"] = "Error in update user";
    echo json_encode($response);
	exit();
}

    $response["success"] = 1;
    $response["message"] = "User profile succesuffly updated";
    echo json_encode($response);
    exit();
    
?>