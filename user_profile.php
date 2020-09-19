<?php 


$response = array();

$idUser =  ltrim(rtrim(filter_input(INPUT_POST, "idUser", FILTER_SANITIZE_STRING)));

require_once "configuration.php";

$dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbConnection->query('SET CHARSET utf8');

$query = "SELECT  surname, name, email FROM users WHERE id = :idUser";
$statement = $dbConnection->prepare($query);
$statement->bindParam(":idUser", $idUser, PDO::PARAM_INT);

try 
{
	$statement->execute();
} 
catch (Exception $th) 
{
    
    $response["success"] = 0;
    $response["message"] = "Error in displaying user information";
    echo json_encode($response);
    exit();
} 

$response["success"] = 1;
$response["message"] = "User information displayed.";


$result = $statement->fetchAll(PDO::FETCH_OBJ);
foreach ($result as $row)
{
    
    $response["surname"] = $row->surname;
    $response["name"] = $row->name;
    $response["email"] = $row->email;
      
}
echo json_encode($response);

?>