<?php
require 'conexao.php';
session_start(); 

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit; 
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Empréstimos à Vencer</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      background-color: rgb(238, 255, 235);
      padding-bottom: 100px;
    }
    .container {
      max-width: 1200px;
    }
    table {
      margin-top: 20px;
    }
    .form-inline input {
      margin-right: 10px;
    }
    .form-inline button {
      margin-top: 10px;
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
  <h2>Listagem de Empréstimos à Vencer Hoje</h2>
  
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

  <div class="table-responsive">
    <table class="table table-bordered mt-4">
      <thead>
        <tr>
          <th>ID</th>
          <th>Cliente</th>
          <th>Livro</th>
          <th>Data de Criação</th>
          <th>Data para Devolução</th>
          <th>Empréstimo Ativo</th>
          <th>Devolver</th>
          <th>Renovar</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $clienteBusca = $_GET['cliente'] ?? '';
        $livroBusca = $_GET['livro'] ?? '';

        $consulta = "SELECT emp.id as empId, cli.nomeCliente, li.titulo, emp.criadoEm, li.ID as livroId, emp.ativo, emp.vencimento
                    FROM emprestimo as emp
                    JOIN clientes as cli ON emp.idCliente = cli.id
                    JOIN livros as li ON emp.idLivro = li.ID
                    WHERE emp.vencimento = CURDATE() AND emp.ativo = 1";

        if ($clienteBusca) {
            $consulta .= " AND cli.nomeCliente LIKE '%$clienteBusca%'";
        }
        if ($livroBusca) {
            $consulta .= " AND li.titulo LIKE '%$livroBusca%'";
        }

        $executaConsulta = mysqli_query($conexao, $consulta);
        if (mysqli_num_rows($executaConsulta) > 0) {
            foreach ($executaConsulta as $emprestimos) {
        ?>
          <tr>
            <td><?= $emprestimos['empId']; ?></td>
            <td><?= $emprestimos['nomeCliente']; ?></td>
            <td><?= $emprestimos['titulo']; ?></td>
            <td><?= date("d/m/Y", strtotime($emprestimos['criadoEm'])); ?></td>
            <td><?= date("d/m/Y", strtotime($emprestimos['vencimento'])); ?></td>
            <td><?= $emprestimos['ativo'] ? 'Sim' : 'Não'; ?></td>
            <td>
              <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#confirmarDevolucaoModal" 
                data-emprestimo-id="<?= $emprestimos['empId']; ?>">
                Devolução
              </button>
            </td>
            <td>
              <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalRenovacao" 
                data-empid="<?= $emprestimos['empId']; ?>">
                Renovar
              </button>
            </td>
          </tr>
        <?php
            }
        } else {
            echo "<tr><td colspan='8' class='text-center'>Nenhum Empréstimo à Vencer Hoje Encontrado.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal de Renovação -->
<div class="modal fade" id="modalRenovacao" tabindex="-1" role="dialog" aria-labelledby="modalRenovacaoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" action="renovaEmprestimo.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalRenovacaoLabel">Confirmar Renovação</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="emprestimoId" id="emprestimoIdRenovacao">
          <div class="form-group">
            <label for="novaDataRenovacao">Nova Data de Vencimento:</label>
            <input type="date" class="form-control" name="novaDataRenovacao" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Renovar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal de Devolução -->
<div class="modal fade" id="confirmarDevolucaoModal" tabindex="-1" role="dialog" aria-labelledby="confirmarDevolucaoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" action="devolveLivro.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmarDevolucaoLabel">Confirmar Devolução</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Tem certeza que deseja devolver este empréstimo?</p>
          <input type="hidden" name="emprestimoId" id="emprestimoIdDevolucao">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-info">Confirmar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
  $('#modalRenovacao').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var emprestimoId = button.data('empid');
    var modal = $(this);
    modal.find('#emprestimoIdRenovacao').val(emprestimoId);
  });

  $('#confirmarDevolucaoModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var emprestimoId = button.data('emprestimo-id');
    var modal = $(this);
    modal.find('#emprestimoIdDevolucao').val(emprestimoId);
  });
</script>

</body>
</html>
