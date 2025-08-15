<?php
session_start();
require 'conexao.php';

// Verifica se é vendedor OU admin OU ambos 
if (
    !isset($_SESSION['Usuario']) ||
    !in_array($_SESSION['Usuario']['tipo_usuario'], ['vendedor', 'ambos', 'admin'])
) {
    header('Location: login.php'); // ou 403 Forbidden
    exit;
}

// Filtros
$filtro_data_inicio = $_GET['data_inicio'] ?? null;
$filtro_data_fim = $_GET['data_fim'] ?? null;

// Consulta base
$query = "
    SELECT 
        Vendas.*, 
        Imagens.titulo as produto,
        Usuarios.nome as comprador,
        Usuarios.email
    FROM Vendas
    JOIN Imagens ON Vendas.id_imagem = Imagens.id
    JOIN Usuarios ON Vendas.id_comprador = Usuarios.id
    WHERE Imagens.id_usuario = ?
";

$params = [$_SESSION['Usuario']['id']];

// Aplicar filtros
if ($filtro_data_inicio) {
    $query .= " AND Vendas.data_venda >= ?";
    $params[] = $filtro_data_inicio;
}

if ($filtro_data_fim) {
    $query .= " AND Vendas.data_venda <= ?";
    $params[] = $filtro_data_fim . ' 23:59:59';
}

$query .= " ORDER BY Vendas.data_venda DESC";

// Executar consulta
$vendas = $pdo->prepare($query);
$vendas->execute($params);
$vendas = $vendas->fetchAll();

// Calcular totais
$total_vendas = array_sum(array_column($vendas, 'valor_pago'));

// Gerar PDF
if (isset($_GET['gerar_pdf'])) {
    require __DIR__ . '/vendor/autoload.php';
    
    $dompdf = new Dompdf\Dompdf();
    
    $html = '
    <h1 style="text-align:center">Relatório de Vendas</h1>
    <p style="text-align:center">Período: ' . ($filtro_data_inicio ? date('d/m/Y', strtotime($filtro_data_inicio)) : 'Início') . 
    ' até ' . ($filtro_data_fim ? date('d/m/Y', strtotime($filtro_data_fim)) : 'Atual') . '</p>
    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Data</th>
                <th>Produto</th>
                <th>Comprador</th>
                <th>Valor</th>
                <th>Método</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';
    
    foreach ($vendas as $venda) {
        $html .= '
            <tr>
                <td>' . date('d/m/Y H:i', strtotime($venda['data_venda'])) . '</td>
                <td>' . htmlspecialchars($venda['produto']) . '</td>
                <td>' . htmlspecialchars($venda['comprador']) . '<br><small>' . $venda['email'] . '</small></td>
                <td>R$ ' . number_format($venda['valor_pago'], 2, ',', '.') . '</td>
                <td>' . htmlspecialchars($venda['metodo_pagamento']) . '</td>
                <td>' . htmlspecialchars($venda['status']) . '</td>
            </tr>';
    }
    
    $html .= '
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align:right"><strong>Total:</strong></td>
                <td colspan="3">R$ ' . number_format($total_vendas, 2, ',', '.') . '</td>
            </tr>
        </tfoot>
    </table>';
    
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("relatorio_vendas.pdf", array("Attachment" => true));
    exit;
}

// Preparar dados para o gráfico (agrupar por dia)
$dadosGrafico = [];
foreach ($vendas as $venda) {
    $data = date('Y-m-d', strtotime($venda['data_venda']));
    if (!isset($dadosGrafico[$data])) {
        $dadosGrafico[$data] = 0;
    }
    $dadosGrafico[$data] += $venda['valor_pago'];
}

// Ordenar por data
ksort($dadosGrafico);

// Classes para os status
$statusClasses = [
    'completo' => 'bg-success',
    'pendente' => 'bg-warning text-dark',
    'cancelado' => 'bg-danger'
];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel do Vendedor - Pixel em Arte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .card-total {
            background: linear-gradient(135deg, #4e54c8, #8f94fb);
            color: white;
        }
        .badge-status {
            font-size: 0.9rem;
            padding: 5px 10px;
        }
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 20px;
        }
        .modal-lg {
            max-width: 90%;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">
            <i class="fas fa-user-tie"></i> Área do Vendedor
        </span>
        <div>
            <a href="dashboard.php" class="btn btn-outline-light me-2">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <a href="logout.php" class="btn btn-danger">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>
    </div>
</nav>    

<div class="container-fluid mt-4">
    <h2><i class="fas fa-chart-line"></i> Painel de Vendas</h2>
    
    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Data Início</label>
                    <input type="date" name="data_inicio" class="form-control" value="<?= $filtro_data_inicio ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Data Fim</label>
                    <input type="date" name="data_fim" class="form-control" value="<?= $filtro_data_fim ?>">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <?php if ($filtro_data_inicio): ?>
                        <a href="vendas_vendedor.php" class="btn btn-outline-secondary ms-2">
                            Limpar
                        </a>
                    <?php endif; ?>
                </div>
                <div class="col-md-3 d-flex align-items-end justify-content-end">
                    <button type="button" class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#graficoModal">
                        <i class="fas fa-chart-bar"></i> Ver Gráfico
                    </button>
                    <a href="?<?= http_build_query(array_merge($_GET, ['gerar_pdf' => 1])) ?>" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Gerar PDF
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Cards de Resumo -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card card-total text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Vendido</h5>
                    <h2>R$ <?= number_format($total_vendas, 2, ',', '.') ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Vendas Realizadas</h5>
                    <h2><?= count($vendas) ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Vendas -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Produto</th>
                            <th>Comprador</th>
                            <th>Valor</th>
                            <th>Método</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vendas as $venda): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($venda['data_venda'])) ?></td>
                            <td><?= htmlspecialchars($venda['produto']) ?></td>
                            <td>
                                <?= htmlspecialchars($venda['comprador']) ?>
                                <small class="text-muted d-block"><?= $venda['email'] ?></small>
                            </td>
                            <td>R$ <?= number_format($venda['valor_pago'], 2, ',', '.') ?></td>
                            <td><?= htmlspecialchars($venda['metodo_pagamento']) ?></td>
                            <td>
                                <span class="badge <?= $statusClasses[$venda['status']] ?? 'bg-secondary' ?> badge-status">
                                    <?= htmlspecialchars($venda['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal do Gráfico -->
<div class="modal fade" id="graficoModal" tabindex="-1" aria-labelledby="graficoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="graficoModalLabel">Gráfico de Vendas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="chart-container">
                    <canvas id="vendasChart"></canvas>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Gráfico
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('vendasChart').getContext('2d');
    const vendasChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [<?= '"' . implode('","', array_keys($dadosGrafico)) . '"' ?>],
            datasets: [{
                label: 'Vendas por Dia (R$)',
                data: [<?= implode(',', array_values($dadosGrafico)) ?>],
                backgroundColor: 'rgba(78, 84, 200, 0.2)',
                borderColor: 'rgba(78, 84, 200, 1)',
                borderWidth: 2,
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'R$ ' + context.raw.toLocaleString('pt-BR');
                        }
                    }
                }
            }
        }
    });
});
</script>
</body>
</html>