<?php
require 'conexao.php';
session_start(); 


if (!isset($_SESSION['id'])) {

    header('Location: index.php');
    exit; 
}
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "UPDATE livros SET removidoEm = CURDATE() WHERE ID = $id";
    if (mysqli_query($conexao, $query)) {

        header("Location: acervo.php");
        exit();
    } else {
        echo "Erro ao remover o livro: " . mysqli_error($conexao);
    }
}
?>
