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
      padding-bottom: 100px; /* espaço pro footer fixo */
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

    /* Tabela com linhas destacadas */
    table th, table td {
      border: 1px solid #ccc !important;
    }

    .table thead th {
      background-color: #343a40;
      color: white;
    }

    @media (max-width: 576px) {
      .btn {
        width: 100%;
      }
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
      <li class="nav-item"><a class="nav-link" href="criaEmprestimo.php">Criar Empréstimo</a></li>
    </ul>
  </div>
</nav>

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

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
