<?php
require_once 'conexion.php';
verificar_sesion();
verificar_rol('profesor');

$profesor_id = $_SESSION['usuario_id'];
$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['asignar'])) {
    $email_estudiante = limpiar_dato($_POST['email_estudiante']);
    $materia_id = (int)$_POST['materia_id'];

    $res = $conn->query("SELECT id, rol FROM usuarios WHERE email = '$email_estudiante'");
    if ($res->num_rows > 0) {
        $est = $res->fetch_assoc();
        if ($est['rol'] == 'estudiante') {
            $est_id = $est['id'];
            $check = $conn->query("SELECT id FROM matriculas WHERE estudiante_id = $est_id AND materia_id = $materia_id");
            if ($check->num_rows == 0) {
                $conn->query("INSERT INTO matriculas (estudiante_id, materia_id, periodo) VALUES ($est_id, $materia_id, '2024-1')");
                $mensaje = '<div style="background: rgba(16, 185, 129, 0.1); color: #34d399; padding: 12px; border-radius: 10px; margin-bottom: 25px; font-size: 0.85rem; border: 1px solid rgba(16, 185, 129, 0.2);"><i class="fa-solid fa-check-circle"></i> Estudiante inscrito correctamente.</div>';
            } else {
                $mensaje = '<div style="background: rgba(245, 158, 11, 0.1); color: #fbbf24; padding: 12px; border-radius: 10px; margin-bottom: 25px; font-size: 0.85rem; border: 1px solid rgba(245, 158, 11, 0.2);"><i class="fa-solid fa-circle-exclamation"></i> El estudiante ya está en esta materia.</div>';
            }
        } else {
            $mensaje = '<div style="background: rgba(244, 63, 94, 0.1); color: #fb7185; padding: 12px; border-radius: 10px; margin-bottom: 25px; font-size: 0.85rem; border: 1px solid rgba(244, 63, 114, 0.2);"><i class="fa-solid fa-xmark-circle"></i> Solo se pueden inscribir estudiantes.</div>';
        }
    } else {
        $mensaje = '<div style="background: rgba(244, 63, 94, 0.1); color: #fb7185; padding: 12px; border-radius: 10px; margin-bottom: 25px; font-size: 0.85rem; border: 1px solid rgba(244, 63, 114, 0.2);"><i class="fa-solid fa-user-slash"></i> Estudiante no encontrado.</div>';
    }
}

$materias = $conn->query("SELECT * FROM materias WHERE profesor_id = $profesor_id");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Académica - Docentes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>
    <div class="background-mesh"></div>
    <div class="dashboard-grid">
        <aside class="sidebar">
            <div class="logo-area" style="margin-bottom: 40px; text-align: center;">
                <i class="fa-solid fa-graduation-cap logo-icon" style="font-size: 2rem; color: var(--primary);"></i>
                <h3 style="color: white; margin-top: 10px;">Unicali<span style="color: var(--primary);">Docente</span></h3>
            </div>
            <nav>
                <a href="dashboard_profesor.php" class="nav-link">
                    <i class="fa-solid fa-house"></i> Inicio
                </a>
                <a href="gestion_materias.php" class="nav-link">
                    <i class="fa-solid fa-book"></i> Mis Materias
                </a>
                <a href="gestion_notas.php" class="nav-link active">
                    <i class="fa-solid fa-user-pen"></i> Gestionar Notas
                </a>
                <a href="asistencia.php" class="nav-link">
                    <i class="fa-solid fa-clipboard-user"></i> Asistencia
                </a>
                <a href="perfil.php" class="nav-link">
                    <i class="fa-solid fa-gear"></i> Configuración
                </a>
                <a href="logout.php" class="nav-link" style="margin-top: auto; color: #f43f5e;">
                    <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
                </a>
            </nav>
        </aside>

        <main class="main-content">
            <header style="margin-bottom: 30px;">
                <h1 class="text-gradient">Gestión Académica</h1>
                <p class="text-muted">Inscribe alumnos y califica tus cursos activos</p>
            </header>

            <?php echo $mensaje; ?>

            <div style="display: grid; gap: 20px;">
                <?php while ($m = $materias->fetch_assoc()):
                    $mid = $m['id'];
                    $count = $conn->query("SELECT COUNT(*) as c FROM matriculas WHERE materia_id = $mid")->fetch_assoc()['c'];
                ?>
                    <div class="card glass-panel fade-in">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                            <div style="display: flex; align-items: center; gap: 20px;">
                                <div style="background: rgba(99, 102, 241, 0.1); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa-solid fa-users-viewfinder" style="color: var(--primary); font-size: 1.2rem;"></i>
                                </div>
                                <div>
                                    <h3 style="margin-bottom: 4px;"><?php echo htmlspecialchars($m['nombre']); ?> <span class="text-muted" style="font-size:0.8rem; font-weight: 500;">(<?php echo htmlspecialchars($m['codigo']); ?>)</span></h3>
                                    <p class="text-muted"><i class="fa-solid fa-user-graduate" style="margin-right: 5px;"></i> <?php echo $count; ?> Estudiantes inscritos</p>
                                </div>
                            </div>
                            <div style="display: flex; gap: 10px;">
                                <button onclick="mostrarAsignar(<?php echo $mid; ?>)" class="btn btn-outline" style="font-size: 0.85rem;">
                                    <i class="fa-solid fa-user-plus"></i> Inscribir
                                </button>
                                <a href="editar_notas.php?materia=<?php echo $mid; ?>" class="btn btn-primary" style="font-size: 0.85rem;">
                                    <i class="fa-solid fa-pen-nib"></i> Calificar
                                </a>
                            </div>
                        </div>

                        <div id="form-asignar-<?php echo $mid; ?>" style="display: none; margin-top: 25px; padding-top: 25px; border-top: 1px solid var(--glass-border);" class="fade-in">
                            <form method="POST" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
                                <input type="hidden" name="materia_id" value="<?php echo $mid; ?>">
                                <input type="hidden" name="asignar" value="1">
                                <div style="flex: 1; min-width: 250px;">
                                    <label class="input-label">Inscribir por Correo</label>
                                    <input type="email" name="email_estudiante" class="input-field" placeholder="correo@unicali.edu.co" required style="margin-bottom: 0;">
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    Finalizar Inscripción
                                </button>
                                <button type="button" onclick="mostrarAsignar(<?php echo $mid; ?>)" class="btn" style="background: rgba(255,255,255,0.05); color: white;">
                                    Cancelar
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </main>
    </div>

    <script>
        function mostrarAsignar(id) {
            var el = document.getElementById('form-asignar-' + id);
            if (el.style.display === 'none') {
                el.style.display = 'block';
            } else {
                el.style.display = 'none';
            }
        }
    </script>
</body>

</html>