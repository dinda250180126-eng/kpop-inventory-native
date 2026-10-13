<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/escape.php';
checkAuth();

$errors = [];

// Ambil daftar produk untuk dropdown pilihan
$products = $pdo->query("SELECT id, name, stock FROM products ORDER BY name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $type = $_POST['type'] ?? '';
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    $notes = trim($_POST['notes'] ?? '');

    if (!$productId) {
        $errors[] = 'Pilih produk merchandise.';
    }
    if (!in_array($type, ['in', 'out'])) {
        $errors[] = 'Tipe transaksi harus masuk (in) atau keluar (out).';
    }
    if ($quantity === false || $quantity <= 0) {
        $errors[] = 'Jumlah mutasi harus lebih dari 0.';
    }

    if (empty($errors)) {
        try {
            // Gunakan Database Transaction agar perubahan stok & pencatatan riwayat aman (Slide P4)
            $pdo->beginTransaction();

            // Cek stok produk saat ini
            $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = :id FOR UPDATE");
            $stmt->execute(['id' => $productId]);
            $currentStock = $stmt->fetchColumn();

            if ($currentStock === false) {
                throw new Exception('Produk tidak ditemukan.');
            }

            // Validasi barang keluar tidak boleh melebihi stok tersedia
            if ($type === 'out' && $currentStock < $quantity) {
                throw new Exception("Stok tidak mencukupi! Sisa stok saat ini hanya $currentStock pcs.");
            }

            // Hitung stok baru
            $newStock = ($type === 'in') ? ($currentStock + $quantity) : ($currentStock - $quantity);

            // Update stok pada tabel products
            $updateStmt = $pdo->prepare("UPDATE products SET stock = :stock, updated_at = NOW() WHERE id = :id");
            $updateStmt->execute(['stock' => $newStock, 'id' => $productId]);

            // Catat ke tabel stock_transactions
            $transStmt = $pdo->prepare("INSERT INTO stock_transactions (product_id, user_id, type, quantity, notes, created_at, updated_at) 
                                        VALUES (:product_id, :user_id, :type, :quantity, :notes, NOW(), NOW())");
            $transStmt->execute([
                'product_id' => $productId,
                'user_id'    => $_SESSION['user']['id'],
                'type'       => $type,
                'quantity'   => $quantity,
                'notes'      => $notes
            ]);

            $pdo->commit();
            header('Location: index.php');
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../../views/header.php';
?>

<div style="max-width: 500px; margin: auto;">
    <h2>+ Catat Mutasi Stok Baru</h2>

    <?php if (!empty($errors)): ?>
        <div style="background: #fee2e2; border: 1px solid #ef4444; color: #b91c1c; padding: 10px; border-radius: 6px; margin-bottom: 16px;">
            <ul style="margin: 0; padding-left: 20px;">
                <?php foreach ($errors as $err): ?>
                    <li><?= e($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div style="margin-bottom: 14px;">
            <label style="display: block; font-weight: bold; margin-bottom: 4px;">Pilih Produk *</label>
            <select name="product_id" required style="width: 100%; box-sizing: border-box; padding: 8px;">
                <option value="">-- Pilih Produk --</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?= e($p['id']) ?>">
                        <?= e($p['name']) ?> (Sisa: <?= e($p['stock']) ?> pcs)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom: 14px;">
            <label style="display: block; font-weight: bold; margin-bottom: 4px;">Jenis Mutasi *</label>
            <select name="type" required style="width: 100%; box-sizing: border-box; padding: 8px;">
                <option value="in">📥 Masuk (Restock Barang)</option>
                <option value="out">📤 Keluar (Barang Terjual)</option>
            </select>
        </div>

        <div style="margin-bottom: 14px;">
            <label style="display: block; font-weight: bold; margin-bottom: 4px;">Jumlah *</label>
            <input type="number" name="quantity" min="1" value="1" required style="width: 100%; box-sizing: border-box; padding: 8px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: bold; margin-bottom: 4px;">Catatan / Keterangan</label>
            <input type="text" name="notes" placeholder="Contoh: Restock batch 1" style="width: 100%; box-sizing: border-box; padding: 8px;">
        </div>

        <button type="submit" class="btn">Simpan Mutasi</button>
        <a href="index.php" style="margin-left: 10px; color: #64748b; text-decoration: none;">Batal</a>
    </form>
</div>

</div></body></html>