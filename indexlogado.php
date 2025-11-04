<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

require_once 'conexao.php';

// Total de clientes
$sqlClientes = "SELECT COUNT(*) as total FROM clientes WHERE removidoEm IS NULL";
$resClientes = mysqli_query($conexao, $sqlClientes);
$qtdClientes = ($resClientes && $row = mysqli_fetch_assoc($resClientes)) ? $row['total'] : 0;

// Clientes com empréstimos ativos
$sqlClientesAtivos = "SELECT COUNT(ID) AS total FROM clientes WHERE ID IN (SELECT idCliente FROM emprestimo WHERE ativo = 1 group by idCliente) and removidoEm IS NULL";
$resClientesAtivos = mysqli_query($conexao, $sqlClientesAtivos);
$qtdClientesAtivos = ($resClientesAtivos && $row = mysqli_fetch_assoc($resClientesAtivos)) ? $row['total'] : 0;

// Clientes sem empréstimos ativos
$sqlClientesSemAtivos = "SELECT COUNT(ID) AS total FROM clientes WHERE ID NOT IN (SELECT idCliente FROM emprestimo WHERE ativo = 1 group by idCliente) and removidoEm IS NULL";
$resClientesSemAtivos = mysqli_query($conexao, $sqlClientesSemAtivos);
$qtdClientesSemAtivos = ($resClientesSemAtivos && $row = mysqli_fetch_assoc($resClientesSemAtivos)) ? $row['total'] : 0;


// Total de livros
$sqlLivros = "SELECT COUNT(*) as total FROM livros WHERE removidoEm IS NULL";
$resLivros = mysqli_query($conexao, $sqlLivros);
$totalLivros = ($resLivros && $row = mysqli_fetch_assoc($resLivros)) ? $row['total'] : 0;

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
$sqlemprestimo = "SELECT COUNT(*) AS totalEmprestimos from emprestimo where ativo = '1'";
$res2 = mysqli_query($conexao, $sqlemprestimo);
$row2 = mysqli_fetch_assoc($res2);
$totalEmp = $row2['totalEmprestimos'] ?? 0;

// Empréstimos à vencer (até 5 dias)
$sqlVencer = "SELECT COUNT(*) as total FROM emprestimo WHERE ativo = '1' AND vencimento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 5 DAY)";
$resVencer = mysqli_query($conexao, $sqlVencer);
$qtdVencer = ($resVencer && $row = mysqli_fetch_assoc($resVencer)) ? $row['total'] : 0;

// Empréstimos atrasados
$sqlvencido = "SELECT COUNT(*) AS totalVencido from emprestimo where vencimento < curdate() AND ativo = '1'";
$res3 = mysqli_query($conexao, $sqlvencido);
$row3 = mysqli_fetch_assoc($res3);
$totalVenc = $row3['totalVencido'] ?? 0;

