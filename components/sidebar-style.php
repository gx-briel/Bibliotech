<?php
// Componente de estilos CSS para a sidebar
?>
<style>
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
    box-shadow: 2px 0 8px rgba(28,14,63,0.08);
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
    letter-spacing: 1px;
  }

  .sidebar .sidebar-header a {
    color: #fff;
    text-decoration: none;
  }

  .sidebar .sidebar-header i {
    margin-right: 8px;
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
    transition: color 0.2s;
  }

  .nav-links li a i {
    margin-right: 8px;
    font-size: 1.2rem;
  }

  .nav-links li a:hover {
    color: #ffcc00;
    text-decoration: underline;
  }

  .logout-btn {
    position: absolute;
    bottom: 1rem;
    left: 1rem;
    right: 1rem;
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
    cursor: pointer;
  }

  .sidebar.hidden ~ .show-sidebar-btn {
    display: block;
  }

  .content {
    margin-left: 250px;
    padding: 2rem;
    flex: 1;
    transition: margin-left 0.3s;
  }

  .sidebar.hidden ~ .content {
    margin-left: 0;
  }

  @media (max-width: 768px) {
    .content {
      margin-left: 0 !important;
    }
  }
</style>
