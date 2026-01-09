<?php
#LOCALHOST DELETE ME
$hostname = 'localhost';
$user = 'root';
$password = '';
$dbs = 'emdp';
#ENDLOCALHOST

//Create connection
$connection = mysqli_connect($hostname, $user, $password, $dbs);

//Check connection
if($connection->connect_error){
    die("Connection failed: " . $connection->connect_error);
}
?>