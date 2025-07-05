<?php
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
  <title>Cadastro de Livros</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    body {
      background-color: rgb(238, 255, 235);
      margin: 0;
      overflow-x: hidden;
      padding-bottom: 60px;
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
    @media (max-width: 768px) {
      .content {
        margin-left: 0 !important;
      }
      .sidebar {
        width: 200px;
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
      <li><a href="acervo.php">Acervo de Livros</a></li>
      <li><a href="cadastroCliente.php">Cadastrar Clientes</a></li>
      <li><a href="listaCliente.php">Listar Clientes</a></li>
      <li><a href="criaEmprestimo.php">Criar Empréstimo</a></li>
    </ul>
    <div class="logout-btn">
      <a href="logout.php" class="btn btn-danger w-100">🚪 Sair</a>
    </div>
  </nav>
  <button id="showSidebarBtn" class="show-sidebar-btn">☰</button>
  <div class="content">
    <div class="container mt-5">
      <div class="row justify-content-center">
        <div class="col-md-8 col-sm-12">
          <div class="card shadow p-4">
            <h2 class="mb-4 text-center">Cadastro de Livros</h2>
            <form method="POST" action="cadastroLivro.php">
              <div class="form-group">
                <label for="titulo">Título do Livro:</label>
                <input type="text" class="form-control" id="titulo" name="titulo" required>
              </div>
              <div class="form-group">
                <label for="isbn">Isbn:</label>
                <input type="text" class="form-control" id="isbn" name="isbn">
              </div>
              <div class="form-group">
                <label for="editora">Editora:</label>
                <input type="text" class="form-control" id="editora" name="editora">
              </div>
              <div class="form-group">
                <label for="lancamento">Data de Lançamento:</label>
                <input type="date" class="form-control" id="lancamento" name="lancamento">
              </div>
              <button type="submit" class="btn btn-info btn-block">Cadastrar Livro</button>
            </form>
          </div>
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

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<div class="modal fade" id="feedbackModal" tabindex="-1" role="dialog" aria-labelledby="feedbackModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="feedbackModalLabel">Cadastro de Livro</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<?php
require("conexao.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $isbn = $_POST['isbn'];
    $editora = $_POST['editora'];
    $lancamento = $_POST['lancamento'];

    $inserelivro = "INSERT INTO livros(titulo, isbn, editora, lancamento) VALUES ('$titulo','$isbn','$editora','$lancamento')";

    $operacaoSQL = mysqli_query($conexao, $inserelivro);
    if (mysqli_affected_rows($conexao) != 0) {
        echo "<script>
                $(document).ready(function() {
                    $('#feedbackModal .modal-body').text('Livro cadastrado com Sucesso!');
                    $('#feedbackModal').modal('show');
                });
              </script>";
    } else {
        echo "<script>
                $(document).ready(function() {
                    $('#feedbackModal .modal-body').text('O Livro não foi cadastrado com Sucesso!');
                    $('#feedbackModal').modal('show');
                });
              </script>";
    }
}
?>

</body>
</html>
