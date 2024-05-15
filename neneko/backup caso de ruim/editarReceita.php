<?php
require_once "conexao.php";

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    // Se o usuário não estiver logado, redireciona para a página de login
    header("Location: login.php");
    exit;
}

// ID do usuário logado
$id_usuario = $_SESSION['usuario_id'];

// Verifica se o ID da receita foi recebido via GET
if (isset($_GET['id'])) {
    $receita_id = $_GET['id'];

    // Verifica se o formulário foi submetido
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Verifica se os dados do formulário foram recebidos corretamente
        if (isset($_POST['categoria']) && isset($_POST['dia_recebimento']) && isset($_POST['valor']) && isset($_POST['conta'])) {
            $categoria = $_POST['categoria'];
            $dia_recebimento = $_POST['dia_recebimento'];
            $valor = $_POST['valor'];
            $conta_id = $_POST['conta'];
            $parcelas_falta = ($categoria == 'divida') ? $_POST['parcelas_falta'] : null;

            // Prepara e executa a consulta SQL para atualizar a receita
            $sql = "UPDATE receitas SET categoria = ?, dia_recebimento = ?, valor = ?, conta_bancaria = ?, parcelas_falta = ? WHERE id_conta = ? AND id_usuario = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sidiiii", $categoria, $dia_recebimento, $valor, $conta_id, $parcelas_falta, $receita_id, $usuario_id);

            if ($stmt->execute()) {
                // Redireciona de volta para a página principal após a atualização bem-sucedida
                header('Location: editarReceita.php');
                exit;
            } else {
                // Em caso de erro na execução da consulta SQL
                echo "Erro ao atualizar receita: " . $stmt->error;
            }

            $stmt->close();
        } else {
            // Caso os dados do formulário não tenham sido recebidos corretamente
            echo "Erro: Dados do formulário incompletos.";
        }
    }

    // Busca a receita a ser editada
    $sql_receita = "SELECT * FROM receitas WHERE id_receita = ? AND id_usuario = ?";
    $stmt_receita = $conn->prepare($sql_receita);
    $stmt_receita->bind_param("ii", $receita_id, $usuario_id);
    $stmt_receita->execute();
    $result_receita = $stmt_receita->get_result();

    if ($result_receita->num_rows == 1) {
        $row_receita = $result_receita->fetch_assoc();
        $categoria = $row_receita['categoria'];
        $dia_recebimento = $row_receita['dia_recebimento'];
        $valor = $row_receita['valor'];
        $conta_id = $row_receita['conta_bancaria'];
        $parcelas_falta = $row_receita['parcelas_falta'];
    } else {
        // Se não houver uma receita com o ID especificado para o usuário logado, redireciona para a página principal
        header("Location: index.php");
        exit;
    }
} else {
    // Se o ID da receita não foi especificado, redireciona para a página principal
    header("Location: index.php");
    exit;
}

// Busca todas as contas bancárias do usuário
$sql_contas = "SELECT * FROM conta_usuario WHERE id_usuario = ?";
$stmt_contas = $conn->prepare($sql_contas);
$stmt_contas->bind_param("i", $usuario_id);
$stmt_contas->execute();
$result_contas = $stmt_contas->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Receita</title>
</head>

<body>
    <h1>Editar Receita</h1>

    <form action="editarReceita.php?id=<?php echo $receita_id; ?>" method="POST">
        <label for="categoria">Categoria:</label>
        <select name="categoria" id="categoria">
            <option value="salario" <?php if ($categoria == 'salario') echo 'selected'; ?>>Salário</option>
            <option value="bico" <?php if ($categoria == 'bico') echo 'selected'; ?>>Bico</option>
            <option value="divida" <?php if ($categoria == 'divida') echo 'selected'; ?>>Dívida a Receber</option>
        </select><br>

        <!-- Adiciona o campo para selecionar a conta bancária -->
        <label for="conta">Conta Bancária:</label>
        <select name="conta" id="conta">
            <?php
            if ($result_contas->num_rows > 0) {
                while ($row_conta = $result_contas->fetch_assoc()) {
                    echo '<option value="' . $row_conta['id_conta'] . '"';
                    if ($row_conta['id_conta'] == $conta_id) {
                        echo ' selected';
                    }
                    echo '>' . $row_conta['banco'] . ' - ' . $row_conta['tipoConta'] . '</option>';
                }
            }
            ?>
        </select><br>

        <label for="dia_recebimento">Dia de Recebimento:</label>
        <input type="number" name="dia_recebimento" id="dia_recebimento" min="1" max="31" value="<?php echo $dia_recebimento; ?>" required><br>

        <label for="valor">Valor:</label>
        <input type="text" name="valor" id="valor" value="<?php echo $valor; ?>" required><br>

        <div id="parcelas_falta_div" style="display: none;">
            <label for="parcelas_falta">Parcelas Faltantes:</label>
            <input type="number" name="parcelas_falta" id="parcelas_falta" min="1" value="<?php echo $parcelas_falta; ?>"><br>
        </div>

        <input type="submit" value="Editar Receita">
    </form>

</body>

</html>
