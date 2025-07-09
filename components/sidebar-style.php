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
    padding: 0;
    display: block;
  }

  .nav-links li a {
    color: white;
    font-weight: bold;
    text-decoration: none;
    display: flex;
    align-items: center;
    transition: color 0.2s;
    padding: 0.75rem 1rem;
  }

  .nav-links li a i {
    margin-right: 8px;
    font-size: 1.2rem;
  }

  .nav-links li a:hover {
    color: #ffcc00;
    text-decoration: underline;
  }

  /* Estilos para submenus */
  .nav-item {
    position: static;
    width: 100%;
  }

  .nav-item .nav-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 20px;
    color: #fff;
    text-decoration: none;
    transition: background-color 0.3s;
    font-weight: bold;
    width: 100%;
  }

  .nav-item .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    text-decoration: none;
    color: #ffcc00;
  }

  .submenu {
    display: none;
    list-style: none;
    padding: 0;
    margin: 0;
    width: 100%;
    background-color: rgba(255, 255, 255, 0.05);
  }

  .submenu.show {
    display: block;
  }

  .submenu li {
    padding: 0;
    width: 100%;
  }

  .submenu a {
    display: block;
    padding: 8px 20px 8px 40px;
    color: #ddd;
    text-decoration: none;
    transition: background-color 0.3s;
    font-size: 0.85rem;
    font-weight: normal;
    width: 100%;
  }

  .submenu a:hover {
    background-color: rgba(255, 255, 255, 0.1);
    text-decoration: none;
    color: #ffcc00;
  }

  .submenu-arrow {
    transition: transform 0.3s ease;
    font-size: 0.8rem;
    color: #fff;
  }

  .submenu-arrow.fa-chevron-up {
    transform: rotate(360deg);
  }

  /* Submenu aninhado (Relatórios) */
  .submenu .nav-item {
    margin-left: 0;
    width: 100%;
  }

  .submenu .nav-item .nav-link {
    padding: 8px 20px 8px 40px;
    background-color: transparent;
    color: #ddd;
    font-size: 0.85rem;
    font-weight: normal;
  }

  .submenu .nav-item .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: #ffcc00;
  }

  .submenu .nav-item .submenu a {
    padding-left: 60px;
    font-size: 0.8rem;
    color: #ccc;
  }

  .submenu .nav-item .submenu a:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: #ffcc00;
  }

  /* Estilização para links de submenu que abrem outros submenus */
  .submenu li > a[onclick] {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 20px 8px 40px;
    color: #ddd;
    text-decoration: none;
    transition: background-color 0.3s;
    font-size: 0.85rem;
    font-weight: normal;
    width: 100%;
  }

  .submenu li > a[onclick]:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: #ffcc00;
  }

  /* Submenu de terceiro nível */
  .submenu .submenu {
    background-color: rgba(255, 255, 255, 0.03);
  }

  .submenu .submenu a {
    padding-left: 60px;
    font-size: 0.8rem;
    color: #ccc;
  }

  .submenu .submenu a:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: #ffcc00;
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
