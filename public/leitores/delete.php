<?php

include '../../include/db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare('DELETE FROM leitores WHERE id_leitor = ?');
$stmt->execute([$id]);
header('Location: read.php');
exit;
?>