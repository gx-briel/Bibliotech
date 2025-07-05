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
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit'] > 0 ? (int)$_GET['limit'] : $defaultLimit;
$offset = 0; // Sempre começa do início para paginação acumulativa

$consulta = "SELECT emp.id as empId, cli.nomeCliente as nomeCliente, li.titulo, emp.criadoEm, li.ID as livroId, emp.ativo, emp.vencimento
             FROM emprestimo as emp
             JOIN clientes as cli on emp.idCliente = cli.id
             JOIN livros as li on emp.idLivro = li.ID
             WHERE emp.ativo = '1'";
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

// Exportação para Excel (xlsx) deve ser processada antes de qualquer saída HTML
if (isset($_POST['export_excel'])) {
    require __DIR__ . '/vendor/autoload.php';
    $exportConsulta = "SELECT emp.id as empId, cli.nomeCliente as nomeCliente, li.titulo, emp.criadoEm, li.ID as livroId, emp.ativo, emp.vencimento
             FROM emprestimo as emp
             JOIN clientes as cli on emp.idCliente = cli.id
             JOIN livros as li on emp.idLivro = li.ID
             WHERE emp.ativo = '1'";
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
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
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
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="emprestimos_ativos.xlsx"');
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
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Listagem de Empréstimos Ativos</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <style>
    body {
      background-color: rgb(238, 255, 235);
      padding-bottom: 60px;
    }
    .table-container {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }
    table {
      width: 100%;
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

    .table-striped tbody tr:nth-of-type(odd) {
      background-color: rgba(0, 0, 0, 0.05);
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
      .sidebar {
        position: fixed;
        min-height: 100vh;
        z-index: 999;
      }
    }

  </style>
</head>
<body>


<div class="wrapper">
  <!-- Sidebar -->
  <nav id="sidebar" class="sidebar">
    <div class="sidebar-header"><a href="indexlogado.php" style="color: #fff; text-decoration: none;"><i class="fa-solid fa-book-open-reader"></i> Bibliotech</a></div>
    <button class="toggle-btn btn btn-sm btn-warning w-100 mb-2" onclick="hideSidebar()">← Recolher</button>
    <ul class="nav-links">
      <li><a href="relatorios.php">Dashboard</a></li>
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


    <div class="container mt-5">
      <h2 class="text-center mb-4">Empréstimos Ativos</h2>
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
              <th>Devolver</th>
              <th>Renovar</th>
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
                <td class="data-criacao"><?= date('d/m/Y', strtotime($emprestimos['criadoEm'])); ?></td>
                <td class="data-vencimento"><?= date('d/m/Y', strtotime($emprestimos['vencimento'])); ?></td>
                <td><?= $emprestimos['ativo'] == 0 ? 'Não' : 'Sim'; ?></td>
                <td>
                  <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalDevolucao" data-empid="<?= $emprestimos['empId']; ?>">Devolver</button>
                </td>
                <td>
                  <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalRenovacao" data-empid="<?= $emprestimos['empId']; ?>">Renovar</button>
                </td>
              </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='8' class='text-center'>Nenhum Empréstimo Ativo Encontrado.</td></tr>";
            }
            ?>
          </tbody>
        </table>
        <div class="d-flex justify-content-between align-items-center mt-2 mb-4">
          <form method="post" action="" class="mb-0">
            <button type="submit" name="export_excel" class="btn btn-success">Exportar Excel</button>
          </form>
          <div>
          <?php
            if ($numRows === $limit) {
              $params = $_GET;
              $params['limit'] = $limit + $defaultLimit;
              $url = '?' . http_build_query($params) . '#tabela';
              echo '<a href="' . $url . '" class="btn btn-primary mr-2" id="mostrarMais50">Mostrar mais 50</a>';
            }
            if ($limit > $defaultLimit) {
              $params = $_GET;
              $params['limit'] = $defaultLimit;
              $url = '?' . http_build_query($params) . '#tabela';
              echo '<a href="' . $url . '" class="btn btn-secondary" id="mostrarMenos">Mostrar menos</a>';
            }
          ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalDevolucao" tabindex="-1" role="dialog" aria-labelledby="modalDevolucaoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDevolucaoLabel">Confirmação de Devolução</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="formDevolucao" autocomplete="off">
        <div class="modal-body">
          <p>Tem certeza que deseja devolver o empréstimo?</p>
        </div>
        <div class="modal-footer">
          <input type="hidden" name="emprestimoId" id="emprestimoIdDevolucao">
          <button type="submit" class="btn btn-danger">Confirmar Devolução</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Fim do modal de devolução -->
<!-- Modal de sucesso da devolução -->
<div class="modal fade" id="modalDevolucaoSucesso" tabindex="-1" role="dialog" aria-labelledby="modalDevolucaoSucessoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modalDevolucaoSucessoLabel">Devolução realizada</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p id="msgDevolucaoSucesso">O livro foi devolvido com sucesso!</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="window.location.reload()">OK</button>
      </div>
    </div>
  </div>
</div>




<div class="modal fade" id="modalRenovacao" tabindex="-1" role="dialog" aria-labelledby="modalRenovacaoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalRenovacaoLabel">Confirmação de Renovação</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Informe a nova data de devolução:</p>
        <form method="POST" action="renovaEmprestimo.php" id="formRenovacao">
          <input type="hidden" name="emprestimoId" id="emprestimoIdRenovacao">
          <div class="input-group">
            <input type="date" name="novaDataRenovacao" class="form-control" required id="novaDataRenovacao">
            <div class="input-group-append">
              <button type="submit" class="btn btn-warning">Confirmar Renovação</button>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>



<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
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

  $(document).ready(function() {
    $(document).on('click', '#mostrarMais50', function() {
      sessionStorage.setItem('scrollPosEmpAtivo', window.scrollY);
    });
    if (sessionStorage.getItem('scrollPosEmpAtivo')) {
      var pos = parseInt(sessionStorage.getItem('scrollPosEmpAtivo'), 10);
      setTimeout(function() {
        window.scrollTo({top: pos, behavior: 'auto'});
        sessionStorage.removeItem('scrollPosEmpAtivo');
      }, 100);
    }
  });
</script>

<script>
  $(document).ready(function() {
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
  });
</script>

<script>
  $(document).ready(function() {
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
    // Envio AJAX robusto do formulário de devolução
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
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="modalDevolucaoSucessoLabel">Devolução realizada</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <p id="msgDevolucaoSucesso"></p>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="window.location.reload()">OK</button>
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
    // Log global para qualquer submit
    $(document).on('submit', 'form', function(e) {
      console.log('Form submetido:', this.action, $(this).serialize());
    });
    // Log extra no botão de submit do modal
    $(document).on('click', '#formDevolucao button[type="submit"]', function() {
      console.log('Botão Confirmar Devolução clicado!');
    });
  });
</script>

</body>
</html>
