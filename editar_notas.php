<?php
require_once 'conexion.php';
verificar_sesion();
verificar_rol('profesor');

if (!isset($_GET['materia'])) {
    header("Location: gestion_notas.php");
    exit();
}

$materia_id = (int)$_GET['materia'];
$profesor_id = $_SESSION['usuario_id'];

// Verificar propiedad de la materia
$check = $conn->query("SELECT * FROM materias WHERE id = $materia_id AND profesor_id = $profesor_id");
if ($check->num_rows == 0) {
    die("No tienes permiso para ver esta materia");
}
$materia = $check->fetch_assoc();

// Verificar periodo de edición
$conf_bg = $conn->query("SELECT valor FROM configuracion WHERE clave = 'edicion_notas_activa'");
$edicion_activa = ($conf_bg && $conf_bg->num_rows > 0) ? $conf_bg->fetch_assoc()['valor'] : '1';

$mensaje = '';
if ($edicion_activa == '0') {
    $mensaje = '<div style="background: rgba(244, 63, 94, 0.1); color: #fb7185; padding: 12px; border-radius: 10px; margin-bottom: 25px; font-size: 0.85rem; border: 1px solid rgba(244, 63, 114, 0.2);"><i class="fa-solid fa-lock"></i> El periodo de edición de notas está cerrado. Contacta al administrador.</div>';
}

// Guardar Notas
if ($_SERVER["REQUEST_METHOD"] == "POST" && $edicion_activa == '1') {
    foreach ($_POST['notas'] as $matricula_id => $cortes) {
        foreach ($cortes as $corte_nombre => $datos) {
            $valor = $datos['valor'];
            $obs = $conn->real_escape_string($datos['obs']);

            if ($valor !== '') {
                if ($valor < 0 || $valor > 5) {
                    $mensaje = '<div style="background: rgba(244, 63, 94, 0.1); color: #fb7185; padding: 12px; border-radius: 10px; margin-bottom: 25px; font-size: 0.85rem; border: 1px solid rgba(244, 63, 114, 0.2);">Error: Notas deben estar entre 0.0 y 5.0</div>';
                    continue;
                }

                $sql_check = "SELECT id FROM notas WHERE matricula_id = $matricula_id AND corte = '$corte_nombre'";
                $exists = $conn->query($sql_check);

                if ($exists->num_rows > 0) {
                    $nid = $exists->fetch_assoc()['id'];
                    $conn->query("UPDATE notas SET valor = '$valor', observacion = '$obs' WHERE id = $nid");
                } else {
                    $conn->query("INSERT INTO notas (matricula_id, corte, valor, observacion) VALUES ($matricula_id, '$corte_nombre', '$valor', '$obs')");
                }
            }
        }
    }
    $mensaje = '<div style="background: rgba(16, 185, 129, 0.1); color: #34d399; padding: 12px; border-radius: 10px; margin-bottom: 25px; font-size: 0.85rem; border: 1px solid rgba(16, 185, 129, 0.2);"><i class="fa-solid fa-check-circle"></i> Notas actualizadas correctamente.</div>';
}

// Obtener estudiantes
$sql_estudiantes = "SELECT u.nombre, u.email, m.id as matricula_id 
                    FROM matriculas m 
                    JOIN usuarios u ON m.estudiante_id = u.id 
                    WHERE m.materia_id = $materia_id 
                    ORDER BY u.nombre";
