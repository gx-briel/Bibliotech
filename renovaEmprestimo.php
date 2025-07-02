<?php
require 'conexao.php';
session_start(); 

if (!isset($_SESSION['id'])) {

    header('Location: index.php');
    exit; 
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!empty($_POST["emprestimoId"]) && isset($_POST["novaDataRenovacao"])) {
        $emprestimoId = intval($_POST["emprestimoId"]);
        $novaDataRenovacao = $_POST["novaDataRenovacao"];

        if (empty($novaDataRenovacao) || !strtotime($novaDataRenovacao)) {
            echo "Data de renovação inválida.";
            exit();
        }
        $queryEmprestimo = "UPDATE emprestimo 
                            SET renovadoEm = NOW(), vencimento = '$novaDataRenovacao' 
                            WHERE id = $emprestimoId AND ativo = 1"; 

        if (mysqli_query($conexao, $queryEmprestimo)) {

            header("Location: listaEmprestimoAtivo.php");
            exit();
        } else {
            echo "Erro ao renovar o empréstimo: " . mysqli_error($conexao);
        }
    } else {
        echo "Empréstimo não encontrado ou data de renovação não informada.";
    }
} else {
    echo "Método de requisição inválido.";
}
?>
