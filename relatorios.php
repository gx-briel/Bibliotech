
<?php
session_start(); 
if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit; 
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Relatórios</title>
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
      background: linear-gradient(180deg, #1c0e3f 60%, #e8f5e9 100%);
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
  </style>
</head>
<body>
<div class="wrapper">
  <!-- Sidebar -->
  <nav id="sidebar" class="sidebar">
    <div class="sidebar-header"><a href="indexlogado.php" style="color: #fff; text-decoration: none;">Bibliotech</a></div>
    <button class="toggle-btn btn btn-sm btn-warning w-100 mb-2" onclick="hideSidebar()">← Recolher</button>
    <ul class="nav-links">
      <li><a href="todosEmprestimos.php">Todos Empréstimos</a></li>
      <li><a href="listaEmprestimoAtivo.php">Empréstimos Ativos</a></li>
      <li><a href="emprestimoVence.php">Empréstimos à Vencer</a></li>
      <li><a href="emprestimoVencido.php">Empréstimos Atrasados</a></li>
    </ul>
    <div class="logout-btn">
      <a href="logout.php" class="btn btn-danger w-100">🚪 Sair</a>
    </div>
  </nav>
  <button id="showSidebarBtn" class="show-sidebar-btn" style="left: 4px; top: 18px; right: auto; cursor: pointer;">☰</button>
  <!-- Conteúdo principal -->
  <div class="content">
    <div class="container">
      <div class="row">
        <?php
        require_once 'conexao.php';
        // Usa a variável $conexao definida em conexao.php
        // Total de empréstimos
        $sqlTodos = "SELECT COUNT(*) as total FROM emprestimo";
        $resTodos = mysqli_query($conexao, $sqlTodos);
        $qtdTodos = ($resTodos && $row = mysqli_fetch_assoc($resTodos)) ? $row['total'] : 0;

        // Empréstimos ativos
        $sqlAtivos = "SELECT COUNT(*) as total FROM emprestimo WHERE ativo = '1'";
        $resAtivos = mysqli_query($conexao, $sqlAtivos);
        $qtdAtivos = ($resAtivos && $row = mysqli_fetch_assoc($resAtivos)) ? $row['total'] : 0;

        // Empréstimos à vencer (exemplo: vencimento nos próximos 5 dias)
        $sqlVencer = "SELECT COUNT(*) as total FROM emprestimo WHERE ativo = '1' AND vencimento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 5 DAY)";
        $resVencer = mysqli_query($conexao, $sqlVencer);
        $qtdVencer = ($resVencer && $row = mysqli_fetch_assoc($resVencer)) ? $row['total'] : 0;

        // Empréstimos atrasados
        $sqlAtrasados = "SELECT COUNT(*) as total FROM emprestimo WHERE ativo = '1' AND vencimento < CURDATE()";
        $resAtrasados = mysqli_query($conexao, $sqlAtrasados);
        $qtdAtrasados = ($resAtrasados && $row = mysqli_fetch_assoc($resAtrasados)) ? $row['total'] : 0;
        ?>
        <div class="col-md-3 mb-4">
          <a href="todosEmprestimos.php" style="text-decoration:none;">
            <div class="card text-white bg-primary h-100">
              <div class="card-body d-flex flex-column justify-content-center align-items-center">
                <h5 class="card-title">Todos Empréstimos</h5>
                <span style="font-size:2.2rem;font-weight:bold;">
                  <?= $qtdTodos ?>
                </span>
                <small>Total de registros</small>
              </div>
            </div>
          </a>
        </div>
        <div class="col-md-3 mb-4">
          <a href="listaEmprestimoAtivo.php" style="text-decoration:none;">
            <div class="card text-white bg-success h-100">
              <div class="card-body d-flex flex-column justify-content-center align-items-center">
                <h5 class="card-title">Empréstimos Ativos</h5>
                <span style="font-size:2.2rem;font-weight:bold;">
                  <?= $qtdAtivos ?>
                </span>
                <small>Atualmente em aberto</small>
              </div>
            </div>
          </a>
        </div>
        <div class="col-md-3 mb-4">
          <a href="emprestimoVence.php" style="text-decoration:none;">
            <div class="card text-white bg-warning h-100">
              <div class="card-body d-flex flex-column justify-content-center align-items-center">
                <h5 class="card-title">À Vencer</h5>
                <span style="font-size:2.2rem;font-weight:bold;">
                  <?= $qtdVencer ?>
                </span>
                <small>Vencem em até 5 dias</small>
              </div>
            </div>
          </a>
        </div>
        <div class="col-md-3 mb-4">
          <a href="emprestimoVencido.php" style="text-decoration:none;">
            <div class="card text-white bg-danger h-100">
              <div class="card-body d-flex flex-column justify-content-center align-items-center">
                <h5 class="card-title">Atrasados</h5>
                <span style="font-size:2.2rem;font-weight:bold;">
                  <?= $qtdAtrasados ?>
                </span>
                <small>Já passaram do prazo</small>
              </div>
            </div>
          </a>
        </div>
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
