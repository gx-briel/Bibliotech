<?php
session_start(); // Inicia a sessão

// Apenas processar se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    include('conexao.php'); // Inclui a conexão com o banco

    $response = ['status' => 'success'];

    if (isset($_POST['usuario']) && isset($_POST['senha'])) {
        $usuario = trim($_POST['usuario']); // Remove espaços extras
        $senha = trim($_POST['senha']); // Remove espaços extras

        if (strlen($usuario) == 0) {
            $response = ['status' => 'usuario_vazio'];
        } else if (strlen($senha) == 0) {
            $response = ['status' => 'senha_vazio'];
        } else {
            // Verifica no banco se o usuário existe
            $sql_code = "SELECT * FROM usuarios WHERE usuario = ?";
            $stmt = $conexao->prepare($sql_code);
            $stmt->bind_param("s", $usuario);
            $stmt->execute();
            $sql_query = $stmt->get_result();

            if ($sql_query->num_rows == 1) {
                $usuario_data = $sql_query->fetch_assoc();

                // Verifica se a senha fornecida é igual à senha armazenada (comparação simples, pois a senha está em texto simples)
                if ($senha == $usuario_data['senha']) {
                    // Inicia a sessão e guarda o ID do usuário
                    $_SESSION['id'] = $usuario_data['ID'];
                    $_SESSION['usuario'] = $usuario_data['usuario']; // Armazenar o nome de usuário também, se necessário

                    $response = ['status' => 'login_ok'];
                } else {
                    $response = ['status' => 'login_falhou'];
                }
            } else {
                $response = ['status' => 'login_falhou'];
            }
        }
    }

    // Retorna a resposta como JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    die();  // Certifique-se de que o script pare aqui, evitando qualquer saída extra
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
      }
      
      .content.sidebar-hidden {
        margin-left: 0 !important;
        width: 100vw !important;
        transition: margin-left 0.3s ease, width 0.3s ease;
      }
      
      .login-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        padding: 50px 40px;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        max-width: 450px;
        width: 100%;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.2);
      }
  
      .login-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #1c0e3f, #667eea, #764ba2);
        border-radius: 20px 20px 0 0;
      }
      
      .login-card h1 {
        font-size: 2.2em;
        margin-bottom: 30px;
        color: #1c0e3f;
        font-weight: 700;
        text-align: center;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: relative;
      }
      
      .login-card h1::after {
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
        margin-bottom: 25px;
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
      
      
      .btn-primary {
        background: linear-gradient(135deg, #1c0e3f, #667eea);
        border: none;
        position: relative;
        overflow: hidden;
      }
      
      .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: left 0.5s;
      }
      
      .btn-primary:hover::before {
        left: 100%;
      }
      
      .btn-primary:hover {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: #fff;
      }
      
      .btn-link {
        color: #667eea;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
      }
      
      .btn-link:hover {
        color: #1c0e3f;
        text-decoration: underline;
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
      
      .login-footer {
        text-align: center;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
      }
      
      .modal-content {
        border-radius: 15px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      }
      
      .modal-header {
        background: linear-gradient(135deg, #1c0e3f, #667eea);
        color: white;
        border-radius: 15px 15px 0 0;
      }
      
      .modal-footer .btn-secondary {
        background-color: #6c757d;
        border: none;
        border-radius: 8px;
      }
      
      /* Animação de entrada */
      .login-card {
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
        
        .login-card {
          padding: 40px 30px;
          margin: 20px 0;
          max-width: 100%;
        }
        
        .login-card h1 {
          font-size: 1.8em;
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
    <div class="login-card">
      <h1>Acesse sua conta</h1>
      <form id="loginForm">
        <div class="form-group">
          <label for="usuario">Usuário</label>
          <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Informe seu usuário" maxlength="4" required>
        </div>
        <div class="form-group">
          <label for="senha">Senha</label>
          <div style="position: relative;">
            <input type="password" class="form-control" id="senha" name="senha" placeholder="Informe sua senha" maxlength="20" required>
            <span class="toggle-password">
              <i class="fa-solid fa-eye"></i>
            </span>
          </div>
        </div>
        <button type="submit" class="btn btn-primary btn-block">
          <i class="fa-solid fa-sign-in-alt mr-2"></i>Entrar
        </button>
      </form>
      <div class="login-footer">
        <button type="button" class="btn btn-link" onclick="window.history.back()">
          <i class="fa-solid fa-arrow-left mr-1"></i>Voltar
        </button>
        <button type="button" class="btn btn-link" onclick="window.location.href = 'cadastroUsuario.php';">
          <i class="fa-solid fa-user-plus mr-1"></i>Criar Usuário
        </button>
      </div>
    </div>
  </div>
</div>

<!-- JavaScript para controle da sidebar e funcionalidades -->
<script>
  // Salva o estado da sidebar no localStorage
  function hideSidebar() {
    const sidebar = document.getElementById('sidebar');
    const showBtn = document.getElementById('showSidebarBtn');
    const content = document.querySelector('.content');
    
    sidebar.classList.add('hidden');
    showBtn.style.display = 'block';
    content.classList.add('sidebar-hidden');
    localStorage.setItem('sidebarState', 'hidden');
    
    console.log('Sidebar hidden - Content classes:', content.classList);
  }
  
  function showSidebar() {
    const sidebar = document.getElementById('sidebar');
    const showBtn = document.getElementById('showSidebarBtn');
    const content = document.querySelector('.content');
    
    sidebar.classList.remove('hidden');
    showBtn.style.display = 'none';
    content.classList.remove('sidebar-hidden');
    localStorage.setItem('sidebarState', 'visible');
    
    console.log('Sidebar visible - Content classes:', content.classList);
  }

  // Ao carregar a página, restaura o estado salvo
  window.addEventListener('DOMContentLoaded', function() {
    const sidebarState = localStorage.getItem('sidebarState');
    const sidebar = document.getElementById('sidebar');
    const showBtn = document.getElementById('showSidebarBtn');
    const content = document.querySelector('.content');
    
    if (sidebarState === 'hidden') {
      sidebar.classList.add('hidden');
      showBtn.style.display = 'block';
      content.classList.add('sidebar-hidden');
      console.log('Page loaded - Sidebar hidden - Content classes:', content.classList);
    } else {
      sidebar.classList.remove('hidden');
      showBtn.style.display = 'none';
      content.classList.remove('sidebar-hidden');
      console.log('Page loaded - Sidebar visible - Content classes:', content.classList);
    }
    
    // Abrir a sidebar
    showBtn.addEventListener('click', showSidebar);
  });

  // Alternar visibilidade da senha
  document.addEventListener('DOMContentLoaded', function() {
    var toggleBtn = document.querySelector('.toggle-password');
    var pwdInput = document.getElementById('senha');
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

<div class="modal fade" id="usuarioModal" tabindex="-1" role="dialog" aria-labelledby="usuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="usuarioModalLabel">Erro de Validação</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Preencha seu usuário.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="senhaModal" tabindex="-1" role="dialog" aria-labelledby="senhaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="senhaModalLabel">Erro de Validação</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Preencha sua senha.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="loginFalhouModal" tabindex="-1" role="dialog" aria-labelledby="loginFalhouModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginFalhouModalLabel">Falha ao Logar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Usuário ou senha incorretos.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    // Formata o valor do campo de usuário somente quando sair do campo (blur)
    $('#usuario').on('blur', function() {
        var valor = $(this).val().trim();
        if (valor.length > 0 && valor.length < 4) {
            $(this).val(valor.padStart(4, '0'));
        }
    });

    $('#loginForm').submit(function(event) {
        event.preventDefault(); // Impede o envio padrão do formulário

        var usuario = $('#usuario').val();
        var senha = $('#senha').val();

        $.ajax({
            url: 'login.php',
            type: 'POST',
            data: { usuario: usuario, senha: senha },
            dataType: 'json',
            success: function(response) {
                console.log(response); // Verifica o conteúdo da resposta
                if (response.status == 'usuario_vazio') {
                    $('#usuarioModal').modal('show');
                } else if (response.status == 'senha_vazio') {
                    $('#senhaModal').modal('show');
                } else if (response.status == 'login_falhou') {
                    $('#loginFalhouModal').modal('show');
                } else if (response.status == 'login_ok') {
                    setTimeout(function() {
                        window.location.href = 'indexlogado.php';
                    }, 500);
                }
            },
            error: function(xhr, status, error) {
                console.error("Erro na requisição: ", xhr.responseText);
            }
        });
    });
});
</script>
</body>
</html>
