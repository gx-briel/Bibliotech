<?php
require 'conexao.php';
session_start(); 


if (!isset($_SESSION['id'])) {

    header('Location: index.php');
    exit;
}
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $consulta = "SELECT * FROM livros WHERE ID = $id";
    $resultado = mysqli_query($conexao, $consulta);
    $livro = mysqli_fetch_assoc($resultado);
} else {

    header("Location: acervo.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $isbn = $_POST['isbn'];
    $editora = $_POST['editora'];
    $lancamento = $_POST['lancamento'];
    $disponivel = isset($_POST['disponivel']) ? 1 : 0;

    $atualizarLivro = "UPDATE livros SET titulo='$titulo', isbn='$isbn', editora='$editora', lancamento='$lancamento', disponivel='$disponivel' WHERE ID=$id";
    mysqli_query($conexao, $atualizarLivro);

    header("Location: acervo.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Editar Livro</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    footer {
      position: fixed;
      bottom: 0;
      width: 100%;
      z-index: 1030;
    }

    body {
      background-color: rgb(238, 255, 235);
      padding-bottom: 60px; 
    }

    .navbar-nav {
      display: flex;
      justify-content: flex-start; 
    }

    .navbar-toggler-icon {
      background-color: #ffffff;
    }

    .navbar-collapse {
      background-color: #1c0e3f; 
    }

    .navbar-nav .nav-link {
      color: white !important;
    }

    .navbar-nav .nav-link:hover {
      color: #ffcc00;
    }

    .navbar-toggler {
      background-color: #1c0e3f; 
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
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #1c0e3f;">
  <a class="navbar-brand" href="indexlogado.php" style="color: white; font-weight: bold;">Bibliotech</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span> 
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" href="acervo.php" style="color: white; font-weight: bold;">Acervo de Livros</a>
      </li>
    </ul>
  </div>
</nav>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-8 col-sm-12">
      <h2>Editar Livro</h2>
      <br><br>
      <form id="form-editar-livro" method="POST" action="">

        <div class="form-group">
          <label for="titulo">Título do Livro:</label>
          <input type="text" class="form-control" id="titulo" name="titulo" value="<?= $livro['titulo']; ?>" required>
        </div>

        <div class="form-group">
          <label for="isbn">ISBN:</label>
          <input type="text" class="form-control" id="isbn" name="isbn" value="<?= $livro['isbn']; ?>" required>
        </div>

        <div class="form-group">
          <label for="editora">Editora:</label>
          <input type="text" class="form-control" id="editora" name="editora" value="<?= $livro['editora']; ?>" required>
        </div>

        <div class="form-group">
          <label for="lancamento">Data de Lançamento:</label>
          <input type="date" class="form-control" id="lancamento" name="lancamento" value="<?= $livro['lancamento']; ?>" required>
        </div>

        <div class="form-check">
          <input type="checkbox" class="form-check-input" id="disponivel" name="disponivel" <?= $livro['disponivel'] ? 'checked' : ''; ?>>
          <label class="form-check-label" for="disponivel">Disponível</label>
        </div>
        <button type="button" class="btn btn-primary mt-3" data-toggle="modal" data-target="#confirmacaoModal">Atualizar Livro</button>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="confirmacaoModal" tabindex="-1" role="dialog" aria-labelledby="confirmacaoModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmacaoModalLabel">Confirmar Atualização</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Tem certeza de que deseja atualizar as informações deste livro?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary" form="form-editar-livro">Confirmar Atualização</button>
      </div>
    </div>
  </div>
</div>

<footer class="footer bg-light mt-5">
  <div class="container text-center">
    <span class="text-muted">© 2024 Bibliotech. Todos os direitos reservados.</span>
  </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
