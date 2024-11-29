<?php
require_once 'conexao.php';

$data = [];
if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];

    // Query para obter despesas
    $sql_despesas = "
        SELECT SUM(valor) AS total_despesas
        FROM despesas_usuario
        WHERE usuario_id = ? AND data_despesa >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    ";
    $stmt_despesas = $conn->prepare($sql_despesas);
    $stmt_despesas->bind_param("i", $usuario_id);
    $stmt_despesas->execute();
    $result_despesas = $stmt_despesas->get_result();
    $total_despesas = $result_despesas->fetch_assoc()['total_despesas'] ?? 0;
    $stmt_despesas->close();

    // Query para obter receitas
    $sql_receitas = "
        SELECT SUM(valor) AS total_receitas
        FROM receitas_usuario
        WHERE usuario_id = ? AND data_recebimento >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    ";
    $stmt_receitas = $conn->prepare($sql_receitas);
    $stmt_receitas->bind_param("i", $usuario_id);
    $stmt_receitas->execute();
    $result_receitas = $stmt_receitas->get_result();
    $total_receitas = $result_receitas->fetch_assoc()['total_receitas'] ?? 0;
    $stmt_receitas->close();

    // Montar dados do JSON
    $data = [
        'labels' => ['Despesas', 'Receitas'],
        'data' => [$total_despesas, $total_receitas]
    ];
}

// Retornar JSON
header('Content-Type: application/json');
echo json_encode($data);
