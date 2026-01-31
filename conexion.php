<?php
// conexion.php - Conexión a Base de Datos y Funciones Globales

// Configuración de Errores (Mostrar solo en desarrollo)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Detectar entorno automáticamente
$servidor = $_SERVER['HTTP_HOST'];
if ($servidor == 'localhost' || $servidor == '127.0.0.1' || $servidor == '::1') {
    // === CONFIGURACIÓN LOCAL (XAMPP / WAMP) ===
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db   = "universidad";
} else {
    // === CONFIGURACIÓN WEB (InfinityFree / Hostinger / etc) ===
    // IMPORTANTE: Cambia estos datos por los que te dio tu hosting en el Panel de Control
    $host = "sql113.infinityfree.com"; // Ejemplo: sql305.infinityfree.com
    $user = "if0_41036450";            // Tu nombre de usuario de MySQL
    $pass = "1234JJPP";        // Tu contraseña de MySQL
    $db   = "if0_41036450_unieducativa"; // El nombre de la base de datos que creaste
}

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Error crítico de conexión: " . $conn->connect_error);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// === FUNCIONES DE AYUDA === //

// Verificar si el usuario está logueado
function verificar_sesion()
{
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit();
    }
}

// Verificar rol (Redirigir si no tiene permiso)
function verificar_rol($rol_requerido)
{
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== $rol_requerido) {
        // Si intenta entrar como profesor pero es estudiante, mandarlo a su dashboard
        if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'estudiante') {
            header("Location: dashboard_estudiante.php");
        } else if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'profesor') {
            header("Location: dashboard_profesor.php");
        } else {
            header("Location: login.php"); // Si no es nada, al login
        }
        exit();
    }
}

// Limpiar inputs
function limpiar_dato($dato)
{
    global $conn;
    return $conn->real_escape_string(trim($dato));
}

// Obtener nombre del usuario actual
function obtener_nombre_usuario()
{
    return isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
}

// Obtener la ruta de la foto del usuario o un avatar por defecto
function obtener_foto_usuario($foto = null)
{
    if ($foto && file_exists(__DIR__ . '/uploads/fotos/' . $foto)) {
        return 'uploads/fotos/' . $foto;
    }
    return 'https://ui-avatars.com/api/?name=User&background=6366f1&color=fff';
}
