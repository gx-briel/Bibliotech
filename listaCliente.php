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
  <style>
    body {
      background-color: rgb(238, 255, 235);
      margin: 0;
      overflow-x: hidden;
      padding-bottom: 100px;
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
      box-shadow: 2px 0 8px rgba(28,14,63,0.08);
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
      letter-spacing: 1px;
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
      transition: color 0.2s;
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
      padding: 2rem 0;
      flex: 1;
      transition: margin-left 0.3s;
      min-height: 100vh;
    }
    .sidebar.hidden ~ .content {
      margin-left: 0;
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
      .content {
        margin-left: 0 !important;
      }
      .sidebar {
        width: 200px;
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


<div class="wrapper">
  <!-- Sidebar -->
  <nav id="sidebar" class="sidebar">
    <div class="sidebar-header"><a href="indexlogado.php" style="color: #fff; text-decoration: none;"><i class="fa-solid fa-book-open-reader"></i> Bibliotech</a></div>
    <button class="toggle-btn btn btn-sm btn-warning w-100 mb-2" onclick="hideSidebar()">← Recolher</button>
    <ul class="nav-links">
      <li><a href="cadastroCliente.php">Cadastrar Clientes</a></li>
      <li><a href="cadastroLivro.php">Cadastrar Livro</a></li>
      <li><a href="acervo.php">Acervo de Livros</a></li>
      <li><a href="criaEmprestimo.php">Criar Empréstimo</a></li>
    </ul>
    <div class="logout-btn">
      <a href="logout.php" class="btn btn-danger w-100">🚪 Sair</a>
    </div>
  </nav>
  <button id="showSidebarBtn" class="show-sidebar-btn">☰</button>
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
