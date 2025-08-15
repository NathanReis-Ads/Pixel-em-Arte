<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['Usuario'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['adicionar'])) {
    $imagem_id = (int)$_GET['adicionar'];
    
    // Buscar dados do produto no banco
    $stmt = $pdo->prepare("SELECT id, titulo, preco, url_imagem FROM Imagens WHERE id = ?");
    $stmt->execute([$imagem_id]);
    $produto = $stmt->fetch();

    if ($produto) {
        // Inicializar carrinho se não existir
        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }

        // Adicionar ou incrementar quantidade
        if (isset($_SESSION['carrinho'][$imagem_id])) {
            $_SESSION['carrinho'][$imagem_id]['quantidade']++;
        } else {
            $_SESSION['carrinho'][$imagem_id] = [
                'id' => $produto['id'],
                'titulo' => $produto['titulo'],
                'preco' => $produto['preco'],
                'url_imagem' => $produto['url_imagem'],
                'quantidade' => 1
            ];
        }
        
        $_SESSION['mensagem'] = "{$produto['titulo']} adicionado ao carrinho!";
    } else {
        $_SESSION['erro'] = "Produto não encontrado!";
    }
    
    header('Location: carrinho.php');
    exit;
}

if (isset($_GET['remover'])) {
    $imagem_id = (int)$_GET['remover'];
    
    if (isset($_SESSION['carrinho'][$imagem_id])) {
        $nome_produto = $_SESSION['carrinho'][$imagem_id]['titulo'];
        unset($_SESSION['carrinho'][$imagem_id]);
        $_SESSION['mensagem'] = "$nome_produto removido do carrinho!";
        
        // Limpar carrinho se estiver vazio
        if (empty($_SESSION['carrinho'])) {
            unset($_SESSION['carrinho']);
        }
    }
    
    header('Location: carrinho.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar_compra'])) {
    try {
        $pdo->beginTransaction();
        
        foreach ($_SESSION['carrinho'] as $imagem_id => $item) {
            // Registrar venda
            $stmt = $pdo->prepare("INSERT INTO Vendas 
                (id_comprador, id_imagem, valor_pago, data_venda, metodo_pagamento) 
                VALUES (?, ?, ?, NOW(), ?)");
            
            $stmt->execute([
                $_SESSION['Usuario']['id'],
                $imagem_id,
                $item['preco'] * $item['quantidade'],
                $_POST['metodo_pagamento'] // Novo campo do formulário
            ]);
            
            // Registrar pagamento (opcional)
            $stmt = $pdo->prepare("INSERT INTO Pagamento 
                (id_venda, status_pagamento, data_pagamento) 
                VALUES (?, 'aprovado', NOW())");
            $stmt->execute([$pdo->lastInsertId()]);
        }
        
        $pdo->commit();
        unset($_SESSION['carrinho']);
        $_SESSION['mensagem'] = "Compra finalizada! Método: " . htmlspecialchars($_POST['metodo_pagamento']);
        header('Location: carrinho.php');
        exit;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['erro'] = "Erro: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Carrinho - Pixel em Arte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .card-number {
            letter-spacing: 2px;
        }
        .card-img-preview {
            height: 80px;
            object-fit: contain;
        }
        #cartaoModal .form-control {
            padding: 10px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="alert alert-success"><?= $_SESSION['mensagem'] ?></div>
            <?php unset($_SESSION['mensagem']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['erro'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['erro'] ?></div>
            <?php unset($_SESSION['erro']); ?>
        <?php endif; ?>

        <h2 class="mb-4"><i class="fas fa-shopping-cart"></i> Seu Carrinho</h2>
        
        <?php if (empty($_SESSION['carrinho'])): ?>
            <div class="alert alert-info">
                Seu carrinho está vazio. <a href="marketplace.php" class="alert-link">Comece a comprar!</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th>Preço Unitário</th>
                            <th>Quantidade</th>
                            <th>Subtotal</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        foreach ($_SESSION['carrinho'] as $id => $item): 
                            $subtotal = $item['preco'] * $item['quantidade'];
                            $total += $subtotal;
                        ?>
                            <tr>
                                <td>
                                    <img src="<?= htmlspecialchars($item['url_imagem'] ?? '') ?>" 
                                         class="card-img-preview me-2">
                                    <?= htmlspecialchars($item['titulo']) ?>
                                </td>
                                <td>R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                                <td><?= $item['quantidade'] ?></td>
                                <td>R$ <?= number_format($subtotal, 2, ',', '.') ?></td>
                                <td>
                                    <a href="carrinho.php?remover=<?= $id ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Remover este item?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-group-divider">
                        <tr>
                            <th colspan="3" class="text-end">Total:</th>
                            <th colspan="2">R$ <?= number_format($total, 2, ',', '.') ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="marketplace.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Continuar Comprando   
                </a>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#cartaoModal">
                    <i class="fas fa-credit-card"></i> Finalizar Compra
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal de Pagamento -->
    <div class="modal fade" id="cartaoModal" tabindex="-1" aria-labelledby="cartaoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="cartaoModalLabel"><i class="fas fa-credit-card"></i> Pagamento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="formPagamento">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Número do Cartão</label>
                                <input type="text" id="numeroCartao" name="numero_cartao" class="form-control card-number" 
                                       placeholder="0000 0000 0000 0000" maxlength="19" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nome no Cartão</label>
                                <input type="text" name="nome_cartao" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Validade</label>
                                <input type="text" name="validade" class="form-control" placeholder="MM/AA" maxlength="5" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">CVV</label>
                                <input type="text" name="cvv" class="form-control" placeholder="123" maxlength="3" required>
                            </div>
                            <div class="col-md-4 mb-3">
    <label class="form-label">Método de Pagamento</label>
    <select name="metodo_pagamento" class="form-select" required>
        <option value="Cartão">Cartão</option>
        <option value="Pix">Pix</option>
        <option value="Boleto">Boleto</option>
        <option value="Transferência">Transferência</option>
    </select>
</div>
                            <div class="col-md-4">
                                <label class="form-label">Parcelas</label>
                                <select name="parcelas" class="form-select" required>
                                    <option value="1">1x R$ <?= number_format($total, 2, ',', '.') ?></option>
                                    <option value="2">2x R$ <?= number_format($total/2, 2, ',', '.') ?></option>
                                    <option value="3">3x R$ <?= number_format($total/3, 2, ',', '.') ?></option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Esta é uma simulação. Nenhum pagamento real será processado.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="finalizar_compra" class="btn btn-success">
                            <i class="fas fa-check"></i> Confirmar Pagamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Formatação do número do cartão
        document.getElementById('numeroCartao').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '');
            if (value.length > 0) {
                value = value.match(new RegExp('.{1,4}', 'g')).join(' ');
            }
            e.target.value = value;
        });

        // Formatação da validade (MM/AA)
        document.querySelector('input[name="validade"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });

        // Validação do formulário
        document.getElementById('formPagamento').addEventListener('submit', function(e) {
            const cartao = document.getElementById('numeroCartao').value.replace(/\s/g, '');
            if (cartao.length !== 16 || !/^\d+$/.test(cartao)) {
                alert('Número do cartão inválido! Deve ter 16 dígitos.');
                e.preventDefault();
            }
        });
    </script>
</body>
</html>