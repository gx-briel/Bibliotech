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

// Ordenação dinâmica
$orderColumns = [
    'empId' => 'emp.id',
    'nomeCliente' => 'cli.nomeCliente',
    'titulo' => 'li.titulo',
    'criadoEm' => 'emp.criadoEm',
    'vencimento' => 'emp.vencimento',
    'ativo' => 'emp.ativo'
];

// Ordenação padrão: id asc
$order = isset($_GET['order']) && isset($orderColumns[$_GET['order']]) ? $_GET['order'] : 'empId';
$dir = isset($_GET['dir']) && in_array(strtolower($_GET['dir']), ['asc', 'desc']) ? strtolower($_GET['dir']) : 'asc';


// Paginação
$defaultLimit = 50;
$mobileLimit = 15;

// Detectar se é mobile
$isMobile = isset($_SERVER['HTTP_USER_AGENT']) &&
            preg_match('/(android|iphone|ipad|mobile)/i', $_SERVER['HTTP_USER_AGENT']);

// Limite baseado no dispositivo
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit'] > 0 ? (int)$_GET['limit'] : ($isMobile ? $mobileLimit : $defaultLimit);
$offset = 0; // Sempre começa do início para paginação acumulativa

$consulta = "SELECT emp.id as empId, cli.nomeCliente as nomeCliente, li.titulo, emp.criadoEm, li.ID as livroId, emp.ativo, emp.vencimento
             FROM emprestimo as emp 
             JOIN clientes as cli on emp.idCliente = cli.id 
             JOIN livros as li on emp.idLivro = li.ID
             WHERE cli.nomeCliente LIKE '%$buscaCliente%' AND li.titulo LIKE '%$buscaLivro%' and ativo = 0";
if ($order) {
    $consulta .= " ORDER BY {$orderColumns[$order]} $dir";
}
$consulta .= " LIMIT $limit OFFSET $offset";

// Exportação para Excel (xlsx) deve ser processada antes de qualquer saída HTML
if (isset($_POST['export_excel'])) {
    // Monta a consulta SEM LIMIT/OFFSET para exportar todos os itens filtrados
    $exportConsulta = "SELECT emp.id as empId, cli.nomeCliente as nomeCliente, li.titulo, emp.criadoEm, li.ID as livroId, emp.ativo, emp.vencimento
             FROM emprestimo as emp 
             JOIN clientes as cli on emp.idCliente = cli.id 
             JOIN livros as li on emp.idLivro = li.ID
             WHERE cli.nomeCliente LIKE '%$buscaCliente%' AND li.titulo LIKE '%$buscaLivro%' and ativo = 0";
    if ($order) {
        $exportConsulta .= " ORDER BY {$orderColumns[$order]} $dir";
    }
    $exportResult = mysqli_query($conexao, $exportConsulta);
    if ($exportResult) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="emprestimos.csv"');
        $output = fopen('php://output', 'w');
        // Adiciona BOM UTF-8 para Excel reconhecer acentuação
        fwrite($output, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($output, ['ID', 'Cliente', 'Livro', 'Data de Criação', 'Data para Devolução', 'Ativo'], ';');
        while ($row = mysqli_fetch_assoc($exportResult)) {
            fputcsv($output, [
                $row['empId'],
                $row['nomeCliente'],
                $row['titulo'],
                date('d/m/Y', strtotime($row['criadoEm'])),
                date('d/m/Y', strtotime($row['vencimento'])),
                $row['ativo'] == 0 ? 'Não' : 'Sim'
            ], ';');
        }
        fclose($output);
        exit;
    }
}

