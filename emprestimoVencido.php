<?php
require 'conexao.php';
session_start(); 

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}


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


// Paginação acumulativa
$defaultLimit = 50;
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit'] > 0 ? (int)$_GET['limit'] : $defaultLimit;
$offset = 0; // Sempre começa do início

// Adiciona cálculo de dias em atraso
$consulta = "SELECT emp.id as empId, cli.nomeCliente, li.titulo, emp.criadoEm, li.ID as livroId, emp.ativo, emp.vencimento, DATEDIFF(CURDATE(), emp.vencimento) as diasAtraso
             FROM emprestimo as emp 
             JOIN clientes as cli on emp.idCliente = cli.id 
             JOIN livros as li on emp.idLivro = li.ID 
             WHERE emp.vencimento < CURDATE() AND emp.ativo = '1'";
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
    $exportConsulta = "SELECT emp.id as empId, cli.nomeCliente, li.titulo, emp.criadoEm, li.ID as livroId, emp.ativo, emp.vencimento, DATEDIFF(CURDATE(), emp.vencimento) as diasAtraso
             FROM emprestimo as emp 
             JOIN clientes as cli on emp.idCliente = cli.id 
             JOIN livros as li on emp.idLivro = li.ID 
             WHERE emp.vencimento < CURDATE() AND emp.ativo = '1'";
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
        header('Content-Disposition: attachment; filename="emprestimos_atrasados.csv"');
        $output = fopen('php://output', 'w');
        // Adiciona BOM UTF-8 para Excel reconhecer acentuação
        fwrite($output, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($output, ['ID', 'Cliente', 'Livro', 'Data de Criação', 'Data para Devolução', 'Dias Atraso', 'Ativo'], ';');
        while ($row = mysqli_fetch_assoc($exportResult)) {
            fputcsv($output, [
                $row['empId'],
                $row['nomeCliente'],
                $row['titulo'],
                date('d/m/Y', strtotime($row['criadoEm'])),
                date('d/m/Y', strtotime($row['vencimento'])),
                $row['diasAtraso'],
                $row['ativo'] == 0 ? 'Não' : 'Sim'
            ], ';');
        }
        fclose($output);
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
  <title>Empréstimos Atrasados</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <?php include 'components/sidebar-style.php'; ?>
  
  <style>

    body {
      background-color: rgb(238, 255, 235);
      padding-bottom: 80px;
      margin: 0;
      overflow-x: hidden;
      font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .container, .container-fluid {
      max-width: 100vw;
      margin: 40px auto 0 auto;
      padding-left: 8px;
      padding-right: 8px;
    }

    .tabela-delimitada th, .tabela-delimitada td {
      border-right: 0.5px solid #555;
    }
    
    .tabela-delimitada th:last-child, .tabela-delimitada td:last-child {
      border-right: none;
    }

  </style>
</head>
<body>

<?php include 'components/sidebar-logado.php'; ?>

  <!-- Conteúdo principal -->
  <div class="content">
    <div class="container mt-5">
      <h2 class="text-center mb-4">Empréstimos Atrasados</h2>
      <form method="GET" action="" class="form-row">
        <div class="form-group col-md-4">
          <input type="text" class="form-control" name="cliente" placeholder="Buscar Cliente" value="<?= htmlspecialchars($clienteBusca ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="form-group col-md-4">
          <input type="text" class="form-control" name="livro" placeholder="Buscar Livro" value="<?= htmlspecialchars($livroBusca ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="form-group col-md-4">
          <button type="submit" class="btn btn-primary btn-block">Buscar</button>
        </div>
      </form>
      <div class="table-responsive mt-4">
        <table class="table table-striped mb-0 tabela-delimitada" id="tabela" style="width:100%; table-layout:auto;">
          <thead class="thead-dark">
            <tr>
              <?php
                function sortLinkVencido($label, $col, $order, $dir) {
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
              <th><?= sortLinkVencido('ID', 'empId', $order, $dir) ?></th>
              <th><?= sortLinkVencido('Cliente', 'nomeCliente', $order, $dir) ?></th>
              <th><?= sortLinkVencido('Livro', 'titulo', $order, $dir) ?></th>
              <th><?= sortLinkVencido('Data de Criação', 'criadoEm', $order, $dir) ?></th>
              <th><?= sortLinkVencido('Data para Devolução', 'vencimento', $order, $dir) ?></th>
              <th>Dias Atraso</th>
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
                <td><?= date('d/m/Y', strtotime($emprestimos['criadoEm'])); ?></td>
                <td><?= date('d/m/Y', strtotime($emprestimos['vencimento'])); ?></td>
                <td><?= $emprestimos['diasAtraso']; ?></td>
                <td>
                  <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalDevolucao" data-empid="<?= $emprestimos['empId']; ?>">Devolver</button>
                </td>
                <td>
                  <button class="btn btn-warning btn-sm ff2" data-toggle="modal" data-target="#modalRenovacao" data-empid="<?= $emprestimos['empId']; ?>">Renovar</button>
                </td>
              </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='8' class='text-center'>Nenhum Empréstimo Atrasado Encontrado.</td></tr>";
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
            // Botão Mostrar mais 50
            if ($numRows === $limit) {
              $params = $_GET;
              $params['limit'] = $limit + $defaultLimit;
              $url = '?' . http_build_query($params) . '#tabela';
              echo '<a href="' . $url . '" class="btn btn-primary mr-2" id="mostrarMais50">Mostrar mais 50</a>';
            }
            // Botão Mostrar menos (voltar ao estado original)
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

<!-- MODAL DEVOLUÇÃO -->
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
          <p>Deseja realmente devolver este livro?</p>
          <input type="hidden" name="emprestimoId" id="emprestimoIdDevolucao">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Confirmar Devolução</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- MODAL RENOVAÇÃO -->
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
              <button type="submit" class="btn btn-warning ff2">Confirmar Renovação</button>
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

<!-- SCRIPTS -->

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  $(document).ready(function() {


    // Salva a posição do scroll ao clicar em "Mostrar mais 50"
    $(document).on('click', '#mostrarMais50', function() {
      sessionStorage.setItem('scrollPosEmpVencido', window.scrollY);
    });

    // Ao carregar, se houver posição salva, rola até ela
    if (sessionStorage.getItem('scrollPosEmpVencido')) {
      var pos = parseInt(sessionStorage.getItem('scrollPosEmpVencido'), 10);
      setTimeout(function() {
        window.scrollTo({top: pos, behavior: 'auto'});
        sessionStorage.removeItem('scrollPosEmpVencido');
      }, 100);
    }

    $('#modalRenovacao').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var empId = button.data('empid');
      var modal = $(this);
      modal.find('#emprestimoIdRenovacao').val(empId);
    });
    $('#modalRenovacao').on('hidden.bs.modal', function(event) {
      $(this).find('#emprestimoIdRenovacao').val('');
    });
    $('#modalDevolucao').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var empId = button.data('empid');
      var modal = $(this);
      modal.find('#emprestimoIdDevolucao').val(empId);
    });
    $('#modalDevolucao').on('hidden.bs.modal', function(event) {
      $(this).find('#emprestimoIdDevolucao').val('');
    });
    // Envio AJAX do formulário de devolução
    $(document).off('submit', '#formDevolucao');
    $(document).on('submit', '#formDevolucao', function(e) {
      e.preventDefault();
      var val = $('#emprestimoIdDevolucao').val();
      if (!val) {
        alert('Erro: ID do empréstimo não definido!');
        return false;
      }
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
  });
</script>

<?php include 'components/sidebar-script.php'; ?>

</body>
</html>
