<?php
session_start();
require_once 'conexao.php';

$email = $_POST['email'];
$senha = $_POST['senha'];

$stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE email = ?");
$stmt->execute([$email]);
$usuario = $stmt->fetch();

if ($usuario && password_verify($senha, $usuario['senha'])) {
    $_SESSION['Usuario'] = $usuario;
header('Location: marketplace.php');
exit;
} else {
    $_SESSION['mensagem'] = "Login ou senha incorretos.";
    header('Location: login.php');
    exit;
}
?>
