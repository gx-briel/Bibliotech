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
        
        $query = "UPDATE clientes SET removidoEm = CURDATE() WHERE id = '$id'";
        
        if (mysqli_query($conexao, $query)) {
            header("Location: listaCliente.php");
            exit();
        } else {
            echo "Erro ao remover o cliente: " . mysqli_error($conexao);
        }
    } else {
        echo "ID do cliente não foi recebido.";
    }
} else {
    echo "Método de requisição inválido.";
}
?>