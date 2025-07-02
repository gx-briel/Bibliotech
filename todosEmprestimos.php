<?php
require 'conexao.php';
session_start(); 

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit; 
}

$buscaCliente = isset($_GET['cliente']) ? $_GET['cliente'] : '';
$buscaLivro = isset($_GET['livro']) ? $_GET['livro'] : '';

// Proteção contra SQL Injection
$buscaCliente = mysqli_real_escape_string($conexao, $buscaCliente);
$buscaLivro = mysqli_real_escape_string($conexao, $buscaLivro);

// Consulta SQL com parâmetros para busca de cliente e livro
$consulta = "SELECT emp.id as empId, cli.nomeCliente as nomeCliente, li.titulo, emp.criadoEm, li.ID as livroId, emp.ativo, emp.vencimento
             FROM emprestimo as emp 
             JOIN clientes as cli on emp.idCliente = cli.id 
             JOIN livros as li on emp.idLivro = li.ID
             WHERE cli.nomeCliente LIKE '%$buscaCliente%' AND li.titulo LIKE '%$buscaLivro%'";

$executaConsulta = mysqli_query($conexao, $consulta);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Listagem de Empréstimos</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>

    body {
      background-color: rgb(238, 255, 235);
      padding-bottom: 60px;
    }
    .navbar {
      background-color: #1c0e3f;
    }
    .navbar-brand, .nav-link {
      color: white !important;
      font-weight: bold;
    }
    .navbar-nav .nav-link:hover {
      color: #ffcc00 !important;
    }
    .table-responsive {
      overflow-x: auto;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
  <a class="navbar-brand" href="indexlogado.php">Bibliotech</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
    <span class="navbar-toggler-icon text-white">&#9776;</span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto">
          <li class="nav-item"><a class="nav-link" href="cadastroCliente.php">Cadastrar Cliente</a></li>
      <li class="nav-item"><a class="nav-link" href="cadastroLivro.php">Cadastrar Livro</a></li>
      <li class="nav-item"><a class="nav-link" href="acervo.php">Acervo de Livros</a></li>
      <li class="nav-item"><a class="nav-link" href="criaEmprestimo.php">Criar Empréstimo</a></li>
    </ul>
  </div>
</nav>

<div class="container mt-5">
  <h2 class="mb-4">Listagem de Empréstimos</h2>

  <form method="GET" action="" class="form-row">
    <div class="form-group col-md-4">
      <input type="text" class="form-control" name="cliente" placeholder="Buscar Cliente">
    </div>
    <div class="form-group col-md-4">
      <input type="text" class="form-control" name="livro" placeholder="Buscar Livro">
    </div>
    <div class="form-group col-md-4">
      <button type="submit" class="btn btn-primary btn-block">Buscar</button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-striped">
      <thead class="thead-dark">
        <tr>
          <th>ID</th>
          <th>Cliente</th>
          <th>Livro</th>
          <th>Data de Criação</th>
          <th>Data para Devolução</th>
          <th>Ativo</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if (mysqli_num_rows($executaConsulta) > 0) {
            while ($emprestimos = mysqli_fetch_assoc($executaConsulta)) {
        ?>
          <tr>
            <td><?= $emprestimos['empId']; ?></td>
            <td><?= $emprestimos['nomeCliente']; ?></td>
            <td><?= $emprestimos['titulo']; ?></td>
            <td><?= date('d/m/Y', strtotime($emprestimos['criadoEm'])); ?></td>
            <td><?= date('d/m/Y', strtotime($emprestimos['vencimento'])); ?></td>
            <td><?= $emprestimos['ativo'] == 0 ? 'Não' : 'Sim'; ?></td>
          </tr>
        <?php
            }
        } else {
            echo "<tr><td colspan='6' class='text-center'>Nenhum Empréstimo encontrado.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
