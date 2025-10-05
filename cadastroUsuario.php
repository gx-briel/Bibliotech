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
  <link rel="stylesheet" href="assets/css/theme.css">
  <style>
    /* Oculta o footer fixo apenas nesta página */
    footer.site-footer { display: none !important; }
  </style>
</head>
<body>

<?php include 'components/sidebar-logoff.php'; ?>

  <div class="content">
    <div class="cadastro-card">
      <h2>Criar novo usuário</h2>
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
          <input type="text" class="form-control" id="nome" name="nome" required maxlength="300" placeholder="Digite seu nome completo">
        </div>
        <div class="form-group">
          <label for="password_input">Senha:</label>
          <div style="position: relative;">
            <input type="password" class="form-control" id="password_input" name="password_input" required maxlength="20" autocomplete="new-password" placeholder="Digite sua senha">
            <span class="toggle-password">
              <i class="fa-solid fa-eye"></i>
            </span>
          </div>
        </div>
        <div class="form-group">
          <label for="email">Email:</label>
          <input type="email" class="form-control" id="email" name="email" maxlength="100" placeholder="Digite seu email">
        </div>
        <div class="form-group">
          <label for="nascimento">Data de Nascimento:</label>
          <input type="date" class="form-control" id="nascimento" name="nascimento">
        </div>
        <button type="submit" class="btn btn-cadastrar btn-block">
          <i class="fa-solid fa-user-plus mr-2"></i>Cadastrar Usuário
        </button>
        <a href="index.php" class="btn btn-secondary btn-block mt-2">
          <i class="fa-solid fa-arrow-left mr-2"></i>Voltar
        </a>
      </form>
    </div>
  </div>
</div>

<script>
  // Salva o estado da sidebar no localStorage
  function hideSidebar() {
    document.getElementById('sidebar').classList.add('hidden');
    document.getElementById('showSidebarBtn').style.display = 'block';
    document.querySelector('.content').classList.add('sidebar-hidden');
    localStorage.setItem('sidebarState', 'hidden');
  }
  
  function showSidebar() {
    document.getElementById('sidebar').classList.remove('hidden');
    document.getElementById('showSidebarBtn').style.display = 'none';
    document.querySelector('.content').classList.remove('sidebar-hidden');
    localStorage.setItem('sidebarState', 'visible');
  }

  // Ao carregar a página, restaura o estado salvo
  window.addEventListener('DOMContentLoaded', function() {
    var sidebarState = localStorage.getItem('sidebarState');
    if (sidebarState === 'hidden') {
      document.getElementById('sidebar').classList.add('hidden');
      document.getElementById('showSidebarBtn').style.display = 'block';
      document.querySelector('.content').classList.add('sidebar-hidden');
    } else {
      document.getElementById('sidebar').classList.remove('hidden');
      document.getElementById('showSidebarBtn').style.display = 'none';
      document.querySelector('.content').classList.remove('sidebar-hidden');
    }
    
    // Clique para abrir a sidebar
    document.getElementById('showSidebarBtn').addEventListener('click', showSidebar);
  });

  // Alternar visibilidade da senha
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