<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/escape.php';
checkAuth();

// Agregat Query
$totalProducts = $pdo->query("SELECT COUNT(id) FROM products")->fetchColumn();
$totalStock = $pdo->query("SELECT SUM(stock) FROM products")->fetchColumn() ?? 0;
$lowStockStmt = $pdo->query("SELECT * FROM products WHERE stock <= 10");
$lowStocks = $lowStockStmt->fetchAll();

require_once __DIR__ . '/../views/header.php';
?>
<h2>📊 Dashboard Ringkasan</h2>
<div style="display: flex; gap: 16px; margin-bottom: 24px;">
    <div style="flex: 1; padding: 16px; background: #f1f5f9; border-radius: 6px;">
        <h4>Total Produk</h4>
        <h2 style="margin: 0; color: #0284c7;"><?= e($totalProducts) ?></h2>
    </div>
    <div style="flex: 1; padding: 16px; background: #f1f5f9; border-radius: 6px;">
        <h4>Total Stok</h4>
        <h2 style="margin: 0; color: #16a34a;"><?= e($totalStock) ?> pcs</h2>
    </div>
</div>

<h3>⚠️ Peringatan Stok Rendah (<= 10 pcs)</h3>
<table>
    <thead><tr><th>Nama Merchandise</th><th>Stok Tersisa</th></tr></thead>
    <tbody>
        <?php foreach ($lowStocks as $ls): ?>
            <tr><td><?= e($ls['name']) ?></td><td style="color: red; font-weight: bold;"><?= e($ls['stock']) ?> pcs</td></tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div></body></html>