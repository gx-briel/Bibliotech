<?php
require 'conexao.php';
session_start(); 

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

$clienteFiltro = isset($_GET['cliente']) ? trim($_GET['cliente']) : '';
$livroFiltro = isset($_GET['livro']) ? trim($_GET['livro']) : '';

$consulta = "SELECT emp.id as empId, cli.nomeCliente, li.titulo, emp.criadoEm, li.ID as livroId, emp.ativo, emp.vencimento
             FROM emprestimo as emp 
             JOIN clientes as cli on emp.idCliente = cli.id 
             JOIN livros as li on emp.idLivro = li.ID 
             WHERE emp.vencimento < CURDATE() AND emp.ativo = '1'";

if (!empty($clienteFiltro)) {
    $consulta .= " AND cli.nomeCliente LIKE '%$clienteFiltro%'";
}
if (!empty($livroFiltro)) {
    $consulta .= " AND li.titulo LIKE '%$livroFiltro%'";
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
  <style>
    body {
      background-color: rgb(238, 255, 235);
      padding-bottom: 80px;
    }
    .navbar {
      background-color: #1c0e3f;
    }
    .navbar-brand, .nav-link {
      color: white !important;
      font-weight: bold;
    }
    .navbar-nav .nav-link:hover {
      color: #ffcc00 !important;
    }
    .btn-info, .btn-warning, .btn-danger {
      color: white;
    }
    .table-responsive {
      overflow-x: auto;
    }
    table {
      min-width: 900px;
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg">
  <a class="navbar-brand" href="indexlogado.php">Bibliotech</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
    <span class="navbar-toggler-icon text-white">&#9776;</span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item"><a class="nav-link" href="cadastroCliente.php">Cadastrar Cliente</a></li>
      <li class="nav-item"><a class="nav-link" href="cadastroLivro.php">Cadastrar Livro</a></li>
      <li class="nav-item"><a class="nav-link" href="acervo.php">Acervo de Livros</a></li>
      <li class="nav-item"><a class="nav-link" href="criaEmprestimo.php">Criar Empréstimo</a></li>
    </ul>
  </div>
</nav>

<!-- CONTEÚDO -->
<div class="container mt-5">
  <h2 class="text-center mb-4">Listagem de Empréstimos Atrasados</h2>

  <form method="GET" action="" class="mb-4">
    <div class="form-row">
      <div class="col-md-5 mb-2">
        <input type="text" class="form-control" name="cliente" placeholder="Buscar Cliente" value="<?= htmlspecialchars($clienteFiltro); ?>">
      </div>
      <div class="col-md-5 mb-2">
        <input type="text" class="form-control" name="livro" placeholder="Buscar Livro" value="<?= htmlspecialchars($livroFiltro); ?>">
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary btn-block">Buscar</button>
      </div>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead class="thead-dark">
        <tr>
          <th>ID</th>
          <th>Cliente</th>
          <th>Livro</th>
          <th>Data de Criação</th>
          <th>Data para Devolução</th>
          <th>Ativo</th>
          <th>Devolver</th>
          <th>Renovar</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($executaConsulta) > 0): ?>
          <?php foreach ($executaConsulta as $emprestimos): ?>
            <tr>
              <td><?= $emprestimos['empId']; ?></td>
              <td><?= $emprestimos['nomeCliente']; ?></td>
              <td><?= $emprestimos['titulo']; ?></td>
              <td><?= date("d/m/Y", strtotime($emprestimos['criadoEm'])); ?></td>
              <td class="text-danger font-weight-bold"><?= date("d/m/Y", strtotime($emprestimos['vencimento'])); ?></td>
              <td><?= $emprestimos['ativo'] == 0 ? 'Não' : 'Sim'; ?></td>
              <td>
                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalDevolucao" data-empid="<?= $emprestimos['empId']; ?>">Devolver</button>
              </td>
              <td>
                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalRenovacao" data-empid="<?= $emprestimos['empId']; ?>">Renovar</button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="8" class="text-center">Nenhum Empréstimo Atrasado Encontrado.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- MODAL DEVOLUÇÃO -->
<div class="modal fade" id="modalDevolucao" tabindex="-1" role="dialog" aria-labelledby="modalDevolucaoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" action="devolveLivro.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirmar Devolução</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <p>Deseja realmente devolver este livro?</p>
          <input type="hidden" name="emprestimoId" id="emprestimoIdDevolucao">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Confirmar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- MODAL RENOVAÇÃO -->
<div class="modal fade" id="modalRenovacao" tabindex="-1" role="dialog" aria-labelledby="modalRenovacaoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" action="renovaEmprestimo.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirmar Renovação</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <p>Deseja renovar este empréstimo?</p>
          <input type="hidden" name="emprestimoId" id="emprestimoIdRenovacao">
          <div class="form-group">
            <label>Nova Data de Devolução</label>
            <input type="date" name="novaDataRenovacao" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Confirmar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- SCRIPTS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
  $('#modalRenovacao').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    $('#emprestimoIdRenovacao').val(button.data('empid'));
  });
  $('#modalDevolucao').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    $('#emprestimoIdDevolucao').val(button.data('empid'));
  });
</script>

</body>
</html>
