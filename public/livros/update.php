<?php
include '../../include/header.php';
include '../../include/db.php';

$id = intval($_GET['id'] ?? 0);
$livro = $pdo->prepare("SELECT * FROM livros WHERE id_livro=?");
$livro->execute([$id]); $livro=$livro->fetch(); if(!$livro) die('Livro não encontrado');

$autores = $pdo->query("SELECT id_autor, nome FROM autores ORDER BY nome")->fetchAll();
$erro='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $titulo = trim($_POST['titulo']??''); $genero=trim($_POST['genero']??'');
  $ano=intval($_POST['ano_publicacao']??0); $id_autor=intval($_POST['id_autor']??0);

  if($titulo==='') $erro='Título é obrigatório.';
  elseif($ano<=1500 || $ano>intval(date('Y'))) $erro='Ano de publicação inválido.';
  elseif($id_autor<=0) $erro='Autor é obrigatório.';
  if(!$erro){
    $upd=$pdo->prepare("UPDATE livros SET titulo=?, genero=?, ano_publicacao=?, id_autor=? WHERE id_livro=?");
    $upd->execute([$titulo,$genero,$ano,$id_autor,$id]);
    header('Location: read.php'); exit;
  }
}
?>
<!DOCTYPE html><html lang="pt-br"><head>
<meta charset="utf-8"><title>Editar Livro</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="container mt-5">
  <h2>Editar Livro</h2>
  <?php if($erro): ?><div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
  <form method="post" class="row g-3">
    <div class="col-md-6"><label class="form-label">Título*</label>
      <input name="titulo" class="form-control" value="<?= htmlspecialchars($livro['titulo']) ?>" required></div>
    <div class="col-md-4"><label class="form-label">Gênero</label>
      <input name="genero" class="form-control" value="<?= htmlspecialchars($livro['genero']) ?>"></div>
    <div class="col-md-2"><label class="form-label">Ano*</label>
      <input type="number" name="ano_publicacao" class="form-control" value="<?= htmlspecialchars($livro['ano_publicacao']) ?>" required></div>
    <div class="col-md-6"><label class="form-label">Autor*</label>
      <select name="id_autor" class="form-select" required>
        <?php foreach($autores as $a): ?>
          <option value="<?= $a['id_autor'] ?>" <?= $livro['id_autor']==$a['id_autor']?'selected':'' ?>><?= htmlspecialchars($a['nome']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-12">
      <button class="btn btn-primary">Salvar</button>
      <a href="read.php" class="btn btn-secondary">Voltar</a>
    </div>
  </form>
</body></html>