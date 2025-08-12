# Pixel em Arte 🎨  

## Descrição do Projeto  
O **Pixel em Arte** é um sistema web desenvolvido para gerenciar compra e venda de sprites e artes digitais. Ele oferece recursos como cadastro e autenticação de usuários, painel de controle para vendedores, carrinho de compras, relatórios dinâmicos em PDF e filtragem por categorias. O projeto foi construído em PHP com integração ao MySQL e estilizado com Bootstrap, visando praticar conceitos de desenvolvimento web full stack.  

## Requisitos  
- **PHP** 7.4+  
- **XAMPP** (para Apache e MySQL)  
- **Composer** (caso use bibliotecas externas como Dompdf)  

> Obs.: O banco de dados foi desenvolvido no **MySQL do XAMPP**.  

## Como importar o banco de dados  
1. Abra o **phpMyAdmin**.  
2. Crie um novo banco de dados chamado **Pixel_em_Artes**.  
3. Vá até a aba **Importar**.  
4. Escolha o arquivo `.txt` fornecido (com o código SQL do banco).  
5. Clique em **Executar** para criar as tabelas e inserir os dados.  

## Configuração do `conexao.php`  
No arquivo `conexao.php`, edite as credenciais conforme o ambiente:  

```php
<?php
$host = 'localhost';
$dbname = 'Pixel_em_Artes';
$user = 'root';
$pass = ''; // ou 'root' dependendo do servidor

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
} 
```

## ⚠️ Importante:

No XAMPP, normalmente o usuário é root e a senha fica vazia ('').

Em outros ambientes (como faculdade), pode ser necessário alterar para root / root ou as credenciais fornecidas.

## Observações
Este projeto foi desenvolvido como trabalho acadêmico e não representa um sistema comercial finalizado.
Estou em constante aprendizado e aprimoramento no desenvolvimento web.
