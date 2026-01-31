<?php
require_once 'conexion.php';

$error = '';
$rol = isset($_GET['rol']) ? $_GET['rol'] : 'estudiante';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = limpiar_dato($_POST['email']);
    $password = $_POST['password'];
    $codigo_docente = isset($_POST['codigo_docente']) ? $_POST['codigo_docente'] : '';

    $stmt = $conn->prepare("SELECT id, nombre, password, rol FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        $login_exitoso = false;

        if (password_verify($password, $usuario['password'])) {
            $login_exitoso = true;
        } else if ($password === $usuario['password']) {
            $login_exitoso = true;
            $new_hash = password_hash($password, PASSWORD_BCRYPT);
            $uid = $usuario['id'];
            $conn->query("UPDATE usuarios SET password = '$new_hash' WHERE id = $uid");
        }

        if ($login_exitoso) {
            // SEGURIDAD ADICIONAL: Si es profesor, debe ingresar el código secreto
            if ($usuario['rol'] == 'profesor' && $codigo_docente !== 'UNICALI_DOCENTE') {
                $error = "Acceso docente denegado. Código de seguridad incorrecto.";
            } else {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['rol'] = $usuario['rol'];

                if ($usuario['rol'] == 'profesor') {
                    header("Location: dashboard_profesor.php");
                } else if ($usuario['rol'] == 'estudiante') {
                    header("Location: dashboard_estudiante.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            }
        } else {
            $error = "La contraseña ingresada es incorrecta.";
        }
    } else {
        $error = "No encontramos ninguna cuenta con ese correo.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Unicali Segura</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="icon" type="image/png" href="favicon.png?v=3">
    <link rel="shortcut icon" href="favicon.ico?v=3">
    <link rel="apple-touch-icon" href="favicon.png?v=3">
</head>

<body>
    <div class="background-mesh"></div>

    <div class="login-container">
        <div style="position: absolute; top: 30px; left: 30px;">
            <a href="index.php" class="btn btn-outline" style="padding: 10px 15px;">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
        </div>
        <div class="glass-panel login-box fade-in" style="max-width: 480px;">
            <div class="logo-area" style="margin-bottom: 30px;">
                <i class="fa-solid fa-graduation-cap logo-large"></i>
                <h2 style="font-size: 2rem;">Acceso al Portal</h2>
                <p class="text-muted">Ingresa tus credenciales para continuar</p>
            </div>

            <?php if ($error): ?>
                <div style="background: rgba(244, 63, 94, 0.1); color: #fb7185; padding: 12px; border-radius: 10px; margin-bottom: 25px; font-size: 0.85rem; border: 1px solid rgba(244, 63, 114, 0.2);">
                    <i class="fa-solid fa-circle-exclamation" style="margin-right: 8px;"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="input-group">
                    <label class="input-label">Correo Institucional</label>
                    <input type="email" name="email" class="input-field" placeholder="nombre@unicali.edu.co" required>
                </div>

                <div class="input-group">
                    <label class="input-label">Contraseña</label>
                    <div class="input-wrapper">
                        <input type="password" name="password" id="password" class="input-field" placeholder="••••••••" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <?php if (isset($_GET['rol']) && $_GET['rol'] == 'profesor'): ?>
                    <div class="input-group fade-in" style="border: 1px dashed var(--primary); padding: 15px; border-radius: 12px; background: rgba(99, 102, 241, 0.05); margin-top: 20px;">
                        <label class="input-label" style="color: var(--primary);">Código de Acceso Docente</label>
                        <input type="password" name="codigo_docente" class="input-field" placeholder="Ingrese el código para entrar" required>
                    </div>
                <?php endif; ?>

                <script>
                    function togglePassword(inputId, btn) {
                        const input = document.getElementById(inputId);
                        const icon = btn.querySelector('i');
                        if (input.type === 'password') {
                            input.type = 'text';
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        } else {
                            input.type = 'password';
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        }
                    }
                </script>

                <div style="text-align: right; margin-bottom: 25px;">
                    <a href="recover_password.php" style="color: var(--primary); text-decoration: none; font-size: 0.85rem; font-weight: 500;">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; height: 50px;">
                    Entrar <i class="fa-solid fa-right-to-bracket" style="margin-left: 8px;"></i>
                </button>
            </form>

            <div style="margin: 30px 0; border-top: 1px solid var(--glass-border);"></div>

            <p style="font-size: 0.9rem; color: var(--text-muted);">
                ¿No tienes una cuenta? <a href="registro.php" style="color: var(--secondary); font-weight: 600; text-decoration: none; margin-left: 5px;">Regístrate</a>
            </p>

            <div class="security-badge">
                <i class="fa-solid fa-shield-halved"></i>
                <span>Conexión Segura SSL - Datos Encriptados</span>
            </div>
        </div>
    </div>
</body>

</html>