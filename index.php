<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bibliotech</title>

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <?php include 'components/sidebar-style.php'; ?>
  
  <style>
    :root{--card:#ffffff;--muted:#6b6f73;}
    
    body {
        background: linear-gradient(to right, #ece9e6, #ffffff);
        margin: 0;
        overflow-x: hidden;
        font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

  .content {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        gap: 1.25rem;
        min-height: 100vh;
        padding: 2rem 2rem 4rem;
        margin-left: 250px; /* Corresponde à largura da sidebar */
        transition: margin-left 0.5s;
        position: relative; /* para posicionar o background por pseudo-elemento */
  }

    /* Imagem de fundo atrás dos cards (transparente) */
    .content::before{
      content: '';
      position: absolute;
      inset: 0;
      background-image: url('fxd2.jpg');
      background-position: center;
      background-repeat: no-repeat;
      background-size: cover;
      opacity: 0.07; /* transparência desejada */
      z-index: 0;
      pointer-events: none;
      filter: saturate(0.9) blur(0px);
    }

    /* Garantir que os elementos do conteúdo fiquem acima do background */
    .top-cards, .main-card, .hero-card { position: relative; z-index: 1 }

    .main-card {
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        padding: 3rem;
        text-align: center;
        max-width: 800px;
        width: 100%;
        animation: fadeIn 1s ease-in-out;
    }

    /* Top cards (acima do hero) */
    .top-cards{
      max-width:1100px;
      width:100%;
      display:grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 1rem;
      margin-bottom: 1.25rem;
      align-items:stretch;
    }

    .card-small{
      background: var(--card);
      border-radius: 10px;
      padding: 0.8rem;
      box-shadow: 0 6px 18px rgba(18,20,22,0.04);
      display:flex;
      gap:0.75rem;
      align-items:flex-start;
      transition:transform .14s ease, box-shadow .14s ease;
      animation: fadeIn 1s ease-in-out both;
      will-change: transform, opacity;
    }
    .card-small:hover{transform:translateY(-6px);box-shadow:0 14px 30px rgba(18,20,22,0.07)}

    /* Staggered delays for cards to animate in sequence */
    .top-cards .card-small:nth-child(1){animation-delay: 0.04s}
    .top-cards .card-small:nth-child(2){animation-delay: 0.12s}
    .top-cards .card-small:nth-child(3){animation-delay: 0.20s}

    .card-media{width:88px;height:64px;border-radius:8px;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:linear-gradient(180deg,#eef5f8,#ffffff)}
    .card-media svg{width:76px;height:56px}

    .card-body h5{margin:0 0 .25rem 0;font-size:1.02rem}
    .card-body p{margin:0;color:var(--muted);font-size:.94rem}
    .card-cta{margin-top:.6rem}
    .card-cta a{font-size:.92rem;text-decoration:none;padding:.45rem .7rem;border-radius:8px;background:linear-gradient(90deg,#2b6ea3,#5aa1d8);color:#fff}

  .hero-icon {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 1.25rem;
  }
  .hero-title {
    font-size: 2rem;
    margin: 0.25rem 0 1rem;
    color: #062c2a;
    font-weight: 700;
    letter-spacing: 0.4px;
  }

    .tagline {
        font-size: 1.5rem;
        color: #333;
        font-weight: 300;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
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
            align-items: stretch;
        }
        .main-card {
            padding: 1.5rem;
        }

      /* Top cards full width on mobile */
      .top-cards{grid-template-columns: 1fr;gap:0.75rem}
      .card-small{flex-direction:row;align-items:center}
      .card-media{width:64px;height:56px}
      .card-media svg{width:56px;height:44px}
      .card-cta a{display:inline-block;margin-top:.4rem}

      /* Hero card stacks visuals above content */
      .hero-card{grid-template-columns:1fr;max-width:100%;padding:1.25rem}
      .hero-visual{order:-1;margin-bottom:.6rem}
      .illustration{max-width:260px}
    }
    @media (max-width: 480px){
      /* Extra small devices tweaks */
      .card-media{width:56px;height:46px}
      .card-media svg{width:48px;height:36px}
      .hero-title{font-size:1.4rem}
      .hero-icon i{font-size:3.2rem}
      .show-sidebar-btn{left:8px;padding:8px 10px}
      .toggle-btn{padding:.5rem .6rem;font-size:.9rem}
    }
  </style>
</head>
<body>
  
<?php 
  $mensagem = null;
  $tipo = null;
  include 'components/sidebar-logoff.php'; 
?>

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

    <!-- Seção de cards (acima do hero) -->
    <div class="top-cards" id="features">
      <div class="card-small">
          <div class="card-media" aria-hidden="true" style="display: flex; align-items: center; justify-content: center; height: 100%;">
            <svg viewBox="0 0 120 90" xmlns="http://www.w3.org/2000/svg">
              <rect x="6" y="10" width="40" height="70" rx="6" fill="#2b6ea3"/>
              <rect x="54" y="20" width="46" height="60" rx="6" fill="#5aa1d8"/>
            </svg>
          </div>
          <div class="card-body" style="display: flex; flex-direction: column; justify-content: center; height: 100%;">
            <h5 style="margin-bottom: 0.25rem;" >Controle de prazos</h5>
            <p>Alertas e relatórios para evitar atrasos e facilitar renovações.</p>
          </div>
      </div>

      <div class="card-small">
          <div class="card-media" aria-hidden="true" style="display: flex; align-items: center; justify-content: center; height: 100%;">
            <svg viewBox="0 0 120 90" xmlns="http://www.w3.org/2000/svg">
              <rect x="6" y="10" width="40" height="70" rx="6" fill="#9cc7e8"/>
              <rect x="54" y="20" width="46" height="60" rx="6" fill="#2b6ea3"/>
            </svg>
          </div>
          <div class="card-body" style="display: flex; flex-direction: column; justify-content: center; height: 100%;">
            <h5 style="margin-bottom: 0.25rem;">Gestão de acervo</h5>
            <p style="text-align: justify;">Categorização e buscas rápidas para aumentar a descoberta de obras.</p>
          </div>
      </div>

      <div class="card-small">
          <div class="card-media" aria-hidden="true" style="display: flex; align-items: center; justify-content: center; height: 100%;">
            <svg viewBox="0 0 120 90" xmlns="http://www.w3.org/2000/svg">
              <rect x="6" y="10" width="40" height="70" rx="6" fill="#ffd699"/>
              <rect x="54" y="20" width="46" height="60" rx="6" fill="#b33a3a"/>
            </svg>
          </div>
          <div class="card-body" style="display: flex; flex-direction: column; justify-content: center; height: 100%;">
            <h5 style="margin-bottom: 0.25rem;">Relatórios inteligentes</h5>
            <p style="text-align: justify;">Indicadores claros sobre uso, disponibilidade e padrões de empréstimo.</p>
          </div>
      </div>
    </div>

  <div class="main-card">
    <div class="hero-icon" aria-hidden="true">
      <i class="fa-solid fa-book-open-reader" style="font-size:4.5rem;color:var(--accent);"></i>
    </div>
    <h1 class="hero-title">Bibliotech</h1>
    <p class="tagline">Seu portal para descobrir e gerenciar livros com facilidade.</p>
  </div>
  </div>
</div>

<?php include 'components/sidebar-script.php'; ?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>