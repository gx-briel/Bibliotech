<?php
require 'conexao.php';
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}
$term = $_GET['term'];
$sql = "SELECT id, titulo FROM livros WHERE titulo LIKE '%$term%' AND disponivel = 1";
$result = $conexao->query($sql);

$livros = array();
while ($row = $result->fetch_assoc()) {
    $livros[] = array(
        'label' => $row['titulo'],
        'value' => $row['id']
    );
}

echo json_encode($livros);
?>
