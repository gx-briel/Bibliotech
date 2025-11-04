<?php
require 'conexao.php';
session_start(); 
if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit; 
}

// Filtros
$clienteBusca = isset($_GET['cliente']) ? trim($_GET['cliente']) : '';
$livroBusca = isset($_GET['livro']) ? trim($_GET['livro']) : '';

// Ordenação dinâmica
$orderColumns = [
    'empId' => 'emp.id',
    'nomeCliente' => 'cli.nomeCliente',
    'titulo' => 'li.titulo',
    'criadoEm' => 'emp.criadoEm',
    'vencimento' => 'emp.vencimento',
    'ativo' => 'emp.ativo'
];
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
$offset = 0;

$consulta = "SELECT emp.id as empId, cli.nomeCliente, li.titulo, emp.criadoEm, li.ID as livroId, emp.ativo, emp.vencimento
            FROM emprestimo as emp
            JOIN clientes as cli ON emp.idCliente = cli.id
            JOIN livros as li ON emp.idLivro = li.ID
            WHERE emp.vencimento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 5 DAY) AND emp.ativo = 1";
if (!empty($clienteBusca)) {
    $consulta .= " AND cli.nomeCliente LIKE '%" . mysqli_real_escape_string($conexao, $clienteBusca) . "%'";
}
if (!empty($livroBusca)) {
    $consulta .= " AND li.titulo LIKE '%" . mysqli_real_escape_string($conexao, $livroBusca) . "%'";
}
if ($order) {
    $consulta .= " ORDER BY {$orderColumns[$order]} $dir";
}
$consulta .= " LIMIT $limit OFFSET $offset";

