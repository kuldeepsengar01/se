<?php
$host="localhost";
$user="root";
$pass="";
$db="seating";

$conn=mysqli_connect($host,$user,$pass,$db);

if($conn){
   echo"connected successfully";
}else{
    echo"not connected";
}
?>