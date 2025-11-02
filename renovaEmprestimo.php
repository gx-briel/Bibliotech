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
            // Retornar JSON para AJAX
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Empréstimo renovado com sucesso!']);
            exit();
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Erro ao renovar o empréstimo: ' . mysqli_error($conexao)]);
            exit();
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Empréstimo não encontrado ou data de renovação não informada.']);
        exit();
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
    exit();
}
?>
