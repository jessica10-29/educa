<?php
require_once 'conexion.php';
verificar_sesion();
$id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];
$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identificacion = limpiar_dato($_POST['identificacion']);
    $telefono = limpiar_dato($_POST['telefono']);
    $direccion = limpiar_dato($_POST['direccion']);
    $ciudad = limpiar_dato($_POST['ciudad']);
    $departamento = limpiar_dato($_POST['departamento']);
    $correo_inst = limpiar_dato($_POST['correo_institucional']);
    $programa = limpiar_dato($_POST['programa_academico']);
    $semestre = limpiar_dato($_POST['semestre']);
    $codigo_est = limpiar_dato($_POST['codigo_estudiantil']);

    $nuevo_pass = $_POST['password'];

    // Update basic info
    $stmt = $conn->prepare("UPDATE usuarios SET identificacion = ?, telefono = ?, direccion = ?, ciudad = ?, departamento = ?, correo_institucional = ?, programa_academico = ?, semestre = ?, codigo_estudiantil = ? WHERE id = ?");
    $stmt->bind_param("sssssssssi", $identificacion, $telefono, $direccion, $ciudad, $departamento, $correo_inst, $programa, $semestre, $codigo_est, $id);

    if ($stmt->execute()) {
        $mensaje = '<div style="background: rgba(16, 185, 129, 0.1); color: #34d399; padding: 12px; border-radius: 10px; margin-bottom: 25px; font-size: 0.85rem; border: 1px solid rgba(16, 185, 129, 0.2);"><i class="fa-solid fa-circle-check"></i> Perfil actualizado correctamente.</div>';
    } else {
        $mensaje = '<div style="background: rgba(244, 63, 94, 0.1); color: #fb7185; padding: 12px; border-radius: 10px; margin-bottom: 25px; font-size: 0.85rem; border: 1px solid rgba(244, 63, 114, 0.2);"><i class="fa-solid fa-circle-exclamation"></i> Error al actualizar el perfil.</div>';
    }
    $stmt->close();

    if (!empty($nuevo_pass)) {
        $hash = password_hash($nuevo_pass, PASSWORD_BCRYPT);
        $stmt_pass = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $stmt_pass->bind_param("si", $hash, $id);
        $stmt_pass->execute();
        $stmt_pass->close();
    }
}

