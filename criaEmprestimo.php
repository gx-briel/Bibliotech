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
$sucesso = false;
$erro_geral = '';

// Removido: busca inicial de clientes e livros (agora será feito via AJAX)

// Handler para busca AJAX de livros
if (isset($_GET['action']) && $_GET['action'] == 'buscar_livros') {
    $termo = isset($_GET['q']) ? $_GET['q'] : '';
    $sql = "SELECT ID, titulo FROM livros WHERE disponivel = 1 AND removidoEm IS NULL ";
    if (!empty($termo)) {
        $sql .= "AND titulo LIKE '%" . $conexao->real_escape_string($termo) . "%' ";
    }
    $sql .= "ORDER BY titulo LIMIT 10";
    
    $result = $conexao->query($sql);
    $livros_json = [];
    while ($row = $result->fetch_assoc()) {
        $livros_json[] = ['id' => $row['ID'], 'titulo' => $row['titulo']];
    }
    header('Content-Type: application/json');
    echo json_encode($livros_json);
    exit;
}

// Handler para busca AJAX de clientes
if (isset($_GET['action']) && $_GET['action'] == 'buscar_clientes') {
    $termo = isset($_GET['q']) ? $_GET['q'] : '';
    $sql = "SELECT id, nomeCliente, cpf FROM clientes WHERE removidoEm IS NULL ";
    if (!empty($termo)) {
        $sql .= "AND nomeCliente LIKE '%" . $conexao->real_escape_string($termo) . "%' ";
    }
    $sql .= "ORDER BY nomeCliente LIMIT 10";
    
    $result = $conexao->query($sql);
    $clientes_json = [];
    while ($row = $result->fetch_assoc()) {
        $clientes_json[] = ['id' => $row['id'], 'nome' => $row['nomeCliente'] . ' - ' . $row['cpf']];
    }
    header('Content-Type: application/json');
    echo json_encode($clientes_json);
    exit;
}

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
            $sucesso = true;
            // Limpar os campos após sucesso
            $codigo_livro = '';
            $codigo_cliente = '';
            $vencimento = '';
        } else {
            $erro_geral = "Erro ao realizar empréstimo: " . $conexao->error;
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
    html, body {
      height: 100%;
      margin: 0;
      overflow-x: hidden;
      font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background: linear-gradient(to right, #ece9e6, #ffffff);
      min-height: 100vh;
      padding-bottom: 0;
    }

    .content {
      margin-left: 250px;
      padding: 2rem;
      transition: margin-left 0.3s;
      position: relative;
      min-height: 100vh;
      padding-bottom: 4rem;
      background: transparent;
    }

    .content::before {
      content: '';
      position: fixed;
      top: 0;
      left: 250px;
      right: 0;
      bottom: 0;
      width: calc(100% - 250px);
      height: 100vh;
      background-image: url('bg1.jpg');
      background-position: center;
      background-repeat: no-repeat;
      background-size: cover;
      opacity: 0.15;
      z-index: 0;
      pointer-events: none;
      filter: saturate(0.9);
      transition: left 0.3s ease, width 0.3s ease;
    }

    .sidebar.hidden ~ .content::before {
      left: 0;
      width: 100%;
    }

    .content > * {
      position: relative;
    }
    
    /* Garantir que conteúdo apareça acima do background */
    .content .container,
    .content .card {
      position: relative;
      z-index: 1;
      overflow: visible;
    }

    .sidebar.hidden ~ .content {
      margin-left: 0;
    }

    .sidebar {
      background: linear-gradient(170deg, #08470bff 40%, #3fac48ff 100%);
    }
    .sidebar .sidebar-header {
      background-color: #08470bff;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    .card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      overflow: visible;
      animation: fadeInUp 0.6s ease-out;
      border-radius: 15px;
    }

    .card-header {
      background: linear-gradient(135deg, #08470b 0%, #3fac48 100%);
      color: white;
      border-bottom: none;
      padding: 1.5rem 2rem;
      animation: fadeIn 0.8s ease-out 0.2s both;
      border-radius: 10px;
    }

    .card-header h2 {
      margin: 0;
      font-size: 1.75rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .card-header h2 i {
      font-size: 1.5rem;
    }

    .card-body {
      padding: 2rem;
      animation: fadeIn 0.8s ease-out 0.4s both;
      overflow: visible;
    }

    .form-group {
      margin-bottom: 1.5rem;
      opacity: 0;
      animation: fadeInUp 0.5s ease-out forwards;
    }

    .form-group:nth-of-type(1) { animation-delay: 0.5s; }
    .form-group:nth-of-type(2) { animation-delay: 0.6s; }
    .form-group:nth-of-type(3) { animation-delay: 0.7s; }

    .form-group label {
      font-weight: 600;
      color: #062c2a;
      margin-bottom: 0.5rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.95rem;
    }

    .form-group label i {
      color: #08470b;
      font-size: 0.9rem;
    }

    .form-control {
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      padding: 1rem 3rem 1rem 1rem;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      background: white;
      min-height: 60px;
      line-height: 1.5;
    }

    select.form-control {
      padding-right: 3rem;
      padding-top: 1rem;
      padding-bottom: 1rem;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2308470b' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 1rem center;
      background-size: 12px 12px;
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
    }

    .form-control:focus {
      border-color: #08470b;
      box-shadow: 0 0 0 0.2rem rgba(8, 71, 11, 0.15);
      outline: none;
    }

    /* Estilização do calendário */
    input[type="date"] {
      position: relative;
      cursor: pointer;
    }

    input[type="date"]::-webkit-calendar-picker-indicator {
      cursor: pointer;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%2308470b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'%3E%3C/rect%3E%3Cline x1='16' y1='2' x2='16' y2='6'%3E%3C/line%3E%3Cline x1='8' y1='2' x2='8' y2='6'%3E%3C/line%3E%3Cline x1='3' y1='10' x2='21' y2='10'%3E%3C/line%3E%3C/svg%3E");
      background-size: 20px 20px;
      background-repeat: no-repeat;
      background-position: center;
      padding: 0.5rem;
      margin-left: 0.5rem;
      border-radius: 5px;
      transition: all 0.3s ease;
    }

    input[type="date"]::-webkit-calendar-picker-indicator:hover {
      background-color: rgba(8, 71, 11, 0.1);
      transform: scale(1.1);
    }

    input[type="date"]::-webkit-datetime-edit {
      color: #333;
      font-weight: 500;
    }

    input[type="date"]::-webkit-datetime-edit-fields-wrapper {
      padding: 0;
    }

    .btn-info {
      background: linear-gradient(135deg, #08470b 0%, #3fac48 100%);
      border: none;
      color: white;
      padding: 0.875rem 2rem;
      border-radius: 10px;
      font-weight: 600;
      font-size: 1rem;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(8, 71, 11, 0.3);
      position: relative;
      overflow: hidden;
      width: 100%;
      opacity: 0;
      animation: fadeInUp 0.5s ease-out 0.9s forwards;
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
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(8, 71, 11, 0.4);
    }

    .btn-info:active {
      transform: translateY(0);
    }

    .invalid-feedback {
      display: block;
      color: #dc3545;
      font-size: 0.875rem;
      margin-top: 0.25rem;
    }

    .is-invalid {
      border-color: #dc3545 !important;
    }

    @media (max-width: 768px) {
      .content {
        margin-left: 0 !important;
        padding: 1rem;
      }

      .content::before {
        left: 0 !important;
        width: 100% !important;
      }

      .card-body {
        padding: 1.5rem;
      }
      
      .card-header {
        padding: 1.25rem 1.5rem;
      }
      
      .card-header h2 {
        font-size: 1.5rem;
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
      <div class="card">
        <div class="card-header">
          <h2><i class="fas fa-book-reader"></i> Criar Empréstimo</h2>
        </div>
        <div class="card-body">
          <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
              <label for="titulo_input"><i class="fas fa-book"></i> Selecione o livro:</label>
              <input type="text" 
                     class="form-control <?php echo !empty($erros['titulo']) ? 'is-invalid' : ''; ?>" 
                     id="titulo_input" 
                     name="titulo_input"
                     placeholder="Digite para buscar..."
                     autocomplete="off"
                     required>
              <input type="hidden" id="titulo" name="titulo" value="<?php echo $codigo_livro; ?>">
              <div id="livros-dropdown"></div>
              <div class="invalid-feedback"><?php echo $erros['titulo']; ?></div>
            </div>
            <div class="form-group">
              <label for="cliente_input"><i class="fas fa-user"></i> Selecione o cliente:</label>
              <input type="text" 
                     class="form-control <?php echo !empty($erros['cliente']) ? 'is-invalid' : ''; ?>" 
                     id="cliente_input" 
                     name="cliente_input"
                     placeholder="Digite para buscar..."
                     autocomplete="off"
                     required>
              <input type="hidden" id="cliente" name="cliente" value="<?php echo $codigo_cliente; ?>">
              <div id="clientes-dropdown"></div>
              <div class="invalid-feedback"><?php echo $erros['cliente']; ?></div>
            </div>
            <div class="form-group">
              <label for="vencimento"><i class="fas fa-calendar-alt"></i> Data para devolução:</label>
              <input type="date" class="form-control" id="vencimento" name="vencimento" value="<?php echo $vencimento; ?>" required>
            </div>
            <button type="submit" class="btn btn-info"><i class="fas fa-check-circle mr-2"></i> Criar Empréstimo</button>
          </form>
        </div>
      </div>
    </div>
  </div>

<!-- Modal de Confirmação de Sucesso -->
<div class="modal fade" id="modalSucesso" tabindex="-1" role="dialog" aria-labelledby="modalSucessoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(135deg, #08470b 0%, #3fac48 100%); color: white;">
        <h5 class="modal-title" id="modalSucessoLabel">
          <i class="fas fa-check-circle mr-2"></i> Sucesso!
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center" style="padding: 2rem;">
        <div style="font-size: 4rem; color: #3fac48; margin-bottom: 1rem;">
          <i class="fas fa-check-circle"></i>
        </div>
        <h4 style="color: #08470b; margin-bottom: 1rem;">Empréstimo criado com sucesso!</h4>
        <p style="color: #666; margin-bottom: 0;">O empréstimo foi registrado e o livro está agora emprestado.</p>
      </div>
      <div class="modal-footer" style="justify-content: center; border-top: none;">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fas fa-times mr-2"></i> Fechar
        </button>
        <a href="listaEmprestimoAtivo.php" class="btn" style="background: linear-gradient(135deg, #08470b 0%, #3fac48 100%); color: white; border: none;">
          <i class="fas fa-list mr-2"></i> Ver Empréstimos Ativos
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Erro -->
<div class="modal fade" id="modalErro" tabindex="-1" role="dialog" aria-labelledby="modalErroLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modalErroLabel">
          <i class="fas fa-exclamation-circle mr-2"></i> Erro!
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center" style="padding: 2rem;">
        <div style="font-size: 4rem; color: #dc3545; margin-bottom: 1rem;">
          <i class="fas fa-times-circle"></i>
        </div>
        <h4 style="color: #dc3545; margin-bottom: 1rem;">Erro ao criar empréstimo</h4>
        <p id="mensagemErro" style="color: #666;"><?php echo htmlspecialchars($erro_geral); ?></p>
      </div>
      <div class="modal-footer" style="justify-content: center; border-top: none;">
        <button type="button" class="btn btn-danger" data-dismiss="modal">
          <i class="fas fa-times mr-2"></i> Fechar
        </button>
      </div>
    </div>
  </div>
</div>

<?php include 'components/sidebar-script.php'; ?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<style>
    #livros-dropdown, #clientes-dropdown {
        display: none;
        position: absolute;
        z-index: 9999 !important;
        border: 1px solid #ddd;
        border-top: none;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        background-color: white;
        max-height: 300px;
        overflow-y: auto;
        width: 100%;
        border-radius: 0 0 10px 10px;
        margin-top: -10px;
    }
    
    #livros-dropdown.show, #clientes-dropdown.show {
        display: block;
    }
    
    #livros-dropdown .dropdown-item, #clientes-dropdown .dropdown-item {
        padding: 0.75rem 1rem;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        display: block;
        color: #333;
        text-decoration: none;
    }
    
    #livros-dropdown .dropdown-item:last-child, #clientes-dropdown .dropdown-item:last-child {
        border-bottom: none;
    }
    
    #livros-dropdown .dropdown-item:hover, #clientes-dropdown .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #08470b;
    }
    
    .form-group {
        position: relative;
        z-index: 2;
    }
    
    /* Ajustar dropdowns para ficarem em container relativo */
    .form-group > div {
        position: relative;
    }
    
    /* Garantir que form-groups apareçam sempre por cima */
    .form-group.active {
        z-index: 10000 !important;
    }
</style>

<script>
$(document).ready(function() {
    let timeoutLivro, timeoutCliente;
    let livrosCache = null, clientesCache = null;
    
    // Função para buscar livros
    function buscarLivros(termo) {
        if (timeoutLivro) clearTimeout(timeoutLivro);
        
        timeoutLivro = setTimeout(function() {
            $.ajax({
                url: '?action=buscar_livros&q=' + encodeURIComponent(termo),
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    livrosCache = data;
                    exibirLivros(data);
                }
            });
        }, 200);
    }
    
    // Função para exibir livros no dropdown
    function exibirLivros(livros) {
        const dropdown = $('#livros-dropdown');
        dropdown.empty();
        
        if (livros.length > 0) {
            livros.forEach(function(livro) {
                dropdown.append(
                    $('<a>').addClass('dropdown-item')
                            .text(livro.titulo)
                            .click(function(e) {
                                e.preventDefault();
                                $('#titulo_input').val(livro.titulo);
                                $('#titulo').val(livro.id);
                                dropdown.removeClass('show');
                            })
                );
            });
            dropdown.addClass('show');
        } else {
            dropdown.removeClass('show');
        }
    }
    
    // Função para buscar clientes
    function buscarClientes(termo) {
        if (timeoutCliente) clearTimeout(timeoutCliente);
        
        timeoutCliente = setTimeout(function() {
            $.ajax({
                url: '?action=buscar_clientes&q=' + encodeURIComponent(termo),
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    clientesCache = data;
                    exibirClientes(data);
                }
            });
        }, 200);
    }
    
    // Função para exibir clientes no dropdown
    function exibirClientes(clientes) {
        const dropdown = $('#clientes-dropdown');
        dropdown.empty();
        
        if (clientes.length > 0) {
            clientes.forEach(function(cliente) {
                dropdown.append(
                    $('<a>').addClass('dropdown-item')
                            .text(cliente.nome)
                            .click(function(e) {
                                e.preventDefault();
                                $('#cliente_input').val(cliente.nome);
                                $('#cliente').val(cliente.id);
                                dropdown.removeClass('show');
                            })
                );
            });
            dropdown.addClass('show');
        } else {
            dropdown.removeClass('show');
        }
    }
    
    // Event listeners para o campo de livros
    $('#titulo_input').on('input', function() {
        const termo = $(this).val();
        if (termo.length > 0) {
            buscarLivros(termo);
        } else {
            $('#livros-dropdown').removeClass('show');
            $('#titulo').val('');
        }
    });
    
    // Event listeners para o campo de clientes
    $('#cliente_input').on('input', function() {
        const termo = $(this).val();
        if (termo.length > 0) {
            buscarClientes(termo);
        } else {
            $('#clientes-dropdown').removeClass('show');
            $('#cliente').val('');
        }
    });
    
    // Fechar dropdowns ao clicar fora
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#titulo_input, #livros-dropdown').length) {
            $('#livros-dropdown').removeClass('show');
        }
        if (!$(e.target).closest('#cliente_input, #clientes-dropdown').length) {
            $('#clientes-dropdown').removeClass('show');
        }
    });
    
    // Carregar lista inicial de livros e clientes ao focar no campo
    $('#titulo_input').on('focus', function() {
        if (!livrosCache) {
            buscarLivros('');
        }
        $(this).closest('.form-group').addClass('active');
    });
    
    $('#titulo_input').on('blur', function() {
        // Delay para permitir clique no dropdown
        setTimeout(function() {
            $('#titulo_input').closest('.form-group').removeClass('active');
        }, 200);
    });
    
    $('#cliente_input').on('focus', function() {
        if (!clientesCache) {
            buscarClientes('');
        }
        $(this).closest('.form-group').addClass('active');
    });
    
    $('#cliente_input').on('blur', function() {
        // Delay para permitir clique no dropdown
        setTimeout(function() {
            $('#cliente_input').closest('.form-group').removeClass('active');
        }, 200);
    });
    
    // Mostrar modal de sucesso ou erro se necessário
    <?php if ($sucesso): ?>
        $('#modalSucesso').modal('show');
        
        // Limpar o formulário quando o modal de sucesso for fechado
        $('#modalSucesso').on('hidden.bs.modal', function () {
            $('#titulo_input').val('');
            $('#titulo').val('');
            $('#cliente_input').val('');
            $('#cliente').val('');
            $('#vencimento').val('');
            $('#livros-dropdown').removeClass('show');
            $('#clientes-dropdown').removeClass('show');
            livrosCache = null;
            clientesCache = null;
        });
    <?php endif; ?>
    
    <?php if (!empty($erro_geral)): ?>
        $('#modalErro').modal('show');
    <?php endif; ?>
});
</script>

</body>
</html>