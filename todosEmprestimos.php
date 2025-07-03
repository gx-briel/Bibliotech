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
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit'] > 0 ? (int)$_GET['limit'] : $defaultLimit;
$offset = 0; // Sempre começa do início para paginação acumulativa

$consulta = "SELECT emp.id as empId, cli.nomeCliente as nomeCliente, li.titulo, emp.criadoEm, li.ID as livroId, emp.ativo, emp.vencimento
             FROM emprestimo as emp 
             JOIN clientes as cli on emp.idCliente = cli.id 
             JOIN livros as li on emp.idLivro = li.ID
             WHERE cli.nomeCliente LIKE '%$buscaCliente%' AND li.titulo LIKE '%$buscaLivro%'";
if ($order) {
    $consulta .= " ORDER BY {$orderColumns[$order]} $dir";
}
$consulta .= " LIMIT $limit OFFSET $offset";

// Exportação para Excel (xlsx) deve ser processada antes de qualquer saída HTML
if (isset($_POST['export_excel'])) {
    require __DIR__ . '/vendor/autoload.php';
    // Monta a consulta SEM LIMIT/OFFSET para exportar todos os itens filtrados
    $exportConsulta = "SELECT emp.id as empId, cli.nomeCliente as nomeCliente, li.titulo, emp.criadoEm, li.ID as livroId, emp.ativo, emp.vencimento
             FROM emprestimo as emp 
             JOIN clientes as cli on emp.idCliente = cli.id 
             JOIN livros as li on emp.idLivro = li.ID
             WHERE cli.nomeCliente LIKE '%$buscaCliente%' AND li.titulo LIKE '%$buscaLivro%'";
    if ($order) {
        $exportConsulta .= " ORDER BY {$orderColumns[$order]} $dir";
    }
    $exportResult = mysqli_query($conexao, $exportConsulta);
    if ($exportResult) {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Cabeçalhos
        $sheet->fromArray([
            'ID', 'Cliente', 'Livro', 'Data de Criação', 'Data para Devolução', 'Ativo'
        ], NULL, 'A1');
        $rowNum = 2;
        while ($row = mysqli_fetch_assoc($exportResult)) {
            $sheet->setCellValue('A' . $rowNum, $row['empId']);
            $sheet->setCellValue('B' . $rowNum, $row['nomeCliente']);
            $sheet->setCellValue('C' . $rowNum, $row['titulo']);
            $sheet->setCellValue('D' . $rowNum, date('d/m/Y', strtotime($row['criadoEm'])));
            $sheet->setCellValue('E' . $rowNum, date('d/m/Y', strtotime($row['vencimento'])));
            $sheet->setCellValue('F' . $rowNum, $row['ativo'] == 0 ? 'Não' : 'Sim');
            $rowNum++;
        }
        // Ajusta largura automática
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="emprestimos.xlsx"');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}

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
      flex: 1;
      transition: margin-left 0.3s;
    }
    .container, .container-fluid {
      max-width: 1100px;
      margin: 40px auto 0 auto;
      padding-left: 24px;
      padding-right: 24px;
    }
    h2, h2.text-center {
      text-align: center;
      margin-bottom: 2rem;
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
    <button id="showSidebarBtn" class="show-sidebar-btn" style="position: fixed; left: 4px; top: 18px; right: auto; cursor: pointer; z-index: 1000;">☰</button>
  <!-- Conteúdo principal -->
  <div class="content">
    <div class="container mt-5">
      <h2 class="text-center mb-4">Listagem de Empréstimos</h2>
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
        <table class="table table-striped mb-0">
          <thead class="thead-dark">
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
                  return "<a href='$url' style='color:inherit;text-decoration:none;'>$label$icon</a>";
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
            $numRows = mysqli_num_rows($executaConsulta);
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
                echo "<tr><td colspan='6' class='text-center'>Nenhum Empréstimo encontrado.</td></tr>";
            }
            ?>
          </tbody>
        </table>
        <div class="d-flex justify-content-between align-items-center mt-2 mb-4">
          <form method="post" action="" style="margin-bottom:0;">
            <button type="submit" name="export_excel" class="btn btn-success">Exportar Excel</button>
          </form>
          <?php
            // Botão mostrar mais 50
            if ($numRows === $limit) {
              $params = $_GET;
              $params['limit'] = $limit + $defaultLimit;
              $url = '?' . http_build_query($params) . '#tabela';
              echo '<a href="' . $url . '" class="btn btn-primary" id="mostrarMais50">Mostrar mais 50</a>';
            }
          ?>
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
  document.getElementById('showSidebarBtn').addEventListener('click', showSidebar);
</script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  $(document).ready(function() {
    $(document).on('click', '#mostrarMais50', function() {
      sessionStorage.setItem('scrollPosEmpTodos', window.scrollY);
    });
    if (sessionStorage.getItem('scrollPosEmpTodos')) {
      var pos = parseInt(sessionStorage.getItem('scrollPosEmpTodos'), 10);
      setTimeout(function() {
        window.scrollTo({top: pos, behavior: 'auto'});
        sessionStorage.removeItem('scrollPosEmpTodos');
      }, 100);
    }
  });
</script>
</body>
</html>
