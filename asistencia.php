<?php
require_once 'conexion.php';
verificar_sesion();
verificar_rol('profesor');

$profesor_id = $_SESSION['usuario_id'];
$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar_asistencia'])) {
    $materia_id = (int)$_POST['materia_id'];
    $fecha = $_POST['fecha'];
    $asistencias = isset($_POST['asistencia']) ? $_POST['asistencia'] : [];

    $sql_alumnos = "SELECT estudiante_id FROM matriculas WHERE materia_id = $materia_id";
    $res_alumnos = $conn->query($sql_alumnos);

    while ($row = $res_alumnos->fetch_assoc()) {
        $estudiante_id = $row['estudiante_id'];
        $estado = in_array($estudiante_id, $asistencias) ? 'presente' : 'ausente';

        $stmt_check = $conn->prepare("SELECT id FROM asistencia WHERE estudiante_id = ? AND materia_id = ? AND fecha = ?");
        $stmt_check->bind_param("iis", $estudiante_id, $materia_id, $fecha);
        $stmt_check->execute();
        $res_check = $stmt_check->get_result();

        if ($res_check->num_rows > 0) {
            $stmt_upd = $conn->prepare("UPDATE asistencia SET estado = ? WHERE estudiante_id = ? AND materia_id = ? AND fecha = ?");
            $stmt_upd->bind_param("siis", $estado, $estudiante_id, $materia_id, $fecha);
            $stmt_upd->execute();
        } else {
            $stmt_ins = $conn->prepare("INSERT INTO asistencia (estudiante_id, materia_id, fecha, estado) VALUES (?, ?, ?, ?)");
            $stmt_ins->bind_param("iiss", $estudiante_id, $materia_id, $fecha, $estado);
            $stmt_ins->execute();
        }
    }
    $mensaje = '<div style="background: rgba(16, 185, 129, 0.1); color: #34d399; padding: 12px; border-radius: 10px; margin-bottom: 25px; font-size: 0.85rem; border: 1px solid rgba(16, 185, 129, 0.2);"><i class="fa-solid fa-check-circle"></i> Asistencia guardada correctamente.</div>';
}

$materias = $conn->query("SELECT * FROM materias WHERE profesor_id = $profesor_id");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Asistencia - Unicali</title>
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
                <a href="gestion_notas.php" class="nav-link">
                    <i class="fa-solid fa-user-pen"></i> Gestionar Notas
                </a>
                <a href="asistencia.php" class="nav-link active">
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
                <h1 class="text-gradient">Control de Asistencia</h1>
                <p class="text-muted">Selecciona una materia para registrar la asistencia del día</p>
            </header>

            <?php echo $mensaje; ?>

            <div style="display: grid; gap: 25px;">
                <?php while ($m = $materias->fetch_assoc()):
                    $mid = $m['id'];
                    $alumnos = $conn->query("SELECT u.id, u.nombre FROM usuarios u JOIN matriculas m ON u.id = m.estudiante_id WHERE m.materia_id = $mid");
                ?>
                    <div class="card glass-panel fade-in">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <div style="background: rgba(6, 182, 212, 0.1); width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa-solid fa-calendar-check" style="color: var(--secondary);"></i>
                                </div>
                                <div>
                                    <h3 style="font-size: 1.1rem;"><?php echo htmlspecialchars($m['nombre']); ?></h3>
                                    <span class="text-muted" style="font-size: 0.8rem;"><?php echo htmlspecialchars($m['codigo']); ?></span>
                                </div>
                            </div>
                            <button onclick="toggleAsistencia(<?php echo $mid; ?>)" class="btn btn-primary" style="padding: 8px 16px; font-size: 0.85rem;">
                                Abrir Registro
                            </button>
                        </div>

                        <div id="asistencia-box-<?php echo $mid; ?>" style="display: none; border-top: 1px solid var(--glass-border); padding-top: 20px;" class="fade-in">
                            <form method="POST">
                                <input type="hidden" name="materia_id" value="<?php echo $mid; ?>">
                                <input type="hidden" name="registrar_asistencia" value="1">

                                <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                                    <label class="text-muted" style="font-size: 0.9rem;">Fecha de Clase:</label>
                                    <input type="date" name="fecha" value="<?php echo date('Y-m-d'); ?>" class="input-field" style="margin-bottom: 0; width: auto; padding: 6px 12px;">
                                </div>

                                <div style="max-height: 300px; overflow-y: auto; margin-bottom: 20px; background: rgba(0,0,0,0.1); border-radius: 10px; padding: 10px;">
                                    <?php if ($alumnos->num_rows > 0): ?>
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <thead>
                                                <tr style="text-align: left; border-bottom: 1px solid var(--glass-border);">
                                                    <th style="padding: 10px 5px; font-size: 0.8rem; text-transform: uppercase;" class="text-muted">Estudiante</th>
                                                    <th style="padding: 10px 5px; text-align: center; font-size: 0.8rem; text-transform: uppercase;" class="text-muted">Asistió</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($a = $alumnos->fetch_assoc()): ?>
                                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                                        <td style="padding: 12px 5px; font-size: 0.9rem;"><?php echo htmlspecialchars($a['nombre']); ?></td>
                                                        <td style="padding: 12px 5px; text-align: center;">
                                                            <input type="checkbox" name="asistencia[]" value="<?php echo $a['id']; ?>" checked style="width: 18px; height: 18px; cursor: pointer;">
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    <?php else: ?>
                                        <p class="text-muted" style="text-align: center; padding: 20px; font-size: 0.9rem;">No hay estudiantes inscritos en esta materia.</p>
                                    <?php endif; ?>
                                </div>

                                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                                    <button type="button" onclick="toggleAsistencia(<?php echo $mid; ?>)" class="btn btn-outline" style="border-color: rgba(255,255,255,0.1); color: #94a3b8;">Cancelar</button>
                                    <button type="submit" class="btn btn-primary" <?php echo ($alumnos->num_rows == 0) ? 'disabled' : ''; ?>>Guardar Asistencia</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </main>
    </div>

    <script>
        function toggleAsistencia(id) {
            var el = document.getElementById('asistencia-box-' + id);
            el.style.display = el.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>

</html>