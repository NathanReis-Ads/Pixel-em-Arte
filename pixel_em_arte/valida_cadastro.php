<?php
session_start();
require_once 'conexao.php';

// Verifica se todos os campos foram preenchidos
if (empty($_POST['email']) || empty($_POST['senha']) || empty($_POST['nome']) || empty($_POST['tipo_usuario'])) {
    $_SESSION['mensagem'] = "Preencha todos os campos.";
    header("Location: cadastro.php");
    exit;
}

// Recebe os dados
$email = $_POST['email'];
$senha = $_POST['senha'];
$nome  = $_POST['nome'];
$tipo  = $_POST['tipo_usuario'];

// Verifica se o e-mail já está cadastrado
$sql_verifica = "SELECT id FROM Usuarios WHERE email = ?";
$stmt_verifica = $pdo->prepare($sql_verifica);
$stmt_verifica->execute([$email]);

if ($stmt_verifica->rowCount() > 0) {
    $_SESSION['mensagem'] = "Este e-mail já está cadastrado.";
    header("Location: cadastro.php");
    exit;
}

// Criptografa a senha
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

// Insere novo usuário
$sql = "INSERT INTO Usuarios (email, senha, nome, tipo_usuario) VALUES (?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);

if ($stmt->execute([$email, $senha_hash, $nome, $tipo])) {
    $_SESSION['mensagem'] = "Cadastro realizado com sucesso!";
    header("Location: login.php");
    exit;
} else {
    $_SESSION['mensagem'] = "Erro ao cadastrar.";
    header("Location: cadastro.php");
    exit;
}
?>