$res_estudiantes = $conn->query($sql_estudiantes);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificar: <?php echo htmlspecialchars($materia['nombre']); ?> - Unicali</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        .input-nota {
            width: 65px !important;
            padding: 8px 4px !important;
            text-align: center;
            margin-bottom: 5px !important;
            font-weight: 700;
            border-radius: 8px !important;
            font-size: 0.9rem !important;
        }

        .input-obs {
            width: 100% !important;
            font-size: 0.75rem !important;
            padding: 6px !important;
            margin-bottom: 0 !important;
            border-radius: 8px !important;
            resize: none;
        }

        th {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            white-space: nowrap;
        }

        td {
            vertical-align: top;
            padding: 15px 10px !important;
        }
    </style>
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
            <header style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                <div>
                    <h1 class="text-gradient"><?php echo htmlspecialchars($materia['nombre']); ?></h1>
                    <p class="text-muted">Registro de calificaciones - 5 Cortes Académicos</p>
                </div>
                <div style="display: flex; gap: 10px;">
                    <a href="gestion_notas.php" class="btn btn-outline">
                        <i class="fa-solid fa-arrow-left"></i> Volver
                    </a>
                    <button type="button" onclick="document.getElementById('form-notas').submit()" class="btn btn-primary" <?php echo ($edicion_activa == '0') ? 'disabled' : ''; ?>>
                        <i class="fa-solid fa-save"></i> Guardar Todo
                    </button>
                </div>
            </header>

            <?php echo $mensaje; ?>

            <div class="card glass-panel fade-in" style="padding: 0; overflow: hidden;">
                <form method="POST" id="form-notas">
                    <div class="table-container" style="overflow-x: auto;">
                        <table style="width: 100%; min-width: 1000px;">
                            <thead>
                                <tr>
                                    <th style="min-width: 180px; text-align: left; padding-left: 20px;">Estudiante</th>
                                    <th>Corte 1 (20%)</th>
                                    <th>Corte 2 (20%)</th>
                                    <th>Corte 3 (20%)</th>
                                    <th>Examen (30%)</th>
                                    <th>Seguim. (10%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($res_estudiantes->num_rows > 0):
                                    while ($est = $res_estudiantes->fetch_assoc()):
                                        $mid = $est['matricula_id'];
                                        $notas_db = [];
                                        $q_notas = $conn->query("SELECT corte, valor, observacion FROM notas WHERE matricula_id = $mid");
                                        while ($n = $q_notas->fetch_assoc()) {
                                            $notas_db[$n['corte']] = $n;
                                        }

                                        function renderInput($mid, $corte, $notas_db, $edicion_activa)
                                        {
                                            $val = isset($notas_db[$corte]) ? $notas_db[$corte]['valor'] : '';
                                            $obs = isset($notas_db[$corte]) ? $notas_db[$corte]['observacion'] : '';
                                            $readonly = ($edicion_activa == '0') ? 'readonly' : '';
                                            echo '<div style="display: flex; flex-direction: column; align-items: center; gap: 5px;">
                                                    <input type="number" step="0.1" min="0" max="5" 
                                                           name="notas[' . $mid . '][' . $corte . '][valor]" 
                                                           value="' . $val . '" 
                                                           class="input-field input-nota" 
                                                           placeholder="0.0" ' . $readonly . '>
                                                    <textarea name="notas[' . $mid . '][' . $corte . '][obs]" 
                                                              class="input-field input-obs" 
                                                              placeholder="Obs..." 
                                                              rows="1" ' . $readonly . '>' . htmlspecialchars($obs) . '</textarea>
                                                  </div>';
                                        }
                                ?>
                                        <tr style="border-bottom: 1px solid var(--glass-border);">
                                            <td style="padding-left: 20px;">
                                                <div style="font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($est['nombre']); ?></div>
                                                <div class="text-muted" style="font-size: 0.7rem;"><?php echo htmlspecialchars($est['email']); ?></div>
                                            </td>
                                            <td><?php renderInput($mid, 'Corte 1', $notas_db, $edicion_activa); ?></td>
                                            <td><?php renderInput($mid, 'Corte 2', $notas_db, $edicion_activa); ?></td>
                                            <td><?php renderInput($mid, 'Corte 3', $notas_db, $edicion_activa); ?></td>
                                            <td><?php renderInput($mid, 'Examen Final', $notas_db, $edicion_activa); ?></td>
                                            <td><?php renderInput($mid, 'Seguimiento', $notas_db, $edicion_activa); ?></td>
                                        </tr>
                                    <?php endwhile;
                                else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 40px;">
                                            <p class="text-muted">No hay estudiantes inscritos en esta materia.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>

</html>