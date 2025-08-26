<?php
include '../../include/header.php';
include '../../include/db.php';

// paginação
$limit  = 5;
$page   = max(1, intval($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

// filtros
$filtro = trim($_GET['filtro'] ?? '');
$params = [];
$where  = ' WHERE 1 ';
if ($filtro !== '') { $where .= ' AND nome LIKE ? '; $params[] = "%$filtro%"; }

// total
$countSql = "SELECT COUNT(*) FROM autores $where";
$countStmt = $pdo->prepare($countSql); $countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$total_paginas = max(1, (int)ceil($total / $limit));

// dados
$sql = "SELECT * FROM autores $where ORDER BY nome LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql); $stmt->execute($params);
$autores = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Autores</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
  <h2>Autores</h2>

  <form method="GET" class="row g-2 mb-3">
    <div class="col-md-4">
      <input type="text" name="filtro" class="form-control" placeholder="Buscar por nome" value="<?= htmlspecialchars($filtro) ?>">
    </div>
    <div class="col-md-8">
      <button class="btn btn-primary">Filtrar</button>
      <a href="create.php" class="btn btn-success">Novo Autor</a>
    </div>
  </form>

  <table class="table table-bordered table-striped">
    <thead>
      <tr><th>ID</th><th>Nome</th><th>Nacionalidade</th><th>Ano de Nasc.</th><th>Ações</th></tr>
    </thead>
    <tbody>
      <?php foreach ($autores as $a): ?>
      <tr>
        <td><?= $a['id_autor'] ?></td>
        <td><?= htmlspecialchars($a['nome']) ?></td>
        <td><?= htmlspecialchars($a['nacionalidade']) ?></td>
        <td><?= htmlspecialchars($a['ano_nascimento']) ?></td>
        <td>
          <a class="btn btn-warning btn-sm" href="update.php?id=<?= $a['id_autor'] ?>">Editar</a>
          <a class="btn btn-danger btn-sm" href="delete.php?id=<?= $a['id_autor'] ?>" onclick="return confirm('Excluir autor?')">Excluir</a>
        </td>
      </tr>
      <?php endforeach; if(!$autores): ?>
      <tr><td colspan="5" class="text-center">Nenhum autor encontrado.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <nav>
    <ul class="pagination">
      <?php for ($i=1; $i <= $total_paginas; $i++): ?>
        <li class="page-item <?= $i==$page ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>&filtro=<?= urlencode($filtro) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
</body>
</html>