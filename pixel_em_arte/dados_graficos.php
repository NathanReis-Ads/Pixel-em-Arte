<?php
ob_start();
session_start();
require 'conexao.php';

header('Content-Type: application/json');

// Verificação de sessão
if (!isset($_SESSION['Usuario'])) {
    echo json_encode(['error' => 'Acesso não autorizado']);
    exit;
}

try {
    $dados = [];
    $usuario_id = $_SESSION['Usuario']['id'];

    // 1. Vendas por mês
    $stmt = $pdo->prepare("
        SELECT 
            DATE_FORMAT(V.data_venda, '%Y-%m') as mes, 
            CAST(SUM(V.valor_pago) AS DECIMAL(10,2)) as total
        FROM Vendas V
        JOIN Imagens I ON V.id_imagem = I.id
        WHERE I.id_usuario = ?
        GROUP BY mes
        ORDER BY mes
    ");
    $stmt->execute([$usuario_id]);
    $dados['vendas_mes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Produtos mais vendidos
    $stmt = $pdo->prepare("
        SELECT 
            I.titulo as produto, 
            COUNT(*) as quantidade
        FROM Vendas V
        JOIN Imagens I ON V.id_imagem = I.id
        WHERE I.id_usuario = ?
        GROUP BY produto
        ORDER BY quantidade DESC
        LIMIT 5
    ");
    $stmt->execute([$usuario_id]);
    $dados['produtos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Métodos de pagamento
    $stmt = $pdo->prepare("
        SELECT 
            V.metodo_pagamento, 
            COUNT(*) as total
        FROM Vendas V
        JOIN Imagens I ON V.id_imagem = I.id
        WHERE I.id_usuario = ?
        GROUP BY V.metodo_pagamento
    ");
    $stmt->execute([$usuario_id]);
    $dados['pagamentos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($_SESSION['Usuario']['tipo_usuario'] === 'admin') {
    $stmt = $pdo->query("
        SELECT 
            DATE_FORMAT(dat_criacao, '%Y-%m') as mes, 
            COUNT(*) as total
        FROM Usuarios
        GROUP BY mes
        ORDER BY mes
    ");
    $dados['usuarios'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    ob_end_clean(); // Limpa o buffer antes de enviar o JSON
    echo json_encode($dados);

} catch (PDOException $e) {
    ob_end_clean();
    echo json_encode(['error' => 'Erro no banco: ' . $e->getMessage()]);
}
