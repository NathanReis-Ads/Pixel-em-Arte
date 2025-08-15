<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['Usuario'])) {
    header('Location: login.php');
    exit;
}

// Função para inserir novo sprite
if (isset($_POST['adicionar'])) {
    $pdo->beginTransaction();

    // Inserir imagem
    $stmt = $pdo->prepare("INSERT INTO Imagens (id_usuario, titulo, descricao, preco, url_imagem) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['Usuario']['id'],
        $_POST['titulo'],
        $_POST['descricao'],
        $_POST['preco'],
        $_POST['url_imagem']
    ]);

    $imagem_id = $pdo->lastInsertId();
    $categoria_id = $_POST['categoria_id'];

    // Relacionar com a categoria
    $stmtCat = $pdo->prepare("INSERT INTO image_categoria (id_imagem, id_categoria) VALUES (?, ?)");
    $stmtCat->execute([$imagem_id, $categoria_id]);

    $pdo->commit();

    $_SESSION['mensagem'] = "Sprite adicionado com sucesso!";
    header("Location: dashboard.php");
    exit;
}


// Função para deletar sprite
if (isset($_GET['excluir'])) {
    $stmt = $pdo->prepare("DELETE FROM Imagens WHERE id = ? AND id_usuario = ?");
    $stmt->execute([$_GET['excluir'], $_SESSION['Usuario']['id']]);
    $_SESSION['mensagem'] = "Sprite excluído com sucesso!";
    header("Location: dashboard.php");
    exit;
}
// Buscar todas as categorias
        $stmtCat = $pdo->query("SELECT id, nome, descricao FROM categoria ORDER BY nome");
        $categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

