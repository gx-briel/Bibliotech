<?php
require 'conexao.php';
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

$pesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : (isset($_POST['pesquisa']) ? $_POST['pesquisa'] : '');

// Proteção contra SQL Injection
$pesquisa = mysqli_real_escape_string($conexao, $pesquisa);

// Paginação
$defaultLimit = 50;
$mobileLimit = 15;

// Detectar se é mobile
$isMobile = isset($_SERVER['HTTP_USER_AGENT']) &&
            preg_match('/(android|iphone|ipad|mobile)/i', $_SERVER['HTTP_USER_AGENT']);

// Limite baseado no dispositivo
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit'] > 0 ? (int)$_GET['limit'] : ($isMobile ? $mobileLimit : $defaultLimit);
$offset = 0; // Sempre começa do início para paginação acumulativa

if ($pesquisa != '') {
    $consulta = "SELECT * FROM livros WHERE titulo LIKE '%$pesquisa%' AND disponivel = 1 AND removidoEm IS NULL LIMIT $limit OFFSET $offset";
} else {
    $consulta = "SELECT * FROM livros WHERE disponivel = 1 AND removidoEm IS NULL LIMIT $limit OFFSET $offset";
}

$resultado = mysqli_query($conexao, $consulta);
$numRows = mysqli_num_rows($resultado);

