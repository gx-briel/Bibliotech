<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bibliotech</title>

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

    .content {
      text-align: center;
    }

    .content img {
      max-width: 900px;
      width: 100%;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      padding: 1rem;
      background-color: #fff;
      transition: transform 0.3s ease;
    }

    .tagline {
      margin-top: 1rem;
      font-size: 1.25rem;
      color: #fff;
      text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }

    footer {
      position: fixed;
      bottom: 0;
      width: 100%;
      background-color: #f8f9fa;
      padding: 10px 0;
      text-align: center;
    }

    @media (max-width: 768px) {
      .content {
        margin-left: 0 !important;
      }
      .content img {
        width: 80%;
      }
    }
  </style>
</head>
<body>
  
<?php include 'components/sidebar-logoff.php'; ?>

  <!-- Conteúdo principal -->
  <div class="content">
    <script>
      const mensagem = <?php echo json_encode($mensagem); ?>;
      const tipo     = <?php echo json_encode($tipo); ?>;
      if (mensagem) {
        Swal.fire({
          title: mensagem,
          icon: tipo === 'sucesso' ? 'success' : 'error',
          showConfirmButton: false,
          timer: 3000
        });
      }
    </script>

    <!-- Centralização vertical absoluta -->
    <div style="position: relative; min-height: 80vh;">
      <div style="position: absolute; top: 60%; left: 50%; transform: translate(-50%, -50%); width: 100%;">
        <div class="text-center">
          <img
            src="fxd2.jpg"
            alt="Logo Bibliotech"
            class="img-fluid"
          >
          <div class="tagline">Seu portal para descobrir e gerenciar livros com facilidade.</div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'components/sidebar-script.php'; ?>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>