// Exportação para CSV
if (isset($_POST['export_excel'])) {
    $exportConsulta = "SELECT emp.id as empId, cli.nomeCliente, li.titulo, emp.criadoEm, li.ID as livroId, emp.ativo, emp.vencimento
            FROM emprestimo as emp
            JOIN clientes as cli ON emp.idCliente = cli.id
            JOIN livros as li ON emp.idLivro = li.ID
            WHERE emp.vencimento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 5 DAY) AND emp.ativo = 1";
    if (!empty($clienteBusca)) {
        $exportConsulta .= " AND cli.nomeCliente LIKE '%" . mysqli_real_escape_string($conexao, $clienteBusca) . "%'";
    }
    if (!empty($livroBusca)) {
        $exportConsulta .= " AND li.titulo LIKE '%" . mysqli_real_escape_string($conexao, $livroBusca) . "%'";
    }
    if ($order) {
        $exportConsulta .= " ORDER BY {$orderColumns[$order]} $dir";
    }
    $exportResult = mysqli_query($conexao, $exportConsulta);
    if ($exportResult) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="emprestimos_a_vencer.csv"');
        $output = fopen('php://output', 'w');
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
  <title>Empréstimos à Vencer</title>
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

    .search-form .input-group .form-control:last-child {
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
      white-space: nowrap;
    }

    .table tbody td {
      border-color: #e0e0e0 !important;
      padding: 1rem 15px;
    }

    .table th:nth-child(1), .table td:nth-child(1) { width: 5%; }
    .table th:nth-child(2), .table td:nth-child(2) { width: 18%; }
    .table th:nth-child(3), .table td:nth-child(3) { width: 18%; }
    .table th:nth-child(4), .table td:nth-child(4) { width: 14%; }
    .table th:nth-child(5), .table td:nth-child(5) { width: 16%; }
    .table th:nth-child(6), .table td:nth-child(6) { width: 10%; }
    .table th:nth-child(7), .table td:nth-child(7) { width: 9%; }
    .table th:nth-child(8), .table td:nth-child(8) { width: 10%; }

    .btn-danger {
      background-color: #dc3545;
      border-color: #dc3545;
      border-radius: 8px;
      font-weight: 600;
      transition: all 0.3s ease;
      color: white;
    }

    .btn-danger:hover {
      background-color: #c82333;
      border-color: #bd2130;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
      color: white;
    }

    .btn-warning {
      background: linear-gradient(135deg, #ffcc00 0%, #e0a800 100%);
      border: none;
      border-radius: 8px;
      font-weight: 600;
      color: #062c2a !important;
      transition: all 0.3s ease;
      box-shadow: 0 2px 8px rgba(255, 204, 0, 0.3);
    }

    .btn-warning:hover {
      background: linear-gradient(135deg, #e0a800 0%, #ffcc00 100%);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(255, 204, 0, 0.4);
      color: #062c2a !important;
    }

    .btn-sm {
      padding: 0.5rem 1rem;
      font-size: 0.875rem;
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

    .mobile-card-actions {
      display: flex;
      gap: 0.5rem;
      margin-top: 0.75rem;
      padding-top: 0.75rem;
      border-top: 1px solid #e0e0e0;
    }

    .mobile-card-actions .btn {
      flex: 1;
      font-size: 0.85rem;
      padding: 0.5rem;
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

    /* Estilos dos Modais */
    .modal-backdrop {
      z-index: 1050 !important;
      background-color: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(4px);
    }

    .modal-backdrop.fade {
      opacity: 0;
    }

    .modal-backdrop.show {
      opacity: 0.6;
    }

    .modal {
      z-index: 1060 !important;
    }

    .modal-content {
      border-radius: 16px;
      border: none;
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
      padding: 0;
    }

    .modal-content .modal-header {
      background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
      color: white;
      border: none;
      border-radius: 16px 16px 0 0;
      padding: 1.5rem;
      text-align: center;
    }

    .modal-content .modal-header .close {
      color: white;
      opacity: 0.9;
      text-shadow: none;
      font-size: 1.5rem;
    }

    .modal-content .modal-header .close:hover {
      opacity: 1;
      color: white;
    }

    .modal-content .modal-header .modal-title {
      font-weight: 700;
      font-size: 1.5rem;
      margin: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }

    .modal-content .modal-body {
      padding: 2rem;
      text-align: center;
    }

    .modal-content .modal-body .modal-icon {
      font-size: 4rem;
      color: #dc3545;
      margin-bottom: 1rem;
    }

    .modal-content .modal-body p {
      font-size: 1.1rem;
      margin-bottom: 0;
    }

    .modal-content .modal-footer {
      padding: 1.5rem;
      border-top: 1px solid #e9ecef;
      display: flex;
      justify-content: center;
      gap: 1rem;
    }

    .modal-content .modal-footer .btn {
      border-radius: 8px;
      font-weight: 600;
      padding: 0.5rem 1.5rem;
    }

    /* Modal de sucesso com background verde */
    .modal-content.success-modal .modal-header {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    }

    .modal-content.success-modal .modal-body .modal-icon {
      color: #28a745 !important;
    }

    /* Modal de renovação com background laranja/amarelo */
    .modal-content.renovacao-modal .modal-header {
      background: linear-gradient(135deg, #ffcc00 0%, #e0a800 100%) !important;
    }

    .modal-content.renovacao-modal .modal-body .modal-icon {
      color: #ffcc00 !important;
    }

    #modalDevolucaoSucesso .modal-header {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
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
      .back-to-top {
        bottom: 1rem;
        right: 1rem;
        width: 45px;
        height: 45px;
        font-size: 1rem;
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
            <i class="fas fa-clock"></i>
            Empréstimos à Vencer em 5 Dias
          </h2>
        </div>
        <div class="card-body">
          <form method="GET" action="" class="search-form">
            <div class="input-group">
              <input type="text" class="form-control" name="cliente" placeholder="Buscar por Cliente..." value="<?= htmlspecialchars($clienteBusca) ?>">
              <input type="text" class="form-control" name="livro" placeholder="Buscar por Livro..." value="<?= htmlspecialchars($livroBusca) ?>">
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
                <th>Devolver</th>
                <th>Renovar</th>
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
                  <td><?= date("d/m/Y", strtotime($emprestimos['criadoEm'])); ?></td>
                  <td><?= date("d/m/Y", strtotime($emprestimos['vencimento'])); ?></td>
                  <td>
                    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalDevolucao" data-empid="<?= $emprestimos['empId']; ?>">
                      <i class="fas fa-undo mr-1"></i>Devolver
                    </button>
                  </td>
                  <td>
                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalRenovacao" data-empid="<?= $emprestimos['empId']; ?>">
                      <i class="fas fa-redo mr-1"></i>Renovar
                    </button>
                  </td>
                </tr>
              <?php
                  }
              } else {
                  echo "<tr><td colspan='8' class='text-center py-4'><i class='fas fa-inbox mr-2'></i>Nenhum Empréstimo à Vencer Encontrado.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>

        <!-- Pagination Buttons -->
        <div class="pagination-buttons">
          <div>
            <form method="post" action="" class="mb-0">
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
                      <i class="fas fa-chevron-down mr-2"></i>Mostrar mais ' . $increment . '
                    </a>';
            }
            if ($limit > $minLimit) {
              $params = $_GET;
              $params['limit'] = $minLimit;
              $url = '?' . http_build_query($params) . '#tabela';
              echo '<a href="' . $url . '" class="btn btn-show-less" id="mostrarMenos">
                      <i class="fas fa-chevron-up mr-2"></i>Mostrar menos
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
              <i class="fas fa-clock mr-2"></i>Empréstimo #<?= $emprestimos['empId']; ?>
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
            <div class="mobile-card-actions">
              <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalDevolucao" data-empid="<?= $emprestimos['empId']; ?>">
                <i class="fas fa-undo mr-1"></i>Devolver
              </button>
              <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalRenovacao" data-empid="<?= $emprestimos['empId']; ?>">
                <i class="fas fa-redo mr-1"></i>Renovar
              </button>
            </div>
          </div>
        <?php
          }
        } else {
        ?>
          <div class="mobile-card">
            <div class="text-center py-4">
              <i class="fas fa-inbox fa-3x mb-3" style="color: #ccc;"></i>
              <p style="color: #666; margin: 0;">Nenhum Empréstimo à Vencer Encontrado.</p>
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
                      <i class="fas fa-chevron-down mr-2"></i>Mostrar mais ' . $increment . '
                    </a>';
            }
            if ($limit > $minLimit) {
              $params = $_GET;
              $params['limit'] = $minLimit;
              $url = '?' . http_build_query($params) . '#tabela';
              echo '<a href="' . $url . '" class="btn btn-show-less" id="mostrarMenosMobile">
                      <i class="fas fa-chevron-up mr-2"></i>Mostrar menos
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

<?php include 'components/sidebar-script.php'; ?>

<!-- Modal de Devolução -->
<div class="modal fade" id="modalDevolucao" tabindex="-1" role="dialog" aria-labelledby="modalDevolucaoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDevolucaoLabel">
          <i class="fas fa-exclamation-triangle"></i>
          Confirmação de Devolução
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="formDevolucao" autocomplete="off">
        <div class="modal-body">
          <div class="modal-icon">
            <i class="fas fa-undo"></i>
          </div>
          <p>Tem certeza que deseja devolver o empréstimo?</p>
          <p style="color: #dc3545; font-weight: 600; margin-top: 1rem;">Esta ação registrará a devolução do livro.</p>
        </div>
        <div class="modal-footer">
          <input type="hidden" name="emprestimoId" id="emprestimoIdDevolucao">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times mr-2"></i>Cancelar
          </button>
          <button type="submit" class="btn btn-danger">
            <i class="fas fa-check mr-2"></i>Confirmar Devolução
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal de Sucesso da Devolução -->
<div class="modal fade" id="modalDevolucaoSucesso" tabindex="-1" role="dialog" aria-labelledby="modalDevolucaoSucessoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content success-modal">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDevolucaoSucessoLabel">
          <i class="fas fa-check-circle"></i>
          Devolução Realizada!
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="modal-icon">
          <i class="fas fa-check-circle"></i>
        </div>
        <h4 style="color: #28a745; margin-bottom: 1rem;">Devolução realizada com sucesso!</h4>
        <p id="msgDevolucaoSucesso" style="color: #666;">O livro foi devolvido e está disponível para novos empréstimos.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: none;" data-dismiss="modal" onclick="window.location.reload()">
          <i class="fas fa-check mr-2"></i>OK
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Renovação -->
<div class="modal fade" id="modalRenovacao" tabindex="-1" role="dialog" aria-labelledby="modalRenovacaoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content renovacao-modal">
      <div class="modal-header">
        <h5 class="modal-title" id="modalRenovacaoLabel">
          <i class="fas fa-clock"></i>
          Renovar Empréstimo
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="modal-icon">
          <i class="fas fa-redo"></i>
        </div>
        <p style="font-weight: 600; color: #062c2a; margin-bottom: 1.5rem;">Informe a nova data de devolução:</p>
        <form method="POST" action="renovaEmprestimo.php" id="formRenovacao">
          <input type="hidden" name="emprestimoId" id="emprestimoIdRenovacao">
          <input type="date" name="novaDataRenovacao" class="form-control mb-3" required id="novaDataRenovacao" style="border-radius: 10px; padding: 0.75rem; border: 2px solid #e0e0e0;">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fas fa-times mr-2"></i>Cancelar
        </button>
        <button type="submit" form="formRenovacao" class="btn" style="background: linear-gradient(135deg, #ffcc00 0%, #e0a800 100%); color: #062c2a; border: none;">
          <i class="fas fa-check mr-2"></i>Confirmar Renovação
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Sucesso da Renovação -->
<div class="modal fade" id="modalRenovacaoSucesso" tabindex="-1" role="dialog" aria-labelledby="modalRenovacaoSucessoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content success-modal">
      <div class="modal-header">
        <h5 class="modal-title" id="modalRenovacaoSucessoLabel">
          <i class="fas fa-check-circle"></i>
          Renovação Realizada!
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="modal-icon">
          <i class="fas fa-check-circle"></i>
        </div>
        <h4 style="color: #28a745; margin-bottom: 1rem;">Empréstimo renovado com sucesso!</h4>
        <p id="msgRenovacaoSucesso" style="color: #666;">O prazo de devolução foi atualizado.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: none;" data-dismiss="modal" onclick="window.location.reload()">
          <i class="fas fa-check mr-2"></i>OK
        </button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  $(document).ready(function() {
    // Scroll restoration
    $(document).on('click', '#mostrarMais50, #mostrarMenos, #mostrarMais50Mobile, #mostrarMenosMobile', function() {
      sessionStorage.setItem('scrollPosEmpVence', window.scrollY);
      sessionStorage.setItem('shouldRestoreScroll', 'true');
    });
    
    if (sessionStorage.getItem('shouldRestoreScroll') === 'true') {
      var pos = parseInt(sessionStorage.getItem('scrollPosEmpVence'), 10);
      if (pos > 0) {
        setTimeout(function() {
          window.scrollTo({top: pos, behavior: 'auto'});
          sessionStorage.removeItem('scrollPosEmpVence');
          sessionStorage.removeItem('shouldRestoreScroll');
        }, 100);
      }
    } else {
      sessionStorage.removeItem('scrollPosEmpVence');
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

    // Modal de Renovação
    $('#modalRenovacao').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var empId = button.data('empid');
      var modal = $(this);
      modal.find('#emprestimoIdRenovacao').val(empId);
      console.log('Abrindo modal de renovação para emprestimoId:', empId);
    });
    
    $('#modalRenovacao').on('hidden.bs.modal', function(event) {
      $(this).find('#emprestimoIdRenovacao').val('');
    });

    // Modal de Devolução
    $('#modalDevolucao').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var empId = button.data('empid');
      var modal = $(this);
      modal.find('#emprestimoIdDevolucao').val(empId);
      console.log('Abrindo modal de devolução para emprestimoId:', empId);
    });
    
    $('#modalDevolucao').on('hidden.bs.modal', function(event) {
      $(this).find('#emprestimoIdDevolucao').val('');
    });

    // Envio AJAX do formulário de devolução
    $(document).off('submit', '#formDevolucao');
    $(document).on('submit', '#formDevolucao', function(e) {
      e.preventDefault();
      var val = $('#emprestimoIdDevolucao').val();
      if (!val) return false;
      var $btn = $(this).find('button[type="submit"]');
      if ($btn.prop('disabled')) return false;
      $btn.prop('disabled', true);
      $.ajax({
        url: 'devolveLivro.php',
        type: 'POST',
        data: { emprestimoId: val },
        dataType: 'json',
        success: function(resp) {
          if (resp && resp.success) {
            $('#modalDevolucao').modal('hide');
            if ($('#modalDevolucaoSucesso').length === 0) {
              $('body').append(`
                <div class="modal fade" id="modalDevolucaoSucesso" tabindex="-1" role="dialog" aria-labelledby="modalDevolucaoSucessoLabel" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content success-modal">
                      <div class="modal-header">
                        <h5 class="modal-title" id="modalDevolucaoSucessoLabel">
                          <i class="fas fa-check-circle"></i>
                          Devolução Realizada!
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <div class="modal-icon">
                          <i class="fas fa-check-circle"></i>
                        </div>
                        <h4 style="color: #28a745; margin-bottom: 1rem;">Devolução realizada com sucesso!</h4>
                        <p id="msgDevolucaoSucesso"></p>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: none;" data-dismiss="modal" onclick="window.location.reload()">
                          <i class="fas fa-check mr-2"></i>OK
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              `);
            }
            $('#msgDevolucaoSucesso').text(resp.message || 'O livro foi devolvido com sucesso!');
            setTimeout(function() {
              $('#modalDevolucaoSucesso').modal('show');
            }, 400);
          } else {
            alert(resp && resp.message ? resp.message : 'Erro ao devolver livro.');
          }
        },
        error: function(xhr) {
          alert('Erro ao processar devolução.');
        },
        complete: function() {
          $btn.prop('disabled', false);
        }
      });
      return false;
    });

    // Envio AJAX do formulário de renovação
    $('#formRenovacao').on('submit', function(e) {
      e.preventDefault();
      var empId = $('#emprestimoIdRenovacao').val();
      var novaData = $('#novaDataRenovacao').val();
      
      if (!empId || !novaData) {
        alert('Por favor, preencha todos os campos.');
        return false;
      }
      
      var $btn = $(this).find('button[type="submit"]');
      if ($btn.prop('disabled')) return false;
      $btn.prop('disabled', true);
      
      $.ajax({
        url: 'renovaEmprestimo.php',
        type: 'POST',
        dataType: 'json',
        data: {
          emprestimoId: empId,
          novaDataRenovacao: novaData
        },
        success: function(response) {
          if (response && response.success !== undefined && response.success) {
            $('#modalRenovacao').modal('hide');
            $('#modalRenovacaoSucesso').modal('show');
          } else {
            alert(response && response.message ? response.message : 'Erro ao renovar empréstimo. Por favor, tente novamente.');
          }
        },
        error: function(xhr) {
          alert('Erro ao processar renovação. Por favor, tente novamente.');
        },
        complete: function() {
          $btn.prop('disabled', false);
        }
      });
      
      return false;
    });
  });
</script>

</body>
</html>
