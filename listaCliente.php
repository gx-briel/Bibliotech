<?php 
require 'conexao.php';
session_start(); 

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit; 
}

$pesquisa = isset($_POST['pesquisa']) ? $_POST['pesquisa'] : '';

if ($pesquisa != '') {
    $consulta = "SELECT * FROM clientes WHERE nomeCliente LIKE ? AND removidoEm IS NULL";
} else {
    $consulta = "SELECT * FROM clientes WHERE removidoEm IS NULL";
}

$stmt = mysqli_prepare($conexao, $consulta);
if ($pesquisa != '') {
    mysqli_stmt_bind_param($stmt, 's', $pesquisa_com_criterio);
    $pesquisa_com_criterio = "%$pesquisa%";
}
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Listagem de Clientes</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      background-color: rgb(238, 255, 235);
      padding-bottom: 100px;
    }

    .table {
      margin-top: 20px;
    }

    .table th, .table td {
      border: 1px solid #ccc !important;
      vertical-align: middle;
      padding: 12px 15px;
    }

    .table thead th {
      background-color: #343a40;
      color: white;
    }

    .btn-danger {
      background-color: #dc3545;
      border-color: #dc3545;
    }
    .btn-primary {
      background-color: #ffcc00;
      border-color: #ffcc00;
    }
    .btn-danger:hover {
      background-color: #c82333;
      border-color: #bd2130;
    }
    .btn-primary:hover {
      background-color: #e0a800;
      border-color: #d39e00;
    }

    .btn-group {
      display: flex;
      justify-content: space-around;
      gap: 10px;
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 1050;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
      background-color: white;
      padding: 20px;
      border-radius: 5px;
      position: relative;
      top: 30%;
      left: 50%;
      transform: translateX(-50%);
      width: 80%;
      max-width: 400px;
      text-align: center;
    }

    .modal-header {
      font-size: 1.5em;
    }

    .modal-footer {
      display: flex;
      justify-content: space-around;
    }

    @media (max-width: 768px) {
      .table-responsive {
        overflow-x: auto;
      }
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

  <script type="text/javascript">
    function mostrarModal(id) {
      document.getElementById('modal').style.display = 'block';
      document.getElementById('confirmarRemocao').onclick = function() {
        document.getElementById('formRemove' + id).submit();
      };
    }

    function fecharModal() {
      document.getElementById('modal').style.display = 'none';
    }

    function aplicarMascaraCPF(cpf) {
      return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
    }

    function aplicarMascaraTelefone(telefone) {
      return telefone.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    }

    function formatarTabela() {
      var cpfs = document.querySelectorAll('.cpf');
      var telefones = document.querySelectorAll('.telefone');

      cpfs.forEach(function(cpf) {
        cpf.textContent = aplicarMascaraCPF(cpf.textContent);
      });

      telefones.forEach(function(telefone) {
        telefone.textContent = aplicarMascaraTelefone(telefone.textContent);
      });
    }

    window.onload = formatarTabela;
  </script>
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
      <li class="nav-item"><a class="nav-link" href="acervo.php">Listar Livros</a></li>
      <li class="nav-item"><a class="nav-link" href="criaEmprestimo.php">Criar Empréstimo</a></li>
    </ul>
  </div>
</nav>

<div class="container mt-5">
  <h2 class="mb-4">Listagem de Clientes</h2>
  <form method="POST" action=""> 
    <div class="input-group mb-3">
      <input type="text" class="form-control" placeholder="Buscar Clientes" name="pesquisa" value="<?= htmlspecialchars($pesquisa) ?>">
      <div class="input-group-append">
        <button class="btn btn-info" type="submit">Buscar</button>
      </div>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-striped table-bordered">
      <thead>
        <tr>
          <th>Código</th>
          <th>Nome Cliente</th>
          <th>CPF</th>
          <th>Telefone</th>
          <th>Endereço</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
      <?php
        if (mysqli_num_rows($resultado) > 0) {
          while ($clientes = mysqli_fetch_assoc($resultado)) {
        ?>
        <tr>
          <td><?= $clientes['id']; ?></td>
          <td><?= $clientes['nomeCliente']; ?></td>
          <td class="cpf"><?= $clientes['cpf']; ?></td>
          <td class="telefone"><?= $clientes['telefone']; ?></td>
          <td>
            <strong>Rua:</strong> <?= $clientes['rua']; ?>, Nº <?= $clientes['numero']; ?><br>
            <strong>Bairro:</strong> <?= $clientes['bairro']; ?><br>
            <strong>Cidade:</strong> <?= $clientes['cidade']; ?> - <?= $clientes['estado']; ?><br>
            <strong>CEP:</strong> <?= $clientes['cep']; ?>
          </td>
          <td>
            <form id="formRemove<?= $clientes['id']; ?>" method="POST" action="removeCliente.php">
              <input type="hidden" name="id" value="<?= $clientes['id']; ?>">
              <div class="btn-group">
                <a href="editarCliente.php?id=<?= $clientes['id']; ?>" class="btn btn-primary btn-sm text-dark">Editar</a>
                <button type="button" class="btn btn-danger btn-sm" onclick="mostrarModal(<?= $clientes['id']; ?>)">Remover</button>
              </div>
            </form>
          </td>
        </tr>
        <?php
          }
        } else {
          echo "<tr><td colspan='6' class='text-center'>Nenhum Cliente Cadastrado.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<div id="modal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h4>Confirmação</h4>
    </div>
    <div class="modal-body">
      <p>Tem certeza que deseja remover este cliente?</p>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="fecharModal()">Cancelar</button>
      <button id="confirmarRemocao" class="btn btn-danger">Remover</button>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
