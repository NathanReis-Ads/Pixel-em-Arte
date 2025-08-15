<?php
$host = 'localhost';
$dbname = 'Pixel_em_Artes';
$user = 'SEU_USUARIO_AQUI';
$pass = 'SUA_SENHA_AQUI';

try {

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {

    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
}
?>

?>