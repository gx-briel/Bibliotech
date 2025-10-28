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
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <?php include 'components/sidebar-style.php'; ?>
  
  <style>
    body {
      background-color: rgb(238, 255, 235);
      margin: 0;
      overflow-x: hidden;
      padding-bottom: 60px;
      font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .content {
      margin-left: 250px;
      padding: 2rem;
      transition: margin-left 0.3s;
    }

    .sidebar.hidden ~ .content {
      margin-left: 0;
    }

    @media (max-width: 768px) {
      .content {
        margin-left: 0 !important;
        padding: 1rem;
      }
    }
  </style>
</head>
<body>


<?php include 'components/sidebar-logado.php'; ?>

  <!-- Conteúdo principal -->
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

<?php include 'components/sidebar-script.php'; ?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
