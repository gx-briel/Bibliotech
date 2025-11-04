<?php
session_start(); 
if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit; 
}

require_once 'conexao.php';

// Consultas para os cards
// Total de clientes
$sqlClientes = "SELECT COUNT(*) as total FROM clientes WHERE removidoEm IS NULL";
$resClientes = mysqli_query($conexao, $sqlClientes);
$qtdClientes = ($resClientes && $row = mysqli_fetch_assoc($resClientes)) ? $row['total'] : 0;

// Clientes com empréstimos ativos
$sqlClientesAtivos = "SELECT COUNT(ID) AS total FROM clientes WHERE ID IN (SELECT idCliente FROM emprestimo WHERE ativo = 1 group by idCliente) and removidoEm IS NULL";
$resClientesAtivos = mysqli_query($conexao, $sqlClientesAtivos);
$qtdClientesAtivos = ($resClientesAtivos && $row = mysqli_fetch_assoc($resClientesAtivos)) ? $row['total'] : 0;

// Total de livros
$sqlLivros = "SELECT COUNT(*) as total FROM livros WHERE removidoEm IS NULL";
$resLivros = mysqli_query($conexao, $sqlLivros);
$qtdLivros = ($resLivros && $row = mysqli_fetch_assoc($resLivros)) ? $row['total'] : 0;

// Livros disponíveis
$sqlLivrosDisponiveis = "SELECT COUNT(*) as total FROM livros WHERE disponivel = 1 AND removidoEm IS NULL";
$resLivrosDisponiveis = mysqli_query($conexao, $sqlLivrosDisponiveis);
$qtdLivrosDisponiveis = ($resLivrosDisponiveis && $row = mysqli_fetch_assoc($resLivrosDisponiveis)) ? $row['total'] : 0;

// Livros emprestados
$sqlLivrosEmprestados = "SELECT COUNT(*) as total FROM livros WHERE disponivel = 0 AND removidoEm IS NULL";
$resLivrosEmprestados = mysqli_query($conexao, $sqlLivrosEmprestados);
$qtdLivrosEmprestados = ($resLivrosEmprestados && $row = mysqli_fetch_assoc($resLivrosEmprestados)) ? $row['total'] : 0;

// Total de empréstimos
$sqlTodos = "SELECT COUNT(*) as total FROM emprestimo";
$resTodos = mysqli_query($conexao, $sqlTodos);
$qtdTodos = ($resTodos && $row = mysqli_fetch_assoc($resTodos)) ? $row['total'] : 0;

// Empréstimos ativos
$sqlAtivos = "SELECT COUNT(*) as total FROM emprestimo WHERE ativo = '1'";
$resAtivos = mysqli_query($conexao, $sqlAtivos);
$qtdAtivos = ($resAtivos && $row = mysqli_fetch_assoc($resAtivos)) ? $row['total'] : 0;

// Empréstimos à vencer (até 5 dias)
$sqlVencer = "SELECT COUNT(*) as total FROM emprestimo WHERE ativo = '1' AND vencimento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 5 DAY)";
$resVencer = mysqli_query($conexao, $sqlVencer);
$qtdVencer = ($resVencer && $row = mysqli_fetch_assoc($resVencer)) ? $row['total'] : 0;

// Empréstimos atrasados
$sqlAtrasados = "SELECT COUNT(*) as total FROM emprestimo WHERE ativo = '1' AND vencimento < CURDATE()";
$resAtrasados = mysqli_query($conexao, $sqlAtrasados);
$qtdAtrasados = ($resAtrasados && $row = mysqli_fetch_assoc($resAtrasados)) ? $row['total'] : 0;

