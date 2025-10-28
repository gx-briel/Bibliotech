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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <?php include 'components/sidebar-style.php'; ?>
  
  <style>
    @media (max-width: 768px) {
      .table-container {
        width: 100vw;
        min-width: 600px;
        overflow-x: auto;
      }
      .table {
        min-width: 600px;
      }
    }
    body {
      background-color: rgb(238, 255, 235);
      margin: 0;
      overflow-x: hidden;
      padding-bottom: 100px;
      font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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


<?php include 'components/sidebar-logado.php'; ?>

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
      <div class="table-container table-responsive mt-4">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>ID</th>
              <th>Título</th>
              <th>ISBN</th>
              <th>Editora</th>
              <th>Lançamento</th>
              <th>Disponível</th>
              <th>Editar</th>
              <th>Remover</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if (mysqli_num_rows($resultado) > 0) {
              while ($livros = mysqli_fetch_assoc($resultado)) {
            ?>
                <tr>
                  <td><?= htmlspecialchars($livros['ID']); ?></td>
                  <td><?= htmlspecialchars($livros['titulo']); ?></td>
                  <td><?= htmlspecialchars($livros['isbn']); ?></td>
                  <td><?= htmlspecialchars($livros['editora']); ?></td>
                  <td>
                    <?php
                    $data = DateTime::createFromFormat('Y-m-d', $livros['lancamento']);
                    echo $data ? $data->format('d/m/Y') : '';
                    ?>
                  </td>
                  <td><?= $livros['disponivel'] ? 'Sim' : 'Não'; ?></td>
                  <td>
                    <a href="editarLivro.php?id=<?= $livros['ID']; ?>" class="btn btn-warning btn-sm mb-1">Editar</a>
                  </td>
                  <td>
                    <a href="#" class="btn btn-danger btn-sm mb-1" data-toggle="modal" data-target="#removerModal<?= $livros['ID']; ?>">Remover</a>
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
                  </td>
                </tr>
            <?php
              }
            } else {
              echo "<tr><td colspan='6' class='text-center'>Nenhum Livro Cadastrado.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include 'components/sidebar-script.php'; ?>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>