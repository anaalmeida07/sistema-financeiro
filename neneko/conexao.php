<?php
session_start(); //inicia a sessão para armazernar os dados logados 

$servername = "localhost";
$username="ana";
$password = "123456";
$dbname= "financeiro";

$conn = new mysqli($servername, $username, $password, $dbname);

//verifica a conexao
if ($conn-> connect_error){
    die ("Falha na conexao: " . $conn->connect_error);
}
?>