<?php
include '../../include/header.php';
include '../../include/db.php';

$limit=5; $page=max(1,intval($_GET['page']??1)); $offset=($page-1)*$limit;
$nome = trim($_GET['nome']??''); $email = trim($_GET['email']??'');
$params=[]; $where=' WHERE 1 ';
if($nome!==''){ $where.=' AND nome LIKE ?'; $params[]="%$nome%"; }
if($email!==''){ $where.=' AND email LIKE ?'; $params[]="%$email%"; }

$c=$pdo->prepare("SELECT COUNT(*) FROM leitores $where"); $c->execute($params);
$total=(int)$c->fetchColumn(); $total_paginas=max(1,(int)ceil($total/$limit));

$sql="SELECT * FROM leitores $where ORDER BY nome LIMIT $limit OFFSET $offset";
$s=$pdo->prepare($sql); $s->execute($params); $leitores=$s->fetchAll();
?>
<!DOCTYPE html><html lang="pt-br"><head>
<meta charset="utf-8"><title>Leitores</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="container mt-5">
  <h2>Leitores</h2>
  <form class="row g-2 mb-3">
    <div class="col-md-4"><input name="nome" class="form-control" placeholder="Nome" value="<?= htmlspecialchars($nome) ?>"></div>
    <div class="col-md-4"><input name="email" class="form-control" placeholder="Email" value="<?= htmlspecialchars($email) ?>"></div>
    <div class="col-md-4 d-grid"><button class="btn btn-primary">Filtrar</button></div>
  </form>
  <a class="btn btn-success mb-3" href="create.php">Novo Leitor</a>
  <table class="table table-bordered table-striped">
    <thead><tr><th>ID</th><th>Nome</th><th>Email</th><th>Telefone</th><th>Ações</th></tr></thead>
    <tbody>
      <?php foreach($leitores as $l): ?>
      <tr>
        <td><?= $l['id_leitor'] ?></td>
        <td><?= htmlspecialchars($l['nome']) ?></td>
        <td><?= htmlspecialchars($l['email']) ?></td>
        <td><?= htmlspecialchars($l['telefone']) ?></td>
        <td>
          <a class="btn btn-warning btn-sm" href="update.php?id=<?= $l['id_leitor'] ?>">Editar</a>
          <a class="btn btn-danger btn-sm" href="delete.php?id=<?= $l['id_leitor'] ?>" onclick="return confirm('Excluir leitor?')">Excluir</a>
        </td>
      </tr>
      <?php endforeach; if(!$leitores): ?>
      <tr><td colspan="5" class="text-center">Nenhum leitor encontrado.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <nav><ul class="pagination">
    <?php for($i=1;$i<=$total_paginas;$i++): ?>
      <li class="page-item <?= $i==$page?'active':'' ?>">
        <a class="page-link" href="?page=<?= $i ?>&nome=<?= urlencode($nome) ?>&email=<?= urlencode($email) ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>
  </ul></nav>
</body></html>