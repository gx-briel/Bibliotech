<?php
require 'conexao.php';
session_start(); 

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit; 
}

$clienteBusca = isset($_POST['clienteBusca']) ? $_POST['clienteBusca'] : '';
$livroBusca = isset($_POST['livroBusca']) ? $_POST['livroBusca'] : '';

$consulta = "SELECT emp.id as empId, cli.nomeCliente as nomeCliente, li.titulo, emp.criadoEm, li.ID as livroId, emp.ativo, emp.vencimento
             FROM emprestimo as emp
             JOIN clientes as cli on emp.idCliente = cli.id
             JOIN livros as li on emp.idLivro = li.ID
             WHERE emp.ativo = '1'";

if ($clienteBusca) {
    $consulta .= " AND cli.nomeCliente LIKE '%" . mysqli_real_escape_string($conexao, $clienteBusca) . "%'";
}

if ($livroBusca) {
    $consulta .= " AND li.titulo LIKE '%" . mysqli_real_escape_string($conexao, $livroBusca) . "%'";
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
    .container {
      max-width: 1200px;
      width: 100%;
    }

    .table-striped tbody tr:nth-of-type(odd) {
      background-color: rgba(0, 0, 0, 0.05);
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

  </style>
</head>
<body>

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

<div class="container mt-5">
  <h2>Listagem de Empréstimos (Ativos)</h2>
  <br><br>
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
  <br>
  <div class="table-container">
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th scope="col">ID</th>
          <th scope="col">Cliente</th>
          <th scope="col">Livro</th>
          <th scope="col">Data de Criação</th>
          <th scope="col">Data para Devolução</th>
          <th scope="col">Empréstimo Ativo</th>
          <th scope="col">Devolver Empréstimo</th>
          <th scope="col">Renovar Empréstimo</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $clienteBusca = isset($_GET['cliente']) ? $_GET['cliente'] : '';
        $livroBusca = isset($_GET['livro']) ? $_GET['livro'] : '';
        $dataBusca = isset($_GET['data']) ? $_GET['data'] : '';

        $consulta = "SELECT emp.id as empId, cli.nomeCliente as nomeCliente, li.titulo, emp.criadoEm, li.ID as livroId, emp.ativo, emp.vencimento
                     FROM emprestimo as emp 
                     JOIN clientes as cli on emp.idCliente = cli.id 
                     JOIN livros as li on emp.idLivro = li.ID 
                     WHERE emp.ativo = '1'";
        if ($clienteBusca) {
            $consulta .= " AND cli.nomeCliente LIKE '%$clienteBusca%'";
        }
        if ($livroBusca) {
            $consulta .= " AND li.titulo LIKE '%$livroBusca%'";
        }
        if ($dataBusca) {
            $consulta .= " AND emp.criadoEm LIKE '%$dataBusca%'";
        }

        $executaConsulta = mysqli_query($conexao, $consulta);
        if (mysqli_num_rows($executaConsulta) > 0) {
            foreach ($executaConsulta as $emprestimos) {
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
      <div class="modal-body">
        <p>Tem certeza que deseja devolver o empréstimo?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <form method="POST" action="devolveLivro.php" id="formDevolucao">
          <input type="hidden" name="emprestimoId" id="emprestimoIdDevolucao">
          <button type="submit" class="btn btn-danger">Confirmar Devolução</button>
        </form>
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
        <p>Tem certeza que deseja renovar o empréstimo?</p>
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

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const dataCriacao = document.querySelectorAll('.data-criacao');
    const dataVencimento = document.querySelectorAll('.data-vencimento');
    Inputmask("99/99/9999").mask(dataCriacao);
    Inputmask("99/99/9999").mask(dataVencimento);
  });

  $('#modalDevolucao').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget);
    var empId = button.data('empid');
    var modal = $(this);
    modal.find('#emprestimoIdDevolucao').val(empId);
  });

  $('#modalRenovacao').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget);
    var empId = button.data('empid');
    var modal = $(this);
    modal.find('#emprestimoIdRenovacao').val(empId);
  });
</script>

</body>
</html>
