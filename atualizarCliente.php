<?php
require 'conexao.php';
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit; 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idCliente = $_POST['id'];
    $nomeCliente = $_POST['nomeCliente'];
    $cpf = $_POST['cpf'];
    $telefone = $_POST['telefone'];
    $cep = $_POST['cep'];
    $rua = $_POST['rua'];
    $numero = $_POST['numero'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];

    $query = "UPDATE clientes SET nomeCliente = '$nomeCliente', cpf = '$cpf', telefone = '$telefone', cep = '$cep', rua = '$rua', numero = '$numero', bairro = '$bairro', cidade = '$cidade', estado = '$estado' WHERE id = $idCliente";

    if (mysqli_query($conexao, $query)) {
        header("Location: listaCliente.php");
        exit;
    } else {
        echo "Erro ao atualizar cliente: " . mysqli_error($conexao);
    }
} else {
    echo "Método de requisição inválido!";
}
?>
