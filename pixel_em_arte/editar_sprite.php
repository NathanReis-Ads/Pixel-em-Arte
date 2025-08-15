<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['Usuario'])) {
    header('Location: login.php');
    exit;
}

// Buscar dados atuais
$sprite = [];
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM Imagens WHERE id = ? AND id_usuario = ?");
    $stmt->execute([$_GET['id'], $_SESSION['Usuario']['id']]);
    $sprite = $stmt->fetch();
    
    if (!$sprite) {
        $_SESSION['mensagem'] = "Sprite não encontrado!";
        header('Location: dashboard.php');
        exit;
    }
}

// Processar atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE Imagens SET 
                          titulo = ?, 
                          descricao = ?, 
                          preco = ?, 
                          url_imagem = ? 
                          WHERE id = ? AND id_usuario = ?");
    
    $stmt->execute([
        $_POST['titulo'],
        $_POST['descricao'],
        $_POST['preco'],
        $_POST['url_imagem'],
        $_POST['id'],
        $_SESSION['Usuario']['id']
    ]);
    
    $_SESSION['mensagem'] = "Sprite atualizado com sucesso!";
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Sprite - Pixel em Arte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Editar Sprite</h2>
        
        <form method="POST">
            <input type="hidden" name="id" value="<?= $sprite['id'] ?>">
            
            <div class="mb-3">
                <label class="form-label">Título</label>
                <input type="text" name="titulo" class="form-control" 
                       value="<?= htmlspecialchars($sprite['titulo']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Descrição</label>
                <textarea name="descricao" class="form-control"><?= 
                    htmlspecialchars($sprite['descricao']) ?></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Preço</label>
                <input type="number" step="0.01" name="preco" class="form-control" 
                       value="<?= $sprite['preco'] ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">URL da Imagem</label>
                <input type="text" name="url_imagem" class="form-control" 
                       value="<?= htmlspecialchars($sprite['url_imagem']) ?>" required>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </div>
</body>
</html>