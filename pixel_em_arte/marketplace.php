<?php
session_start();
require 'conexao.php';

$where = '';
$params = [];

if (isset($_GET['categoria']) && is_numeric($_GET['categoria'])) {
    $where = "WHERE image_categoria.id_categoria = ?";
    $params[] = $_GET['categoria'];
}

$query = "
SELECT 
    Imagens.*, 
    Usuarios.nome as vendedor_nome,
    categoria.nome AS categoria_nome,
    categoria.descricao AS categoria_desc
FROM Imagens
JOIN Usuarios ON Imagens.id_usuario = Usuarios.id
LEFT JOIN image_categoria ON Imagens.id = image_categoria.id_imagem
LEFT JOIN categoria ON image_categoria.id_categoria = categoria.id
$where
ORDER BY Imagens.id DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$sprites = $stmt->fetchAll();


?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Marketplace - Pixel em Arte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sprite-card {
            border: none;
            border-radius: 10px;
            transition: all 0.3s;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .sprite-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .sprite-img {
            height: 200px;
            object-fit: contain;
            background: #f5f5f5;
        }
        .price-tag {
            background: #e63946;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="marketplace.php">Pixel em Arte</a>
        <div class="d-flex align-items-center">
            <?php if(isset($_SESSION['Usuario'])): ?>
                <span class="text-white me-3">Olá, <?= htmlspecialchars($_SESSION['Usuario']['nome']) ?></span>
                <a href="dashboard.php" class="btn btn-outline-light me-2">Meu Dashboard</a>
                <a href="carrinho.php" class="btn btn-outline-light me-2">
                    <i class="fas fa-shopping-cart"></i>
                </a>
                <a href="logout.php" class="btn btn-danger">Sair</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-light">Entrar</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container mb-5">
    <h1 class="text-center mb-5">Pixel Arts Disponíveis</h1>
<form method="GET" class="mb-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <select name="categoria" class="form-select" onchange="this.form.submit()">
                <option value="">Todas as categorias</option>
                <?php
                $cats = $pdo->query("SELECT id, nome FROM categoria ORDER BY nome")->fetchAll();
                foreach ($cats as $cat) {
                    $selected = (isset($_GET['categoria']) && $_GET['categoria'] == $cat['id']) ? 'selected' : '';
                    echo "<option value='{$cat['id']}' $selected>" . htmlspecialchars($cat['nome']) . "</option>";
                }
                ?>
            </select>
        </div>
    </div>
</form>
    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
        <?php foreach($sprites as $sprite): ?>
        <div class="col">
            <div class="card h-100 sprite-card">
                <img src="<?= htmlspecialchars($sprite['url_imagem']) ?>" class="card-img-top sprite-img" alt="<?= htmlspecialchars($sprite['titulo']) ?>">
                <div class="card-body">
    <h5 class="card-title"><?= htmlspecialchars($sprite['titulo']) ?></h5>
    <p class="card-text text-muted small"><?= htmlspecialchars($sprite['descricao']) ?></p>
    
    <?php if (!empty($sprite['categoria_nome'])): ?>
        <span class="badge bg-secondary mb-2" 
              title="<?= htmlspecialchars($sprite['categoria_desc']) ?>">
            <?= htmlspecialchars($sprite['categoria_nome']) ?>
        </span>
    <?php endif; ?>
    
    <p class="text-muted small">Vendedor: <?= htmlspecialchars($sprite['vendedor_nome']) ?></p>
</div>
                <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                    <span class="price-tag">R$ <?= number_format($sprite['preco'], 2, ',', '.') ?></span>
                    <a href="carrinho.php?adicionar=<?= $sprite['id'] ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-cart-plus"></i> Adicionar
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>