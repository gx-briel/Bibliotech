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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  
  <?php include 'components/sidebar-style.php'; ?>
  
  <style>
    /* Ajuste de largura para colunas de CPF e Telefone */
    .table th.cpf-col, .table td.cpf-col {
      min-width: 140px;
      max-width: 180px;
      white-space: nowrap;
    }
    .table th.tel-col, .table td.tel-col {
      min-width: 130px;
      max-width: 170px;
      white-space: nowrap;
    }
    body {
      background-color: rgb(238, 255, 235);
      margin: 0;
      overflow-x: hidden;
      padding-bottom: 100px;
    }
    .wrapper {
      display: flex;
    }
    .content {
      margin-left: 250px;
      padding: 2rem 0;
      flex: 1;
      transition: margin-left 0.3s;
      min-height: 100vh;
    }
    .sidebar.hidden ~ .content {
      margin-left: 0;
    }
    .table-container {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }
    table {
      width: 100%;
    }
    .table-striped tbody tr:nth-of-type(odd) {
      background-color: rgba(0, 0, 0, 0.05);
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
      .content {
        margin-left: 0 !important;
      }
      .table-responsive {
        overflow-x: auto;
      }
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


<?php include 'components/sidebar-logado.php'; ?>

  <!-- Conteúdo principal -->
  <div class="content">
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
      <div class="table-container table-responsive mt-4">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nome Cliente</th>
              <th class="cpf-col">CPF</th>
              <th class="tel-col">Telefone</th>
              <th>Endereço</th>
              <th>Editar</th>
              <th>Remover</th>
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
              <td class="cpf-col cpf"><?= $clientes['cpf']; ?></td>
              <td class="tel-col telefone"><?= $clientes['telefone']; ?></td>
              <td>
                <strong>Rua:</strong> <?= $clientes['rua']; ?>, Nº <?= $clientes['numero']; ?><br>
                <strong>Bairro:</strong> <?= $clientes['bairro']; ?><br>
                <strong>Cidade:</strong> <?= $clientes['cidade']; ?> - <?= $clientes['estado']; ?><br>
                <strong>CEP:</strong> <?= $clientes['cep']; ?>
              </td>
              <td>
                <a href="editarCliente.php?id=<?= $clientes['id']; ?>" class="btn btn-primary btn-sm text-dark">Editar</a>
              </td>
              <td>
                <form id="formRemove<?= $clientes['id']; ?>" method="POST" action="removeCliente.php" style="display:inline;">
                  <input type="hidden" name="id" value="<?= $clientes['id']; ?>">
                  <button type="button" class="btn btn-danger btn-sm" onclick="mostrarModal(<?= $clientes['id']; ?>)">Remover</button>
                </form>
                <!-- Modal de confirmação -->
                <div class="modal" id="modalRemover<?= $clientes['id']; ?>">
                  <div class="modal-content">
                    <div class="modal-header">Remover Cliente</div>
                    <div class="modal-body">Tem certeza que deseja remover este cliente?</div>
                    <div class="modal-footer">
                      <button class="btn btn-secondary" onclick="fecharModal(<?= $clientes['id']; ?>)">Cancelar</button>
                      <button class="btn btn-danger" onclick="confirmarRemocao(<?= $clientes['id']; ?>)">Remover</button>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
            <?php
              }
            } else {
              echo "<tr><td colspan='7' class='text-center'>Nenhum Cliente Cadastrado.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include 'components/sidebar-script.php'; ?>

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
