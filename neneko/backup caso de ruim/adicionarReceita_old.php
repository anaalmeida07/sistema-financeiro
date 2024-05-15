<?php
require_once "conexao.php";

// Verifica se o usuário está logado
if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
} else {
    // Se o usuário não estiver logado, redirecione para a página de login
    header("Location: login.php");
    exit;
}

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se os dados do formulário foram recebidos corretamente
    if (isset($_POST['categoria']) && isset($_POST['dia_recebimento']) && isset($_POST['valor']) && isset($_POST['conta'])) {
        $categoria = $_POST['categoria'];
        $dia_recebimento = $_POST['dia_recebimento'];
        $valor = $_POST['valor'];
        $conta_id = $_POST['conta'];
        $parcelas_falta = ($categoria == 'divida') ? $_POST['parcelas_falta'] : null;

        // Verifica se uma nova categoria foi inserida
        if (!empty($_POST['nova_categoria'])) {
            $nova_categoria = $_POST['nova_categoria'];

            // Insere a nova categoria no banco de dados
            $sql_insere_categoria = "INSERT INTO categorias (id_usuario, categoria) VALUES (?, ?)";
            $stmt_insere_categoria = $conn->prepare($sql_insere_categoria);
            $stmt_insere_categoria->bind_param("si", $nova_categoria, $usuario_id);
            $stmt_insere_categoria->execute();

            // Define a nova categoria para ser usada
            $categoria = $nova_categoria;
        }

        // Prepara e executa a consulta SQL para inserir a nova receita
        $sql = "INSERT INTO receitas (id_usuario, categoria, dia_recebimento, valor, conta_bancaria, parcelas_falta) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isidsi", $usuario_id, $categoria, $dia_recebimento, $valor, $conta_id, $parcelas_falta);

        // Executa a consulta
        if ($stmt->execute()) {
            // Adiciona as receitas futuras apenas se a data de recebimento da receita principal for futura
            $data_atual = date('Y-m-d');
            $data_recebimento = date('Y-m') . '-' . $dia_recebimento;
            if ($data_atual <= $data_recebimento) {
                $stmt_future = $conn->prepare($sql);
                $stmt_future->bind_param("isidii", $usuario_id, $categoria, $dia_recebimento, $valor, $conta_id, $parcelas_falta);
                $stmt_future->execute();
            }

            // Redireciona de volta para a página principal após inserção bem-sucedida
            header('Location: adicionarReceita.php');
            exit;
        } else {
            // Em caso de erro na execução da consulta SQL
            echo "Erro ao inserir nova receita: " . $stmt->error;
        }

        $stmt->close();
    } else {
        // Caso os dados do formulário não tenham sido recebidos corretamente
        echo "Erro: Dados do formulário incompletos.";
    }
}

// Busca todas as contas bancárias do usuário
$sql_contas = "SELECT * FROM conta_usuario WHERE id_usuario = ?";
$stmt_contas = $conn->prepare($sql_contas);
$stmt_contas->bind_param("i", $usuario_id);
$stmt_contas->execute();
$result_contas = $stmt_contas->get_result();

// Função para calcular a soma total das receitas do usuário
function calcularTotalReceitas($conn, $usuario_id)
{
    $sql_total = "SELECT SUM(valor) AS total FROM receitas WHERE id_usuario = ?";
    $stmt_total = $conn->prepare($sql_total);
    $stmt_total->bind_param("i", $usuario_id);
    $stmt_total->execute();
    $result = $stmt_total->get_result();
    $total = $result->fetch_assoc()['total'];
    return $total;
}

