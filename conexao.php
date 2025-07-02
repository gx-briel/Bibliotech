<?php
$servidor = "localhost";
$usuario = "root";
$senha = "1234";
$dbname = "biblioteca";
$conexao = mysqli_connect($servidor, $usuario, $senha, $dbname);
if (!$conexao) {
die("Erro ao realizar conexão com banco de dados: ".
mysqli_connect_error());
}
?>