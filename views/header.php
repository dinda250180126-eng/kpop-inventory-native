<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>K-Pop Inventory (PHP Native)</title>
    <style>
        body { font-family: sans-serif; background: #f8fafc; margin: 0; padding: 20px; color: #1e293b; }
        .container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; }
        nav { display: flex; gap: 12px; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; }
        nav a { text-decoration: none; color: #0284c7; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #e2e8f0; padding: 8px 12px; text-align: left; }
        th { background: #f1f5f9; }
        .btn { padding: 6px 12px; background: #0284c7; color: white; border-radius: 4px; text-decoration: none; border: none; cursor: pointer; }
        .btn-danger { background: #e11d48; }
        .badge { padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <nav>
        <a href="../dashboard.php">Dashboard</a>
        <a href="../products/index.php">Produk</a>
        <a href="../stock/index.php">Mutasi Stok</a>
        <a href="../logout.php" style="margin-left:auto; color: #e11d48;">Logout (<?= e($_SESSION['user']['name'] ?? '') ?>)</a>
    </nav>