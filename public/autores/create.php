<?php
include '../../include/header.php';
include '../../include/db.php';

$erro = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $nome = trim($_POST['nome'] ?? '');
  $nac  = trim($_POST['nacionalidade'] ?? '');
  $ano  = $_POST['ano_nascimento'] !== '' ? intval($_POST['ano_nascimento']) : null;

  if ($nome==='') $erro = 'Nome é obrigatório.';
  if (!$erro) {
    $stmt = $pdo->prepare("INSERT INTO autores (nome, nacionalidade, ano_nascimento) VALUES (?, ?, ?)");
    $stmt->execute([$nome, $nac, $ano]);
    header('Location: read.php'); exit;
  }
}
?>
<!DOCTYPE html><html lang="pt-br"><head>
<meta charset="utf-8"><title>Novo Autor</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="container mt-5">
  <h2>Novo Autor</h2>
  <?php if($erro): ?><div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
  <form method="post" class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Nome*</label>
      <input name="nome" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Nacionalidade</label>
      <input name="nacionalidade" class="form-control">
    </div>
    <div class="col-md-3">
      <label class="form-label">Ano de Nasc.</label>
      <input type="number" name="ano_nascimento" class="form-control">
    </div>
    <div class="col-12">
      <button class="btn btn-primary">Salvar</button>
      <a href="read.php" class="btn btn-secondary">Voltar</a>
    </div>
  </form>
</body></html>