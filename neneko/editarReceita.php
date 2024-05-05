<?php
require_once "conexao.php";

// Verifica se a variável $_GET['id'] está definida
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Consulta SQL para obter as informações da receita com o ID especificado
    $sql = "SELECT * FROM `receitas` WHERE `id` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se a consulta retornou resultados
    if ($result->num_rows > 0) {
        // Recupera os dados da receita
        $row = $result->fetch_assoc();
        $categoria = $row['categoria'];
        $dia_recebimento = $row['dia_recebimento'];
        $valor = $row['valor'];
        $parcelas_falta = $row['parcelas_falta'] ?? '';

        // Se o formulário for enviado (submit), atualiza os dados
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Verifica se os índices estão definidos antes de acessá-los
            if (isset($_POST['submit'])) {
                $categoria = $_POST['categoria'];
                $dia_recebimento = $_POST['dia_recebimento'];
                $valor = $_POST['valor'];
                $parcelas_falta = ($categoria == 'divida') ? $_POST['parcelas_falta'] : '';

                // Atualiza a consulta para usar um marcador de posição (?) para o ID
                $sql_update_receita = "UPDATE `receitas` SET `categoria`=?, `dia_recebimento`=?, `valor`=?, `parcelas_falta`=? WHERE `id`=?";

                $stmt_update_receita = $conn->prepare($sql_update_receita);
                $stmt_update_receita->bind_param("sssii", $categoria, $dia_recebimento, $valor, $parcelas_falta, $id);

                // Executa a atualização da receita
                if ($stmt_update_receita->execute()) {
                    header('Location: adicionarReceita.php');
                    exit;
                } else {
                    echo "Erro ao atualizar receita: " . $stmt_update_receita->error;
                }

                $stmt_update_receita->close();
            }
        }
    } else {
        echo "Nenhuma receita encontrada com o ID especificado.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "ID da receita não especificado.";
}
?>

<div class="form">
    <!-- Adiciona um campo oculto para passar o ID da receita -->
    <form method="post" action="editarReceita.php?id=<?php echo $id; ?>" id="formEditarReceita">
        <label for="categoria">Categoria:</label>
        <select name="categoria" id="categoria">
            <option value="salario" <?php if ($categoria == 'salario') echo 'selected'; ?>>Salário</option>
            <option value="bico" <?php if ($categoria == 'bico') echo 'selected'; ?>>Bico</option>
            <option value="divida" <?php if ($categoria == 'divida') echo 'selected'; ?>>Dívida a Receber</option>
        </select><br>

        <label for="dia_recebimento">Dia de Recebimento:</label>
        <input type="number" name="dia_recebimento" id="dia_recebimento" min="1" max="31" value="<?php echo $dia_recebimento; ?>"><br>

        <label for="valor">Valor:</label>
        <input type="text" name="valor" id="valor" value="<?php echo $valor; ?>"><br>

        <div id="parcelas_falta_div" style="<?php echo ($categoria == 'divida') ? 'display:block;' : 'display:none;'; ?>">
            <label for="parcelas_falta">Parcelas Faltantes:</label>
            <input type="number" name="parcelas_falta" id="parcelas_falta" value="<?php echo $parcelas_falta; ?>"><br>
        </div>

        <button class="button-45" type="submit" name="submit" role="button">Salvar</button>
    </form>
</div>
