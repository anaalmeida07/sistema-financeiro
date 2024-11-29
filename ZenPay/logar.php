<?php 
require_once "conexao.php";

session_start(); // Inicia a sessão

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if (empty($email) || empty($senha)) {
        header('Location: login.php?erro=campos_vazios');
        exit();
    }

    // Verificar a autenticidade do usuário
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();

            // Verificar a senha
            if (password_verify($senha, $row['senha'])) {
                // Login bem-sucedido
                $_SESSION['usuario_id'] = $row['id_usuario'];
                $_SESSION['nome_usuario'] = $row['nome'];

                header('Location: home.php?login=sucesso');
                exit();
            } else {
                // Senha incorreta
                header('Location: login.php?erro=senha_incorreta');
                exit();
            }
        } else {
            // Usuário não encontrado
            header('Location: login.php?erro=usuario_nao_encontrado');
            exit();
        }

        $stmt->close();
    } else {
        echo "Erro ao preparar a consulta.";
        exit();
    }
}

$conn->close();
?>
