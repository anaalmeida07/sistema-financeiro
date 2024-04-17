<?php
require_once "conexao.php";

// Verifica se o formulário de exclusão foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    // Obtém o ID da conta a ser excluída
    $id = $_POST['delete_id'];

    // Deleta a relação entre a conta e o usuário
    $sql_delete_conta_usuario = "DELETE FROM `conta_usuario` WHERE id_conta = ?";
    $stmt_delete_conta_usuario = $conn->prepare($sql_delete_conta_usuario);
    $stmt_delete_conta_usuario->bind_param("i", $id);

    // Executa a exclusão da relação entre conta e usuário
    if ($stmt_delete_conta_usuario->execute()) {
        // Deleta a conta do banco de dados com base no ID
        $sql_delete_conta = "DELETE FROM `conta` WHERE id_conta = ?";
        $stmt_delete_conta = $conn->prepare($sql_delete_conta);
        $stmt_delete_conta->bind_param("i", $id);

        // Executa a exclusão da conta
        if ($stmt_delete_conta->execute()) {
            // Redireciona de volta para a página de exibição de contas
            header('Location: exibirConta.php');
            exit;
        } else {
            echo "Erro ao excluir conta: " . $stmt_delete_conta->error;
        }
    } else {
        echo "Erro ao excluir relação entre conta e usuário: " . $stmt_delete_conta_usuario->error;
    }

    $stmt_delete_conta_usuario->close();
    $stmt_delete_conta->close();
}

// Verifica se o ID da conta a ser excluída foi passado via GET
if(isset($_GET['id']) && !empty($_GET['id'])) {
    $contaId = $_GET['id'];

    // Busca a conta específica com base no ID fornecido
    $sql = "SELECT * FROM `conta` WHERE `id_conta` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $contaId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se a conta foi encontrada
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $contaBanco = $row['banco'];
        $contaTipo = $row['tipoConta'];

        // Exibe as informações da conta
        echo "<div class='contaBox'>";
        echo "<h1>Conta ID: $contaId</h1>";
        echo "<h3>Banco: $contaBanco</h3>";
        echo "<h3>Tipo de Conta: $contaTipo</h3>";
        echo "<div class='button-group'>";
        echo "<form method='post' action=''>";
        echo "<input type='hidden' name='delete_id' value='$contaId'>";
        echo "<button class='button-46' type='submit' onclick='return confirm(\"Tem certeza que deseja excluir esta conta?\")'>Apagar</button>";
        echo "</form>";
        echo "</div>";
        echo "</div>";
    } else {
        echo "Conta não encontrada.";
    }

    $stmt->close();
} else {
    echo "ID da conta não fornecido.";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Conta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .contaBox {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .button-group {
            text-align: right;
        }
        .button-46 {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .button-46:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Excluir Conta</h2>
        <?php
        if(isset($_GET['id']) && !empty($_GET['id'])) {
            // Aqui vai o código PHP que você já forneceu
        } else {
            echo "ID da conta não fornecido.";
        }
        ?>
    </div>
</body>
</html>