$executaConsulta = mysqli_query($conexao, $consulta);
$numRows = mysqli_num_rows($executaConsulta);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Listagem de Empréstimos</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <?php include 'components/sidebar-style.php'; ?>
  
  <style>
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

    body {
      background: linear-gradient(to right, #ece9e6, #ffffff);
      margin: 0;
      overflow-x: hidden;
      padding-bottom: 0;
      font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .page-header-card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
      overflow: hidden;
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(10px);
      margin-bottom: 2rem;
      animation: fadeInUp 0.4s ease-out;
    }

    .page-header-card .card-header {
      background: linear-gradient(135deg, #062c2a 0%, #08470b 100%);
      color: white;
      padding: 1.5rem 2rem;
      border: none;
      position: relative;
    }

    .page-header-card .card-header::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    }

    .page-header-card .card-header h2 {
      margin: 0;
      font-size: 1.75rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .page-header-card .card-header h2 i {
      font-size: 1.5rem;
    }

    .page-header-card .card-body {
      padding: 1.5rem 2rem;
    }

    .search-form .input-group {
      display: flex;
      width: 100%;
      gap: 0.5rem;
      align-items: stretch;
    }

    .search-form .form-control {
      border: 2px solid #e0e0e0;
      border-radius: 12px !important;
      padding: 0.75rem 1rem;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      flex: 1;
      height: auto;
    }

    .search-form .input-group .form-control {
      border-radius: 12px !important;
    }

    .search-form .input-group .form-control:first-child {
      border-radius: 12px !important;
    }

    .search-form .form-control:focus {
      border-color: #08470b;
      box-shadow: 0 0 0 0.2rem rgba(8, 71, 11, 0.15);
      outline: none;
    }

    .search-form .btn-info {
      background: linear-gradient(135deg, #08470b 0%, #062c2a 100%);
      border: 2px solid #08470b;
      border-radius: 12px;
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(8, 71, 11, 0.3);
      color: white;
      height: auto;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .search-form .btn-info:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(8, 71, 11, 0.4);
      background: linear-gradient(135deg, #062c2a 0%, #08470b 100%);
      border-color: #062c2a;
    }

    .search-form .btn-info:focus {
      border-color: #08470b;
      outline: none;
      box-shadow: 0 0 0 0.2rem rgba(8, 71, 11, 0.25);
    }

    .table-wrapper {
      border: none;
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
      overflow: hidden;
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(10px);
      animation: fadeInUp 0.4s ease-out 0.15s both;
    }

    .table-container {
      position: relative;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      z-index: 1;
    }

    .table-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      width: 100%;
      height: 100%;
      background-image: url('bg1.jpg');
      background-position: center;
      background-repeat: no-repeat;
      background-size: cover;
      opacity: 0.05;
      z-index: 0;
      pointer-events: none;
      filter: saturate(0.9) blur(0px);
    }

    .table-container table {
      position: relative;
      z-index: 1;
      background-color: rgba(255, 255, 255, 0.95);
      margin-bottom: 0;
    }

    table {
      width: 100%;
    }

    .table-striped tbody tr:nth-of-type(odd) {
      background-color: rgba(6, 44, 42, 0.03);
    }

    .table-striped tbody tr:hover {
      background-color: rgba(8, 71, 11, 0.08);
      transition: background-color 0.2s ease;
    }

    .table th, .table td {
      border: 1px solid #ccc !important;
      vertical-align: middle;
      padding: 12px 15px;
    }

    .table thead th {
      background: linear-gradient(135deg, #062c2a 0%, #08470b 100%);
      color: white;
      font-weight: 600;
      border: none !important;
      padding: 1rem 15px;
    }

    .table tbody td {
      border-color: #e0e0e0 !important;
      padding: 1rem 15px;
    }

    .btn-success {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      border: none;
      border-radius: 8px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
      color: white;
    }

    .btn-success:hover {
      background: linear-gradient(135deg, #20c997 0%, #28a745 100%);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
      color: white;
    }

    .pagination-buttons {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 1.5rem;
      padding: 1rem;
      background: rgba(255, 255, 255, 0.7);
      border-radius: 10px;
    }

    .btn-show-more, .btn-show-less {
      background: linear-gradient(135deg, #08470b 0%, #062c2a 100%);
      border: none;
      border-radius: 10px;
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      color: white;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(8, 71, 11, 0.3);
    }

    .btn-show-more:hover, .btn-show-less:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(8, 71, 11, 0.4);
      background: linear-gradient(135deg, #062c2a 0%, #08470b 100%);
      color: white;
    }

    .content {
      margin-left: 250px;
      padding: 2rem;
      padding-bottom: 4rem;
      transition: margin-left 0.3s;
      position: relative;
      min-height: 100vh;
      background: transparent;
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
      filter: saturate(0.9) blur(0px);
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

    .sidebar.hidden ~ .content {
      margin-left: 0;
    }

    .table thead th a {
      color: white;
      text-decoration: none;
    }

    .table thead th a:hover {
      color: rgba(255, 255, 255, 0.8);
      text-decoration: underline;
    }

    /* Visualização em cards para mobile */
    .mobile-card-view {
      display: none;
    }

    .mobile-card {
      background: white;
      border-radius: 12px;
      padding: 1rem;
      margin-bottom: 1rem;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      border-left: 4px solid #08470b;
    }

    .mobile-card-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      padding: 0.5rem 0;
      border-bottom: 1px solid #e0e0e0;
    }

    .mobile-card-row:last-child {
      border-bottom: none;
    }

    .mobile-card-label {
      font-weight: 600;
      color: #062c2a;
      font-size: 0.85rem;
      margin-right: 0.5rem;
      min-width: 120px;
    }

    .mobile-card-value {
      color: #333;
      font-size: 0.9rem;
      text-align: right;
      flex: 1;
      word-break: break-word;
    }

    .mobile-card-header {
      font-weight: 700;
      font-size: 1rem;
      color: #08470b;
      margin-bottom: 0.5rem;
      padding-bottom: 0.5rem;
      border-bottom: 2px solid #08470b;
    }

    /* Botão flutuante voltar ao topo */
    .back-to-top {
      position: fixed;
      bottom: 2rem;
      right: 2rem;
      width: 50px;
      height: 50px;
      background: linear-gradient(135deg, #08470b 0%, #062c2a 100%);
      color: white;
      border: none;
      border-radius: 50%;
      font-size: 1.2rem;
      cursor: pointer;
      box-shadow: 0 4px 15px rgba(8, 71, 11, 0.4);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 1000;
      transition: all 0.3s ease;
      opacity: 0;
    }

    .back-to-top.show {
      display: flex;
      opacity: 1;
    }

    .back-to-top:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 20px rgba(8, 71, 11, 0.6);
      background: linear-gradient(135deg, #062c2a 0%, #08470b 100%);
    }

    .back-to-top:active {
      transform: translateY(-2px);
    }

    @media (max-width: 768px) {
      .content::before {
        left: 0;
        width: 100%;
      }
      .content {
        margin-left: 0 !important;
        padding: 1rem;
      }
      
      /* Ocultar tabela no mobile */
      .table-wrapper {
        display: none;
      }
      
      /* Mostrar visualização em cards */
      .mobile-card-view {
        display: block;
      }
      
      .page-header-card .card-header {
        padding: 1rem 1.5rem;
      }
      
      .page-header-card .card-header h2 {
        font-size: 1.4rem;
      }
      
      .page-header-card .card-body {
        padding: 1rem 1.5rem;
      }
      
      .search-form .input-group {
        flex-direction: column;
        gap: 0.75rem;
      }
      
      .search-form .form-control {
        border-radius: 12px !important;
        width: 100%;
      }
      
      .search-form .btn-info {
        border-radius: 12px !important;
        width: 100%;
      }
      
      .pagination-buttons {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
        flex-wrap: wrap;
      }
      
      .pagination-buttons > div {
        width: 100%;
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
      }
      
      .pagination-buttons .btn {
        width: 100%;
        margin: 0;
      }
      
      .mobile-card {
        padding: 0.75rem;
      }
      
      .mobile-card-label {
        font-size: 0.8rem;
        min-width: 100px;
      }
      
      .mobile-card-value {
        font-size: 0.85rem;
      }
      
      .back-to-top {
        bottom: 1rem;
        right: 1rem;
        width: 45px;
        height: 45px;
        font-size: 1rem;
      }
    }
    
    /* Scroll horizontal melhorado para desktop */
    @media (min-width: 769px) {
      .table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #08470b rgba(0,0,0,0.1);
      }
      
      .table-container::-webkit-scrollbar {
        height: 8px;
      }
      
      .table-container::-webkit-scrollbar-track {
        background: rgba(0,0,0,0.1);
        border-radius: 4px;
      }
      
      .table-container::-webkit-scrollbar-thumb {
        background: #08470b;
        border-radius: 4px;
      }
      
      .table-container::-webkit-scrollbar-thumb:hover {
        background: #062c2a;
      }
    }

    @media (max-width: 576px) {
      .btn {
        width: 100%;
      }
    }
  </style>
</head>
<body>

<?php include 'components/sidebar-logado.php'; ?>

  <!-- Conteúdo principal -->
  <div class="content">
    <div class="container mt-5">
      <!-- Header Card -->
      <div class="page-header-card">
        <div class="card-header">
          <h2>
            <i class="fas fa-list"></i>
            Listagem de Empréstimos
          </h2>
        </div>
        <div class="card-body">
          <form method="GET" action="" class="search-form">
            <div class="input-group">
              <input type="text" class="form-control" name="cliente" placeholder="Buscar por Cliente..." value="<?= htmlspecialchars($buscaCliente) ?>">
              <input type="text" class="form-control" name="livro" placeholder="Buscar por Livro..." value="<?= htmlspecialchars($buscaLivro) ?>">
              <button type="submit" class="btn btn-info">
                <i class="fas fa-search mr-2"></i>Buscar
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Table Card -->
      <div class="table-wrapper">
        <div class="table-container table-responsive">
          <table class="table table-striped mb-0">
            <thead>
              <tr>
                <?php
                  // Função para gerar links de ordenação
                  function sortLink($label, $col, $order, $dir) {
                    $nextDir = 'asc';
                    $icon = '';
                    if ($order === $col) {
                      if ($dir === 'asc') {
                        $nextDir = 'desc';
                        $icon = ' ▲';
                      } elseif ($dir === 'desc') {
                        $nextDir = '';
                        $icon = ' ▼';
                      }
                    }
                    $params = $_GET;
                    if ($order === $col && $dir === 'desc') {
                      unset($params['order'], $params['dir']);
                      $url = '?' . http_build_query($params);
                    } else {
                      $params['order'] = $col;
                      $params['dir'] = $nextDir;
                      $url = '?' . http_build_query($params);
                    }
                    return "<a href='$url'>$label$icon</a>";
                  }
                ?>
                <th><?= sortLink('ID', 'empId', $order, $dir) ?></th>
                <th><?= sortLink('Cliente', 'nomeCliente', $order, $dir) ?></th>
                <th><?= sortLink('Livro', 'titulo', $order, $dir) ?></th>
                <th><?= sortLink('Data de Criação', 'criadoEm', $order, $dir) ?></th>
                <th><?= sortLink('Data para Devolução', 'vencimento', $order, $dir) ?></th>
                <th><?= sortLink('Ativo', 'ativo', $order, $dir) ?></th>
              </tr>
            </thead>
            <tbody>
              <?php
              if ($numRows > 0) {
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
                  echo "<tr><td colspan='6' class='text-center py-4'><i class='fas fa-inbox mr-2'></i>Nenhum Empréstimo encontrado.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>

        <!-- Pagination Buttons -->
        <div class="pagination-buttons">
          <div>
            <form method="post" action="" style="margin-bottom:0;">
              <button type="submit" name="export_excel" class="btn btn-success">
                <i class="fas fa-file-excel mr-2"></i>Exportar CSV
              </button>
            </form>
          </div>
          <div>
            <?php
            $increment = $isMobile ? $mobileLimit : $defaultLimit;
            $minLimit = $isMobile ? $mobileLimit : $defaultLimit;
            
            if ($numRows === $limit) {
              $params = $_GET;
              $params['limit'] = $limit + $increment;
              $url = '?' . http_build_query($params) . '#tabela';
              echo '<a href="' . $url . '" class="btn btn-show-more mr-2" id="mostrarMais50">
                      <i class="fas fa-chevron-down mr-2"></i>
                      Mostrar mais ' . $increment . '
                    </a>';
            }
            if ($limit > $minLimit) {
              $params = $_GET;
              $params['limit'] = $minLimit;
              $url = '?' . http_build_query($params) . '#tabela';
              echo '<a href="' . $url . '" class="btn btn-show-less" id="mostrarMenos">
                      <i class="fas fa-chevron-up mr-2"></i>
                      Mostrar menos
                    </a>';
            }
            ?>
          </div>
        </div>
      </div>

      <!-- Mobile Card View -->
      <div class="mobile-card-view">
        <?php
        if ($numRows > 0) {
          mysqli_data_seek($executaConsulta, 0); // Reset pointer
          while ($emprestimos = mysqli_fetch_assoc($executaConsulta)) {
        ?>
          <div class="mobile-card">
            <div class="mobile-card-header">
              <i class="fas fa-check-circle mr-2"></i>Empréstimo #<?= $emprestimos['empId']; ?>
            </div>
            <div class="mobile-card-row">
              <span class="mobile-card-label"><i class="fas fa-user mr-1"></i>Cliente:</span>
              <span class="mobile-card-value"><?= htmlspecialchars($emprestimos['nomeCliente']); ?></span>
            </div>
            <div class="mobile-card-row">
              <span class="mobile-card-label"><i class="fas fa-book mr-1"></i>Livro:</span>
              <span class="mobile-card-value"><?= htmlspecialchars($emprestimos['titulo']); ?></span>
            </div>
            <div class="mobile-card-row">
              <span class="mobile-card-label"><i class="fas fa-calendar-plus mr-1"></i>Criação:</span>
              <span class="mobile-card-value"><?= date('d/m/Y', strtotime($emprestimos['criadoEm'])); ?></span>
            </div>
            <div class="mobile-card-row">
              <span class="mobile-card-label"><i class="fas fa-calendar-check mr-1"></i>Vencimento:</span>
              <span class="mobile-card-value"><?= date('d/m/Y', strtotime($emprestimos['vencimento'])); ?></span>
            </div>
            <div class="mobile-card-row">
              <span class="mobile-card-label"><i class="fas fa-info-circle mr-1"></i>Ativo:</span>
              <span class="mobile-card-value">
                <?php if ($emprestimos['ativo'] == 0): ?>
                  <span style="color: #6c757d; font-weight: 600;"><i class="fas fa-times mr-1"></i>Não</span>
                <?php else: ?>
                  <span style="color: #28a745; font-weight: 600;"><i class="fas fa-check mr-1"></i>Sim</span>
                <?php endif; ?>
              </span>
            </div>
          </div>
        <?php
          }
        } else {
        ?>
          <div class="mobile-card">
            <div class="text-center py-4">
              <i class="fas fa-inbox fa-3x mb-3" style="color: #ccc;"></i>
              <p style="color: #666; margin: 0;">Nenhum Empréstimo encontrado.</p>
            </div>
          </div>
        <?php
        }
        ?>
        
        <!-- Pagination Buttons (Mobile) -->
        <div class="pagination-buttons">
          <div>
            <form method="post" action="" style="margin-bottom:0;">
              <button type="submit" name="export_excel" class="btn btn-success">
                <i class="fas fa-file-excel mr-2"></i>Exportar CSV
              </button>
            </form>
          </div>
          <div>
            <?php
            $increment = $isMobile ? $mobileLimit : $defaultLimit;
            $minLimit = $isMobile ? $mobileLimit : $defaultLimit;
            
            if ($numRows === $limit) {
              $params = $_GET;
              $params['limit'] = $limit + $increment;
              $url = '?' . http_build_query($params) . '#tabela';
              echo '<a href="' . $url . '" class="btn btn-show-more" id="mostrarMais50Mobile">
                      <i class="fas fa-chevron-down mr-2"></i>
                      Mostrar mais ' . $increment . '
                    </a>';
            }
            if ($limit > $minLimit) {
              $params = $_GET;
              $params['limit'] = $minLimit;
              $url = '?' . http_build_query($params) . '#tabela';
              echo '<a href="' . $url . '" class="btn btn-show-less" id="mostrarMenosMobile">
                      <i class="fas fa-chevron-up mr-2"></i>
                      Mostrar menos
                    </a>';
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Botão flutuante voltar ao topo -->
  <button class="back-to-top" id="backToTop" aria-label="Voltar ao topo">
    <i class="fas fa-arrow-up"></i>
  </button>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  $(document).ready(function() {
    $(document).on('click', '#mostrarMais50, #mostrarMenos, #mostrarMais50Mobile, #mostrarMenosMobile', function() {
      sessionStorage.setItem('scrollPosEmpTodos', window.scrollY);
      sessionStorage.setItem('shouldRestoreScroll', 'true');
    });
    
    if (sessionStorage.getItem('shouldRestoreScroll') === 'true') {
      var pos = parseInt(sessionStorage.getItem('scrollPosEmpTodos'), 10);
      if (pos > 0) {
        setTimeout(function() {
          window.scrollTo({top: pos, behavior: 'auto'});
          sessionStorage.removeItem('scrollPosEmpTodos');
          sessionStorage.removeItem('shouldRestoreScroll');
        }, 100);
      }
    } else {
      sessionStorage.removeItem('scrollPosEmpTodos');
      sessionStorage.removeItem('shouldRestoreScroll');
    }

    // Botão voltar ao topo
    var backToTopBtn = $('#backToTop');

    $(window).scroll(function() {
      if ($(window).scrollTop() > 300) {
        backToTopBtn.addClass('show');
      } else {
        backToTopBtn.removeClass('show');
      }
    });

    backToTopBtn.on('click', function() {
      $('html, body').animate({
        scrollTop: 0
      }, 600);
    });
  });
</script>

<?php include 'components/sidebar-script.php'; ?>

</body>
</html>
