<?php
require 'conexao.php';
session_start(); 

if (!isset($_SESSION['id'])) {

    header('Location: index.php');
    exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST["emprestimoId"])) {
        $emprestimoId = intval($_POST["emprestimoId"]);
        // Busca empréstimo ativo
        $queryInfo = "SELECT idLivro, ativo FROM emprestimo WHERE id = ? LIMIT 1";
        $stmt = mysqli_prepare($conexao, $queryInfo);
        mysqli_stmt_bind_param($stmt, "i", $emprestimoId);
        mysqli_stmt_execute($stmt);
        $resultInfo = mysqli_stmt_get_result($stmt);
        $emprestimo = mysqli_fetch_assoc($resultInfo);
        if ($emprestimo && !empty($emprestimo['idLivro']) && $emprestimo['ativo']) {
            $idLivro = $emprestimo['idLivro'];

            $queryLivro = "UPDATE livros SET disponivel = 1 WHERE ID = $idLivro";
            $queryEmprestimo = "UPDATE emprestimo SET devolvidoEm = NOW() WHERE id = $emprestimoId";
            $queryAtivo = "UPDATE emprestimo SET ativo = '0' WHERE id = $emprestimoId";

            if (mysqli_query($conexao, $queryLivro) && mysqli_query($conexao, $queryEmprestimo) && mysqli_query($conexao, $queryAtivo)) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'O livro foi devolvido com sucesso!'
                ]);
                exit();
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Erro ao devolver livro: ' . mysqli_error($conexao)
                ]);
                exit();
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Empréstimo não encontrado ou já devolvido.'
            ]);
            exit();
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Empréstimo não encontrado'
        ]);
        exit();
    }
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Método de requisição inválido.'
    ]);
    exit();
}
