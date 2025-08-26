<?php
include '../../include/header.php';
include '../../include/db.php';

$limit=5; $page=max(1,intval($_GET['page']??1)); $offset=($page-1)*$limit;

$titulo = trim($_GET['titulo'] ?? '');
$genero = trim($_GET['genero'] ?? '');
$ano    = trim($_GET['ano'] ?? '');
$autor  = intval($_GET['autor'] ?? 0);

$params=[]; $where=' WHERE 1 ';
if($titulo!==''){ $where.=' AND l.titulo LIKE ?'; $params[]="%$titulo%"; }
if($genero!==''){ $where.=' AND l.genero LIKE ?'; $params[]="%$genero%"; }
if($ano!==''){ $where.=' AND l.ano_publicacao = ?'; $params[]=(int)$ano; }
if($autor>0){  $where.=' AND l.id_autor = ?'; $params[]=$autor; }

$countSql = "SELECT COUNT(*) 
             FROM livros l LEFT JOIN autores a ON a.id_autor=l.id_autor $where";
$c = $pdo->prepare($countSql); $c->execute($params); 
$total=(int)$c->fetchColumn(); $total_paginas=max(1,(int)ceil($total/$limit));

$sql = "SELECT l.*, a.nome AS autor
        FROM livros l LEFT JOIN autores a ON a.id_autor=l.id_autor
        $where ORDER BY l.titulo LIMIT $limit OFFSET $offset";
$stmt=$pdo->prepare($sql); $stmt->execute($params); $livros=$stmt->fetchAll();

$autores = $pdo->query("SELECT id_autor, nome FROM autores ORDER BY nome")->fetchAll();
?>
<!DOCTYPE html><html lang="pt-br"><head>
<meta charset="utf-8"><title>Livros</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="container mt-5">
  <h2>Livros</h2>
  <form class="row g-2 mb-3" method="get">
    <div class="col-md-3"><input class="form-control" name="titulo" placeholder="Título" value="<?= htmlspecialchars($titulo) ?>"></div>
    <div class="col-md-3"><input class="form-control" name="genero" placeholder="Gênero" value="<?= htmlspecialchars($genero) ?>"></div>
    <div class="col-md-2"><input type="number" class="form-control" name="ano" placeholder="Ano" value="<?= htmlspecialchars($ano) ?>"></div>
    <div class="col-md-3">
      <select name="autor" class="form-select">
        <option value="0">Autor (todos)</option>
        <?php foreach($autores as $a): ?>
          <option value="<?= $a['id_autor'] ?>" <?= $autor==$a['id_autor']?'selected':'' ?>>
            <?= htmlspecialchars($a['nome']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-1 d-grid"><button class="btn btn-primary">Filtrar</button></div>
  </form>
  <a href="create.php" class="btn btn-success mb-3">Novo Livro</a>

  <table class="table table-bordered table-striped">
    <thead><tr><th>ID</th><th>Título</th><th>Gênero</th><th>Ano</th><th>Autor</th><th>Ações</th></tr></thead>
    <tbody>
      <?php foreach($livros as $l): ?>
      <tr>
        <td><?= $l['id_livro'] ?></td>
        <td><?= htmlspecialchars($l['titulo']) ?></td>
        <td><?= htmlspecialchars($l['genero']) ?></td>
        <td><?= htmlspecialchars($l['ano_publicacao']) ?></td>
        <td><?= htmlspecialchars($l['autor']) ?></td>
        <td>
          <a class="btn btn-warning btn-sm" href="update.php?id=<?= $l['id_livro'] ?>">Editar</a>
          <a class="btn btn-danger btn-sm" href="delete.php?id=<?= $l['id_livro'] ?>" onclick="return confirm('Excluir livro?')">Excluir</a>
        </td>
      </tr>
      <?php endforeach; if(!$livros): ?>
      <tr><td colspan="6" class="text-center">Nenhum livro encontrado.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <nav><ul class="pagination">
    <?php for($i=1;$i<=$total_paginas;$i++): ?>
      <li class="page-item <?= $i==$page?'active':'' ?>">
        <a class="page-link" href="?page=<?= $i ?>&titulo=<?= urlencode($titulo) ?>&genero=<?= urlencode($genero) ?>&ano=<?= urlencode($ano) ?>&autor=<?= $autor ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>
  </ul></nav>
</body></html>