// Criar array com todos os livros para usar depois (para os modais)
$todos_livros = [];
while ($row = mysqli_fetch_assoc($resultado)) {
    $todos_livros[] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Acervo de Livros</title>
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
    }

    .search-form .form-control {
      border: 2px solid #e0e0e0;
      border-radius: 12px !important;
      border-right: none;
      padding: 0.75rem 1rem;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      flex: 1;
    }

    .search-form .input-group .form-control {
      border-radius: 12px !important;
    }

    .search-form .input-group .form-control:first-child {
      border-radius: 12px !important;
    }

    .search-form .form-control:focus {
      border-color: #08470b;
      box-shadow: none;
      outline: none;
      border-right: none;
      z-index: 1;
    }

    .search-form .form-control:focus + .input-group-append .btn-info {
      border-color: #08470b;
      z-index: 2;
    }

    .search-form .input-group-append {
      display: flex;
      margin: 0;
    }

    .search-form .btn-info {
      background: linear-gradient(135deg, #08470b 0%, #062c2a 100%);
      border: 2px solid #08470b;
      border-left: none;
      border-radius: 12px;
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(8, 71, 11, 0.3);
      display: flex;
      align-items: center;
      justify-content: center;
      white-space: nowrap;
      height: 70%;
      margin: 0;
    }

    .search-form .btn-info:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(8, 71, 11, 0.4);
      background: linear-gradient(135deg, #062c2a 0%, #08470b 100%);
      border-color: #062c2a;
    }

    .search-form .btn-info:focus {
      border-color: #08470b;
      border-left: none;
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

    .btn-danger {
      background-color: #dc3545;
      border-color: #dc3545;
      border-radius: 8px;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-danger:hover {
      background-color: #c82333;
      border-color: #bd2130;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
    }

    .btn-sm {
      padding: 0.5rem 1rem;
      font-size: 0.875rem;
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

    /* Estilos do Modal Bootstrap */
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
      color: #6b6f73;
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

    html, body {
      height: 100%;
      min-height: 100vh;
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
        border-right: 2px solid #e0e0e0 !important;
        width: 100%;
      }
      
      .search-form .input-group-append {
        width: 100%;
      }
      
      .search-form .btn-info {
        border-radius: 12px !important;
        border-left: 2px solid #08470b !important;
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

    .content > * {
      position: relative;
      z-index: 1;
    }
    
    /* Garantir que modais fiquem fora do contexto de .content */
    .modal,
    .modal-backdrop {
      position: fixed;
    }
    
    .modal {
      z-index: 1060 !important;
    }

    .sidebar.hidden ~ .content {
      margin-left: 0;
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

  <div class="content">
    <div class="container mt-5">
      <!-- Header Card -->
      <div class="page-header-card">
        <div class="card-header">
          <h2>
            <i class="fas fa-book-open"></i>
            Acervo de Livros
          </h2>
        </div>
        <div class="card-body">
          <form method="GET" action="" class="search-form">
            <div class="input-group">
              <input type="text" class="form-control" placeholder="Buscar livro por título..." name="pesquisa" value="<?= htmlspecialchars($pesquisa) ?>">
              <div class="input-group-append">
                <button class="btn btn-info" type="submit">
                  <i class="fas fa-search mr-2"></i>
                  Buscar
                </button>
              </div>
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
              <th>ID</th>
              <th>Título</th>
              <th>ISBN</th>
              <th>Editora</th>
              <th>Lançamento</th>
              <th>Disponível</th>
              <th>Editar</th>
              <th>Remover</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($numRows > 0) {
              foreach ($todos_livros as $livros) {
            ?>
                <tr>
                  <td><?= htmlspecialchars($livros['ID']); ?></td>
                  <td><?= htmlspecialchars($livros['titulo']); ?></td>
                  <td><?= htmlspecialchars($livros['isbn']); ?></td>
                  <td><?= htmlspecialchars($livros['editora']); ?></td>
                  <td>
                    <?php
                    $data = DateTime::createFromFormat('Y-m-d', $livros['lancamento']);
                    echo $data ? $data->format('d/m/Y') : '';
                    ?>
                  </td>
                  <td><?= $livros['disponivel'] ? 'Sim' : 'Não'; ?></td>
                  <td>
                    <a href="editarLivro.php?id=<?= $livros['ID']; ?>" class="btn btn-warning btn-sm">
                      <i class="fas fa-edit mr-1"></i>
                      Editar
                    </a>
                  </td>
                  <td>
                    <a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#removerModal<?= $livros['ID']; ?>">
                      <i class="fas fa-trash mr-1"></i>
                      Remover
                    </a>
                  </td>
                </tr>
            <?php
              }
            } else {
              echo "<tr><td colspan='8' class='text-center py-4'><i class='fas fa-inbox mr-2'></i>Nenhum Livro Cadastrado.</td></tr>";
            }
            ?>
          </tbody>
        </table>
        </div>

        <!-- Pagination Buttons -->
        <div class="pagination-buttons">
          <div></div>
          <div>
            <?php
            $increment = $isMobile ? $mobileLimit : $defaultLimit;
            $minLimit = $isMobile ? $mobileLimit : $defaultLimit;
            
            if ($numRows === $limit) {
              $params = $_GET;
              if ($pesquisa) {
                $params['pesquisa'] = $pesquisa;
              }
              $params['limit'] = $limit + $increment;
              $url = '?' . http_build_query($params);
              echo '<a href="' . $url . '" class="btn btn-show-more mr-2" id="mostrarMais50">
                      <i class="fas fa-chevron-down mr-2"></i>
                      Mostrar mais ' . $increment . '
                    </a>';
            }
            if ($limit > $minLimit) {
              $params = $_GET;
              if ($pesquisa) {
                $params['pesquisa'] = $pesquisa;
              }
              $params['limit'] = $minLimit;
              $url = '?' . http_build_query($params);
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
          foreach ($todos_livros as $livros) {
        ?>
          <div class="mobile-card">
            <div class="mobile-card-header">
              <i class="fas fa-book mr-2"></i>Livro #<?= htmlspecialchars($livros['ID']); ?>
            </div>
            <div class="mobile-card-row">
              <span class="mobile-card-label"><i class="fas fa-book mr-1"></i>Título:</span>
              <span class="mobile-card-value"><?= htmlspecialchars($livros['titulo']); ?></span>
            </div>
            <div class="mobile-card-row">
              <span class="mobile-card-label"><i class="fas fa-barcode mr-1"></i>ISBN:</span>
              <span class="mobile-card-value"><?= htmlspecialchars($livros['isbn']); ?></span>
            </div>
            <div class="mobile-card-row">
              <span class="mobile-card-label"><i class="fas fa-building mr-1"></i>Editora:</span>
              <span class="mobile-card-value"><?= htmlspecialchars($livros['editora']); ?></span>
            </div>
            <div class="mobile-card-row">
              <span class="mobile-card-label"><i class="fas fa-calendar mr-1"></i>Lançamento:</span>
              <span class="mobile-card-value">
                <?php
                $data = DateTime::createFromFormat('Y-m-d', $livros['lancamento']);
                echo $data ? $data->format('d/m/Y') : '';
                ?>
              </span>
            </div>
            <div class="mobile-card-row">
              <span class="mobile-card-label"><i class="fas fa-check-circle mr-1"></i>Disponível:</span>
              <span class="mobile-card-value">
                <?php if ($livros['disponivel']): ?>
                  <span style="color: #28a745; font-weight: 600;"><i class="fas fa-check mr-1"></i>Sim</span>
                <?php else: ?>
                  <span style="color: #dc3545; font-weight: 600;"><i class="fas fa-times mr-1"></i>Não</span>
                <?php endif; ?>
              </span>
            </div>
            <div class="mobile-card-actions">
              <a href="editarLivro.php?id=<?= $livros['ID']; ?>" class="btn btn-warning btn-sm">
                <i class="fas fa-edit mr-1"></i>Editar
              </a>
              <a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#removerModal<?= $livros['ID']; ?>">
                <i class="fas fa-trash mr-1"></i>Remover
              </a>
            </div>
          </div>
        <?php
          }
        } else {
        ?>
          <div class="mobile-card">
            <div class="text-center py-4">
              <i class="fas fa-inbox fa-3x mb-3" style="color: #ccc;"></i>
              <p style="color: #666; margin: 0;">Nenhum Livro Cadastrado.</p>
            </div>
          </div>
        <?php
        }
        ?>
        
        <!-- Pagination Buttons (Mobile) -->
        <div class="pagination-buttons">
          <div></div>
          <div>
            <?php
            $increment = $isMobile ? $mobileLimit : $defaultLimit;
            $minLimit = $isMobile ? $mobileLimit : $defaultLimit;
            
            if ($numRows === $limit) {
              $params = $_GET;
              if ($pesquisa) {
                $params['pesquisa'] = $pesquisa;
              }
              $params['limit'] = $limit + $increment;
              $url = '?' . http_build_query($params);
              echo '<a href="' . $url . '" class="btn btn-show-more" id="mostrarMais50Mobile">
                      <i class="fas fa-chevron-down mr-2"></i>
                      Mostrar mais ' . $increment . '
                    </a>';
            }
            if ($limit > $minLimit) {
              $params = $_GET;
              if ($pesquisa) {
                $params['pesquisa'] = $pesquisa;
              }
              $params['limit'] = $minLimit;
              $url = '?' . http_build_query($params);
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

<?php include 'components/sidebar-script.php'; ?>

<!-- Modais de confirmação de remoção (fora do loop para evitar conflitos) -->
<?php
if (count($todos_livros) > 0) {
  foreach ($todos_livros as $livros_modal) {
?>
<div class="modal fade" id="removerModal<?= $livros_modal['ID']; ?>" tabindex="-1" role="dialog" aria-labelledby="removerModalLabel<?= $livros_modal['ID']; ?>" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="removerModalLabel<?= $livros_modal['ID']; ?>">
          <i class="fas fa-exclamation-triangle mr-2"></i>
          Confirmar Remoção
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="modal-icon">
          <i class="fas fa-trash-alt"></i>
        </div>
        <p>Tem certeza de que deseja remover o livro <strong>"<?= htmlspecialchars($livros_modal['titulo']); ?>"</strong>?</p>
        <p style="color: #dc3545; font-weight: 600; margin-top: 1rem;">Esta ação não pode ser desfeita.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fas fa-times mr-2"></i>Cancelar
        </button>
        <a href="removerLivro.php?id=<?= $livros_modal['ID']; ?>" class="btn btn-danger">
          <i class="fas fa-check mr-2"></i>Confirmar Remoção
        </a>
      </div>
    </div>
  </div>
</div>
<?php
  }
}
?>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
  $(document).ready(function() {
    // Garantir que os modais sejam fechados corretamente
    $('.modal').on('hidden.bs.modal', function () {
      $(this).find('form')[0]?.reset();
    });

    // Prevenir múltiplos cliques
    $('[data-toggle="modal"]').on('click', function(e) {
      e.preventDefault();
      var target = $(this).data('target');
      $(target).modal('show');
    });

    // Preservar posição de scroll apenas ao clicar em "mostrar mais/menos"
    $(document).on('click', '#mostrarMais50, #mostrarMenos, #mostrarMais50Mobile, #mostrarMenosMobile', function() {
      sessionStorage.setItem('scrollPosAcervo', window.scrollY);
      sessionStorage.setItem('shouldRestoreScroll', 'true');
    });
    
    // Restaurar posição apenas se veio de um clique, não de um refresh
    if (sessionStorage.getItem('shouldRestoreScroll') === 'true') {
      var pos = parseInt(sessionStorage.getItem('scrollPosAcervo'), 10);
      if (pos > 0) {
        setTimeout(function() {
          window.scrollTo({top: pos, behavior: 'auto'});
          sessionStorage.removeItem('scrollPosAcervo');
          sessionStorage.removeItem('shouldRestoreScroll');
        }, 100);
      }
    } else {
      // Limpar qualquer posição salva se for um refresh normal
      sessionStorage.removeItem('scrollPosAcervo');
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
</body>
</html>