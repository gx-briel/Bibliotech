<?php
require 'conexao.php';
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

$pesquisa = isset($_POST['pesquisa']) ? $_POST['pesquisa'] : '';

if ($pesquisa != '') {
    $consulta = "SELECT * FROM livros WHERE titulo LIKE ? AND removidoEm IS NULL";
} else {
    $consulta = "SELECT * FROM livros WHERE removidoEm IS NULL";
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
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Acervo de Livros</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
    .form-control {
      border-radius: 10px;
    }
    .btn-primary {
      background-color: #1c0e3f;
      border-color: #1c0e3f;
    }
    .btn-primary:hover {
      background-color: #16254a;
      border-color: #16254a;
    }
    table th, table td {
      border: 1px solid #ccc !important;
    }
    .table thead th {
      background-color: #343a40;
      color: white;
    }
    @media (max-width: 768px) {
      .content {
        margin-left: 0 !important;
      }
      .sidebar {
        width: 200px;
      }
    }
    @media (max-width: 576px) {
      .btn {
        width: 100%;
      }
    }
  </style>
</head>
<body>


<div class="wrapper">
  <!-- Sidebar -->
  <nav id="sidebar" class="sidebar">
    <div class="sidebar-header"><a href="indexlogado.php" style="color: #fff; text-decoration: none;">Bibliotech</a></div>
    <button class="toggle-btn btn btn-sm btn-warning w-100 mb-2" onclick="hideSidebar()">← Recolher</button>
    <ul class="nav-links">
      <li><a href="cadastroCliente.php">Cadastrar Cliente</a></li>
      <li><a href="cadastroLivro.php">Cadastrar Livro</a></li>
      <li><a href="criaEmprestimo.php">Criar Empréstimo</a></li>
    </ul>
    <div class="logout-btn">
      <a href="logout.php" class="btn btn-danger w-100">🚪 Sair</a>
    </div>
  </nav>
  <button id="showSidebarBtn" class="show-sidebar-btn">☰</button>
  <div class="content">
    <div class="container mt-5">
      <h2 class="mb-4">Acervo de Livros</h2>
      <form method="POST" action="" class="mb-4">
        <div class="input-group">
          <input type="text" class="form-control" placeholder="Buscar livro" name="pesquisa" value="<?= htmlspecialchars($pesquisa) ?>">
          <div class="input-group-append">
            <button class="btn btn-info" type="submit">Buscar</button>
          </div>
        </div>
      </form>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Título</th>
              <th>ISBN</th>
              <th>Editora</th>
              <th>Lançamento</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if (mysqli_num_rows($resultado) > 0) {
              while ($livros = mysqli_fetch_assoc($resultado)) {
            ?>
                <tr>
                  <td><?= htmlspecialchars($livros['titulo']); ?></td>
                  <td><?= htmlspecialchars($livros['isbn']); ?></td>
                  <td><?= htmlspecialchars($livros['editora']); ?></td>
                  <td>
                    <?php
                    $data = DateTime::createFromFormat('Y-m-d', $livros['lancamento']);
                    echo $data ? $data->format('d/m/Y') : '';
                    ?>
                  </td>
                  <td>
                    <a href="editarLivro.php?id=<?= $livros['ID']; ?>" class="btn btn-warning btn-sm mb-1">Editar</a>
                    <a href="#" class="btn btn-danger btn-sm mb-1" data-toggle="modal" data-target="#removerModal<?= $livros['ID']; ?>">Remover</a>
                  </td>
                </tr>

                <!-- Modal de confirmação -->
                <div class="modal fade" id="removerModal<?= $livros['ID']; ?>" tabindex="-1" role="dialog" aria-labelledby="removerModalLabel<?= $livros['ID']; ?>" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="removerModalLabel<?= $livros['ID']; ?>">Confirmar Remoção</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        Tem certeza de que deseja remover este livro? A data de remoção será registrada.
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <a href="removerLivro.php?id=<?= $livros['ID']; ?>" class="btn btn-danger">Confirmar Remoção</a>
                      </div>
                    </div>
                  </div>
                </div>
            <?php
              }
            } else {
              echo "<tr><td colspan='5' class='text-center'>Nenhum Livro Cadastrado.</td></tr>";
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

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
