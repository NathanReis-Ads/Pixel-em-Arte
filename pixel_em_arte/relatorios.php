<?php
session_start();
if (!isset($_SESSION['Usuario'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatórios - Pixel em Arte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #3f51b5, #9c27b0);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .relatorio-box {
            background: white;
            padding: 30px;
            border-radius: 15px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .relatorio-box h3 {
            margin-bottom: 25px;
        }
    </style>
</head>
<body>

<div class="relatorio-box text-center">
    <h3><i class="fas fa-file-pdf text-danger"></i> Relatórios em PDF</h3>

    <div class="d-grid gap-3">
        <a href="relatorio_vendas.php" class="btn btn-outline-primary">
            <i class="fas fa-chart-line"></i> Vendas por Mês
        </a>
        <a href="relatorio_produtos.php" class="btn btn-outline-success">
            <i class="fas fa-palette"></i> Produtos Mais Vendidos
        </a>
        <a href="relatorio_pagamentos.php" class="btn btn-outline-info">
            <i class="fas fa-credit-card"></i> Métodos de Pagamento
        </a>
        <?php if ($_SESSION['Usuario']['tipo_usuario'] === 'admin'): ?>
        <a href="relatorio_usuarios.php" class="btn btn-outline-warning">
            <i class="fas fa-users"></i> Crescimento de Usuários
        </a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