// Empréstimos finalizados
$sqlFinalizados = "SELECT COUNT(*) as total FROM emprestimo WHERE ativo = '0' OR devolvidoEm IS NOT NULL";
$resFinalizados = mysqli_query($conexao, $sqlFinalizados);
$qtdFinalizados = ($resFinalizados && $row = mysqli_fetch_assoc($resFinalizados)) ? $row['total'] : 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Relatórios</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <?php include 'components/sidebar-style.php'; ?>
  <style>
    @keyframes fadeInUpSmooth {
      from { opacity: 0; transform: translateY(20px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .fade-in-up {
      opacity: 0;
      animation: fadeInUpSmooth 0.6s ease-in-out both;
      will-change: opacity, transform;
    }

    body {
      margin: 0;
      overflow-x: hidden;
      font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(to right, #ece9e6, #ffffff);
      min-height: 100vh;
    }

    .content {
      margin-left: 250px;
      padding: 2rem;
      transition: margin-left 0.3s;
      position: relative;
      min-height: 100vh;
    }

    .content::before {
      content: '';
      position: fixed;
      top: 0;
      left: 250px;
      right: 0;
      bottom: 0;
      width: calc(100% - 250px);
      height: 100vh;
      background-image: url('bg1.jpg');
      background-position: center;
      background-repeat: no-repeat;
      background-size: cover;
      opacity: 0.15;
      z-index: 0;
      pointer-events: none;
      filter: saturate(0.9);
      transition: left 0.3s ease, width 0.3s ease;
    }

    .sidebar.hidden ~ .content::before {
      left: 0;
      width: 100%;
    }

    .content > * {
      position: relative;
      z-index: 1;
    }

    .page-header {
      text-align: center;
      margin-bottom: 2rem;
      padding: 3rem 2rem;
      background: linear-gradient(135deg, #062c2a 0%, #08470b 100%);
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(8, 71, 11, 0.25);
      backdrop-filter: blur(10px);
      position: relative;
      overflow: hidden;
    }

    .page-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 100%);
      opacity: 0.5;
    }

    .page-header::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    }

    .page-header h1 {
      font-size: 2.5rem;
      font-weight: 700;
      color: #ffffff;
      margin: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 1rem;
      position: relative;
      z-index: 1;
      text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .page-header h1 i {
      font-size: 2.5rem;
      opacity: 0.95;
    }

    @media (max-width: 768px) {
      .page-header {
        padding: 2rem 1.5rem;
        border-radius: 16px;
      }
      .page-header h1 {
        font-size: 1.75rem;
        flex-direction: column;
        gap: 0.75rem;
      }
      .page-header h1 i {
        font-size: 2rem;
      }
    }

    .card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      margin-bottom: 1.5rem;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      height: 100%;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .dashboard-card-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: #fff;
      text-align: center;
      margin-bottom: 1rem;
    }

    .dashboard-card-value {
      font-size: 2.5rem;
      font-weight: 700;
      color: #fff;
      text-align: center;
    }

    .dashboard-card-desc {
      font-size: 0.9rem;
      color: #fff;
        text-align: center;
      margin-top: 0.5rem;
      }

      .card-body {
      padding: 1.5rem;
      }

    .icon-wrapper {
      display: flex;
      justify-content: center;
      margin-bottom: 1rem;
    }

    .icon-wrapper i {
      font-size: 3rem;
      color: rgba(255, 255, 255, 0.9);
    }

    .bg-danger {
      background: linear-gradient(135deg, #e7737eff 0%, #c82333 100%) !important;
    }

    .bg-warning {
      background: linear-gradient(135deg, #daba5dff 0%, #FFC107 100%) !important;
    }

    .bg-success {
      background: linear-gradient(135deg, #72ca87ff 0%, #1e7e34 100%) !important;
    }

    .bg-primary {
      background: linear-gradient(135deg, #56a4f8ff 0%, #0056b3 100%) !important;
    }

    .bg-info {
      background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
    }

    .bg-secondary {
      background: linear-gradient(135deg, #6c757d 0%, #545b62 100%) !important;
    }

    .bg-dark {
      background: linear-gradient(135deg, #343a40 0%, #23272b 100%) !important;
    }

    .bg-purple {
      background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%) !important;
    }

    @media (max-width: 768px) {
      .content {
        margin-left: 0 !important;
        padding: 1rem;
      }
      .content::before {
        left: 0;
        width: 100%;
      }
      .dashboard-card-value {
        font-size: 2rem;
      }
    }

    .sidebar.hidden ~ .content {
      margin-left: 0;
    }

    /* Centralizar linhas com 2 cards */
    .row-2-cards {
      display: flex;
      justify-content: center;
    }

    @media (min-width: 768px) {
      .row-2-cards > .col-md-6 {
        max-width: 500px;
      }
    }
  </style>
</head>
<body>

<?php include 'components/sidebar-logado.php'; ?>

<div class="content">
  <div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header fade-in-up">
      <h1>
        <i class="fas fa-chart-line"></i>
        Dashboard & Relatórios
      </h1>
    </div>

    <div class="row">
      <!-- Clientes -->
      <div class="col-12 mb-3">
        <h3 class="text-center mb-3 fade-in-up" style="color: #062c2a; font-weight: 600;">
          <i class="fas fa-users"></i> Clientes
        </h3>
      </div>

      <div class="row row-2-cards w-100 mx-0">
        <div class="col-12 col-md-6 col-lg-6 mb-3 fade-in-up" style="animation-delay: 0.1s;">
          <div class="card text-white bg-info">
            <a href="listaCliente.php" style="text-decoration:none;display:block;width:100%;">
              <div class="card-body d-flex flex-column justify-content-center align-items-center">
                <div class="icon-wrapper">
                  <i class="fas fa-users"></i>
                </div>
                <h5 class="dashboard-card-title">Total de Clientes</h5>
                <span class="dashboard-card-value"><?= $qtdClientes ?></span>
                <small class="dashboard-card-desc">Clientes cadastrados</small>
              </div>
            </a>
          </div>
        </div>

        <div class="col-12 col-md-6 col-lg-6 mb-3 fade-in-up" style="animation-delay: 0.2s;">
          <div class="card text-white bg-primary">
            <a href="clientesComEmprestimo.php" style="text-decoration:none;display:block;width:100%;">
              <div class="card-body d-flex flex-column justify-content-center align-items-center">
                <div class="icon-wrapper">
                  <i class="fas fa-user-check"></i>
                </div>
                <h5 class="dashboard-card-title">Clientes com Empréstimos</h5>
                <span class="dashboard-card-value"><?= $qtdClientesAtivos ?></span>
                <small class="dashboard-card-desc">Com empréstimos ativos</small>
              </div>
            </a>
          </div>
        </div>
      </div>

      <!-- Livros -->
      <div class="col-12 mb-3 mt-4">
        <h3 class="text-center mb-3 fade-in-up" style="color: #062c2a; font-weight: 600;">
          <i class="fas fa-book"></i> Livros
        </h3>
      </div>

      <div class="col-12 col-md-6 col-lg-4 mb-3 fade-in-up" style="animation-delay: 0.3s;">
        <div class="card text-white bg-purple">
          <a href="acervo.php" style="text-decoration:none;display:block;width:100%;">
            <div class="card-body d-flex flex-column justify-content-center align-items-center">
              <div class="icon-wrapper">
                <i class="fas fa-book"></i>
              </div>
              <h5 class="dashboard-card-title">Total de Livros</h5>
              <span class="dashboard-card-value"><?= $qtdLivros ?></span>
              <small class="dashboard-card-desc">Livros no acervo</small>
            </div>
          </a>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4 mb-3 fade-in-up" style="animation-delay: 0.4s;">
        <div class="card text-white bg-success">
          <a href="livrosDisponiveis.php" style="text-decoration:none;display:block;width:100%;">
            <div class="card-body d-flex flex-column justify-content-center align-items-center">
              <div class="icon-wrapper">
                <i class="fas fa-book-open"></i>
              </div>
              <h5 class="dashboard-card-title">Livros Disponíveis</h5>
              <span class="dashboard-card-value"><?= $qtdLivrosDisponiveis ?></span>
              <small class="dashboard-card-desc">Prontos para empréstimo</small>
            </div>
          </a>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4 mb-3 fade-in-up" style="animation-delay: 0.5s;">
        <div class="card text-white bg-secondary">
          <a href="livrosIndisponiveis.php" style="text-decoration:none;display:block;width:100%;">
            <div class="card-body d-flex flex-column justify-content-center align-items-center">
              <div class="icon-wrapper">
                <i class="fas fa-hand-holding"></i>
              </div>
              <h5 class="dashboard-card-title">Livros Emprestados</h5>
              <span class="dashboard-card-value"><?= $qtdLivrosEmprestados ?></span>
              <small class="dashboard-card-desc">Atualmente emprestados</small>
            </div>
          </a>
        </div>
      </div>

      <!-- Empréstimos -->
      <div class="col-12 mb-3 mt-4">
        <h3 class="text-center mb-3 fade-in-up" style="color: #062c2a; font-weight: 600;">
          <i class="fas fa-exchange-alt"></i> Empréstimos
        </h3>
      </div>

      <div class="col-12 col-md-6 col-lg-4 mb-3 fade-in-up" style="animation-delay: 0.6s;">
        <div class="card text-white bg-dark">
          <a href="todosEmprestimos.php" style="text-decoration:none;display:block;width:100%;">
            <div class="card-body d-flex flex-column justify-content-center align-items-center">
              <div class="icon-wrapper">
                <i class="fas fa-list"></i>
              </div>
              <h5 class="dashboard-card-title">Todos Empréstimos</h5>
              <span class="dashboard-card-value"><?= $qtdTodos ?></span>
              <small class="dashboard-card-desc">Total de registros</small>
            </div>
          </a>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4 mb-3 fade-in-up" style="animation-delay: 0.7s;">
        <div class="card text-white bg-success">
          <a href="listaEmprestimoAtivo.php" style="text-decoration:none;display:block;width:100%;">
            <div class="card-body d-flex flex-column justify-content-center align-items-center">
              <div class="icon-wrapper">
                <i class="fas fa-check-circle"></i>
              </div>
              <h5 class="dashboard-card-title">Empréstimos Ativos</h5>
              <span class="dashboard-card-value"><?= $qtdAtivos ?></span>
              <small class="dashboard-card-desc">Atualmente em aberto</small>
            </div>
          </a>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4 mb-3 fade-in-up" style="animation-delay: 0.8s;">
        <div class="card text-white bg-warning">
          <a href="emprestimoVence.php" style="text-decoration:none;display:block;width:100%;">
            <div class="card-body d-flex flex-column justify-content-center align-items-center">
              <div class="icon-wrapper">
                <i class="fas fa-clock"></i>
              </div>
              <h5 class="dashboard-card-title">À Vencer</h5>
              <span class="dashboard-card-value"><?= $qtdVencer ?></span>
              <small class="dashboard-card-desc">Vencem em até 5 dias</small>
            </div>
          </a>
        </div>
      </div>

      <div class="row row-2-cards w-100 mx-0">
        <div class="col-12 col-md-6 col-lg-6 mb-3 fade-in-up" style="animation-delay: 0.9s;">
        <div class="card text-white bg-danger">
          <a href="emprestimoVencido.php" style="text-decoration:none;display:block;width:100%;">
              <div class="card-body d-flex flex-column justify-content-center align-items-center">
                <div class="icon-wrapper">
                  <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h5 class="dashboard-card-title">Atrasados</h5>
                <span class="dashboard-card-value"><?= $qtdAtrasados ?></span>
              <small class="dashboard-card-desc">Já passaram do prazo</small>
            </div>
          </a>
        </div>
      </div>

        <div class="col-12 col-md-6 col-lg-6 mb-3 fade-in-up" style="animation-delay: 1.0s;">
          <div class="card text-white bg-info">
            <a href="emprestimosFinalizados.php" style="text-decoration:none;display:block;width:100%;">
              <div class="card-body d-flex flex-column justify-content-center align-items-center">
                <div class="icon-wrapper">
                  <i class="fas fa-check-double"></i>
                </div>
                <h5 class="dashboard-card-title">Finalizados</h5>
                <span class="dashboard-card-value"><?= $qtdFinalizados ?></span>
                <small class="dashboard-card-desc">Livros devolvidos</small>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'components/sidebar-script.php'; ?>

</body>
</html>