$u = $conn->query("SELECT * FROM usuarios WHERE id = $id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Perfil - Unicali</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>
    <div class="background-mesh"></div>
    <div class="dashboard-grid">
        <aside class="sidebar">
            <div class="logo-area" style="margin-bottom: 40px; text-align: center;">
                <i class="fa-solid fa-graduation-cap logo-icon" style="font-size: 2rem; color: var(--primary);"></i>
                <h3 style="color: white; margin-top: 10px;">Unicali<span style="color: var(--primary);"><?php echo $rol == 'profesor' ? 'Docente' : 'Estudiante'; ?></span></h3>
            </div>
            <nav>
                <a href="<?php echo $rol == 'profesor' ? 'dashboard_profesor.php' : 'dashboard_estudiante.php'; ?>" class="nav-link">
                    <i class="fa-solid fa-house"></i> Inicio
                </a>
                <?php if ($rol == 'profesor'): ?>
                    <a href="gestion_materias.php" class="nav-link">
                        <i class="fa-solid fa-book"></i> Mis Materias
                    </a>
                    <a href="gestion_notas.php" class="nav-link">
                        <i class="fa-solid fa-user-pen"></i> Gestionar Notas
                    </a>
                    <a href="asistencia.php" class="nav-link">
                        <i class="fa-solid fa-clipboard-user"></i> Asistencia
                    </a>
                    <a href="perfil.php" class="nav-link active">
                        <i class="fa-solid fa-gear"></i> Configuración
                    </a>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <a href="dashboard_estudiante.php" class="nav-link">
                            <i class="fa-solid fa-house"></i> Inicio
                        </a>
                        <a href="ver_asistencia.php" class="nav-link">
                            <i class="fa-solid fa-calendar-check"></i> Mis Asistencias
                        </a>
                        <a href="ver_notas.php" class="nav-link">
                            <i class="fa-solid fa-chart-line"></i> Mis Notas
                        </a>
                        <a href="historial.php" class="nav-link">
                            <i class="fa-solid fa-receipt"></i> Historial Académico
                        </a>
                        <a href="perfil.php" class="nav-link active">
                            <i class="fa-solid fa-gear"></i> Mi Perfil
                        </a>
                    </div>
                <?php endif; ?>
                <a href="logout.php" class="nav-link" style="margin-top: auto; color: #f43f5e;">
                    <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
                </a>
            </nav>
        </aside>

        <main class="main-content">
            <header style="margin-bottom: 30px;">
                <h1 class="text-gradient">Mi Perfil</h1>
                <p class="text-muted">Gestiona tu información de seguridad y acceso</p>
            </header>

            <div style="max-width: 600px; margin: 0 auto;">
                <?php echo $mensaje; ?>

                <div class="card glass-panel fade-in">
                    <div style="text-align: center; margin-bottom: 30px;">
                        <div style="width: 100px; height: 100px; background: rgba(99, 102, 241, 0.1); border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; border: 2px solid var(--primary);">
                            <i class="fa-solid fa-user-astronaut" style="font-size: 3rem; color: var(--primary);"></i>
                        </div>
                        <h2 style="margin-bottom: 5px;"><?php echo htmlspecialchars($u['nombre']); ?></h2>
                        <span style="background: var(--primary); color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;"><?php echo $rol; ?></span>
                    </div>

                    <form method="POST">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="input-group">
                                <label class="input-label">Nombre Completo</label>
                                <input type="text" value="<?php echo htmlspecialchars($u['nombre']); ?>" class="input-field" disabled style="opacity: 0.6; cursor: not-allowed;">
                            </div>
                            <div class="input-group">
                                <label class="input-label">Email de Login</label>
                                <input type="email" value="<?php echo htmlspecialchars($u['email']); ?>" class="input-field" disabled style="opacity: 0.6; cursor: not-allowed;">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="input-group">
                                <label class="input-label">Cédula</label>
                                <input type="text" name="identificacion" value="<?php echo htmlspecialchars($u['identificacion'] ?? ''); ?>" class="input-field" placeholder="No especificado">
                            </div>
                            <div class="input-group">
                                <label class="input-label">Teléfono</label>
                                <input type="text" name="telefono" value="<?php echo htmlspecialchars($u['telefono'] ?? ''); ?>" class="input-field" placeholder="No especificado">
                            </div>
                        </div>

                        <div class="input-group">
                            <label class="input-label">Dirección de residencia</label>
                            <input type="text" name="direccion" value="<?php echo htmlspecialchars($u['direccion'] ?? ''); ?>" class="input-field" placeholder="No especificado">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="input-group">
                                <label class="input-label">Ciudad</label>
                                <input type="text" name="ciudad" value="<?php echo htmlspecialchars($u['ciudad'] ?? ''); ?>" class="input-field" placeholder="No especificado">
                            </div>
                            <div class="input-group">
                                <label class="input-label">Departamento</label>
                                <input type="text" name="departamento" value="<?php echo htmlspecialchars($u['departamento'] ?? ''); ?>" class="input-field" placeholder="No especificado">
                            </div>
                        </div>

                        <div class="input-group">
                            <label class="input-label">Correo Institucional</label>
                            <input type="email" name="correo_institucional" value="<?php echo htmlspecialchars($u['correo_institucional'] ?? ''); ?>" class="input-field" placeholder="No especificado">
                        </div>

                        <div class="input-group">
                            <label class="input-label">Programa Académico</label>
                            <input type="text" name="programa_academico" value="<?php echo htmlspecialchars($u['programa_academico'] ?? ''); ?>" class="input-field" placeholder="No especificado">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="input-group">
                                <label class="input-label">Semestre</label>
                                <input type="text" name="semestre" value="<?php echo htmlspecialchars($u['semestre'] ?? ''); ?>" class="input-field" placeholder="No especificado">
                            </div>
                            <div class="input-group">
                                <label class="input-label">Código Estudiantil</label>
                                <input type="text" name="codigo_estudiantil" value="<?php echo htmlspecialchars($u['codigo_estudiantil'] ?? ''); ?>" class="input-field" placeholder="No especificado">
                            </div>
                        </div>

                        <div class="input-group">
                            <label class="input-label">Cambiar Contraseña</label>
                            <div class="input-wrapper">
                                <input type="password" name="password" id="password" class="input-field" placeholder="Dejar en blanco para no cambiar">
                                <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px; justify-content: center; height: 50px; font-weight: 600;">
                            Actualizar Todo <i class="fa-solid fa-save" style="margin-left: 8px;"></i>
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>

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
</body>

</html>