<?php
include '../../include/header.php';
include '../../include/db.php';

$limit=5; $page=max(1,intval($_GET['page']??1)); $offset=($page-1)*$limit;
$status = $_GET['status'] ?? 'ativos'; // ativos | concluidos
$leitor = intval($_GET['leitor'] ?? 0);

$params=[]; $where=' WHERE 1 ';
if($status==='ativos'){ $where.=' AND e.data_devolucao IS NULL '; }
elseif($status==='concluidos'){ $where.=' AND e.data_devolucao IS NOT NULL '; }
if($leitor>0){ $where.=' AND e.id_leitor = ? '; $params[]=$leitor; }

$countSql="SELECT COUNT(*) FROM emprestimos e $where";
$c=$pdo->prepare($countSql); $c->execute($params); 
$total=(int)$c->fetchColumn(); $total_paginas=max(1,(int)ceil($total/$limit));

$sql = "SELECT e.*, l.titulo, r.nome
        FROM emprestimos e
        JOIN livros l ON l.id_livro=e.id_livro
        JOIN leitores r ON r.id_leitor=e.id_leitor
        $where
        ORDER BY e.id_emprestimo DESC
        LIMIT $limit OFFSET $offset";
$s=$pdo->prepare($sql); $s->execute($params); $rows=$s->fetchAll();

$leitores = $pdo->query("SELECT id_leitor, nome FROM leitores ORDER BY nome")->fetchAll();
?>
<!DOCTYPE html><html lang="pt-br"><head>
<meta charset="utf-8"><title>Empréstimos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="container mt-5">
  <div class="d-flex justify-content-between align-items-center">
    <h2>Empréstimos</h2>
    <a class="btn btn-success" href="create.php">Novo Empréstimo</a>
  </div>

  <ul class="nav nav-tabs my-3">
    <li class="nav-item"><a class="nav-link <?= $status==='ativos'?'active':'' ?>" href="?status=ativos">Ativos</a></li>
    <li class="nav-item"><a class="nav-link <?= $status==='concluidos'?'active':'' ?>" href="?status=concluidos">Concluídos</a></li>
  </ul>

  <form class="row g-2 mb-3">
    <input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>">
    <div class="col-md-6">
      <select name="leitor" class="form-select">
        <option value="0">Filtrar por leitor (todos)</option>
        <?php foreach($leitores as $lt): ?>
          <option value="<?= $lt['id_leitor'] ?>" <?= $leitor==$lt['id_leitor']?'selected':'' ?>><?= htmlspecialchars($lt['nome']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2 d-grid"><button class="btn btn-primary">Aplicar</button></div>
  </form>

  <table class="table table-bordered table-striped">
    <thead><tr><th>ID</th><th>Livro</th><th>Leitor</th><th>Empréstimo</th><th>Devolução</th><th>Ações</th></tr></thead>
    <tbody>
      <?php foreach($rows as $r): ?>
      <tr>
        <td><?= $r['id_emprestimo'] ?></td>
        <td><?= htmlspecialchars($r['titulo']) ?></td>
        <td><?= htmlspecialchars($r['nome']) ?></td>
        <td><?= htmlspecialchars($r['data_emprestimo']) ?></td>
        <td><?= $r['data_devolucao'] ?: '-' ?></td>
        <td>
          <?php if(!$r['data_devolucao']): ?>
            <a class="btn btn-warning btn-sm" href="update.php?id=<?= $r['id_emprestimo'] ?>">Registrar Devolução</a>
          <?php endif; ?>
          <a class="btn btn-danger btn-sm" href="delete.php?id=<?= $r['id_emprestimo'] ?>" onclick="return confirm('Excluir empréstimo?')">Excluir</a>
        </td>
      </tr>
      <?php endforeach; if(!$rows): ?>
      <tr><td colspan="6" class="text-center">Nada encontrado.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <nav><ul class="pagination">
    <?php for($i=1;$i<=$total_paginas;$i++): ?>
      <li class="page-item <?= $i==$page?'active':'' ?>">
        <a class="page-link" href="?status=<?= urlencode($status) ?>&leitor=<?= $leitor ?>&page=<?= $i ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>
  </ul></nav>
</body></html>