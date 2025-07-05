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
      background: linear-gradient(180deg, #388e3c 60%, #e8f5e9 100%);
      color: white;
      min-height: 100vh;
      transition: transform 0.3s ease;
      position: fixed;
      z-index: 999;
      box-shadow: 2px 0 8px rgba(56,142,60,0.08);
    }
    .sidebar.hidden {
      transform: translateX(-100%);
    }
    .sidebar .sidebar-header {
      padding: 1rem;
      font-size: 1.5rem;
      font-weight: bold;
      background-color: #256029;
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
      color: #c8e6c9;
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
    @media (max-width: 768px) {
      .content {
        margin-left: 0 !important;
      }
      .sidebar {
        width: 200px;
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

<div class="wrapper">
  <!-- Sidebar -->
  <nav id="sidebar" class="sidebar">
    <div class="sidebar-header"><a href="indexlogado.php" style="color: #fff; text-decoration: none;"><i class="fa-solid fa-book-open-reader"></i> Bibliotech</a></div>
    <button class="toggle-btn btn btn-sm btn-warning w-100 mb-2" onclick="hideSidebar()">← Recolher</button>
    <ul class="nav-links">
      <li><a href="cadastroCliente.php">Cadastrar Clientes</a></li>
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

