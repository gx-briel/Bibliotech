<?php
require 'conexao.php';
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}
$term = $_GET['term'];
$sql = "SELECT id, nome FROM clientes WHERE nome LIKE '%$term%'";
$result = $conexao->query($sql);

$clientes = array();
while ($row = $result->fetch_assoc()) {
    $clientes[] = array(
        'label' => $row['nome'],
        'value' => $row['id']
    );
}

echo json_encode($clientes);
?>
