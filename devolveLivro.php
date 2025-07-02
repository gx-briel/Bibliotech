<?php
require 'conexao.php';
session_start(); 

if (!isset($_SESSION['id'])) {

    header('Location: index.php');
    exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST["emprestimoId"])) {
        $emprestimoId = $_POST["emprestimoId"];
        
        $queryInfo = "SELECT idLivro FROM emprestimo WHERE id = $emprestimoId";
        $resultInfo = mysqli_query($conexao, $queryInfo);
        $emprestimo = mysqli_fetch_assoc($resultInfo);
        $idLivro = $emprestimo['idLivro'];

        $queryLivro = "UPDATE livros SET disponivel = 1 WHERE ID = $idLivro";
        
        $queryEmprestimo = "UPDATE emprestimo SET devolvidoEm = NOW() WHERE id = $emprestimoId";

        $queryAtivo = "UPDATE emprestimo SET ativo = '0' WHERE id = $emprestimoId";

        if (mysqli_query($conexao, $queryLivro) && mysqli_query($conexao, $queryEmprestimo) && mysqli_query($conexao, $queryAtivo)) {
            header("Location: listaEmprestimoAtivo.php");
            exit();
        } else {
            echo "Erro ao devolver livro: " . mysqli_error($conexao);
        }
    } else {
        echo "Empréstimo não encontrado";
    }
} else {
    echo "Método de requisição inválido.";
}
