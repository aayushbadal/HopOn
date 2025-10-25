<?php



define("DB_HOST", "localhost");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "");
define("DB_NAME", "hopon");


// create connection using mysqli procedural
$conn = mysqli_connect(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_NAME);

//check connection
if(!$conn){
    die("Connection failed: " . mysqli_connect_error());
}