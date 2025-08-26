<?php
include '../../include/header.php';
include '../../include/db.php';

$id = intval($_GET['id'] ?? 0);
$st = $pdo->prepare("SELECT * FROM emprestimos WHERE id_emprestimo=?");
$st->execute([$id]); $emp=$st->fetch(); if(!$emp) die('Empréstimo não encontrado');

$erro='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $devolucao = $_POST['data_devolucao'] ?? '';
  if($devolucao==='') $erro='Informe a data de devolução.';
  elseif(strtotime($devolucao) < strtotime($emp['data_emprestimo'])) $erro='Devolução não pode ser anterior ao empréstimo.';
  if(!$erro){
    $pdo->prepare("UPDATE emprestimos SET data_devolucao=? WHERE id_emprestimo=?")->execute([$devolucao,$id]);
    header('Location: read.php?status=concluidos'); exit;
  }
}
?>
<!DOCTYPE html><html lang="pt-br"><head>
<meta charset="utf-8"><title>Registrar Devolução</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="container mt-5">
  <h2>Registrar Devolução</h2>
  <p><strong>Empréstimo:</strong> #<?= $emp['id_emprestimo'] ?> | <strong>Data empréstimo:</strong> <?= htmlspecialchars($emp['data_emprestimo']) ?></p>
  <?php if($erro): ?><div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
  <form method="post" class="row g-3">
    <div class="col-md-4">
      <label class="form-label">Data de Devolução*</label>
      <input type="date" name="data_devolucao" class="form-control" value="<?= date('Y-m-d') ?>" required>
    </div>
    <div class="col-12">
      <button class="btn btn-primary">Salvar</button>
      <a href="read.php" class="btn btn-secondary">Voltar</a>
    </div>
  </form>
</body></html>