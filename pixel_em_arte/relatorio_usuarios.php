<?php
session_start();
require 'conexao.php';
require __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;

// Permitir apenas admin
if (
    !isset($_SESSION['Usuario']) ||
    $_SESSION['Usuario']['tipo_usuario'] !== 'admin'
) {
    http_response_code(403);
    exit('Acesso restrito ao administrador.');
}

// Consulta: usuários por mês
$stmt = $pdo->query("
    SELECT 
        DATE_FORMAT(dat_criacao, '%Y-%m') AS mes,
        COUNT(*) AS total
    FROM Usuarios
    GROUP BY mes
    ORDER BY mes
");

$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Montar HTML para o PDF
$html = '<h2 style="text-align:center">Relatório: Crescimento de Usuários</h2>';
$html .= '<table width="100%" border="1" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th>Mês</th>
                    <th>Usuários Cadastrados</th>
                </tr>
            </thead>
            <tbody>';

$total_geral = 0;

foreach ($usuarios as $u) {
    $html .= '<tr>
                <td>' . htmlspecialchars($u['mes']) . '</td>
                <td>' . $u['total'] . '</td>
              </tr>';
    $total_geral += $u['total'];
}

$html .= '</tbody>
          <tfoot>
            <tr>
                <td><strong>Total Geral</strong></td>
                <td><strong>' . $total_geral . '</strong></td>
            </tr>
          </tfoot>
        </table>';

// Gerar PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("relatorio_usuarios.pdf", ['Attachment' => true]);
exit;
