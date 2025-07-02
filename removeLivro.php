<?php
require 'conexao.php';
session_start(); 


if (!isset($_SESSION['id'])) {

    header('Location: index.php');
    exit; 
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST["id"])) {
        $id = $_POST["id"];
        
        $query = "DELETE FROM produtos WHERE id = '$id'";
        
        if (mysqli_query($conexao, $query)) {
            header("Location: listaProduto.php");
            exit(); 
        } else {
            echo "Erro ao remover o produto: " . mysqli_error($conexao);
        }
    } else {
        echo "ID do produto não foi recebido.";
    }
} else {
    echo "Método de requisição inválido.";
}
?>