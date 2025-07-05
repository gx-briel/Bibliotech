<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

require_once 'conexao.php';

$sql = "SELECT COUNT(*) AS totalLivros FROM livros";
$res = mysqli_query($conexao, $sql);
$row = mysqli_fetch_assoc($res);
$totalLivros = $row['totalLivros'] ?? 0;

$sqlemprestimo = "SELECT COUNT(*) AS totalEmprestimos from emprestimo where ativo = '1'";
$res2 = mysqli_query($conexao, $sqlemprestimo);
$row2 = mysqli_fetch_assoc($res2);
$totalEmp = $row2['totalEmprestimos'] ?? 0;

$sqlvencido = "SELECT COUNT(*) AS totalVencido from emprestimo where vencimento < curdate() AND ativo = '1'";
$res3 = mysqli_query($conexao, $sqlvencido);
$row3 = mysqli_fetch_assoc($res3);
$totalVenc = $row3['totalVencido'] ?? 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - Bibliotech</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
      background: linear-gradient(180deg, #1c0e3f 60%, #e8f5e9 100%);
      color: white;
      min-height: 100vh;
      transition: transform 0.3s ease;
      position: fixed;
      z-index: 999;
      box-shadow: 2px 0 8px rgba(28,14,63,0.08);
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
      text-decoration: underline;
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

    /* Estilo harmonizado para a imagem */
    .img-harmonizada {
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      padding: 1rem;
      background-color: #fff;
      max-width: 900px;
      width: 100%;
    }
  </style>
</head>
<body>

<div class="wrapper">
  <!-- Sidebar -->
  <nav id="sidebar" class="sidebar">
    <div class="sidebar-header"><a href="indexlogado.php" style="color: #fff; text-decoration: none;"><i class="fa-solid fa-book-open-reader" style="margin-right:8px;"></i><span style="letter-spacing:1px;">Bibliotech</span></a></div>
    <button class="toggle-btn btn btn-sm btn-warning w-100 mb-2" onclick="hideSidebar()">← Recolher</button>
    <ul class="nav-links">
      <li><a href="cadastroCliente.php">Cadastrar Clientes</a></li>
      <li><a href="listaCliente.php">Listar Clientes</a></li>
      <li><a href="cadastroLivro.php">Cadastrar Livro</a></li>
      <li><a href="acervo.php">Acervo de Livros</a></li>
      <li><a href="criaEmprestimo.php">Criar Empréstimo</a></li>
      <li><a href="relatorios.php">Relatórios</a></li>
    </ul>

    <!-- Botão de logout no rodapé -->
    <div class="logout-btn">
      <a href="logout.php" class="btn btn-danger w-100">🚪 Sair</a>
    </div>
  </nav>

  <!-- Botão para mostrar sidebar -->
  <button id="showSidebarBtn" class="show-sidebar-btn" style="position: fixed; left: 4px; top: 18px; right: auto; cursor: pointer; z-index: 1000;">☰</button>

  <!-- Conteúdo principal -->
  <div class="content">
    <div class="container-fluid">
      <div class="row text-center">
        <div class="col-md-4 mb-4">
          <div class="card bg-primary text-white" style="cursor: pointer;" onclick="window.location.href='acervo.php'">
            <div class="card-body">
              <h5 class="card-title">Total de Livros</h5>
              <p class="card-text display-4"><?= $totalLivros; ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card bg-success text-white" style="cursor: pointer;" onclick="window.location.href='listaEmprestimoAtivo.php'">
            <div class="card-body">
              <h5 class="card-title">Empréstimos Ativos</h5>
              <p class="card-text display-4"><?= $totalEmp; ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card bg-danger text-white" style="cursor: pointer;" onclick="window.location.href='emprestimoVencido.php'">
            <div class="card-body">
              <h5 class="card-title">Vencidos</h5>
              <p class="card-text display-4"><?= $totalVenc; ?></p>
            </div>
          </div>
        </div>
      </div>

      <div class="row justify-content-center mt-4">
        <div class="col-md-6 text-center">
          <img
            src="fxd2.jpg"
            alt="Logo Bibliotech"
            class="img-fluid img-harmonizada"
          >
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
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
</body>
</html>
