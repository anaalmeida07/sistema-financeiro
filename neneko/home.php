<?php
// Iniciar a sessão no início do arquivo
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Bem-vindo!</title>
    <link rel="icon" href="img/gatinho.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/home.css">
</head>

<body>
    <div class="barra">
        <h1>Neneko <img src="img/gatinho.png" alt="logo"></h1>
        <nav>
            <ul>
                <li><a href="paginas/sobre/sobre.php">Sobre nós</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>

    <div class="btn-group">
        <button type="button" id="addContaBtn" class="btn btn-outline-primary">Adicionar Conta Bancária</button>
        <button type="button" id="addReceitaBtn" class="btn btn-outline-primary">Adicionar Nova Receita</button>
        <button type="button" id="addDespesaBtn" class="btn btn-outline-primary">Adicionar Nova Despesa</button>
    </div>

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
                    if (isset($_SESSION['usuario_id'])) {
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
                    } else {
                        echo "<option value=''>Nenhuma conta encontrada</option>";
                    }
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
                    if (isset($_SESSION['usuario_id'])) {
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
                    } else {
                        echo "<option value=''>Nenhuma conta encontrada</option>";
                    }
                    ?>
                </select><br><br>

                <input type="submit" value="Adicionar Despesa">
            </form>
        </div>
    </div>

    <div class="list-group">
        <?php
        require_once 'conexao.php';
        echo "<li class='list-group-item active' aria-current='true'>Contas Bancárias</li>";
        if (isset($_SESSION['usuario_id'])) {
            $usuario_id = $_SESSION['usuario_id'];
            $sql = "SELECT nome, saldo FROM contas_bancarias WHERE usuario_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<a href='#' class='list-group-item list-group-item-action'>";
                    echo "<h5 class='mb-1'>{$row['nome']}</h5>";
                    echo "<p class='mb-1'>Saldo: R$ {$row['saldo']}</p>";
                    echo "</a>";
                }
            } else {
                echo "<p class='list-group-item'>Nenhuma conta bancária encontrada.</p>";
            }
            $stmt->close();
        } else {
            echo "<p class='list-group-item'>Nenhum usuário logado.</p>";
        }
        ?>
    </div>

    <div class="extrato">
        <h2 class="bv-home">extrato</h2>
        <hr>
        <?php
        require_once 'conexao.php';
        if (isset($_SESSION['usuario_id'])) {
            $usuario_id = $_SESSION['usuario_id'];
            $sql = "
                SELECT 'despesa' AS tipo, valor, categoria, data_despesa AS data, cb.nome AS conta
                FROM despesas_usuario du
                JOIN contas_bancarias cb ON du.conta_id = cb.id
                WHERE du.usuario_id = ?
                UNION
                SELECT 'receita' AS tipo, valor, categoria, data_recebimento AS data, cb.nome AS conta
                FROM receitas_usuario ru
                JOIN contas_bancarias cb ON ru.conta_destino_id = cb.id
                WHERE ru.usuario_id = ?
                ORDER BY data DESC
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $usuario_id, $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $classe = $row['tipo'] == 'receita' ? 'receita' : 'despesa';
                    $data = date('d/m/Y H:i:s', strtotime($row['data']));
                    echo "<div class='extrato-item {$classe}'>";
                    echo "<h5>R$ {$row['valor']}</h5>";
                    echo "<p>{$row['categoria']} - {$row['conta']}</p>";
                    echo "<p>{$data}</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>Nenhuma transação encontrada.</p>";
            }
            $stmt->close();
        } else {
            echo "<p>Nenhum usuário logado.</p>";
        }
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
