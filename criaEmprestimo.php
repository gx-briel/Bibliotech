<?php
require 'conexao.php';
session_start(); 
if (!isset($_SESSION['id'])) {

    header('Location: index.php');
    exit; 
}

$erros = array(
    'titulo' => '',
    'cliente' => '',
    'vencimento' => ''
);

$codigo_livro = '';
$codigo_cliente = '';
$vencimento = '';

$clientes = $conexao->query("SELECT id, nomeCliente FROM clientes where removidoEm is null ORDER BY nomeCliente");
$livros = $conexao->query("SELECT ID, titulo FROM livros WHERE disponivel = 1 AND removidoEm is null ORDER BY titulo");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $codigo_livro = $_POST['titulo']; 
    $codigo_cliente = $_POST['cliente']; 
    $vencimento = $_POST['vencimento'];

    $result_cliente = $conexao->query("SELECT * FROM clientes WHERE id = $codigo_cliente");
    if ($result_cliente->num_rows == 0) {
        $erros['cliente'] = "Cliente não encontrado.";
    }

    $result_livro = $conexao->query("SELECT * FROM livros WHERE ID = $codigo_livro AND disponivel = 1");
    if ($result_livro->num_rows == 0) {
        $erros['titulo'] = "Livro não encontrado ou não está disponível para empréstimo.";
    }

    if (empty($erros['cliente']) && empty($erros['titulo'])) {

        $sql_emprestimo = "INSERT INTO emprestimo (idCliente, idLivro, criadoEm, renovadoEm, vencimento, devolvidoEm)
                           VALUES (?, ?, NOW(), NULL, ?, NULL)";

        $stmt = $conexao->prepare($sql_emprestimo);
        $stmt->bind_param("iis", $codigo_cliente, $codigo_livro, $vencimento);
        
        if ($stmt->execute()) {

            $conexao->query("UPDATE livros SET disponivel = 0 WHERE ID = $codigo_livro");
            echo '<div class="alert alert-success mt-3">Empréstimo realizado com sucesso.</div>';
            header("Location: listaEmprestimoAtivo.php");
            exit;
        } else {
            echo '<div class="alert alert-danger mt-3">Erro ao realizar empréstimo: ' . $conexao->error . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Criar um Empréstimo</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <?php include 'components/sidebar-style.php'; ?>
  
  <style>
    body {
      background-color: rgb(238, 255, 235);
      margin: 0;
      overflow-x: hidden;
      padding-bottom: 100px;
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
    /* Sobrescrever o gradiente da sidebar para esta página */
    .sidebar {
      background: linear-gradient(170deg, #08470bff 40%, #3fac48ff 100%);
    }
    .sidebar .sidebar-header {
      background-color: #08470bff;
    }

    /*Botao de criar*/
    .btn-info {
      position: relative;
      overflow: hidden;
      background: linear-gradient(170deg, #08470bff 40%, #3fac48ff 100%);
      border: none;
      width: 100%;
    }
    .btn-info::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
      transition: left 0.5s;
    }
    .btn-info:hover::before { 
      left: 100%; 
    }
    .btn-info:hover {
      background: linear-gradient(170deg, #3fac48ff 0%, #08470bff 100%);
      color: #fff;
    }

    @media (max-width: 768px) {
      .content {
        margin-left: 0 !important;
        padding: 1rem;
      }
    }
    @media (max-width: 576px) {
      .container {
          padding: 15px;
      }
      .form-group {
          margin-bottom: 1rem;
      }
    }
  </style>
</head>

<?php include 'components/sidebar-logado.php'; ?>

  <!-- Conteúdo principal -->
  <div class="content">
    <div class="container mt-5">
      <h2 class="mb-4">Criar Empréstimo</h2>
      <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
          <label for="titulo">Selecione o livro:</label>
          <select class="form-control <?php echo !empty($erros['titulo']) ? 'is-invalid' : ''; ?>" id="titulo" name="titulo" required>
            <option value="">Selecione um livro</option>
            <?php while ($livro = $livros->fetch_assoc()): ?>
              <option value="<?php echo $livro['ID']; ?>" <?php echo $codigo_livro == $livro['ID'] ? 'selected' : ''; ?>>
                <?php echo $livro['titulo']; ?>
              </option>
            <?php endwhile; ?>
          </select>
          <div class="invalid-feedback"><?php echo $erros['titulo']; ?></div>
        </div>
        <div class="form-group">
          <label for="cliente">Selecione o cliente:</label>
          <select class="form-control <?php echo !empty($erros['cliente']) ? 'is-invalid' : ''; ?>" id="cliente" name="cliente" required>
            <option value="">Selecione um cliente</option>
            <?php while ($cliente = $clientes->fetch_assoc()): ?>
              <option value="<?php echo $cliente['id']; ?>" <?php echo $codigo_cliente == $cliente['id'] ? 'selected' : ''; ?>>
                <?php echo $cliente['nomeCliente']; ?>
              </option>
            <?php endwhile; ?>
          </select>
          <div class="invalid-feedback"><?php echo $erros['cliente']; ?></div>
        </div>
        <div class="form-group">
          <label for="vencimento">Data para devolução:</label>
          <input type="date" class="form-control" id="vencimento" name="vencimento" value="<?php echo $vencimento; ?>" required>
        </div>
        <button type="submit" class="btn btn-info">Criar Empréstimo</button>
      </form>
    </div>
  </div>
</div>
</div>

<?php include 'components/sidebar-script.php'; ?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>