// Buscar todos os sprites do usuário
$imagens = $pdo->prepare("SELECT * FROM Imagens WHERE id_usuario = ? ORDER BY id DESC");
$imagens->execute([$_SESSION['Usuario']['id']]);
$imagens = $imagens->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Pixel em Arte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card-img-preview {
            height: 150px;
            object-fit: contain;
            background-color: #f5f5f5;
        }
        .navbar-custom {
            background: linear-gradient(135deg, #3f51b5, #9c27b0);
        }
        .card {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .card-header {
            font-weight: 600;
            cursor: pointer;
        }
        .card-body canvas {
            width: 100% !important;
            height: 250px !important;
        }
        .toggle-chart.active {
            transform: translateY(2px);
            box-shadow: inset 0 3px 5px rgba(0,0,0,0.2);
        }
        .chart-card {
            transition: all 0.3s ease;
        }
        canvas {
    border: 1px solid red !important;
    background-color: #f9f9f9 !important;
}
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Pixel em Arte</a>
            <div class="d-flex align-items-center">
                <?php 
                $tipo = $_SESSION['Usuario']['tipo_usuario'];
                
                 if (in_array($tipo, ['vendedor', 'ambos', 'admin'])): ?>
                    <a href="vendas_vendedor.php" class="btn btn-outline-light me-2">
                        <i class="fas fa-chart-line"></i> Painel de Vendas
                    </a>
                <?php endif; ?>
                
                <?php if (in_array($tipo, ['comprador', 'ambos', 'admin'])): ?>
                    <a href="minhas_vendas.php" class="btn btn-outline-light me-2">
                        <i class="fas fa-shopping-bag"></i> Minhas Compras
                    </a>
                <?php endif; ?>
                
                <a href="marketplace.php" class="btn btn-outline-light me-2">
                    <i class="fas fa-store"></i> Marketplace
                </a>
                <a href="relatorios.php" class="btn btn-outline-light me-2">
                    <i class="fas fa-file-alt"></i> Relatórios
                </a>
                <span class="text-white me-3">Olá, <?= htmlspecialchars($_SESSION['Usuario']['nome']) ?></span>
                <a href="logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <!-- Mensagens de feedback -->
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="alert alert-info alert-dismissible fade show">
                <?= $_SESSION['mensagem'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['mensagem']); ?>
        <?php endif; ?>

        <h2 class="mb-4">Meu Dashboard</h2>

        <!-- Controles das Abas -->
        <div class="mb-4">
            <button class="btn btn-primary me-2 toggle-chart" data-target="vendasMesChart">
                <i class="fas fa-chart-line"></i> Vendas por Mês
            </button>
            <button class="btn btn-success me-2 toggle-chart" data-target="produtosChart">
                <i class="fas fa-chart-pie"></i> Produtos Mais Vendidos
            </button>
            <button class="btn btn-info me-2 toggle-chart" data-target="pagamentosChart">
                <i class="fas fa-credit-card"></i> Métodos de Pagamento
            </button>
            <?php if ($_SESSION['Usuario']['tipo_usuario'] === 'admin'): ?>
            <button class="btn btn-warning toggle-chart" data-target="usuariosChart">
                <i class="fas fa-users"></i> Crescimento de Usuários
            </button>
            <?php endif; ?>
        </div>

        <!-- Container dos Gráficos (inicialmente oculto) -->
        <div id="charts-container" style="display: none;">
            <!-- Vendas por Mês -->
            <div class="card mb-4 chart-card" id="vendasMesChart-container">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-chart-line"></i> Vendas por Mês
                </div>
                <div class="card-body">
                    <canvas id="vendasMesChart" height="250"></canvas>
                </div>
            </div>

            <!-- Produtos Mais Vendidos -->
            <div class="card mb-4 chart-card" id="produtosChart-container">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-chart-pie"></i> Produtos Mais Vendidos
                </div>
                <div class="card-body">
                    <canvas id="produtosChart" height="250"></canvas>
                </div>
            </div>

            <!-- Métodos de Pagamento -->
            <div class="card mb-4 chart-card" id="pagamentosChart-container">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-credit-card"></i> Métodos de Pagamento
                </div>
                <div class="card-body">
                    <canvas id="pagamentosChart" height="250"></canvas>
                </div>
            </div>

            <!-- Crescimento de Usuários (se for admin) -->
            <?php if ($_SESSION['Usuario']['tipo_usuario'] === 'admin'): ?>
            <div class="card mb-4 chart-card" id="usuariosChart-container">
                <div class="card-header bg-warning text-dark">
                    <i class="fas fa-users"></i> Crescimento de Usuários
                </div>
                <div class="card-body">
                    <canvas id="usuariosChart" height="250"></canvas>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <!-- Formulário para adicionar novo sprite -->
        <div class="card mb-5">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-plus-circle"></i> Adicionar Novo Sprite
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="adicionar" value="1">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="titulo" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Preço (R$)</label>
                            <input type="number" step="0.01" name="preco" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea name="descricao" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL da Imagem</label>
                        <input type="text" name="url_imagem" class="form-control" required>
                    </div>
                    <div class="mb-3">
                         <label class="form-label">Categoria</label>
                        <select name="categoria_id" class="form-select" required>
                        <option value="">Selecione uma categoria</option>
                        <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>" title="<?= htmlspecialchars($cat['descricao']) ?>">
                    <?= htmlspecialchars($cat['nome']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <small class="form-text text-muted">Passe o mouse sobre o nome para ver a descrição.</small>
</div>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Salvar Sprite
                    </button>
                </form>
            </div>
        </div>

        <!-- Listagem dos sprites -->
        <h3 class="mb-4">Meus Sprites</h3>
        
        <?php if (empty($imagens)): ?>
            <div class="alert alert-info">
                Você ainda não possui sprites cadastrados.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Preview</th>
                            <th>Título</th>
                            <th>Descrição</th>
                            <th>Preço</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($imagens as $img): ?>
                        <tr>
                            <td>
                                <img src="<?= htmlspecialchars($img['url_imagem']) ?>" 
                                     class="card-img-preview" 
                                     alt="<?= htmlspecialchars($img['titulo']) ?>">
                            </td>
                            <td><?= htmlspecialchars($img['titulo']) ?></td>
                            <td><?= htmlspecialchars($img['descricao']) ?></td>
                            <td>R$ <?= number_format($img['preco'], 2, ',', '.') ?></td>
                            <td>
                                <a href="editar_sprite.php?id=<?= $img['id'] ?>" 
                                   class="btn btn-sm btn-warning"
                                   title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="dashboard.php?excluir=<?= $img['id'] ?>" 
                                   class="btn btn-sm btn-danger"
                                   title="Excluir"
                                   onclick="return confirm('Tem certeza que deseja excluir este sprite?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    // Variável para controlar se os gráficos já foram carregados
    let chartsLoaded = false;
    let chartsInstances = {};
    
    // Função para formatar valores monetários
    const formatMoney = (value) => {
        return 'R$ ' + parseFloat(value).toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    };

    // Opções comuns dos gráficos
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            tooltip: {
                callbacks: {
                    label: (context) => formatMoney(context.raw)
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: (value) => formatMoney(value)
                }
            }
        }
    };

    // Função para inicializar um gráfico específico
    const initChart = (target) => {
        if (chartsInstances[target]) return; // Já foi inicializado
        
        $.ajax({
    url: 'dados_graficos.php',
    dataType: 'json',
    beforeSend: function() {
        // Limpa qualquer erro anterior
        $('#charts-container').find('.alert-danger').remove();
    },
    success: function(data) {
        console.log("Dados recebidos:", data);
        
        // Verifica se os dados são válidos
        if (!data || typeof data !== 'object') {
            throw new Error("Dados inválidos recebidos do servidor");
        }

        switch(target) {
            case 'vendasMesChart':
                chartsInstances[target] = new Chart(
                    document.getElementById(target),
                    {
                        type: 'line',
                        data: {
                            labels: data.vendas_mes ? data.vendas_mes.map(item => item.mes) : [],
                            datasets: [{
                                label: 'Vendas',
                                data: data.vendas_mes ? data.vendas_mes.map(item => parseFloat(item.total)) : [],
                                borderColor: '#4e54c8',
                                backgroundColor: '#8f94fb',
                                tension: 0.3,
                                fill: true
                            }]
                        },
                        options: chartOptions
                    }
                );
                break;
                
            case 'produtosChart':
                chartsInstances[target] = new Chart(
                    document.getElementById(target),
                    {
                        type: 'pie',
                        data: {
                            labels: data.produtos ? data.produtos.map(item => item.produto) : [],
                            datasets: [{
                                data: data.produtos ? data.produtos.map(item => parseInt(item.quantidade)) : [],
                                backgroundColor: ['#FF6384','#36A2EB','#FFCE56','#4BC0C0','#9966FF']
                            }]
                        },
                        options: chartOptions
                    }
                );
                break;
                
            case 'pagamentosChart':
                chartsInstances[target] = new Chart(
                    document.getElementById(target),
                    {
                        type: 'doughnut',
                        data: {
                            labels: data.pagamentos ? data.pagamentos.map(item => item.metodo_pagamento) : [],
                            datasets: [{
                                data: data.pagamentos ? data.pagamentos.map(item => parseInt(item.total)) : [],
                                backgroundColor: ['#FF9F40','#FFCD56','#47CCCC']
                            }]
                        },
                        options: chartOptions
                    }
                );
                break;

                case 'usuariosChart':
    chartsInstances[target] = new Chart(
        document.getElementById(target),
        {
            type: 'bar',
            data: {
                labels: data.usuarios.map(item => item.mes),
                datasets: [{
                    label: 'Novos Usuários',
                    data: data.usuarios.map(item => parseInt(item.total)),
                    backgroundColor: '#fbc02d'
                }]
            },
            options: { /* ... */ }
        }
    );
    break;
        }
    },
    error: function(xhr, status, error) {
    console.error("Erro ao carregar dados:", status, error);
    let errorMsg = "Erro ao carregar dados. Verifique o console.";
    
    // Tenta extrair a resposta do servidor para diagnóstico
    try {
        const responseText = xhr.responseText.trim();
        if (responseText) {
            errorMsg += "<br><br>Resposta do servidor:<br><code>" + 
                responseText.substring(0, 200) + 
                (responseText.length > 200 ? "..." : "") + 
                "</code>";
        }
    } catch (e) {
        console.error("Erro ao processar resposta:", e);
    }
    
    $('#charts-container').html(
        '<div class="alert alert-danger">' + errorMsg + '</div>'
    );
}
});
    };

    // Controle dos botões
    $('.toggle-chart').click(function() {
        const target = $(this).data('target');
        
        // Mostra o container se estiver oculto
        if ($('#charts-container').is(':hidden')) {
            $('#charts-container').show();
        }
        
        // Esconde todos os gráficos
        $('.chart-card').hide();
        
        // Mostra apenas o gráfico selecionado
        $(`#${target}-container`).show();
        
        // Inicializa o gráfico se necessário
        initChart(target);
        
        // Atualiza os botões ativos
        $('.toggle-chart').removeClass('active');
        $(this).addClass('active');
    });
});
</script>
</body>
</html>