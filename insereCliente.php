<?php
require("conexao.php");
session_start(); 


if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

$nomeCliente = mysqli_real_escape_string($conexao, $_POST['nome']);
$cpf = preg_replace('/\D/', '', $_POST['cpf']);
$cep = preg_replace('/\D/', '', $_POST['cep']);
$rua = mysqli_real_escape_string($conexao, $_POST['rua']);
$numero = mysqli_real_escape_string($conexao, $_POST['numero']);
$bairro = mysqli_real_escape_string($conexao, $_POST['bairro']);
$cidade = mysqli_real_escape_string($conexao, $_POST['cidade']);
$estado = mysqli_real_escape_string($conexao, $_POST['estado']);
$telefone = preg_replace('/\D/', '', $_POST['telefone']);

$sql = "SELECT * FROM clientes WHERE cpf = '$cpf' AND removidoEm IS NULL";
$result = mysqli_query($conexao, $sql);

if ($result->num_rows > 0) {
    echo "<script>alert('Erro: CPF já cadastrado!'); window.location.href='cadastroCliente.php';</script>";
    exit;
}

$insereCliente = "INSERT INTO clientes (nomeCliente, cpf, cep, rua, numero, bairro, cidade, estado, telefone) 
                  VALUES ('$nomeCliente', '$cpf', '$cep', '$rua', '$numero', '$bairro', '$cidade', '$estado', '$telefone')";

$operacaoSQL = mysqli_query($conexao, $insereCliente);

if ($operacaoSQL) {

    header("Location: listaCliente.php");
    exit; 
} else {

    echo "<script>alert('Erro ao Cadastrar Clientes. Tente novamente!'); window.location.href='cadastroCliente.php';</script>";
}
?>