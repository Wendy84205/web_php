<?php
require_once 'db.php';

$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();
?>

<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5>Danh mục sản phẩm</h5>
    </div>
    <div class="card-body">
        <ul class="list-group">
            <?php foreach ($categories as $cat): ?> 
                <li class="list-group-item">
                    <a href="category.php?id=<?= $cat['id'] ?>">
                        <?= htmlspecialchars($cat['name']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>