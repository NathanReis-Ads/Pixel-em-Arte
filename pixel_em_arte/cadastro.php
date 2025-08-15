<?php
session_start();
if (isset($_SESSION['Usuario'])) {
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Cadastro - Pixel em Arte</title>
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
        <h3 class="text-center mb-4">Faça parte da Pixel em Arte 🎮</h3>

        <form action="valida_cadastro.php" method="POST">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" name="nome" id="nome" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" name="senha" id="senha" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="tipo_usuario" class="form-label">Tipo de Usuário</label>
                <select name="tipo_usuario" id="tipo_usuario" class="form-control" required>
                    <option value="comprador">Comprador</option>
                    <option value="vendedor">Vendedor</option>
                    <option value="ambos">Ambos</option>
                </select>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success">Cadastrar</button>
                <a href="login.php" class="btn btn-outline-secondary">Já tem uma conta? Entrar</a>
            </div>
        </form>
    </div>

</body>
<?php
if (isset($_SESSION['mensagem'])) {
    echo '<div class="alert alert-info text-center">'.$_SESSION['mensagem'].'</div>';
    unset($_SESSION['mensagem']);
}
?>
</html>


