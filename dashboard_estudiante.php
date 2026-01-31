<?php
require_once 'conexion.php';
verificar_sesion();
verificar_rol('estudiante');

$estudiante_id = $_SESSION['usuario_id'];
$nombre_estudiante = obtener_nombre_usuario();

// Obtener materias inscritas
$sql_matriculas = "SELECT m.nombre, m.codigo, u.nombre as profesor 
                   FROM matriculas mat 
                   JOIN materias m ON mat.materia_id = m.id 
                   JOIN usuarios u ON m.profesor_id = u.id 
                   WHERE mat.estudiante_id = $estudiante_id";
$res_matriculas = $conn->query($sql_matriculas);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estudiante | Unicali</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="icon" type="image/png" href="favicon.png?v=3">
    <link rel="shortcut icon" href="favicon.ico?v=3">
    <link rel="apple-touch-icon" href="favicon.png?v=3">
</head>

<body>
    <div class="background-mesh"></div>

    <div class="mobile-toggle" id="side-toggle">
        <i class="fa-solid fa-bars"></i>
    </div>

    <div class="dashboard-grid">
        <aside class="sidebar">
            <div class="logo-area" style="margin-bottom: 40px; text-align: center;">
                <i class="fa-solid fa-graduation-cap logo-icon" style="font-size: 2rem; color: var(--primary);"></i>
                <h3 style="color: white; margin-top: 10px;">Unicali<span style="color: var(--primary);">Estudiante</span></h3>
            </div>
            <nav>
                <a href="dashboard_estudiante.php" class="nav-link active">
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
                <a href="historial.php" class="nav-link">
                    <i class="fa-solid fa-receipt"></i> Historial Pago
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
                    <h1 class="text-gradient">Hola, <?php echo htmlspecialchars($nombre_estudiante); ?></h1>
                    <p class="text-muted">¡Qué gusto tenerte de vuelta! Aquí está tu resumen académico.</p>
                </div>
                <a href="pdf.php" target="_blank" class="btn btn-primary" style="padding: 12px 24px;">
                    <i class="fa-solid fa-file-pdf"></i> Descargar Reporte Completo
                </a>
            </header>

            <div class="card glass-panel fade-in">
                <div style="display: flex; gap: 15px; align-items: center;">
                    <div style="background: rgba(16, 185, 129, 0.1); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-calendar-day" style="color: #10b981; font-size: 1.2rem;"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 1rem;">Mi Asistencia</h3>
                        <p class="text-muted" style="font-size: 0.8rem;">Control de puntualidad diaria.</p>
                    </div>
                </div>
                <a href="ver_asistencia.php" class="btn btn-outline" style="width: 100%; margin-top: 15px; font-size: 0.85rem;">
                    <i class="fa-solid fa-check-to-slot"></i> Ver Asistencias
                </a>
            </div>

            <div class="card glass-panel fade-in">
                <div style="display: flex; gap: 15px; align-items: center;">
                    <div style="background: rgba(99, 102, 241, 0.1); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-award" style="color: var(--primary); font-size: 1.2rem;"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 1rem;">Certificado Oficial</h3>
                        <p class="text-muted" style="font-size: 0.8rem;">Descarga tu historial de calificaciones.</p>
                    </div>
                </div>
                <a href="pdf.php" target="_blank" class="btn btn-outline" style="width: 100%; margin-top: 15px; font-size: 0.85rem;">
                    <i class="fa-solid fa-download"></i> Obtener PDF
                </a>
            </div>

            <div class="card glass-panel fade-in">
                <div style="display: flex; gap: 15px; align-items: center;">
                    <div style="background: rgba(6, 182, 212, 0.1); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-comment-dots" style="color: var(--secondary); font-size: 1.2rem;"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 1rem;">Observaciones</h3>
                        <p class="text-muted" style="font-size: 0.8rem;">Revisa el feedback de tus docentes.</p>
                    </div>
                </div>
                <a href="observaciones.php" class="btn btn-outline" style="width: 100%; margin-top: 15px; font-size: 0.85rem;">
                    <i class="fa-solid fa-eye"></i> Ver Comentarios
                </a>
            </div>
    </div>

    <h2 style="margin-bottom: 20px;">Mis Materias</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
        <?php
        if ($res_matriculas && $res_matriculas->num_rows > 0) {
            while ($row = $res_matriculas->fetch_assoc()) {
                echo '<div class="card glass-panel fade-in">';
                echo '<div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">';
                echo '<div>';
                echo '<span style="background: rgba(99, 102, 241, 0.1); color: var(--primary); font-size: 0.7rem; font-weight: 700; padding: 4px 10px; border-radius: 20px; text-transform: uppercase;">' . htmlspecialchars($row['codigo']) . '</span>';
                echo '<h3 style="margin-top: 10px; font-size: 1.1rem;">' . htmlspecialchars($row['nombre']) . '</h3>';
                echo '<p class="text-muted" style="font-size: 0.85rem; margin-top: 4px;"><i class="fa-solid fa-chalkboard-user"></i> Prof. ' . htmlspecialchars($row['profesor']) . '</p>';
                echo '</div>';
                echo '</div>';
                echo '<div style="display: flex; gap: 10px;">';
                echo '<a href="ver_notas.php?materia=' . urlencode($row['nombre']) . '" class="btn btn-primary" style="flex: 1; font-size: 0.85rem; padding: 10px;">Ver Calificaciones</a>';
                echo '<a href="pdf.php?materia=' . urlencode($row['nombre']) . '" target="_blank" class="btn btn-outline" style="padding: 10px; width: 45px;"><i class="fa-solid fa-file-pdf"></i></a>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<div class="card glass-panel" style="grid-column: 1/-1; text-align: center; padding: 40px;">';
            echo '<i class="fa-solid fa-box-open" style="font-size: 2rem; opacity: 0.2; display: block; margin-bottom: 10px;"></i>';
            echo '<p class="text-muted">Aún no tienes materias inscritas para este periodo.</p>';
            echo '</div>';
        }
        ?>
    </div>
    </main>
    </div>

    <script>
        const btn = document.getElementById('side-toggle');
        const sidebar = document.querySelector('.sidebar');

        btn.onclick = () => {
            sidebar.classList.toggle('active');
            const icon = btn.querySelector('i');
            if (sidebar.classList.contains('active')) {
                icon.classList.replace('fa-bars', 'fa-xmark');
            } else {
                icon.classList.replace('fa-xmark', 'fa-bars');
            }
        };
    </script>
</body>

</html>