<?php
require_once 'conexion.php';
verificar_sesion();
verificar_rol('profesor');

$profesor_id = $_SESSION['usuario_id'];
$nombre_profesor = obtener_nombre_usuario();

// Obtener estadísticas rápidas
$sql_materias = "SELECT COUNT(*) as total FROM materias WHERE profesor_id = $profesor_id";
$res_materias = $conn->query($sql_materias);
$total_materias = $res_materias->fetch_assoc()['total'];

// Dummy data incase queries fail or empty
$total_alumnos = 0; // Se calcularía con un JOIN complejo
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Docente | Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="icon" type="image/png" href="favicon.png?v=3">
    <link rel="shortcut icon" href="favicon.ico?v=3">
    <link rel="apple-touch-icon" href="favicon.png?v=3">
</head>

<body>
    <div class="mobile-toggle" id="side-toggle">
        <i class="fa-solid fa-bars"></i>
    </div>
    <div class="dashboard-grid">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo-area" style="margin-bottom: 40px; text-align: center;">
                <i class="fa-solid fa-graduation-cap logo-icon" style="font-size: 2rem; color: var(--primary);"></i>
                <h3 style="color: white; margin-top: 10px;">Unicali<span style="color: var(--primary);">Docente</span></h3>
            </div>

            <nav>
                <a href="dashboard_profesor.php" class="nav-link active">
                    <i class="fa-solid fa-house"></i> Inicio
                </a>
                <a href="gestion_materias.php" class="nav-link">
                    <i class="fa-solid fa-book"></i> Mis Materias
                </a>
                <a href="gestion_notas.php" class="nav-link">
                    <i class="fa-solid fa-user-pen"></i> Gestionar Notas
                </a>
                <a href="asistencia.php" class="nav-link">
                    <i class="fa-solid fa-clipboard-user"></i> Asistencia
                </a>
                <a href="perfil.php" class="nav-link">
                    <i class="fa-solid fa-gear"></i> Configuración
                </a>
                <a href="logout.php" class="nav-link" style="margin-top: auto; color: var(--danger);">
                    <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div>
                    <h1 class="text-gradient">Hola, Profe. <?php echo htmlspecialchars($nombre_profesor); ?></h1>
                    <p class="text-muted">Resumen de tu actividad académica</p>
                </div>
                <div class="user-avatar" style="width: 40px; height: 40px; background: var(--primary); border-radius: 50%;"></div>
            </header>

            <!-- Stats Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px;">
                <div class="card stat-card glass-panel">
                    <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 2rem;"><?php echo $total_materias; ?></h3>
                        <p class="text-muted">Materias Activas</p>
                    </div>
                </div>

                <div class="card stat-card glass-panel">
                    <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: var(--info);">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 2rem;">--</h3>
                        <p class="text-muted">Estudiantes Totales</p>
                    </div>
                </div>
            </div>

            <!-- Accesos Rápidos -->
            <h2 style="margin-bottom: 20px;">Acciones Rápidas</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <a href="gestion_notas.php" class="card glass-panel" style="text-decoration: none; color: inherit; text-align: center;">
                    <i class="fa-solid fa-pen-to-square" style="font-size: 2.5rem; color: var(--accent); margin-bottom: 15px;"></i>
                    <h4>Subir Notas</h4>
                    <p class="text-muted" style="font-size: 0.8rem;">Calificar cortes y parciales</p>
                </a>

                <a href="crear_materia.php" class="card glass-panel" style="text-decoration: none; color: inherit; text-align: center;">
                    <i class="fa-solid fa-plus-circle" style="font-size: 2.5rem; color: var(--primary); margin-bottom: 15px;"></i>
                    <h4>Nueva Materia</h4>
                    <p class="text-muted" style="font-size: 0.8rem;">Crear curso académico</p>
                </a>
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