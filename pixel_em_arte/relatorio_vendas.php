<?php
session_start();
require 'conexao.php';
require __DIR__ . '/vendor/autoload.php'; // Caminho do Dompdf

use Dompdf\Dompdf;

// Verifica permissão
if (
    !isset($_SESSION['Usuario']) ||
    !in_array($_SESSION['Usuario']['tipo_usuario'], ['vendedor', 'ambos', 'admin'])
) {
    http_response_code(403);
    exit('Acesso negado.');
}

$usuario_id = $_SESSION['Usuario']['id'];

// Consulta: vendas por mês
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
$vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Monta HTML do PDF
$html = '<h2 style="text-align:center">Relatório: Vendas por Mês</h2>';
$html .= '<table width="100%" border="1" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th>Mês</th>
                    <th>Total (R$)</th>
                </tr>
            </thead>
            <tbody>';

$total_geral = 0;

foreach ($vendas as $v) {
    $html .= '<tr>
                <td>' . htmlspecialchars($v['mes']) . '</td>
                <td>R$ ' . number_format($v['total'], 2, ',', '.') . '</td>
              </tr>';
    $total_geral += $v['total'];
}

$html .= '</tbody>
          <tfoot>
            <tr>
                <td><strong>Total Geral</strong></td>
                <td><strong>R$ ' . number_format($total_geral, 2, ',', '.') . '</strong></td>
            </tr>
          </tfoot>
        </table>';

// Gera PDF com DOMPDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("relatorio_vendas_mes.pdf", ['Attachment' => true]);
exit;
