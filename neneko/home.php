<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Bem-vindo!</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="icon" href="img/gatinho.png" type="image/x-icon">
    <style>
        .conta-box {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            padding-top: 100px;
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
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #7952b3;
            text-decoration: none;
            cursor: pointer;
        }

        #addContaBtn, #addReceitaBtn, #addDespesaBtn{
            background-color: #7952b3;
            padding: 20px 15px;
            margin: 10px;
        }
    </style>
</head>

<body>
    <div class="barra">
        <h1>Neneko <img src="img/gatinho.png" alt="logo"></h1>
        <nav>
            <ul>
                <li><a href="paginas/sobre/sobre.php">Sobre nós</a></li>
                <li><a href="#">Templates</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>

    <button id="addContaBtn">Adicionar Conta Bancária</button>
    <button id="addReceitaBtn">Adicionar Nova Receita</button>
    <button id="addDespesaBtn">Adicionar Nova Despesa</button>

    <!-- Modal para adicionar conta bancária -->
    <div id="addContaModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeContaModal">&times;</span>
            <h2>Adicionar Nova Conta Bancária</h2>
            <form action="adicionar_conta.php" method="post">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required><br><br>

                <label for="tipo_conta">Tipo de Conta:</label>
                <input type="text" id="tipo_conta" name="tipo_conta" required><br><br>

                <label for="saldo">Saldo:</label>
                <input type="text" id="saldo" name="saldo" required><br><br>

                <input type="submit" value="Adicionar Conta">
            </form>
        </div>
    </div>

    <!-- Modal para adicionar receita -->
    <div id="addReceitaModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeReceitaModal">&times;</span>
            <h2>Adicionar Nova Receita</h2>
            <form action="processar_receita.php" method="post">
                <label for="valor">Valor:</label>
                <input type="text" id="valor" name="valor" required><br><br>

                <label for="categoria">Categoria:</label>
                <select id="categoria" name="categoria">
                    <option value="Salário">Salário</option>
                    <option value="Outra fonte de renda">Outra fonte de renda</option>
                </select><br><br>

                <label for="conta_destino">Conta de Destino:</label>
                <select id="conta_destino" name="conta_destino" required>
                    <?php
                    require_once 'conexao.php';
                    $usuario_id = $_SESSION['usuario_id'];
                    $sql = "SELECT id, nome FROM contas_bancarias WHERE usuario_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $usuario_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['nome']}</option>";
                    }
                    $stmt->close();
                    ?>
                </select><br><br>

                <input type="submit" value="Adicionar Receita">
            </form>
        </div>
    </div>

    <!-- Modal para adicionar despesa -->
    <div id="addDespesaModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeDespesaModal">&times;</span>
            <h2>Adicionar Nova Despesa</h2>
            <form action="processar_despesa.php" method="post">
                <label for="valor">Valor:</label>
                <input type="text" id="valor" name="valor" required><br><br>

                <label for="categoria">Categoria:</label>
                <input type="text" id="categoria" name="categoria" required><br><br>

                <label for="conta_id">Conta de Origem:</label>
                <select id="conta_id" name="conta_id" required>
                    <?php
                    require_once 'conexao.php';
                    $usuario_id = $_SESSION['usuario_id'];
                    $sql = "SELECT id, nome FROM contas_bancarias WHERE usuario_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $usuario_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['nome']}</option>";
                    }
                    $stmt->close();
                    ?>
                </select><br><br>

                <input type="submit" value="Adicionar Despesa">
            </form>
        </div>
    </div>

    <div class="contas">
        <h2 class="bv-home">Contas Bancárias</h2>
        <?php
        require_once 'conexao.php';
        $usuario_id = $_SESSION['usuario_id'];
        $sql = "SELECT nome, saldo FROM contas_bancarias WHERE usuario_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='conta-box'>";
                echo "<h3>{$row['nome']}</h3>";
                echo "<p>Saldo: {$row['saldo']}</p>";
                echo "</div>";
            }
        } else {
            echo "<p>Nenhuma conta bancária encontrada.</p>";
        }
        $stmt->close();
        $conn->close();
        ?>
    </div>

    <script>
        // Obtém os elementos dos modais
        var addContaModal = document.getElementById("addContaModal");
        var addReceitaModal = document.getElementById("addReceitaModal");
        var addDespesaModal = document.getElementById("addDespesaModal");

        // Obtém os botões que abrem os modais
        var addContaBtn = document.getElementById("addContaBtn");
        var addReceitaBtn = document.getElementById("addReceitaBtn");
        var addDespesaBtn = document.getElementById("addDespesaBtn");

        // Obtém os elementos de fechar os modais
        var closeContaModal = document.getElementById("closeContaModal");
        var closeReceitaModal = document.getElementById("closeReceitaModal");
        var closeDespesaModal = document.getElementById("closeDespesaModal");

        // Adiciona eventos para abrir os modais
        addContaBtn.onclick = function() {
            addContaModal.style.display = "block";
        }

        addReceitaBtn.onclick = function() {
            addReceitaModal.style.display = "block";
        }

        addDespesaBtn.onclick = function() {
            addDespesaModal.style.display = "block";
        }

        // Adiciona eventos para fechar os modais
        closeContaModal.onclick = function() {
            addContaModal.style.display = "none";
        }

        closeReceitaModal.onclick = function() {
            addReceitaModal.style.display = "none";
        }

        closeDespesaModal.onclick = function() {
            addDespesaModal.style.display = "none";
        }

        // Fecha os modais ao clicar fora do conteúdo do modal
        window.onclick = function(event) {
            if (event.target == addContaModal) {
                addContaModal.style.display = "none";
            }
            if (event.target == addReceitaModal) {
                addReceitaModal.style.display = "none";
            }
            if (event.target == addDespesaModal) {
                addDespesaModal.style.display = "none";
            }
        }
    </script>

</body>

</html>