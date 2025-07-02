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
  <style>
    body {
      background-color: rgb(238, 255, 235);
      padding-bottom: 60px;
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
      <li class="nav-item"><a class="nav-link" href="acervo.php">Listar Livros</a></li>
      <li class="nav-item"><a class="nav-link" href="criaEmprestimo.php">Criar Empréstimo</a></li>
    </ul>
  </div>
</nav>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-8 col-sm-12">
      <h2>Cadastro de Livros</h2>
      <br><br>
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
