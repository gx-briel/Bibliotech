<?php
session_start();
$mensagem = $_SESSION['mensagem'] ?? null;
$tipo = $_SESSION['tipo'] ?? null;
unset($_SESSION['mensagem'], $_SESSION['tipo']);

// Gerar o número do usuário
require("conexao.php");
$sql = "SELECT MAX(usuario) AS max_usuario FROM usuarios";
$result = mysqli_query($conexao, $sql);
$row = mysqli_fetch_assoc($result);
$ultimoUsuario = $row['max_usuario'] ?? 0;
$novoUsuario = str_pad($ultimoUsuario + 1, 4, '0', STR_PAD_LEFT); // Gera o próximo número de usuário com 4 dígitos (0001, 0002, ...)
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Cadastro de Usuários</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background-color: #f8f9fa;
    }

    .navbar {
      background-color: #1c0e3f;
    }

    .navbar-brand {
      color: white;
      font-weight: bold;
    }

    .container {
      max-width: 600px;
      margin-top: 50px;
    }

    .form-group label {
      font-weight: bold;
    }

    .btn-info {
      background-color: #1c0e3f;
      border-color: #1c0e3f;
    }

    .btn-info:hover {
      background-color: #1565c0;
      border-color: #1565c0;
    }

    .alert {
      margin-top: 20px;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <a class="navbar-brand" href="indexlogado.php">Bibliotech</a>
</nav>

<div class="container">
  <h2 class="text-center">Cadastro de Usuários</h2>
  <form method="POST" action="insereUsuario.php" autocomplete="off">
    
    <!-- Mensagem de Sucesso ou Erro -->
    <?php if ($mensagem): ?>
      <div class="alert alert-<?php echo $tipo === 'sucesso' ? 'success' : 'danger'; ?>" role="alert">
        <?php echo $mensagem; ?>
      </div>
    <?php endif; ?>

    <div class="form-group">
      <label for="user_input">Usuário:</label>
      <input type="text" class="form-control" id="user_input" name="user_input" value="<?php echo $novoUsuario; ?>" readonly disabled>
    </div>
    
    <div class="form-group">
      <label for="nome">Nome Completo:</label>
      <input type="text" class="form-control" id="nome" name="nome" required maxlength="300">
    </div>

    <div class="form-group">
      <label for="password_input">Senha:</label>
      <input type="password" class="form-control" id="password_input" name="password_input" required maxlength="20" autocomplete="new-password"> 
    </div>
    
    <div class="form-group">
      <label for="email">Email:</label>
      <input type="email" class="form-control" id="email" name="email" maxlength="100">
    </div>
    
    <div class="form-group">
      <label for="nascimento">Data de Nascimento:</label>
      <input type="date" class="form-control" id="nascimento" name="nascimento">
    </div>
    
    <button type="submit" class="btn btn-info btn-block">Cadastrar Usuário</button>
    <a href="index.php" class="btn btn-secondary btn-block mt-2">Voltar</a>
  </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php if ($mensagem): ?>
  <script>
    Swal.fire({
      title: '<?php echo $mensagem; ?>',
      icon: '<?php echo $tipo === 'sucesso' ? 'success' : 'error'; ?>',
      showConfirmButton: true,
    });
  </script>
<?php endif; ?>

</body>
</html>
