<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termômetro</title>
</head>
<body>
    <form action="processaTermometro.php" method="POST">
        <label for="meta">Quanto você deseja gastar no mês?</label>
        <input type="number" min="1" name="meta" required><br>
        <button type="submit">Salvar</button>
    </form>
</body>
</html>

<?php
require_once "conexao.php";
echo "<a href='termometro.php'><button class='add-button'>Voltar</button></a>";

if (!isset($_SESSION['usuario_id'])) {
    // Se o usuário não estiver logado, redireciona para a página de login
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se os dados do formulário foram recebidos corretamente
    if (isset($_POST['meta'])) {
        $meta = $_POST['meta'];

        // Prepare a consulta SQL para inserir a meta na tabela meta
        $sql = "INSERT INTO meta (id_usuario, meta) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);

        // Verifica se a preparação da consulta foi bem-sucedida
        if ($stmt) {
            // Associa os parâmetros e executa a consulta
            $stmt->bind_param("id", $usuario_id, $meta);
            $stmt->execute();

            // Verifica se a consulta foi executada com sucesso
            if ($stmt->affected_rows > 0) {
                echo "Meta inserida com sucesso!";
            } else {
                echo "Erro ao inserir a meta.";
            }

            // Fecha a instrução preparada
            $stmt->close();
        } else {
            echo "Erro na preparação da consulta.";
        }
    } else {
        echo "Dados do formulário ausentes.";
    }
} else {
    echo "Requisição inválida.";
}


// Fecha a conexão com o banco de dados
$conn->close();
?>

