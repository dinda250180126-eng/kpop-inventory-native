<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/escape.php';
checkAuth();

$stmt = $pdo->query("SELECT st.*, p.name AS product_name, u.name AS user_name 
                     FROM stock_transactions st 
                     JOIN products p ON st.product_id = p.id 
                     LEFT JOIN users u ON st.user_id = u.id 
                     ORDER BY st.created_at DESC");
$transactions = $stmt->fetchAll();

require_once __DIR__ . '/../../views/header.php';
?>
<div style="display: flex; justify-content: space-between; align-items: center;">
    <h2>🔄 Riwayat Mutasi Stok</h2>
    <a href="create.php" class="btn">+ Catat Mutasi Baru</a>
</div>

<table>
    <thead><tr><th>Tanggal</th><th>Merchandise</th><th>Jenis</th><th>Jumlah</th><th>Catatan</th><th>Petugas</th></tr></thead>
    <tbody>
        <?php foreach ($transactions as $t): ?>
        <tr>
            <td><?= e($t['created_at']) ?></td>
            <td><?= e($t['product_name']) ?></td>
            <td><?= $t['type'] === 'in' ? '<span style="color:green;">Masuk</span>' : '<span style="color:red;">Keluar</span>' ?></td>
            <td><?= e($t['quantity']) ?> pcs</td>
            <td><?= e($t['notes']) ?></td>
            <td><?= e($t['user_name'] ?? 'Sistem') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div></body></html>