<?php 

$id = ltrim(rtrim(filter_input(INPUT_POST, "id", FILTER_SANITIZE_STRING)));


$password_old = ltrim(rtrim(filter_input(INPUT_POST, "password_old", FILTER_SANITIZE_STRING)));
if (empty($password_old) || (!filter_var($password_old, FILTER_SANITIZE_STRING)) )
{
	$response["success"] = 0;
    $response["message"] = "Error in attribute password_old";
    echo json_encode($response);
	exit();
}


$password_new = ltrim(rtrim(filter_input(INPUT_POST, "password_new", FILTER_SANITIZE_STRING)));
if (empty($password_new) || (!filter_var($password_new, FILTER_SANITIZE_STRING)) )
{
	$response["success"] = 0;
    $response["message"] = "Error in attribute password_new";
    echo json_encode($response);
	exit();
}

$password_new2 = ltrim(rtrim(filter_input(INPUT_POST, "password_new2", FILTER_SANITIZE_STRING)));
if(!filter_var($password_new2, FILTER_SANITIZE_STRING) )
{
	$response["success"] = 0;
    $response["message"] = "Error in attribute password_new2";
    echo json_encode($response);
	exit();
}

require_once "configuration.php";


$dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbConnection->query('SET CHARSET utf8');


$query ="SELECT id, password from users WHERE id=:id";

$statement = $dbConnection->prepare($query);
$statement->bindParam(":id", $id, PDO::PARAM_INT);
try 
{
	$statement->execute();
} 
catch (Exception $th) 
{	
    $response["success"] = 0;
    $response["message"] = "Error in chcecking old password in database";
    echo json_encode($response);
	exit();
}

        $result = $statement->fetchAll(PDO::FETCH_OBJ);
		
        $passDataBase = null;
             foreach ($result as $row)
            {
                $passDataBase = $row->password;
                  
            }

            if(password_verify($password_old, $passDataBase ) == true)
            {
                if($password_new==$password_new2)
                {
                    $password_new = password_hash($password_new, PASSWORD_DEFAULT);

                    $query = "UPDATE  `users` SET password=:password_new WHERE id=:id";
                    $statement = $dbConnection->prepare($query);
                    $statement->bindParam(":password_new", $password_new, PDO::PARAM_STR);
                    $statement->bindParam(":id", $id, PDO::PARAM_INT);
                    try 
                    {
                        $statement->execute();
                    } 
                    catch (Exception $th) 
                    {
                        $response["success"] = 0;
                        $response["message"] = "Error in update user password";
                        echo json_encode($response);
                        exit();
                    }
                    $response["success"] = 1;
                    $response["message"] = "User password succesuffly updated";
                    echo json_encode($response);
                    exit();

                }
                else
                {
                    $response["success"] = 0;
                    $response["message"] = "Error new passwords are not the same";
                    echo json_encode($response);
                    exit();
                }
            }
            else{
                $response["success"] = 0;
                $response["message"] = "Error wrong old password";
                echo json_encode($response);
                exit();

            }

    
?>