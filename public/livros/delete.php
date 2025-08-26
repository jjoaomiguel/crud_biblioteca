<?php
include '../../include/db.php';
$id = intval($_GET['id'] ?? 0);
$pdo->prepare("DELETE FROM livros WHERE id_livro=?")->execute([$id]);
header('Location: read.php');

exit;

?>