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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Listagem de Empréstimos</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      background-color: rgb(238, 255, 235);
      margin: 0;
      overflow-x: hidden;
    }
    .wrapper {
      display: flex;
    }
    .sidebar {
      width: 250px;
      background-color: #1c0e3f;
      color: white;
      min-height: 100vh;
      transition: transform 0.3s ease;
      position: fixed;
      z-index: 999;
    }
    .sidebar.hidden {
      transform: translateX(-100%);
    }
    .sidebar .sidebar-header {
      padding: 1rem;
      font-size: 1.5rem;
      font-weight: bold;
      background-color: #150a2c;
      text-align: center;
    }
    .nav-links {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    .nav-links li {
      padding: 0.75rem 1rem;
    }
    .nav-links li a {
      color: white;
      font-weight: bold;
      text-decoration: none;
      display: block;
    }
    .nav-links li a:hover {
      color: #ffcc00;
    }
    .toggle-btn {
      background: none;
      border: none;
      color: white;
      font-size: 1.1rem;
      padding: 0.5rem 1rem;
      text-align: left;
      width: 100%;
      cursor: pointer;
    }
    .logout-btn {
      position: absolute;
      bottom: 1rem;
      left: 1rem;
      right: 1rem;
    }
    .show-sidebar-btn {
      position: fixed;
      top: 15px;
      left: 15px;
      z-index: 1000;
      background-color: #1c0e3f;
      color: white;
      border: none;
      border-radius: 4px;
      padding: 8px 12px;
      font-size: 1.2rem;
      display: none;
    }
    .sidebar.hidden ~ .show-sidebar-btn {
      display: block;
    }
    .content {
      margin-left: 250px;
      padding: 2rem;
      flex: 1;
      transition: margin-left 0.3s;
    }
    .sidebar.hidden ~ .content {
      margin-left: 0;
    }
    .card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    @media (max-width: 768px) {
      .content {
        margin-left: 0 !important;
      }
    }
    .table-responsive {
      overflow-x: auto;
    }
  </style>
</head>
<body>
<div class="wrapper">
  <!-- Sidebar -->
  <nav id="sidebar" class="sidebar">
    <div class="sidebar-header"><a href="indexlogado.php" style="color: #fff; text-decoration: none;">Bibliotech</a></div>
    <button class="toggle-btn btn btn-sm btn-warning w-100 mb-2" onclick="hideSidebar()">← Recolher</button>
    <ul class="nav-links">
      <li><a href="cadastroCliente.php">Cadastrar Cliente</a></li>
      <li><a href="cadastroLivro.php">Cadastrar Livro</a></li>
      <li><a href="acervo.php">Acervo de Livros</a></li>
      <li><a href="listaCliente.php">Lista Clientes</a></li>
      <li><a href="criaEmprestimo.php">Criar Empréstimo</a></li>
      <li><a href="relatorios.php">Empréstimos</a></li>
    </ul>
    <div class="logout-btn">
      <a href="logout.php" class="btn btn-danger w-100">🚪 Sair</a>
    </div>
  </nav>
    <button id="showSidebarBtn" class="show-sidebar-btn" style="position: fixed; left: 4px; top: 18px; right: auto; cursor: pointer; z-index: 1000;">☰</button>
  <!-- Conteúdo principal -->
  <div class="content">
    <div class="container-fluid">
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
      <div class="table-responsive mt-4">
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
  </div>
</div>
<script>
  function hideSidebar() {
    document.getElementById('sidebar').classList.add('hidden');
    document.getElementById('showSidebarBtn').style.display = 'block';
  }
  function showSidebar() {
    document.getElementById('sidebar').classList.remove('hidden');
    document.getElementById('showSidebarBtn').style.display = 'none';
  }

  // Clique simples para abrir a sidebar
  document.getElementById('showSidebarBtn').addEventListener('click', showSidebar);
</script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
