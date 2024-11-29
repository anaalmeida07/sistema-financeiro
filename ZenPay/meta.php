<?php
session_start();
include('conexao.php'); // Conexão com o banco de dados



$usuario_id = $_SESSION['id_usuario'];

// Consulta para pegar as contas bancárias do usuário
$sql_contas = "SELECT id, nome FROM contas_bancarias WHERE usuario_id = ?";
$stmt_contas = $conn->prepare($sql_contas);
$stmt_contas->bind_param("i", $usuario_id);
$stmt_contas->execute();
$result_contas = $stmt_contas->get_result();
$contas = [];
while ($row = $result_contas->fetch_assoc()) {
    $contas[] = $row;
}

// Consulta para pegar as metas do usuário
$sql_metas = "SELECT nome_meta, valor_meta, valor_acumulado FROM metas WHERE usuario_id = ?";
$stmt_metas = $conn->prepare($sql_metas);
$stmt_metas->bind_param("i", $usuario_id);
$stmt_metas->execute();
$result_metas = $stmt_metas->get_result();
$metas = [];
while ($row = $result_metas->fetch_assoc()) {
    $metas[] = $row;
}
$stmt_metas->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metas</title>
    <!-- Adicione seus arquivos de CSS aqui -->
</head>
<body>
    <div class="container">
        <!-- Botão para voltar à página inicial -->
        <button onclick="window.location.href='home.php'">Voltar para Home</button>

        <!-- Botão para adicionar nova meta -->
        <button id="addMetaBtn">Adicionar Meta</button>

        <!-- Modal de adicionar meta -->
        <div id="addMetaModal" style="display:none;">
            <div class="modal-content">
                <h2>Adicionar Meta</h2>
                <form id="addMetaForm" method="POST" action="adicionar_meta.php">
                    <label for="nome_meta">Nome da Meta:</label>
                    <input type="text" id="nome_meta" name="nome_meta" required>
                    <br>
                    <label for="valor_meta">Valor a ser alcançado:</label>
                    <input type="number" id="valor_meta" name="valor_meta" required>
                    <br>
                    <label>
                        <input type="checkbox" id="adicionar_dinheiro_checkbox"> Quer adicionar dinheiro agora?
                    </label>
                    <br>
                    <div id="adicionar_dinheiro_fields" style="display:none;">
                        <label for="valor_meta_atual">Valor a colocar na meta:</label>
                        <input type="number" id="valor_meta_atual" name="valor_meta_atual">
                        <br>
                        <label for="conta_bancaria">Escolha a conta bancária:</label>
                        <select id="conta_bancaria" name="conta_bancaria">
                            <?php foreach ($contas as $conta): ?>
                                <option value="<?= $conta['id'] ?>"><?= $conta['nome'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <br>
                    <button type="submit">Salvar Meta</button>
                </form>
                <button onclick="closeModal()">Fechar</button>
            </div>
        </div>

        <h3>Metas Registradas</h3>

        <?php if (count($metas) == 0): ?>
            <p>Nenhuma meta registrada</p>
        <?php else: ?>
            <ul>
                <?php foreach ($metas as $meta): ?>
                    <li>
                        <h4><?= $meta['nome_meta'] ?></h4>
                        <p>Valor a ser alcançado: R$ <?= number_format($meta['valor_meta'], 2, ',', '.') ?></p>
                        <p>Valor alcançado: R$ <?= number_format($meta['valor_atual_meta'], 2, ',', '.') ?></p>
                        <!-- Termômetro -->
                        <div style="width: 100%; background-color: #ccc; height: 10px; border-radius: 5px;">
                            <div style="width: <?= ($meta['valor_atual_meta'] / $meta['valor_meta']) * 100 ?>%; background-color: green; height: 100%; border-radius: 5px;"></div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <script>
        // Função para abrir o modal de adicionar meta
        document.getElementById('addMetaBtn').onclick = function() {
            document.getElementById('addMetaModal').style.display = 'block';
        };

        // Função para fechar o modal
        function closeModal() {
            document.getElementById('addMetaModal').style.display = 'none';
        }

        // Mostrar os campos de adicionar dinheiro quando o checkbox for marcado
        document.getElementById('adicionar_dinheiro_checkbox').onclick = function() {
            var fields = document.getElementById('adicionar_dinheiro_fields');
            if (this.checked) {
                fields.style.display = 'block';
            } else {
                fields.style.display = 'none';
            }
        };
    </script>
</body>
</html>
