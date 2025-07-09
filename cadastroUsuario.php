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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <?php include 'components/sidebar-style.php'; ?>
  
  <style>
    body {
      background-color: rgb(216, 107, 107);
      margin: 0;
      overflow-x: hidden;
      padding-bottom: 80px;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
      background-color: #150a2c;
      text-align: center;
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
    .nav-links {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    .nav-links li {
      padding: 0.75rem 1rem;
      display: flex;
      align-items: center;
    }
    .nav-links li a {
      color: white;
      font-weight: bold;
      text-decoration: none;
      display: flex;
      align-items: center;
    }
    .nav-links li a i {
      margin-right: 8px;
      font-size: 1.2rem;
    }
    .nav-links li a:hover {
      color: #ffcc00;
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
      padding: 2rem;
      flex: 1;
      transition: margin-left 0.3s;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }
    .sidebar.hidden ~ .content {
      margin-left: 0;
    }
    .cadastro-card {
      background-color: #fff;
      padding: 40px 30px 30px 30px;
      border-radius: 10px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.15);
      max-width: 500px;
      width: 100%;
    }
    .cadastro-card h2 {
      font-size: 1.8em;
      margin-bottom: 20px;
      color: #1c0e3f;
      font-weight: bold;
      text-align: center;
    }
    .form-group label {
      font-weight: bold;
    }
    .btn-info {
      background-color: #1c0e3f;
      border: none;
    }
    .btn-info:hover {
      background-color: #28a745 !important; /* verde Bootstrap */
      color: #fff !important;
    }
    .alert {
      margin-top: 20px;
    }
    
    .content {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }
  </style>
</head>
<body>

<?php include 'components/sidebar-logoff.php'; ?>

  <!-- Conteúdo principal -->
  <div class="content">
    <div class="cadastro-card">
      <h2>Cadastro de Usuários</h2>
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
          <div style="position: relative;">
            <input type="password" class="form-control" id="password_input" name="password_input" required maxlength="20" autocomplete="new-password" style="padding-right: 40px;">
            <span class="toggle-password" style="position: absolute; right: 16px; top: 0; bottom: 0; display: flex; align-items: center; cursor: pointer; color: #888; z-index: 10;">
              <i class="fa-solid fa-eye"></i>
            </span>
          </div>
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
  </div>
</div>

<script>
  // Salva o estado da sidebar no localStorage
  function hideSidebar() {
    document.getElementById('sidebar').classList.add('hidden');
    document.getElementById('showSidebarBtn').style.display = 'block';
    localStorage.setItem('sidebarState', 'hidden');
  }
  function showSidebar() {
    document.getElementById('sidebar').classList.remove('hidden');
    document.getElementById('showSidebarBtn').style.display = 'none';
    localStorage.setItem('sidebarState', 'visible');
  }

  // Ao carregar a página, restaura o estado salvo
  window.addEventListener('DOMContentLoaded', function() {
    var sidebarState = localStorage.getItem('sidebarState');
    if (sidebarState === 'hidden') {
      document.getElementById('sidebar').classList.add('hidden');
      document.getElementById('showSidebarBtn').style.display = 'block';
    } else {
      document.getElementById('sidebar').classList.remove('hidden');
      document.getElementById('showSidebarBtn').style.display = 'none';
    }
    // Clique simples para abrir a sidebar (precisa estar dentro do DOMContentLoaded para garantir que o botão existe)
    document.getElementById('showSidebarBtn').addEventListener('click', showSidebar);
  });

  // Alternar visibilidade da senha (ícone dentro do campo)
  document.addEventListener('DOMContentLoaded', function() {
    var toggleBtn = document.querySelector('.toggle-password');
    var pwdInput = document.getElementById('password_input');
    var icon = toggleBtn.querySelector('i');
    toggleBtn.addEventListener('click', function() {
      if (pwdInput.type === 'password') {
        pwdInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        pwdInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    });
  });
</script>

<script>
  function hideSidebar() {
    document.getElementById('sidebar').classList.add('hidden');
    document.getElementById('showSidebarBtn').style.display = 'block';
  }
  function showSidebar() {
    document.getElementById('sidebar').classList.remove('hidden');
    document.getElementById('showSidebarBtn').style.display = 'none';
  }
</script>

<?php include 'components/sidebar-script.php'; ?>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php if ($mensagem): ?>
  <script>
    Swal.fire({
      title: <?php echo json_encode($mensagem); ?>,
      icon: <?php echo json_encode($tipo === 'sucesso' ? 'success' : 'error'); ?>,
      showConfirmButton: true,
    });
  </script>
<?php endif; ?>
</body>
</html>