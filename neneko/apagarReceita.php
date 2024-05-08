<?php
require_once "conexao.php";

// Verifica se o formulário de exclusão foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    // Obtém o ID da receita a ser excluída
    $id = $_POST['delete_id'];

    // Deleta a receita do banco de dados com base no ID
    $sql_delete_receita = "DELETE FROM `receitas` WHERE id = ?";
    $stmt_delete_receita = $conn->prepare($sql_delete_receita);
    $stmt_delete_receita->bind_param("i", $id);

    // Executa a exclusão da receita
    if ($stmt_delete_receita->execute()) {
        // Redireciona de volta para a página de exibição de receitas
        header('Location: adicionarReceita.php');
        exit;
    } else {
        echo "Erro ao excluir receita: " . $stmt_delete_receita->error;
    }

    $stmt_delete_receita->close();
}

// Verifica se o ID da receita a ser excluída foi passado via GET
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $receitaId = $_GET['id'];

    // Busca a receita específica com base no ID fornecido
    $sql = "SELECT * FROM `receitas` WHERE `id` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $receitaId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se a receita foi encontrada
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $categoria = $row['categoria'];
        $dia_recebimento = $row['dia_recebimento'];
        $valor = $row['valor'];
        $parcelas_falta = $row['parcelas_falta'] ?? '';

        // Exibe as informações da receita
        echo "<div class='receitaBox'>";
        echo "<h1>Receita ID: $receitaId</h1>";
        echo "<h3>Categoria: $categoria</h3>";
        echo "<h3>Dia de Recebimento: $dia_recebimento</h3>";
        echo "<h3>Valor: $valor</h3>";
        if ($categoria == 'divida') {
            echo "<h3>Parcelas Faltantes: $parcelas_falta</h3>";
        }
        echo "<div class='button-group'>";
        echo "<form method='post' action=''>";
        echo "<input type='hidden' name='delete_id' value='$receitaId'>";
        echo "<button class='button-46' type='submit' onclick='return confirm(\"Tem certeza que deseja excluir esta receita?\")'>Apagar</button>";
        echo "</form>";
        echo "</div>";
        echo "</div>";
    } else {
        echo "Receita não encontrada.";
    }

    $stmt->close();
} else {
    echo "ID da receita não fornecido.";
}

$conn->close();
?>