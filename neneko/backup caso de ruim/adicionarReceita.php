<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Receita</title>
    <link rel="stylesheet" href="css/adicionarReceita.css">
</head>

<body>
    <div class="barra">
        <h1>Neneko <img src="img/gatinho.png" alt="logo"></h1>
        <nav>
            <ul>
                <li><a href="paginas/sobre/sobre.php">Sobre nós</a></li>
                <li><a href="">Templates</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>

    <h1 class="bv-title">Receitas</h1>

    <a href='home.php'><button class='back-button'>Voltar</button></a>
    <a href='adicionarConta.php'><button class='back-button'>Adicionar Conta</button></a>

    <form action="processar_receita.php" method="post">
        <label for="valor">Valor:</label>
        <input type="text" id="valor" name="valor" required><br>

        <label for="categoria">Categoria:</label>
        <select name="categoria" id="categoria">
            <option value="salario">Salário</option>
            <option value="bico">Bico</option>
            <option value="divida">Dívida a Receber</option>
        </select><br>

        <div id="parcelas" style="display: none;">
            <label for="quantidade_parcelas">Quantidade de Parcelas:</label>
            <input type="number" id="quantidade_parcelas" name="quantidade_parcelas">
        </div>

        <label for="conta">Conta Bancária:</label>
        <select name="conta" id="conta">
            <?php
            // Conexão com o banco de dados (substitua pelos seus detalhes de conexão)
            $conexao = new mysqli("localhost", "usuario", "senha", "nome_do_banco");

            // Verifica conexão
            if ($conexao->connect_error) {
                die("Erro de conexão: " . $conexao->connect_error);
            }

            // Consulta para selecionar as contas bancárias do usuário
            $id_usuario = $_SESSION['id_usuario']; // Supondo que você armazene o ID do usuário em uma sessão
            $query_contas = "SELECT id_conta, banco, tipoConta FROM conta_usuario WHERE id_usuario = ?";
            $stmt = $conexao->prepare($query_contas);
            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();
            $result_contas = $stmt->get_result();

            // Exibe as contas bancárias como opções no select
            if ($result_contas->num_rows > 0) {
                while ($row = $result_contas->fetch_assoc()) {
                    echo "<option value='" . $row['id_conta'] . "'>" . $row['banco'] . " - " . $row['tipoConta'] . "</option>";
                }
            } else {
                echo "<option value=''>Nenhuma conta encontrada</option>";
            }

            // Fecha a conexão e o statement
            $stmt->close();
            $conexao->close();
            ?>
        </select><br>

        <button type="submit">Adicionar Receita</button>
    </form>

    <script>
        // Função para mostrar/ocultar o campo de quantidade de parcelas se a categoria for 'Dívida a Receber'
        document.getElementById('categoria').addEventListener('change', function() {
            var parcelas = document.getElementById('parcelas');
            if (this.value === 'divida') {
                parcelas.style.display = 'block';
            } else {
                parcelas.style.display = 'none';
            }
        });
    </script>
</body>

</html>
