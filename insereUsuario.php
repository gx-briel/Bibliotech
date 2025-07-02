<?php
require("conexao.php");
session_start();

// Dados do formulário
$nomeUsuario = $_POST['nome'];
$senha = $_POST['password_input'];
$email = $_POST['email'];
$nascimento = empty($_POST['nascimento']) ? "NULL" : "'" . mysqli_real_escape_string($conexao, $_POST['nascimento']) . "'";

// 1. Verificar o último número de usuário cadastrado
$queryUltimoUsuario = "SELECT usuario FROM usuarios ORDER BY usuario DESC LIMIT 1";
$result = mysqli_query($conexao, $queryUltimoUsuario);

if ($result->num_rows > 0) {
    // Se existe ao menos um usuário cadastrado, pegar o último número de usuário
    $ultimoUsuario = mysqli_fetch_assoc($result)['usuario'];
    // Incrementar o número de usuário
    $novoUsuario = str_pad((int)$ultimoUsuario + 1, 4, '0', STR_PAD_LEFT);
} else {
    // Se não houver nenhum usuário cadastrado, começar com 0001
    $novoUsuario = '0001';
}

// 2. Verificar se o número de usuário já existe
$sqlVerificarUsuario = "SELECT * FROM usuarios WHERE usuario = '$novoUsuario'";
$resultVerificar = mysqli_query($conexao, $sqlVerificarUsuario);

if ($resultVerificar->num_rows > 0) {
    // Se o número de usuário já existe, adicionar mais 1 (caso ocorra algum erro)
    $novoUsuario = str_pad((int)$novoUsuario + 1, 4, '0', STR_PAD_LEFT);
}

// 3. Verificar se o número de usuário já está em uso
$sql = "SELECT * FROM usuarios WHERE usuario = '$novoUsuario'";
$result = mysqli_query($conexao, $sql);

if ($result->num_rows > 0) {
    $_SESSION['mensagem'] = "USUÁRIO JÁ CADASTRADO";
    $_SESSION['tipo'] = "erro";
    header("Location: cadastroUsuario.php");
    exit();
} else {
    // 4. Inserir os dados do usuário no banco de dados
    $insereUsuario = "INSERT INTO usuarios(nome, usuario, senha, email, nascimento) 
                      VALUES ('$nomeUsuario', '$novoUsuario', '$senha', '$email', $nascimento)";
    $operacaoSQL = mysqli_query($conexao, $insereUsuario);

    if (mysqli_affected_rows($conexao) != 0) {
        $_SESSION['mensagem'] = "Usuário cadastrado com sucesso!";
        $_SESSION['tipo'] = "sucesso";
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['mensagem'] = "O cliente não foi cadastrado com sucesso!";
        $_SESSION['tipo'] = "erro";
        header("Location: cadastrousuario.php");
        exit();
    }
}
?>