// Empréstimos finalizados
$sqlFinalizados = "SELECT COUNT(*) as total FROM emprestimo WHERE ativo = '0' OR devolvidoEm IS NOT NULL";
$resFinalizados = mysqli_query($conexao, $sqlFinalizados);
$qtdFinalizados = ($resFinalizados && $row = mysqli_fetch_assoc($resFinalizados)) ? $row['total'] : 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - Bibliotech</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <?php include 'components/sidebar-style.php'; ?>
  
  <style>
    :root {
      --card: #ffffff;
      --muted: #6b6f73;
    }

    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      margin: 0;
      overflow-x: hidden;
      font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .sidebar.hidden ~ .content {
      margin-left: 0;
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
      opacity: 0.1;
      z-index: 0;
      pointer-events: none;
      filter: saturate(0.9);
      transition: left 0.3s ease, width 0.3s ease;
    }

    .sidebar.hidden ~ .content::before {
      left: 0;
      width: 100%;
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

    .content {
      position: relative;
      margin-left: 250px;
      padding: 2rem;
      transition: margin-left 0.3s;
      min-height: 100vh;
      background: transparent;
    }

    .content > * {
      position: relative;
      z-index: 1;
    }

    .container-fluid {
      position: relative;
      z-index: 1;
    }

    /* Header Welcome Card */
    .welcome-card {
      border: none;
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
      background: linear-gradient(135deg, #1a202c 0%, #2d3748 50%, #4a5568 100%);
      overflow: hidden;
      position: relative;
      margin-bottom: 2rem;
    }

    .welcome-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0) 100%);
      pointer-events: none;
    }

    .welcome-card h3 {
      color: white;
      margin-bottom: 0.5rem;
      font-weight: 700;
    }

    .welcome-card p {
      color: rgba(255, 255, 255, 0.85);
    }

    .welcome-card i {
      color: #3498db;
      filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
    }

    .card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      position: relative;
      z-index: 1;
      overflow: hidden;
      backdrop-filter: blur(10px);
    }

    .card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
      pointer-events: none;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .card:hover::before {
      opacity: 1;
    }

    .card:hover {
      transform: translateY(-10px) scale(1.02);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    }

    .card-body {
      position: relative;
      z-index: 1;
    }

    /* Gradients for dashboard cards */
    .card.bg-primary {
      background: linear-gradient(135deg,rgb(91, 206, 118) 0%, #1e7e34 100%) !important;
    }

    .card.bg-success {
      background: linear-gradient(135deg,rgb(91, 206, 118) 0%, #1e7e34 100%) !important;
    }

    .card.bg-danger {
      background: linear-gradient(135deg,rgb(233, 98, 111) 0%, #c82333 100%) !important;
    }

    .card.bg-warning {
      background: linear-gradient(135deg, #daba5dff 0%, #FFC107 100%) !important;
    }

    .card.bg-info {
      background: linear-gradient(135deg, #56a4f8ff 0%, #17a2b8 100%) !important;
    }

    .card.bg-secondary {
      background: linear-gradient(135deg, #6c757d 0%, #545b62 100%) !important;
    }

    .card.bg-dark {
      background: linear-gradient(135deg, #343a40 0%, #23272b 100%) !important;
    }

    .card.bg-purple {
      background: linear-gradient(135deg, #56a4f8ff 0%, #17a2b8 100%) !important;
    }

    .card.bg-light {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    }

    .card-title {
      font-weight: 600;
      font-size: 1.1rem;
      letter-spacing: 0.3px;
    }

    .card-text {
      font-weight: 700;
      font-size: 2.5rem;
      margin-top: 0.5rem;
      text-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .card i {
      filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
      transition: transform 0.3s ease;
    }

    .card:hover i {
      transform: scale(1.1) rotate(5deg);
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(15px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .fade-in-up {
      animation: fadeInUp 0.6s ease-in-out both;
    }

    .card {
      animation: fadeIn 0.6s ease-in-out both;
    }

    /* Desabilitar animação CSS quando controlada via JavaScript */
    .card[style*="animation"] {
      animation: none !important;
    }

    @media (max-width: 768px) {
      .content::before {
        left: 0;
        width: 100%;
      }
    }

    .col-md-4:nth-child(1) .card {
      animation-delay: 0.1s;
    }

    .col-md-4:nth-child(2) .card {
      animation-delay: 0.2s;
    }

    .col-md-4:nth-child(3) .card {
      animation-delay: 0.3s;
    }

    .col-md-4:nth-child(4) .card {
      animation-delay: 0.4s;
    }

    .col-md-4:nth-child(5) .card {
      animation-delay: 0.5s;
    }

    .col-md-4:nth-child(6) .card {
      animation-delay: 0.6s;
    }

    .col-md-4:nth-child(7) .card {
      animation-delay: 0.7s;
    }

    .col-md-4:nth-child(8) .card {
      animation-delay: 0.8s;
    }

    .col-md-4:nth-child(9) .card {
      animation-delay: 0.9s;
    }

    @media (max-width: 768px) {
      .content {
        margin-left: 0 !important;
        padding: 1rem;
      }
      .card-text {
        font-size: 2rem;
      }
    }

    /* Botões de ocultar */
    .hide-card-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      background: rgba(255, 255, 255, 0.2);
      border: none;
      border-radius: 50%;
      width: 32px;
      height: 32px;
      color: white;
      font-size: 14px;
      cursor: pointer;
      z-index: 10;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
      opacity: 0.7;
    }

    .hide-card-btn:hover {
      opacity: 1;
      background: rgba(255, 255, 255, 0.3);
      transform: scale(1.1);
    }

    .section-header {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 15px;
      flex-wrap: wrap;
    }

    .section-header h3 {
      margin-bottom: 0 !important;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }

    .hide-section-btn {
      background: transparent;
      border: 2px solid #2c3e50;
      border-radius: 20px;
      color: #2c3e50;
      padding: 0.25rem 0.75rem;
      font-size: 0.85rem;
      cursor: pointer;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
      height: fit-content;
      line-height: 1;
      animation: fadeInUp 0.6s ease-in-out both;
    }

    .hide-section-btn:hover {
      background: #2c3e50;
      color: white;
      transform: translateY(-2px);
    }

    /* Delays de animação para cada botão correspondente ao título */
    #section-clientes .hide-section-btn {
      animation-delay: 0.1s;
    }

    #section-livros .hide-section-btn {
      animation-delay: 0.2s;
    }

    #section-emprestimos .hide-section-btn {
      animation-delay: 0.3s;
    }

    #section-urgencias .hide-section-btn {
      animation-delay: 0.4s;
    }

    .card.hidden {
      display: none !important;
    }

    .section-container {
      transition: opacity 0.3s ease;
    }

    /* Animação de fade out para cards sendo ocultos */
    @keyframes fadeOut {
      from {
        opacity: 1;
        transform: translateY(0);
      }
      to {
        opacity: 0;
        transform: translateY(-20px);
      }
    }

    /* Ocultar apenas os cards quando a seção está oculta */
    .section-container.section-hidden .row.text-center.mb-4 {
      display: none !important;
    }


  </style>
</head>
<body>

<?php include 'components/sidebar-logado.php'; ?>

  <div class="content">
    <div class="container-fluid">

      <!-- Welcome section -->
      <div class="row">
        <div class="col-12 mb-3">
          <div class="card welcome-card">
            <div class="card-body text-center p-4">
              <h3 class="mb-2">
                <i class="fas fa-user-circle"></i> Bem-vindo, <?= htmlspecialchars($_SESSION['nome'] ?? 'Usuário'); ?>!
              </h3>
              <p class="mb-0">
                Gerencie sua biblioteca de forma eficiente e moderna.
              </p>
              <button class="btn btn-sm btn-light mt-3" onclick="restoreAllCards()" style="border-radius: 20px; padding: 0.4rem 1rem;">
                <i class="fas fa-redo mr-2"></i> Restaurar Cards Ocultos
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Clientes -->
      <div class="section-container" id="section-clientes">
        <div class="col-12 mb-3">
          <div class="section-header">
            <h3 class="fade-in-up" style="color: #2c3e50; font-weight: 600; animation-delay: 0.1s; margin: 0;">
              <i class="fas fa-users"></i> Clientes
            </h3>
            <button class="hide-section-btn" onclick="toggleSection('clientes')" title="Ocultar/Mostrar seção">
              <i class="fas fa-eye-slash"></i>
            </button>
          </div>
        </div>
        <div class="row text-center mb-4">
          <div class="col-md-4 mb-4">
            <div class="card bg-info text-white" id="card-clientes-total" style="cursor: pointer; position: relative;">
              <button class="hide-card-btn" onclick="event.stopPropagation(); toggleCard('clientes-total')" title="Ocultar card">
                <i class="fas fa-minus"></i>
              </button>
              <div class="card-body" onclick="window.location.href='listaCliente.php'">
                <i class="fas fa-users fa-2x mb-3"></i>
                <h5 class="card-title">Total de Clientes</h5>
                <p class="card-text display-4"><?= $qtdClientes; ?></p>
              </div>
            </div>
          </div>

          <div class="col-md-4 mb-4">
            <div class="card bg-primary text-white" id="card-clientes-ativos" style="cursor: pointer; position: relative;">
              <button class="hide-card-btn" onclick="event.stopPropagation(); toggleCard('clientes-ativos')" title="Ocultar card">
                <i class="fas fa-minus"></i>
              </button>
              <div class="card-body" onclick="window.location.href='clientesComEmprestimo.php'">
                <i class="fas fa-user-check fa-2x mb-3"></i>
                <h5 class="card-title">Clientes com Empréstimos</h5>
                <p class="card-text display-4"><?= $qtdClientesAtivos; ?></p>
              </div>
            </div>
          </div>

          <div class="col-md-4 mb-4">
            <div class="card bg-secondary text-white" id="card-clientes-sem" style="cursor: pointer; position: relative;">
              <button class="hide-card-btn" onclick="event.stopPropagation(); toggleCard('clientes-sem')" title="Ocultar card">
                <i class="fas fa-minus"></i>
              </button>
              <div class="card-body" onclick="window.location.href='clientesSemEmprestimo.php'">
                <i class="fas fa-chart-line fa-2x mb-3"></i>
                <h5 class="card-title">Clientes Sem Empréstimos</h5>
                <p class="card-text display-4"><?= $qtdClientesSemAtivos; ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Livros -->
      <div class="section-container" id="section-livros">
        <div class="col-12 mb-3 mt-4">
          <div class="section-header">
            <h3 class="fade-in-up" style="color: #2c3e50; font-weight: 600; animation-delay: 0.2s; margin: 0;">
              <i class="fas fa-book"></i> Livros
            </h3>
            <button class="hide-section-btn" onclick="toggleSection('livros')" title="Ocultar/Mostrar seção">
              <i class="fas fa-eye-slash"></i>
            </button>
          </div>
        </div>
        <div class="row text-center mb-4">
          <div class="col-md-4 mb-4">
            <div class="card bg-purple text-white" id="card-livros-total" style="cursor: pointer; position: relative;">
              <button class="hide-card-btn" onclick="event.stopPropagation(); toggleCard('livros-total')" title="Ocultar card">
                <i class="fas fa-minus"></i>
              </button>
              <div class="card-body" onclick="window.location.href='acervo.php'">
                <i class="fas fa-book fa-2x mb-3"></i>
                <h5 class="card-title">Total de Livros</h5>
                <p class="card-text display-4"><?= $totalLivros; ?></p>
              </div>
            </div>
          </div>

          <div class="col-md-4 mb-4">
            <div class="card bg-success text-white" id="card-livros-disponiveis" style="cursor: pointer; position: relative;">
              <button class="hide-card-btn" onclick="event.stopPropagation(); toggleCard('livros-disponiveis')" title="Ocultar card">
                <i class="fas fa-minus"></i>
              </button>
              <div class="card-body" onclick="window.location.href='livrosDisponiveis.php'">
                <i class="fas fa-book-open fa-2x mb-3"></i>
                <h5 class="card-title">Livros Disponíveis</h5>
                <p class="card-text display-4"><?= $qtdLivrosDisponiveis; ?></p>
              </div>
            </div>
          </div>

          <div class="col-md-4 mb-4">
            <div class="card bg-secondary text-white" id="card-livros-emprestados" style="cursor: pointer; position: relative;">
              <button class="hide-card-btn" onclick="event.stopPropagation(); toggleCard('livros-emprestados')" title="Ocultar card">
                <i class="fas fa-minus"></i>
              </button>
              <div class="card-body" onclick="window.location.href='livrosIndisponiveis.php'">
                <i class="fas fa-hand-holding fa-2x mb-3"></i>
                <h5 class="card-title">Livros Emprestados</h5>
                <p class="card-text display-4"><?= $qtdLivrosEmprestados; ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empréstimos -->
      <div class="section-container" id="section-emprestimos">
        <div class="col-12 mb-3 mt-4">
          <div class="section-header">
            <h3 class="fade-in-up" style="color: #2c3e50; font-weight: 600; animation-delay: 0.3s; margin: 0;">
              <i class="fas fa-exchange-alt"></i> Empréstimos
            </h3>
            <button class="hide-section-btn" onclick="toggleSection('emprestimos')" title="Ocultar/Mostrar seção">
              <i class="fas fa-eye-slash"></i>
            </button>
          </div>
        </div>
        <div class="row text-center mb-4">
          <div class="col-md-4 mb-4">
            <div class="card bg-dark text-white" id="card-emprestimos-todos" style="cursor: pointer; position: relative;">
              <button class="hide-card-btn" onclick="event.stopPropagation(); toggleCard('emprestimos-todos')" title="Ocultar card">
                <i class="fas fa-minus"></i>
              </button>
              <div class="card-body" onclick="window.location.href='todosEmprestimos.php'">
                <i class="fas fa-list fa-2x mb-3"></i>
                <h5 class="card-title">Todos Empréstimos</h5>
                <p class="card-text display-4"><?= $qtdTodos; ?></p>
              </div>
            </div>
          </div>

          <div class="col-md-4 mb-4">
            <div class="card bg-success text-white" id="card-emprestimos-ativos" style="cursor: pointer; position: relative;">
              <button class="hide-card-btn" onclick="event.stopPropagation(); toggleCard('emprestimos-ativos')" title="Ocultar card">
                <i class="fas fa-minus"></i>
              </button>
              <div class="card-body" onclick="window.location.href='listaEmprestimoAtivo.php'">
                <i class="fas fa-book-reader fa-2x mb-3"></i>
                <h5 class="card-title">Empréstimos Ativos</h5>
                <p class="card-text display-4"><?= $totalEmp; ?></p>
              </div>
            </div>
          </div>

          <div class="col-md-4 mb-4">
            <div class="card bg-info text-white" id="card-emprestimos-finalizados" style="cursor: pointer; position: relative;">
              <button class="hide-card-btn" onclick="event.stopPropagation(); toggleCard('emprestimos-finalizados')" title="Ocultar card">
                <i class="fas fa-minus"></i>
              </button>
              <div class="card-body" onclick="window.location.href='emprestimosFinalizados.php'">
                <i class="fas fa-check-double fa-2x mb-3"></i>
                <h5 class="card-title">Finalizados</h5>
                <p class="card-text display-4"><?= $qtdFinalizados; ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empréstimos - Urgências -->
      <div class="section-container" id="section-urgencias">
        <div class="col-12 mb-3 mt-4">
          <div class="section-header">
            <h3 class="fade-in-up" style="color: #2c3e50; font-weight: 600; animation-delay: 0.4s; margin: 0;">
              <i class="fas fa-exclamation-circle"></i> Urgências
            </h3>
            <button class="hide-section-btn" onclick="toggleSection('urgencias')" title="Ocultar/Mostrar seção">
              <i class="fas fa-eye-slash"></i>
            </button>
          </div>
        </div>
        <div class="row text-center mb-4">
          <div class="col-md-4 mb-4">
            <div class="card bg-warning text-white" id="card-urgencias-vencer" style="cursor: pointer; position: relative;">
              <button class="hide-card-btn" onclick="event.stopPropagation(); toggleCard('urgencias-vencer')" title="Ocultar card">
                <i class="fas fa-minus"></i>
              </button>
              <div class="card-body" onclick="window.location.href='emprestimoVence.php'">
                <i class="fas fa-clock fa-2x mb-3"></i>
                <h5 class="card-title">À Vencer</h5>
                <p class="card-text display-4"><?= $qtdVencer; ?></p>
              </div>
            </div>
          </div>

          <div class="col-md-4 mb-4">
            <div class="card bg-danger text-white" id="card-urgencias-atrasados" style="cursor: pointer; position: relative;">
              <button class="hide-card-btn" onclick="event.stopPropagation(); toggleCard('urgencias-atrasados')" title="Ocultar card">
                <i class="fas fa-minus"></i>
              </button>
              <div class="card-body" onclick="window.location.href='emprestimoVencido.php'">
                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                <h5 class="card-title">Atrasados</h5>
                <p class="card-text display-4"><?= $totalVenc; ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<!-- Scripts -->
<?php include 'components/sidebar-script.php'; ?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
  // Função para ocultar/mostrar cards individuais
  function toggleCard(cardId) {
    const card = document.getElementById('card-' + cardId);
    const cardParent = card.closest('.col-md-4');
    
    if (card.classList.contains('hidden')) {
      // Mostrar card com animação
      card.classList.remove('hidden');
      cardParent.style.display = '';
      card.style.opacity = '0';
      card.style.transform = 'translateY(20px)';
      // Forçar repaint
      void card.offsetWidth;
      // Aplicar animação de entrada
      card.style.animation = 'fadeInUp 0.6s ease-in-out forwards';
      // Após a animação, limpar estilos inline
      setTimeout(() => {
        card.style.opacity = '';
        card.style.transform = '';
        card.style.animation = '';
      }, 600);
      localStorage.setItem('card-' + cardId, 'visible');
    } else {
      // Ocultar card com animação de saída
      card.style.animation = 'fadeOut 0.4s ease-out forwards';
      // Após a animação, ocultar completamente
      setTimeout(() => {
        card.classList.add('hidden');
        cardParent.style.display = 'none';
        card.style.animation = '';
        card.style.opacity = '';
        card.style.transform = '';
      }, 400);
      localStorage.setItem('card-' + cardId, 'hidden');
    }
  }

  // Mapeamento de seções para seus cards
  const sectionCardsMap = {
    'clientes': ['clientes-total', 'clientes-ativos', 'clientes-sem'],
    'livros': ['livros-total', 'livros-disponiveis', 'livros-emprestados'],
    'emprestimos': ['emprestimos-todos', 'emprestimos-ativos', 'emprestimos-finalizados'],
    'urgencias': ['urgencias-vencer', 'urgencias-atrasados']
  };

  // Função auxiliar para restaurar um card individualmente
  function restoreCard(cardId) {
    const card = document.getElementById('card-' + cardId);
    if (!card) return;
    
    const cardParent = card.closest('.col-md-4');
    if (card.classList.contains('hidden') && cardParent) {
      card.classList.remove('hidden');
      cardParent.style.display = '';
      card.style.opacity = '0';
      card.style.transform = 'translateY(20px)';
      void card.offsetWidth;
      card.style.animation = 'fadeInUp 0.6s ease-in-out forwards';
      setTimeout(() => {
        card.style.opacity = '';
        card.style.transform = '';
        card.style.animation = '';
      }, 600);
      localStorage.setItem('card-' + cardId, 'visible');
    }
  }

  // Função para ocultar/mostrar seções inteiras (apenas os cards, mantendo título e botão)
  function toggleSection(sectionId) {
    const section = document.getElementById('section-' + sectionId);
    const btn = document.querySelector('#section-' + sectionId + ' .hide-section-btn');
    const icon = btn.querySelector('i');
    const cardsRow = section.querySelector('.row.text-center.mb-4');
    
    if (section.classList.contains('section-hidden')) {
      // Mostrar cards IMEDIATAMENTE - remover a classe primeiro
      section.classList.remove('section-hidden');
      
      if (cardsRow) {
        // Tornar visível IMEDIATAMENTE sem esperar
        cardsRow.style.display = 'flex';
        cardsRow.style.opacity = '1';
      }
      
      // Restaurar todos os cards individuais desta seção que foram ocultos
      if (sectionCardsMap[sectionId]) {
        sectionCardsMap[sectionId].forEach(cardId => {
          const cardState = localStorage.getItem('card-' + cardId);
          if (cardState === 'hidden') {
            const card = document.getElementById('card-' + cardId);
            const cardParent = card.closest('.col-md-4');
            if (card && cardParent) {
              card.classList.remove('hidden');
              cardParent.style.display = '';
            }
          }
        });
      }
      
      // Aplicar animação muito rápida de entrada
      if (cardsRow) {
        const allCards = cardsRow.querySelectorAll('.card');
        
        // Aplicar animação imediatamente em todos os cards simultaneamente
        allCards.forEach((card) => {
          // Limpar animações CSS anteriores
          card.style.animation = 'none';
          // Estado inicial para animação
          card.style.opacity = '0';
          card.style.transform = 'translateY(10px)';
          // Forçar repaint
          void card.offsetWidth;
          
          // Aplicar animação extremamente rápida (0.05s) sem delay
          card.style.animation = 'fadeInUp 0.05s ease-out forwards';
          setTimeout(() => {
            card.style.animation = '';
            card.style.opacity = '';
            card.style.transform = '';
          }, 50);
        });
        
        // Limpar estilos da row imediatamente
        cardsRow.style.opacity = '';
        cardsRow.style.transform = '';
        cardsRow.style.display = '';
      }
      
      // Atualizar localStorage dos cards restaurados
      if (sectionCardsMap[sectionId]) {
        sectionCardsMap[sectionId].forEach(cardId => {
          const cardState = localStorage.getItem('card-' + cardId);
          if (cardState === 'hidden') {
            localStorage.setItem('card-' + cardId, 'visible');
          }
        });
      }
      
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
      localStorage.setItem('section-' + sectionId, 'visible');
    } else {
      // Ocultar cards com animação
      if (cardsRow) {
        // Adicionar animação de fade out
        cardsRow.style.animation = 'fadeOut 0.4s ease-out forwards';
        // Após a animação, ocultar completamente
        setTimeout(() => {
          section.classList.add('section-hidden');
          cardsRow.style.display = 'none';
          cardsRow.style.animation = '';
          cardsRow.style.opacity = '';
          cardsRow.style.transform = '';
        }, 400);
      } else {
        section.classList.add('section-hidden');
      }
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
      localStorage.setItem('section-' + sectionId, 'hidden');
    }
  }

  // Carregar preferências salvas ao carregar a página
  document.addEventListener('DOMContentLoaded', function() {
    // Restaurar estado dos cards
    const cardIds = [
      'clientes-total', 'clientes-ativos', 'clientes-sem',
      'livros-total', 'livros-disponiveis', 'livros-emprestados',
      'emprestimos-todos', 'emprestimos-ativos', 'emprestimos-finalizados',
      'urgencias-vencer', 'urgencias-atrasados'
    ];
    
    cardIds.forEach(cardId => {
      const state = localStorage.getItem('card-' + cardId);
      if (state === 'hidden') {
        const card = document.getElementById('card-' + cardId);
        const cardParent = card.closest('.col-md-4');
        if (card && cardParent) {
          card.classList.add('hidden');
          cardParent.style.display = 'none';
        }
      }
    });

    // Restaurar estado das seções
    const sectionIds = ['clientes', 'livros', 'emprestimos', 'urgencias'];
    
    sectionIds.forEach(sectionId => {
      const state = localStorage.getItem('section-' + sectionId);
      if (state === 'hidden') {
        const section = document.getElementById('section-' + sectionId);
        const btn = section.querySelector('.hide-section-btn');
        const icon = btn.querySelector('i');
        const cardsRow = section.querySelector('.row.text-center.mb-4');
        
        if (section && btn && icon) {
          section.classList.add('section-hidden');
          if (cardsRow) {
            cardsRow.style.display = 'none';
          }
          icon.classList.remove('fa-eye-slash');
          icon.classList.add('fa-eye');
        }
      }
    });
  });

  // Função para restaurar todos os cards e seções ocultos
  function restoreAllCards() {
    // Restaurar todos os cards
    const cardIds = [
      'clientes-total', 'clientes-ativos', 'clientes-sem',
      'livros-total', 'livros-disponiveis', 'livros-emprestados',
      'emprestimos-todos', 'emprestimos-ativos', 'emprestimos-finalizados',
      'urgencias-vencer', 'urgencias-atrasados'
    ];
    
    cardIds.forEach(cardId => {
      const card = document.getElementById('card-' + cardId);
      const cardParent = card ? card.closest('.col-md-4') : null;
      if (card && cardParent) {
        card.classList.remove('hidden');
        cardParent.style.display = '';
        localStorage.setItem('card-' + cardId, 'visible');
      }
    });

    // Restaurar todas as seções
    const sectionIds = ['clientes', 'livros', 'emprestimos', 'urgencias'];
    
    sectionIds.forEach(sectionId => {
      const section = document.getElementById('section-' + sectionId);
      const btn = section ? section.querySelector('.hide-section-btn') : null;
      const icon = btn ? btn.querySelector('i') : null;
      const cardsRow = section ? section.querySelector('.row.text-center.mb-4') : null;
      
      if (section && btn && icon) {
        section.classList.remove('section-hidden');
        if (cardsRow) {
          cardsRow.style.display = '';
        }
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
        localStorage.setItem('section-' + sectionId, 'visible');
      }
    });
  }
</script>

</body>
</html>
