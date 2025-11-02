<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Cadastro de Livros</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <?php include 'components/sidebar-style.php'; ?>
  
  <style>
    html, body {
      height: 100%;
    }
    
    body {
      background: linear-gradient(to right, #ece9e6, #ffffff);
      margin: 0;
      overflow-x: hidden;
      padding-bottom: 0;
      font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      min-height: 100vh;
    }

    .content {
      margin-left: 250px;
      padding: 2rem;
      padding-bottom: 4rem;
      transition: margin-left 0.3s;
      position: relative;
      min-height: 100vh;
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
      filter: saturate(0.9) blur(0px);
      transition: left 0.3s ease, width 0.3s ease;
    }

    .sidebar.hidden ~ .content::before {
      left: 0;
      width: 100%;
    }

    @media (max-width: 768px) {
      .content::before {
        left: 0;
        width: 100%;
      }
    }

    .content > * {
      position: relative;
      z-index: 1;
    }

    .sidebar.hidden ~ .content {
      margin-left: 0;
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
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
      overflow: hidden;
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(10px);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      animation: fadeInUp 0.6s ease-out;
    }

    .card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.18);
    }

    .card-header {
      background: linear-gradient(135deg, #062c2a 0%, #08470b 100%);
      color: white;
      padding: 1.5rem 2rem;
      border: none;
      position: relative;
      animation: fadeIn 0.8s ease-out 0.2s both;
    }

    .card-header::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
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
    }

    .form-group {
      margin-bottom: 1.5rem;
      opacity: 0;
      animation: fadeInUp 0.5s ease-out forwards;
    }

    .form-group:nth-child(1) { animation-delay: 0.5s; }
    .form-group:nth-child(2) { animation-delay: 0.6s; }
    .form-group:nth-child(3) { animation-delay: 0.7s; }
    .form-group:nth-child(4) { animation-delay: 0.8s; }

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
      padding: 0.75rem 1rem;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      background: white;
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

    input[type="date"]::-webkit-datetime-edit-text {
      color: #08470b;
      padding: 0 0.3em;
      font-weight: 600;
    }

    input[type="date"]::-webkit-datetime-edit-month-field,
    input[type="date"]::-webkit-datetime-edit-day-field,
    input[type="date"]::-webkit-datetime-edit-year-field {
      color: #062c2a;
      padding: 0 0.2em;
    }

    input[type="date"]::-webkit-datetime-edit-month-field:focus,
    input[type="date"]::-webkit-datetime-edit-day-field:focus,
    input[type="date"]::-webkit-datetime-edit-year-field:focus {
      background-color: rgba(8, 71, 11, 0.1);
      border-radius: 4px;
      color: #08470b;
      font-weight: 700;
    }

    /* Estilo para o calendário dropdown (webkit) */
    input[type="date"]::-webkit-calendar-picker-indicator:active {
      background-color: rgba(8, 71, 11, 0.2);
    }

    /* Estilos para Firefox */
    input[type="date"]::-moz-calendar-picker-indicator {
      cursor: pointer;
      padding: 0.5rem;
      margin-left: 0.5rem;
      border-radius: 5px;
      background-color: #08470b;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='white'%3E%3Cpath d='M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z'/%3E%3C/svg%3E");
      background-size: 16px 16px;
      background-repeat: no-repeat;
      background-position: center;
      transition: all 0.3s ease;
    }

    input[type="date"]::-moz-calendar-picker-indicator:hover {
      background-color: #062c2a;
      transform: scale(1.1);
    }

    .btn-info {
      background: linear-gradient(135deg, #08470b 0%, #062c2a 100%);
      border: none;
      border-radius: 10px;
      padding: 0.875rem 2rem;
      font-weight: 600;
      font-size: 1rem;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(8, 71, 11, 0.3);
      opacity: 0;
      animation: fadeInUp 0.5s ease-out 1.0s forwards;
      color: white;
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
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(8, 71, 11, 0.4);
    }

    .btn-info:active {
      transform: translateY(0);
    }

    @media (max-width: 768px) {
      .content {
        margin-left: 0 !important;
        padding: 1rem;
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
  </style>
</head>
<body>


<?php include 'components/sidebar-logado.php'; ?>

  <!-- Conteúdo principal -->
  <div class="content">
    <div class="container mt-5">
      <div class="row justify-content-center">
        <div class="col-md-8 col-sm-12">
          <div class="card">
            <div class="card-header">
              <h2>
                <i class="fas fa-book"></i>
                Cadastro de Livros
              </h2>
            </div>
            <div class="card-body">
              <form method="POST" action="cadastroLivro.php">
                <div class="form-group">
                  <label for="titulo">
                    <i class="fas fa-heading"></i>
                    Título do Livro
                  </label>
                  <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Digite o título do livro..." required>
                </div>
                <div class="form-group">
                  <label for="isbn">
                    <i class="fas fa-barcode"></i>
                    ISBN
                  </label>
                  <input type="text" class="form-control" id="isbn" name="isbn" placeholder="Digite o ISBN do livro...">
                </div>
                <div class="form-group">
                  <label for="editora">
                    <i class="fas fa-building"></i>
                    Editora
                  </label>
                  <input type="text" class="form-control" id="editora" name="editora" placeholder="Digite o nome da editora...">
                </div>
                <div class="form-group">
                  <label for="lancamento">
                    <i class="fas fa-calendar"></i>
                    Data de Lançamento
                  </label>
                  <input type="date" class="form-control" id="lancamento" name="lancamento">
                </div>
                <button type="submit" class="btn btn-info btn-block">
                  <i class="fas fa-plus-circle mr-2"></i>
                  Cadastrar Livro
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<div class="modal fade" id="feedbackModal" tabindex="-1" role="dialog" aria-labelledby="feedbackModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="feedbackModalLabel">Cadastro de Livro</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<?php
require("conexao.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $isbn = $_POST['isbn'];
    $editora = $_POST['editora'];
    $lancamento = $_POST['lancamento'];

    $inserelivro = "INSERT INTO livros(titulo, isbn, editora, lancamento) VALUES ('$titulo','$isbn','$editora','$lancamento')";

    $operacaoSQL = mysqli_query($conexao, $inserelivro);
    if (mysqli_affected_rows($conexao) != 0) {
        echo "<script>
                $(document).ready(function() {
                    $('#feedbackModal .modal-body').text('Livro cadastrado com Sucesso!');
                    $('#feedbackModal').modal('show');
                });
              </script>";
    } else {
        echo "<script>
                $(document).ready(function() {
                    $('#feedbackModal .modal-body').text('O Livro não foi cadastrado com Sucesso!');
                    $('#feedbackModal').modal('show');
                });
              </script>";
    }
}
?>

<?php include 'components/sidebar-script.php'; ?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
