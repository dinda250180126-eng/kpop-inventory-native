<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/escape.php';
checkAuth();

$search = trim($_GET['q'] ?? '');
$sql = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND name LIKE :search";
    $params['search'] = "%$search%";
}
$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

require_once __DIR__ . '/../../views/header.php';
?>
<div style="display: flex; justify-content: space-between; align-items: center;">
    <h2>📦 Master Produk Merchandise</h2>
    <a href="create.php" class="btn">+ Tambah Produk</a>
</div>

<form method="GET" style="margin: 15px 0;">
    <input type="text" name="q" placeholder="Cari nama produk..." value="<?= e($search) ?>" style="padding: 6px 10px; width: 250px;">
    <button type="submit" class="btn">Cari</button>
</form>

<table>
    <thead><tr><th>Nama</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr></thead>
    <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
            <td><?= e($p['name']) ?></td>
            <td><?= e($p['category']) ?></td>
            <td>Rp <?= number_format($p['price'], 0, ',', '.') ?></td>
            <td><?= e($p['stock']) ?> pcs</td>
            <td>
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <a href="delete.php?id=<?= e($p['id']) ?>" onclick="return confirm('Hapus produk ini?');" style="color: red;">Hapus</a>
                <?php else: ?>
                    <span style="color: #94a3b8;">Hanya Admin</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div></body></html>