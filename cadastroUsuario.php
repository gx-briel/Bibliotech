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
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      margin: 0;
      padding: 0;
      overflow: hidden;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      height: 100vh;
    }
    
    .content {
      margin-left: 250px;
      padding: 2rem;
      flex: 1;
      transition: margin-left 0.3s;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      width: calc(100vw - 250px);
      overflow-y: auto;
    }
    
    .content.sidebar-hidden {
      margin-left: 0 !important;
      width: 100vw !important;
      transition: margin-left 0.3s ease, width 0.3s ease;
    }
    
    .cadastro-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      padding: 40px 30px;
      border-radius: 20px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
      max-width: 550px;
      width: 100%;
      position: relative;
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.2);
      max-height: 90vh;
      overflow-y: auto;
      margin: 20px 0;
    }
    
    .cadastro-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #1c0e3f, #667eea, #764ba2);
      border-radius: 20px 20px 0 0;
    }
    
    .cadastro-card h2 {
      font-size: 2em;
      margin-bottom: 25px;
      color: #1c0e3f;
      font-weight: 700;
      text-align: center;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      position: relative;
    }
    
    .cadastro-card h2::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 60px;
      height: 3px;
      background: linear-gradient(90deg, #1c0e3f, #667eea);
      border-radius: 2px;
    }
    
    .form-group {
      margin-bottom: 20px;
      position: relative;
    }
    
    .form-group label {
      font-weight: 600;
      color: #333;
      margin-bottom: 8px;
      display: block;
    }
    
    .form-control {
      border-radius: 12px;
      box-shadow: none;
      border: 2px solid #e9ecef;
      padding: 15px 20px;
      font-size: 16px;
      transition: all 0.3s ease;
      background: rgba(255, 255, 255, 0.9);
    }
    
    .form-control:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
      background: rgba(255, 255, 255, 1);
      outline: none;
    }
    
    .form-control:disabled {
      background: rgba(248, 249, 250, 0.9);
      color: #6c757d;
      border-color: #e9ecef;
    }

    
    .btn-info {
      background: linear-gradient(135deg, #1c0e3f, #667eea);
      border: none;
      position: relative;
      overflow: hidden;
    }
    
    .btn-info::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
      transition: left 0.5s;
    }
    
    .btn-info:hover::before {
      left: 100%;
    }
    
    .btn-info:hover {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: #fff;
    }
    
    .btn-secondary {
      background: linear-gradient(135deg, #6c757d, #5a6268);
      border: none;
      position: relative;
      overflow: hidden;
    }
    
    .btn-secondary::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
      transition: left 0.5s;
    }
    
    .btn-secondary:hover::before {
      left: 100%;
    }
    
    .btn-secondary:hover {
      background: linear-gradient(135deg, #495057, #343a40);
      color: #fff;
    }
    
    .toggle-password {
      position: absolute;
      right: 20px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #888;
      z-index: 10;
      transition: color 0.3s ease;
    }
    
    .toggle-password:hover {
      color: #667eea;
    }
    
    .alert {
      margin-top: 20px;
      border-radius: 12px;
      border: none;
      padding: 15px 20px;
      font-weight: 500;
    }
    
    .alert-success {
      background: linear-gradient(135deg, #d4edda, #c3e6cb);
      color: #155724;
    }
    
    .alert-danger {
      background: linear-gradient(135deg, #f8d7da, #f5c6cb);
      color: #721c24;
    }
    
    /* Animação de entrada */
    .cadastro-card {
      animation: slideUp 0.6s ease-out;
    }
    
    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(50px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    @media (max-width: 768px) {
      .content {
        margin-left: 0 !important;
        padding: 1rem;
        width: 100vw !important;
      }
      
      .content.sidebar-hidden {
        margin-left: 0 !important;
        width: 100vw !important;
      }
      
      .cadastro-card {
        padding: 30px 20px;
        margin: 10px 0;
        max-width: 100%;
        max-height: 95vh;
      }
      
      .cadastro-card h2 {
        font-size: 1.6em;
        margin-bottom: 20px;
      }
      
      .form-control {
        padding: 12px 16px;
      }
      
      .btn {
        padding: 12px 24px;
        font-size: 14px;
      }
    }
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
        <button type="submit" class="btn btn-info btn-block">
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