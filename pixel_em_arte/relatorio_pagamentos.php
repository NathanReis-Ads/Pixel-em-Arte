<?php
session_start();
require 'conexao.php';
require __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;

// Permissão: vendedor, ambos, admin
if (
    !isset($_SESSION['Usuario']) ||
    !in_array($_SESSION['Usuario']['tipo_usuario'], ['vendedor', 'ambos', 'admin'])
) {
    http_response_code(403);
    exit('Acesso negado.');
}

$usuario_id = $_SESSION['Usuario']['id'];

// Consulta: métodos de pagamento agrupados
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
$pagamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Montar HTML para PDF
$html = '<h2 style="text-align:center">Relatório: Métodos de Pagamento</h2>';
$html .= '<table width="100%" border="1" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th>Método</th>
                    <th>Quantidade</th>
                </tr>
            </thead>
            <tbody>';

foreach ($pagamentos as $p) {
    $html .= '<tr>
                <td>' . htmlspecialchars($p['metodo_pagamento']) . '</td>
                <td>' . $p['total'] . '</td>
              </tr>';
}

$html .= '</tbody></table>';

// Gerar PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("relatorio_metodos_pagamento.pdf", ['Attachment' => true]);
exit;
