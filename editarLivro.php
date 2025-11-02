<?php
require 'conexao.php';
session_start(); 


if (!isset($_SESSION['id'])) {

    header('Location: index.php');
    exit;
}
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $consulta = "SELECT * FROM livros WHERE ID = $id";
    $resultado = mysqli_query($conexao, $consulta);
    $livro = mysqli_fetch_assoc($resultado);
} else {

    header("Location: acervo.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $isbn = $_POST['isbn'];
    $editora = $_POST['editora'];
    $lancamento = $_POST['lancamento'];
    $disponivel = isset($_POST['disponivel']) ? 1 : 0;

    $atualizarLivro = "UPDATE livros SET titulo='$titulo', isbn='$isbn', editora='$editora', lancamento='$lancamento', disponivel='$disponivel' WHERE ID=$id";
    mysqli_query($conexao, $atualizarLivro);

    header("Location: acervo.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Editar Livro</title>
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
      .content {
        margin-left: 0 !important;
        padding: 1rem;
      }
      .content::before {
        left: 0;
        width: 100%;
      }
    }

    .content > * {
      position: relative;
      z-index: 1;
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

    .row > div:nth-of-type(1) .form-group { animation-delay: 0.5s; }
    .row > div:nth-of-type(2) .form-group { animation-delay: 0.6s; }
    .row > div:nth-of-type(3) .form-group { animation-delay: 0.7s; }
    .row > div:nth-of-type(4) .form-group { animation-delay: 0.8s; }
    .row > div:nth-of-type(5) .form-group { animation-delay: 0.9s; }

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

    .form-check {
      margin-bottom: 1.5rem;
      opacity: 0;
      animation: fadeInUp 0.5s ease-out 1.0s forwards;
      display: flex;
      align-items: center;
      position: relative;
    }

    .form-check-input {
      width: 1.25rem;
      height: 1.25rem;
      margin: 0;
      margin-right: 0.75rem;
      cursor: pointer;
      flex-shrink: 0;
      order: -1;
    }

    .form-check-label {
      font-weight: 600;
      color: #062c2a;
      margin: 0;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }

    .form-check-input:checked {
      background-color: #08470b;
      border-color: #08470b;
    }

    .form-check-input:focus {
      border-color: #08470b;
      box-shadow: 0 0 0 0.2rem rgba(8, 71, 11, 0.15);
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

    .btn-primary {
      background: linear-gradient(135deg, #08470b 0%, #062c2a 100%);
      border: none;
      border-radius: 10px;
      padding: 0.875rem 2rem;
      font-weight: 600;
      font-size: 1rem;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(8, 71, 11, 0.3);
      opacity: 0;
      animation: fadeInUp 0.5s ease-out 1.4s forwards;
      color: white;
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
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(8, 71, 11, 0.4);
    }

    .btn-primary:active {
      transform: translateY(0);
    }

    .btn-secondary {
      background: linear-gradient(135deg, #b8860b 0%, #8b6914 100%);
      border: none;
      border-radius: 10px;
      padding: 0.875rem 2rem;
      font-weight: 600;
      font-size: 1rem;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(184, 134, 11, 0.3);
      opacity: 0;
      animation: fadeInUp 0.5s ease-out 1.4s forwards;
      color: white;
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
      background: linear-gradient(135deg, #8b6914 0%, #b8860b 100%);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(184, 134, 11, 0.4);
      color: white;
    }

    /* Modal estilizado */
    .modal {
      z-index: 1060 !important;
    }

    .modal-backdrop {
      z-index: 1050 !important;
      background-color: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(4px);
    }

    .modal-content {
      border-radius: 16px;
      border: none;
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
      overflow: hidden;
      animation: none !important;
    }

    .modal-header {
      background: linear-gradient(135deg, #062c2a 0%, #08470b 100%);
      color: white;
      border-radius: 16px 16px 0 0;
      border: none;
      padding: 1.5rem;
      animation: none !important;
    }

    .modal-header .close {
      color: white;
      opacity: 0.9;
      text-shadow: none;
      font-size: 1.5rem;
    }

    .modal-header .close:hover {
      opacity: 1;
      color: white;
    }

    .modal-header .modal-title {
      font-weight: 700;
      font-size: 1.25rem;
    }

    .modal-body {
      padding: 1.5rem;
      color: #6b6f73;
      animation: none !important;
    }

    .modal-footer {
      padding: 1rem 1.5rem;
      border-top: 1px solid #e0e0e0;
      display: flex;
      justify-content: center;
      gap: 1rem;
      animation: none !important;
    }

    .modal-footer .btn {
      border-radius: 8px;
      font-weight: 600;
      padding: 0.5rem 1.5rem;
      min-width: 120px;
    }

    .modal-footer .btn,
    .modal .btn-primary,
    .modal .btn-secondary {
      animation: none !important;
      opacity: 1 !important;
      transform: none !important;
    }

    .modal .btn-primary:hover,
    .modal .btn-secondary:hover {
      transform: translateY(-2px) !important;
    }

    @media (max-width: 768px) {
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
        <div class="col-lg-10 col-xl-9 col-md-11 col-sm-12">
          <div class="card">
            <div class="card-header">
              <h2>
                <i class="fas fa-book-edit"></i>
                Editar Livro
              </h2>
            </div>
            <div class="card-body">
              <form id="form-editar-livro" method="POST" action="">
                <div class="row">
                  <!-- Título - Full Width -->
                  <div class="col-12">
                    <div class="form-group">
                      <label for="titulo">
                        <i class="fas fa-heading"></i>
                        Título do Livro
                      </label>
                      <input type="text" class="form-control" id="titulo" name="titulo" value="<?= htmlspecialchars($livro['titulo']); ?>" placeholder="Digite o título do livro..." required>
                    </div>
                  </div>

                  <!-- ISBN - Full Width -->
                  <div class="col-12">
                    <div class="form-group">
                      <label for="isbn">
                        <i class="fas fa-barcode"></i>
                        ISBN
                      </label>
                      <input type="text" class="form-control" id="isbn" name="isbn" value="<?= htmlspecialchars($livro['isbn']); ?>" placeholder="Digite o ISBN do livro..." required>
                    </div>
                  </div>

                  <!-- Editora - Full Width -->
                  <div class="col-12">
                    <div class="form-group">
                      <label for="editora">
                        <i class="fas fa-building"></i>
                        Editora
                      </label>
                      <input type="text" class="form-control" id="editora" name="editora" value="<?= htmlspecialchars($livro['editora']); ?>" placeholder="Digite o nome da editora..." required>
                    </div>
                  </div>

                  <!-- Data de Lançamento - Full Width -->
                  <div class="col-12">
                    <div class="form-group">
                      <label for="lancamento">
                        <i class="fas fa-calendar"></i>
                        Data de Lançamento
                      </label>
                      <input type="date" class="form-control" id="lancamento" name="lancamento" value="<?= htmlspecialchars($livro['lancamento']); ?>" required>
                    </div>
                  </div>

                  <!-- Disponível - Full Width -->
                  <div class="col-12">
                    <div class="form-check">
                      <input type="checkbox" class="form-check-input" id="disponivel" name="disponivel" <?= $livro['disponivel'] ? 'checked' : ''; ?>>
                      <label class="form-check-label" for="disponivel">
                        <i class="fas fa-check-circle mr-2"></i>
                        Disponível
                      </label>
                    </div>
                  </div>
                </div>

                <div class="row mt-4">
                  <div class="col-12 d-flex justify-content-end" style="gap: 1rem;">
                    <a href="acervo.php" class="btn btn-secondary">
                      <i class="fas fa-times mr-2"></i>
                      Cancelar
                    </a>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirmacaoModal">
                      <i class="fas fa-save mr-2"></i>
                      Atualizar Livro
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<div class="modal fade" id="confirmacaoModal" tabindex="-1" role="dialog" aria-labelledby="confirmacaoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmacaoModalLabel">
          <i class="fas fa-exclamation-circle mr-2"></i>
          Confirmar Atualização
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Tem certeza de que deseja atualizar as informações do livro <strong>"<?= htmlspecialchars($livro['titulo']); ?>"</strong>?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fas fa-times mr-1"></i>
          Cancelar
        </button>
        <button type="submit" class="btn btn-primary" form="form-editar-livro">
          <i class="fas fa-check mr-1"></i>
          Confirmar
        </button>
      </div>
    </div>
  </div>
</div>

<?php include 'components/sidebar-script.php'; ?>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