// Busca todas as receitas do usuário
$sql_receitas = "SELECT * FROM receitas WHERE id_usuario = ?";
$stmt_receitas = $conn->prepare($sql_receitas);
$stmt_receitas->bind_param("i", $usuario_id);
$stmt_receitas->execute();
$result_receitas = $stmt_receitas->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Receita</title>
    <link rel="stylesheet" href="css/adicionarReceita.css">
    <style>
        /* CSS para o modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="barra">
        <h1>Neneko <img src="img/gatinho.png" alt="logo"></h1>
        <nav>
            <ul>
                <li><a href="paginas/sobre/sobre.php">Sobre nós</a></li>
                <li><a href="">Templates</a></li>
                <li><a href="logout.php">Logout</a></li>
                <!-- Link para a página logout.php -->
            </ul>
        </nav>
    </div>
    <h1 class="bv-title">Adicionar Receita</h1>

    <!-- Botão para abrir o modal -->
    <button class='back-button' id="openModal">Adicionar Receita</button>

    <!-- O Modal -->
    <div id="myModal" class="modal">
        <!-- Conteúdo do Modal -->
        <div class="modal-content">
            <span class="close">&times;</span>
            <form action="adicionarReceita.php" method="POST">
                <label for="categoria">Categoria:</label>
                <select name="categoria" id="categoria">
                    <option value="salario">Salário</option>
                    <option value="bico">Bico</option>
                    <option value="divida">Dívida a Receber</option>
                </select><br>

                <!-- Campo para inserir uma nova categoria -->
                <label for="nova_categoria">Nova Categoria:</label>
                <input type="text" name="nova_categoria" id="nova_categoria"><br>
                <!-- Botão para salvar a nova categoria -->
                <button type="button" onclick="salvarNovaCategoria()">Salvar Categoria</button><br>

                <!-- Adiciona o campo para selecionar a conta bancária -->
                <label for="conta">Conta Bancária:</label>
                <select name="conta" id="conta">
                    <?php
                    if ($result_contas->num_rows > 0) {
                        while ($row_conta = $result_contas->fetch_assoc()) {
                            echo '<option value="' . $row_conta['id_conta'] . '">' . $row_conta['banco'] . ' - ' . $row_conta['tipoConta'] . '</option>';
                        }
                    }
                    ?>
                </select><br>

                <label for="dia_recebimento">Dia de Recebimento:</label>
                <input type="number" name="dia_recebimento" id="dia_recebimento" min="1" max="31" required><br>

                <label for="valor">Valor:</label>
                <input type="text" name="valor" id="valor" required><br>

                <div id="parcelas_falta_div" style="display: none;">
                    <label for="parcelas_falta">Parcelas Faltantes:</label>
                    <input type="number" name="parcelas_falta" id="parcelas_falta" min="1"><br>
                </div>

                <input type="submit" value="Adicionar Receita">
            </form>
        </div>
    </div>

    <?php
    // Calcula e exibe o valor total das receitas do usuário
    $total_receitas = calcularTotalReceitas($conn, $usuario_id);
    echo "<h2 class='titulo-lado'>Total de Receitas: R$ " . number_format($total_receitas, 2) . "</h2>";
    ?>

    <!-- Exibe as receitas do usuário -->
    <table>
        <thead>
            <tr>
                <th>Categoria</th>
                <th>Dia de Recebimento</th>
                <th>Valor</th>
                <th>Conta Bancária</th>
                <th>Parcelas Faltantes</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result_receitas->num_rows > 0) {
                while ($row_receita = $result_receitas->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row_receita['categoria'] . "</td>";
                    echo "<td>" . $row_receita['dia_recebimento'] . "</td>";
                    echo "<td>R$ " . number_format($row_receita['valor'], 2) . "</td>";
                    echo "<td>" . $row_receita['conta_bancaria'] . "</td>";
                    echo "<td>" . ($row_receita['parcelas_falta'] ? $row_receita['parcelas_falta'] : '-') . "</td>";
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>

    <script>
        // Função para salvar nova categoria
        function salvarNovaCategoria() {
            var novaCategoria = document.getElementById('nova_categoria').value;
            if (novaCategoria.trim() !== '') {
                var categoriaSelect = document.getElementById('categoria');
                var option = document.createElement("option");
                option.text = novaCategoria;
                option.value = novaCategoria;
                categoriaSelect.add(option);
                document.getElementById('nova_categoria').value = '';
            }
        }

        // Obtém o modal
        var modal = document.getElementById("myModal");

        // Obtém o botão que abre o modal
        var btn = document.getElementById("openModal");

        // Obtém o elemento <span> que fecha o modal
        var span = document.getElementsByClassName("close")[0];

        // Quando o usuário clica no botão, abre o modal
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // Quando o usuário clica em <span> (x), fecha o modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // Quando o usuário clica em qualquer lugar fora do modal, fecha o modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

</body>

</html>
