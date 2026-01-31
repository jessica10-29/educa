<?php
require_once 'conexion.php';
$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = limpiar_dato($_POST['email']);
    // Aquí iría la lógica de envío de correo
    // Como no tenemos servidor de correo configurado, simulamos el éxito
    $mensaje = '<div style="padding: 15px; background: rgba(16, 185, 129, 0.2); color: #6ee7b7; border-radius: 8px; margin-bottom: 20px;">
        <i class="fa-solid fa-check-circle"></i> Si el correo existe, recibirás un enlace de recuperación.
    </div>';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="background-mesh"></div>
    <div class="login-container">
        <div style="position: absolute; top: 30px; left: 30px;">
            <a href="login.php" class="btn btn-outline" style="padding: 10px 15px;">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
        </div>
        <div class="glass-panel login-box fade-in">
            <p class="text-muted" style="margin-bottom: 30px;">Ingresa tu correo para restablecer la contraseña</p>

            <?php echo $mensaje; ?>

            <form method="POST" action="">
                <div class="input-group">
                    <label class="input-label">Correo Registrado</label>
                    <input type="email" name="email" class="input-field" placeholder="ejemplo@unicali.edu.co" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Enviar Enlace
                </button>
            </form>

            <div style="margin-top: 20px;">
                <a href="login.php" class="btn btn-outline" style="width: 100%;">
                    <i class="fa-solid fa-arrow-left"></i> Volver al Login
                </a>
            </div>
        </div>
    </div>
</body>

</html>