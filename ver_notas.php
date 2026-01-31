<?php
require_once 'conexion.php';
verificar_sesion();
verificar_rol('estudiante');

if (!isset($_GET['materia'])) {
    header("Location: dashboard_estudiante.php");
    exit();
}

$nombre_materia = urldecode($_GET['materia']);
$estudiante_id = $_SESSION['usuario_id'];

// Buscar ID materia
$stmt = $conn->prepare("SELECT id, codigo, descripcion, profesor_id FROM materias WHERE nombre = ?");
$stmt->bind_param("s", $nombre_materia);
$stmt->execute();
$mat_info = $stmt->get_result()->fetch_assoc();

if (!$mat_info) {
    header("Location: dashboard_estudiante.php");
    exit();
}

$materia_id = $mat_info['id'];

// Obtener notas
$sql_notas = "SELECT * FROM notas WHERE matricula_id = (SELECT id FROM matriculas WHERE estudiante_id = $estudiante_id AND materia_id = $materia_id)";
$res_notas = $conn->query($sql_notas);

$notas_arr = [];
while ($n = $res_notas->fetch_assoc()) {
    $notas_arr[] = $n;
}

// Calcular promedio con pesos: 20, 20, 20, 30, 10
$suma = 0;
foreach ($notas_arr as $n) {
    if ($n['corte'] == 'Corte 1') $suma += $n['valor'] * 0.2;
    if ($n['corte'] == 'Corte 2') $suma += $n['valor'] * 0.2;
    if ($n['corte'] == 'Corte 3') $suma += $n['valor'] * 0.2;
    if ($n['corte'] == 'Examen Final')   $suma += $n['valor'] * 0.3;
    if ($n['corte'] == 'Seguimiento')    $suma += $n['valor'] * 0.1;
}
$promedio_final = number_format($suma, 1);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notas: <?php echo htmlspecialchars($nombre_materia); ?> - Unicali</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>
    <div class="background-mesh"></div>
    <div class="dashboard-grid">
        <aside class="sidebar">
            <div class="logo-area" style="margin-bottom: 40px; text-align: center;">
                <i class="fa-solid fa-graduation-cap logo-icon" style="font-size: 2rem; color: var(--primary);"></i>
                <h3 style="color: white; margin-top: 10px;">Unicali<span style="color: var(--primary);">Estudiante</span></h3>
            </div>
            <nav>
                <a href="dashboard_estudiante.php" class="nav-link">
                    <i class="fa-solid fa-house"></i> Inicio
                </a>
                <a href="ver_asistencia.php" class="nav-link">
                    <i class="fa-solid fa-calendar-check"></i> Mis Asistencias
                </a>
                <a href="ver_notas.php" class="nav-link active">
                    <i class="fa-solid fa-chart-line"></i> Mis Notas
                </a>
                <a href="historial.php" class="nav-link">
                    <i class="fa-solid fa-receipt"></i> Historial Académico
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
            <header style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
                <div>
                    <h1 class="text-gradient"><?php echo htmlspecialchars($nombre_materia); ?></h1>
                    <p class="text-muted"><?php echo htmlspecialchars($mat_info['codigo']); ?> • <?php echo htmlspecialchars($mat_info['descripcion']); ?></p>
                </div>
                <a href="pdf.php?materia=<?php echo urlencode($nombre_materia); ?>" class="btn btn-outline" style="background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.2); color: #f87171;">
                    <i class="fa-solid fa-file-pdf"></i> Informe PDF
                </a>
            </header>

            <div class="stats-grid" style="margin-bottom: 30px;">
                <div class="card glass-panel fade-in">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p class="text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Promedio Definitivo</p>
                            <h2 style="font-size: 1.8rem; margin: 5px 0; color: <?php echo $promedio_final >= 3 ? '#34d399' : '#fb7185'; ?>;">
                                <?php echo $promedio_final; ?>
                            </h2>
                            <span style="font-size: 0.75rem; color: #94a3b8;">5 Cortes Académicos</span>
                        </div>
                        <div style="background: rgba(99, 102, 241, 0.1); width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-award" style="color: var(--primary); font-size: 1.2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <h3 style="margin-bottom: 20px;">Detalle de Calificaciones</h3>
            <div class="card glass-panel fade-in" style="padding: 0; overflow: hidden;">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Corte / Evaluación</th>
                                <th>Peso</th>
                                <th>Calificación</th>
                                <th>Observaciones</th>
                                <th>Fecha Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $cortes_pesos = [
                                'Corte 1' => '20%',
                                'Corte 2' => '20%',
                                'Corte 3' => '20%',
                                'Examen Final' => '30%',
                                'Seguimiento' => '10%'
                            ];

                            if (count($notas_arr) > 0):
                                // Ordenar notas según los cortes definidos
                                $orden = array_keys($cortes_pesos);
                                usort($notas_arr, function ($a, $b) use ($orden) {
                                    return array_search($a['corte'], $orden) - array_search($b['corte'], $orden);
                                });

                                foreach ($notas_arr as $nota): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600;"><?php echo $nota['corte']; ?></div>
                                        </td>
                                        <td>
                                            <span class="text-muted" style="font-size: 0.85rem;"><?php echo $cortes_pesos[$nota['corte']] ?? '-'; ?></span>
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <div style="width: 40px; height: 40px; border-radius: 50%; background: <?php echo $nota['valor'] >= 3 ? 'rgba(52, 211, 153, 0.1)' : 'rgba(244, 63, 94, 0.1)'; ?>; display: flex; align-items: center; justify-content: center; font-weight: 700; color: <?php echo $nota['valor'] >= 3 ? '#34d399' : '#fb7185'; ?>;">
                                                    <?php echo $nota['valor']; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($nota['observacion']): ?>
                                                <p style="font-size: 0.85rem;" class="text-muted">
                                                    <i class="fa-solid fa-quote-left" style="margin-right: 5px; opacity: 0.5;"></i>
                                                    <?php echo htmlspecialchars($nota['observacion']); ?>
                                                </p>
                                            <?php else: ?>
                                                <span class="text-muted" style="font-size: 0.8rem;">Sin observaciones</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span style="font-size: 0.8rem;" class="text-muted">
                                                <i class="fa-regular fa-calendar" style="margin-right: 5px;"></i>
                                                <?php echo date('d M, Y', strtotime($nota['updated_at'])); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 40px;">
                                        <i class="fa-solid fa-folder-open" style="font-size: 2rem; opacity: 0.2; display: block; margin-bottom: 10px;"></i>
                                        <p class="text-muted">No se han registrado notas para esta materia.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>

</html>