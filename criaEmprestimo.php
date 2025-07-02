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
  <style>
    body {
      background-color: #e8f5e9;
    }

    footer {
      position: fixed;
      bottom: 0;
      width: 100%;
      z-index: 1030;
    }

    .navbar {
      background-color: #388e3c;
    }
    .navbar-brand, .nav-link {
      color: white !important;
      font-weight: bold;
    }
    .navbar-nav .nav-link:hover {
      color: #ffcc00 !important;
    }


    .btn-info {
      background-color: #388e3c; 
      border: none;
      width: 100%; 
    }

    .btn-info:hover {
      background-color: #2c6b29; 
    }

    .form-control.is-invalid {
      border-color: #dc3545;
      padding-right: calc(1.5em + .75rem);
      background-image: url('https://cdn-icons-png.flaticon.com/512/198/198622.png');
      background-repeat: no-repeat;
      background-position: right calc(.375em + .1875rem) center;
      background-size: calc(.75em + .375rem) calc(.75em + .375rem);
    }

    .invalid-feedback {
      display: block;
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
    <h2>Criar Empréstimo</h2>
    <br><br>
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

