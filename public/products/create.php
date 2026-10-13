<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/escape.php';
checkAuth();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);$stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);

    // Validasi input server-side
    if ($name === '') {$errors[] = 'Nama produk wajib diisi.';
    }
    if ($category === '') {$errors[] = 'Kategori wajib diisi.';
    }
    if ($price === false || $price < 0) {$errors[] = 'Harga tidak boleh negatif.';
    }
    if ($stock === false || $stock < 0) {$errors[] = 'Stok awal tidak boleh negatif.';
    }

    if (empty($errors)) {
        $stmt =$pdo->prepare("INSERT INTO products (name, category, price, stock, created_at, updated_at) VALUES (:name, :category, :price, :stock, NOW(), NOW())");
        $stmt->execute([
            'name' => $name,
            'category' => $category,
            'price' => $price,
            'stock' => $stock
        ]);

        header('Location: index.php');
        exit;
    }
}

require_once __DIR__ . '/../../views/header.php';
?>

<div style="max-width: 500px; margin: auto;">
    <h2>+ Tambah Produk Merchandise</h2>

    <?php if (!empty($errors)): ?>
        <div style="background: #fee2e2; border: 1px solid #ef4444; color: #b91c1c; padding: 10px; border-radius: 6px; margin-bottom: 16px;">
            <ul style="margin: 0; padding-left: 20px;">
                <?php foreach ($errors as$err): ?>
                    <li><?= e($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div style="margin-bottom: 14px;">
            <label style="display: block; font-weight: bold; margin-bottom: 4px;">Nama Produk *</label>
            <input type="text" name="name" value="<?= e($_POST['name'] ?? '') ?>" required style="width: 100%; box-sizing: border-box; padding: 8px;">
        </div>

        <div style="margin-bottom: 14px;">
            <label style="display: block; font-weight: bold; margin-bottom: 4px;">Kategori *</label>
            <input type="text" name="category" placeholder="Contoh: Lightstick, Album, Photocard" value="<?= e($_POST['category'] ?? '') ?>" required style="width: 100%; box-sizing: border-box; padding: 8px;">
        </div>

        <div style="margin-bottom: 14px;">