<?php 
session_start();
if (isset($_SESSION['Usuario'])) {
    unset($_SESSION['carrinho']);
    header('Location: dashboard.php');
    exit;}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Bem-vindo à Pixel em Arte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #3f51b5, #9c27b0);
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0px 8px 30px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>

    <div class="login-container">
        <h3 class="text-center mb-4">Entre para o universo da Pixel em Arte 🎮</h3>

        <form action="valida_login.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required autofocus>
            </div>

            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" name="senha" class="form-control" required>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Entrar</button>
                <a href="cadastro.php" class="btn btn-outline-secondary">Criar Conta</a>
            </div>
        </form>
    </div>

</body>

</html>
