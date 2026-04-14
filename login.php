<?php
/**
 * PROYECTO: BioGrid - Gestión de Activos
 * AUTOR: Diego Palacios
 * FECHA: 31/03/2026
 * DESCRIPCIÓN: Portal de acceso profesional. 
 * Combina identidad corporativa con seguridad de ingreso.
 */
session_start();
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso | BioGrid - Gestión de Activos</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --dark: #0f172a;
            --slate: #64748b;
        }

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            display: flex;
            height: 100vh;
            background-color: #f8fafc;
            overflow: hidden;
        }

        /* Lado Izquierdo: Branding y Mensaje */
        .login-info {
            flex: 1;
            background: linear-gradient(135deg, var(--dark) 0%, #1e293b 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 80px;
            color: white;
            position: relative;
        }

        .login-info::before {
            content: "";
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: url('https://www.transparenttextures.com/patterns/carbon-fibre.png');
            opacity: 0.1;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 40px;
        }

        .brand-logo img { height: 60px; }
        .brand-logo h1 { font-size: 2.5rem; font-weight: 800; margin: 0; }
        .brand-logo h1 span { color: var(--primary); }

        .info-content h2 { font-size: 1.8rem; font-weight: 700; margin-bottom: 20px; color: var(--primary); }
        .info-content p { font-size: 1.1rem; line-height: 1.6; color: #cbd5e1; max-width: 500px; }

        .features { margin-top: 40px; display: grid; gap: 20px; }
        .feature-item { display: flex; align-items: center; gap: 15px; font-size: 1rem; color: #f1f5f9; }
        .feature-item i { color: var(--primary); font-size: 1.2rem; }

        /* Lado Derecho: Formulario */
        .login-form-container {
            width: 450px;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            box-shadow: -10px 0 30px rgba(0,0,0,0.05);
            z-index: 10;
        }

        .form-header { margin-bottom: 40px; }
        .form-header h3 { font-size: 1.5rem; color: var(--dark); margin: 0; font-weight: 800; }
        .form-header p { color: var(--slate); margin-top: 10px; font-size: 0.9rem; }

        .form-group { margin-bottom: 20px; position: relative; }
        .form-group label { display: block; font-size: 0.8rem; font-weight: 700; color: var(--slate); text-transform: uppercase; margin-bottom: 8px; }
        .form-group i { position: absolute; left: 15px; top: 40px; color: var(--slate); }
        .form-group input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
            box-sizing: border-box;
        }
        .form-group input:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }
        .btn-login:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3); }

        .alert {
            padding: 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }

        .footer-copy { margin-top: auto; font-size: 0.75rem; color: var(--slate); text-align: center; }

        @media (max-width: 900px) {
            .login-info { display: none; }
            .login-form-container { width: 100%; padding: 40px; }
        }
    </style>
</head>
<body>

    <div class="login-info">
        <div class="brand-logo">
            <img src="assets/img/logo_biogrid.png" alt="BioGrid Logo" onerror="this.src='https://cdn-icons-png.flaticon.com/512/2092/2092030.png'">
            <h1>BioGrid<span>.</span></h1>
        </div>
        
        <div class="info-content">
            <h2>Gestión Inteligente de Activos</h2>
            <p>
                BioGrid es la plataforma líder en el censo y control de infraestructura para <strong>Electrohuila</strong>. 
                Optimiza la toma de decisiones mediante datos precisos y georreferenciación en tiempo real.
            </p>
        </div>

        <div class="features">
            <div class="feature-item"><i class="fa fa-check-circle"></i> Censos georreferenciados de luminarias y redes.</div>
            <div class="feature-item"><i class="fa fa-check-circle"></i> Análisis predictivo de carga energética.</div>
            <div class="feature-item"><i class="fa fa-check-circle"></i> Control administrativo de usuarios y roles.</div>
            <div class="feature-item"><i class="fa fa-check-circle"></i> Visor cartográfico avanzado con Leaflet.</div>
        </div>
    </div>

    <div class="login-form-container">
        <div class="form-header">
            <h3>Bienvenido</h3>
            <p>Por favor, ingresa tus credenciales.</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <i class="fa fa-exclamation-circle"></i> Usuario o contraseña incorrectos.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'logged_out'): ?>
            <div class="alert alert-success">
                <i class="fa fa-info-circle"></i> Sesión cerrada con éxito.
            </div>
        <?php endif; ?>

        <form action="auth_functions.php?action=login" method="POST">
            <div class="form-group">
                <label>Usuario / Email</label>
                <i class="fa fa-user"></i>
                <input type="text" name="usuario" required placeholder="tu-usuario" autofocus>
            </div>

            <div class="form-group">
                <label>Contraseña</label>
                <i class="fa fa-lock"></i>
                <input type="password" name="password" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn-login">
                Ingresar al Sistema
            </button>
        </form>

        <div class="footer-copy">
            &copy; 2026 BioGrid. Desarrollado por <strong>Diego Palacios</strong>.<br>
            Neiva, Huila.
        </div>
    </div>

</body>
</html>