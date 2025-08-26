<?php
include '../../include/header.php';
include '../../include/db.php';

// livros disponíveis (sem empréstimo ativo)
$livros = $pdo->query("
  SELECT l.id_livro, l.titulo
  FROM livros l
  LEFT JOIN emprestimos e 
    ON e.id_livro=l.id_livro AND e.data_devolucao IS NULL
  WHERE e.id_emprestimo IS NULL
  ORDER BY l.titulo
")->fetchAll();
$leitores = $pdo->query("SELECT id_leitor, nome FROM leitores ORDER BY nome")->fetchAll();

$erro='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $id_livro = intval($_POST['id_livro']??0);
  $id_leitor= intval($_POST['id_leitor']??0);
  $data     = $_POST['data_emprestimo'] ?? date('Y-m-d');

  if($id_livro<=0 || $id_leitor<=0){ $erro='Selecione livro e leitor.'; }
  // livro não pode estar emprestado
  if(!$erro){
    $q=$pdo->prepare("SELECT COUNT(*) FROM emprestimos WHERE id_livro=? AND data_devolucao IS NULL");
    $q->execute([$id_livro]);
    if($q->fetchColumn()>0) $erro='Este livro já está emprestado.';
  }
  // leitor no máx 3 ativos
  if(!$erro){
    $q=$pdo->prepare("SELECT COUNT(*) FROM emprestimos WHERE id_leitor=? AND data_devolucao IS NULL");
    $q->execute([$id_leitor]);
    if($q->fetchColumn()>=3) $erro='Este leitor já possui 3 empréstimos ativos.';
  }

  if(!$erro){
    $ins=$pdo->prepare("INSERT INTO emprestimos (id_livro, id_leitor, data_emprestimo) VALUES (?,?,?)");
    $ins->execute([$id_livro,$id_leitor,$data]);
    header('Location: read.php'); exit;
  }
}
?>
<!DOCTYPE html><html lang="pt-br"><head>
<meta charset="utf-8"><title>Novo Empréstimo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="container mt-5">
  <h2>Novo Empréstimo</h2>
  <?php if($erro): ?><div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
  <form method="post" class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Livro*</label>
      <select name="id_livro" class="form-select" required>
        <option value="">Selecione</option>
        <?php foreach($livros as $l): ?>
          <option value="<?= $l['id_livro'] ?>"><?= htmlspecialchars($l['titulo']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">Leitor*</label>
      <select name="id_leitor" class="form-select" required>
        <option value="">Selecione</option>
        <?php foreach($leitores as $r): ?>
          <option value="<?= $r['id_leitor'] ?>"><?= htmlspecialchars($r['nome']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Data do Empréstimo*</label>
      <input type="date" name="data_emprestimo" class="form-control" value="<?= date('Y-m-d') ?>" required>
    </div>
    <div class="col-12">
      <button class="btn btn-primary">Salvar</button>
      <a href="read.php" class="btn btn-secondary">Voltar</a>
    </div>
  </form>
</body></html>