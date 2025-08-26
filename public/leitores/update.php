<?php
include '../../include/header.php';
include '../../include/db.php';

$id=intval($_GET['id']??0);
$leitor=$pdo->prepare("SELECT * FROM leitores WHERE id_leitor=?");
$leitor->execute([$id]); $leitor=$leitor->fetch(); if(!$leitor) die('Leitor não encontrado');

$erro='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $nome=trim($_POST['nome']??''); $email=trim($_POST['email']??''); $tel=trim($_POST['telefone']??'');
  if($nome==='') $erro='Nome é obrigatório.';
  if(!$erro){
    try{
      $pdo->prepare("UPDATE leitores SET nome=?, email=?, telefone=? WHERE id_leitor=?")->execute([$nome,$email,$tel,$id]);
      header('Location: read.php'); exit;
    }catch(PDOException $e){ $erro='Email já cadastrado ou inválido.'; }
  }
}
?>
<!DOCTYPE html><html lang="pt-br"><head>
<meta charset="utf-8"><title>Editar Leitor</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="container mt-5">
  <h2>Editar Leitor</h2>
  <?php if($erro): ?><div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
  <form method="post" class="row g-3">
    <div class="col-md-6"><label class="form-label">Nome*</label><input name="nome" class="form-control" value="<?= htmlspecialchars($leitor['nome']) ?>" required></div>
    <div class="col-md-6"><label class="form-label">Email</label><input name="email" type="email" class="form-control" value="<?= htmlspecialchars($leitor['email']) ?>"></div>
    <div class="col-md-4"><label class="form-label">Telefone</label><input name="telefone" class="form-control" value="<?= htmlspecialchars($leitor['telefone']) ?>"></div>
    <div class="col-12"><button class="btn btn-primary">Salvar</button> <a href="read.php" class="btn btn-secondary">Voltar</a></div>
  </form>
</body></html>