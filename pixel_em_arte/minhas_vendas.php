<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['Usuario'])) {
    header('Location: login.php');
    exit;
}

// Buscar vendas do usuário
$vendas = $pdo->prepare("
    SELECT Vendas.*, Imagens.titulo, Imagens.url_imagem 
    FROM Vendas
    JOIN Imagens ON Vendas.id_imagem = Imagens.id
    WHERE Vendas.id_comprador = ?
    ORDER BY Vendas.data_venda DESC
");
$vendas->execute([$_SESSION['Usuario']['id']]);
$vendas = $vendas->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Histórico de Compras - Pixel em Arte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .venda-card {
            border-left: 4px solid #28a745;
            transition: all 0.3s;
        }
        .venda-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4"><i class="fas fa-history"></i> Histórico de Compras</h2>
        
        <?php if (empty($vendas)): ?>
            <div class="alert alert-info">
                Você ainda não realizou compras. <a href="marketplace.php" class="alert-link">Comece a comprar!</a>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 g-4">
                <?php foreach ($vendas as $venda): ?>
                <div class="col">
                    <div class="card venda-card mb-3">
                        <div class="row g-0">
                            <div class="col-md-2">
                                <img src="<?= htmlspecialchars($venda['url_imagem']) ?>" 
                                     class="img-fluid rounded-start" 
                                     style="height: 100px; object-fit: cover;">
                            </div>
                            <div class="col-md-10">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($venda['titulo']) ?></h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p><strong>Valor:</strong> R$ <?= number_format($venda['valor_pago'], 2, ',', '.') ?></p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Método:</strong> <?= htmlspecialchars($venda['metodo_pagamento']) ?></p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($venda['data_venda'])) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
