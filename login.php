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
    <style>
      body {
        background-color: rgb(216, 107, 107);
        margin: 0;
        overflow-x: hidden;
        padding-bottom: 80px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      }
      .wrapper {
        display: flex;
      }
      .sidebar {
        width: 250px;
        background: linear-gradient(180deg, #1c0e3f 60%, #e8f5e9 100%);
        color: white;
        min-height: 100vh;
        transition: transform 0.3s ease;
        position: fixed;
        z-index: 999;
      }
      .sidebar.hidden {
        transform: translateX(-100%);
      }
      .sidebar .sidebar-header {
        padding: 1rem;
        font-size: 1.5rem;
        font-weight: bold;
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
      .login-card {
        background-color: #fff;
        padding: 40px 30px 30px 30px;
        border-radius: 10px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        max-width: 400px;
        width: 100%;
      }
      .login-card h1 {
        font-size: 1.8em;
        margin-bottom: 20px;
        color: #1c0e3f;
        font-weight: bold;
      }
      .form-control {
        border-radius: 8px;
        box-shadow: none;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        padding: 12px;
      }
      .btn {
        transition: background-color 0.3s ease, transform 0.2s ease;
      }
      .btn:hover {
        transform: translateY(-2px);
      }
      .btn-primary {
        background-color: #1c0e3f;
        border: none;
      }
      .btn-primary:hover {
        background-color: #28a745 !important; /* verde Bootstrap */
        color: #fff !important;
      }
      .modal-content {
        border-radius: 8px;
      }
      .modal-header {
        background-color: #1c0e3f;
        color: white;
      }
      .modal-footer .btn-secondary {
        background-color: #ddd;
      }
      @media (max-width: 768px) {
        .content {
          margin-left: 0 !important;
        }
        .login-card {
          padding: 30px 10px;
        }
      }
    </style>
</head>
<body>


<div class="wrapper">
  <!-- Sidebar -->
  <nav id="sidebar" class="sidebar">
    <div class="sidebar-header"><a href="indexlogado.php" style="color: #fff; text-decoration: none;"><i class="fa-solid fa-book-open-reader" style="margin-right:8px;"></i><span style="letter-spacing:1px;">Bibliotech</span></a></div>
    <button class="toggle-btn btn btn-sm btn-warning w-100 mb-2" onclick="hideSidebar()" style="font-weight: bold; font-size: 1rem;"><i class="fa-solid fa-angles-left mr-2"></i> Recolher Menu</button>
    <ul class="nav-links">
      <li><a href="login.php"><i class="fa-solid fa-right-to-bracket"></i> Login</a></li>
      <li><a href="cadastroUsuario.php"><i class="fa-solid fa-user-plus"></i> Cadastrar Usuário</a></li>
    </ul>
  </nav>
  <!-- Botão para mostrar sidebar -->
  <button id="showSidebarBtn" class="show-sidebar-btn" onclick="showSidebar()">☰</button>
  <!-- Conteúdo principal -->
  <div class="content">
    <div class="login-card">
      <h1 class="text-center">Acesse sua conta</h1>
      <form id="loginForm">
        <div class="form-group">
          <label for="usuario">Usuário</label>
          <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Informe seu usuário" maxlength="4" required>
        </div>
        <div class="form-group">
          <label for="senha">Senha</label>
          <div style="position: relative;">
            <input type="password" class="form-control" id="senha" name="senha" placeholder="Informe sua senha" maxlength="20" required style="padding-right: 40px;">
            <span class="toggle-password" style="position: absolute; right: 16px; top: 0; bottom: 0; display: flex; align-items: center; cursor: pointer; color: #888; z-index: 10;">
              <i class="fa-solid fa-eye"></i>
            </span>
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
        <button type="submit" class="btn btn-primary btn-block">Entrar</button>
      </form>
      <div class="mt-3 text-center">
        <button type="button" class="btn btn-link" onclick="window.history.back()">Voltar</button>
        <button type="button" class="btn btn-link" onclick="window.location.href = 'cadastroUsuario.php';">Criar Usuário</button>
      </div>
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
</script>


<!-- Modal de erro para usuário vazio -->
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

<!-- Modal de erro para senha vazia -->
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

<!-- Modal de falha no login -->
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
