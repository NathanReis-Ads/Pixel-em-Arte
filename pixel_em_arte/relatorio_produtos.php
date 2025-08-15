<?php
session_start();
require 'conexao.php';
require __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;

// Permissões: vendedor, ambos, admin
if (
    !isset($_SESSION['Usuario']) ||
    !in_array($_SESSION['Usuario']['tipo_usuario'], ['vendedor', 'ambos', 'admin'])
) {
    http_response_code(403);
    exit('Acesso negado.');
}

$usuario_id = $_SESSION['Usuario']['id'];

// Consulta: Produtos mais vendidos (top 5)
$stmt = $pdo->prepare("
    SELECT 
        I.titulo AS produto,
        COUNT(*) AS quantidade
    FROM Vendas V
    JOIN Imagens I ON V.id_imagem = I.id
    WHERE I.id_usuario = ?
    GROUP BY produto
    ORDER BY quantidade DESC
    LIMIT 5
");
$stmt->execute([$usuario_id]);
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Monta HTML para o PDF
$html = '<h2 style="text-align:center">Relatório: Produtos Mais Vendidos</h2>';
$html .= '<table width="100%" border="1" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade Vendida</th>
                </tr>
            </thead>
            <tbody>';

foreach ($produtos as $p) {
    $html .= '<tr>
                <td>' . htmlspecialchars($p['produto']) . '</td>
                <td>' . $p['quantidade'] . '</td>
              </tr>';
}

$html .= '</tbody></table>';

// Gerar o PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("relatorio_produtos_mais_vendidos.pdf", ['Attachment' => true]);
exit;
