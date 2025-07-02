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
    <style>
        body {
            background-color: rgb(238, 255, 235); /* Cor do fundo consistente com o resto do sistema */
            font-family: 'Arial', sans-serif;
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: flex-start; /* Alinha a parte superior da tela */
            min-height: 80vh; /* Ajusta a altura para que a área de login fique mais para cima */
            padding-top: 100px; /* Adiciona um espaço extra no topo */
        }

        .login-card {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        .login-card h1 {
            font-size: 1.8em;
            margin-bottom: 20px;
            color: #333;
            font-weight: bold;
        }

        .form-control {
            border-radius: 8px;
            box-shadow: none;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 12px;
        }

        /* Adicionando transições suaves para os botões */
        .btn {
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px); /* Leve elevação no hover */
        }

        .btn-info:hover {
            background-color: #17a2b8;
        }

        .btn-secondary:hover {
            background-color: #6c757d;
        }

        .btn-danger:hover {
            background-color: #dc3545;
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

    .navbar {
      background-color: #1c0e3f;
    }
    .navbar-brand, .nav-link {
      color: white !important;
      font-weight: bold;
    }
    .navbar-nav .nav-link:hover {
      color: #ffcc00 !important;
    }

    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <a class="navbar-brand" href="indexlogado.php">Bibliotech</a>

</nav>

<div class="login-container">
    <div class="login-card">
        <h1 class="text-center">Acesse sua conta</h1>
        <form id="loginForm">
            <div class="form-group">
                <label for="usuario">Usuário</label>
                <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Informe seu usuário" maxlength="4" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" placeholder="Informe sua senha" maxlength="20" required>

            </div>
            <button type="submit" class="btn btn-primary btn-block">Entrar</button>
        </form>
        <!-- Botões adicionais -->
        <div class="mt-3 text-center">
            <button type="button" class="btn btn-link" onclick="window.history.back()">Voltar</button>
            <button type="button" class="btn btn-link" onclick="window.location.href = 'cadastroUsuario.php';">Criar Usuário</button>
        </div>
    </div>
</div>


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
