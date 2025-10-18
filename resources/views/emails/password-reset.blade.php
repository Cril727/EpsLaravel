<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecimiento de Contraseña - EPS SOG</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f7fa;
        }
        .container {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #0c82ea 0%, #0a6bb8 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .content {
            padding: 30px 20px;
        }
        .password-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .password-label {
            font-size: 14px;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        .password {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 2px;
            background-color: rgba(255, 255, 255, 0.2);
            padding: 15px 20px;
            border-radius: 6px;
            display: inline-block;
            margin: 10px 0;
            border: 2px dashed rgba(255, 255, 255, 0.5);
        }
        .warning-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .warning-box h3 {
            color: #856404;
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        .warning-box p {
            color: #856404;
            margin: 0;
            font-size: 14px;
        }
        .instructions {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #0c82ea;
        }
        .instructions h3 {
            color: #0c82ea;
            margin: 0 0 10px 0;
            font-size: 18px;
        }
        .instructions ol {
            margin: 0;
            padding-left: 20px;
        }
        .instructions li {
            margin-bottom: 8px;
            color: #555;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 10px 10px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .footer p {
            margin: 0;
            font-size: 12px;
            color: #6c757d;
        }
        .footer .logo {
            font-weight: bold;
            color: #0c82ea;
        }
        .security-note {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .security-note h4 {
            color: #0056b3;
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        .security-note p {
            color: #0056b3;
            margin: 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Restablecimiento de Contraseña</h1>
            <p>EPS CRIS - Sistema de Gestión Médica</p>
        </div>

        <div class="content">
            <p>Hola,</p>

            <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en el sistema EPS CRIS. Para tu seguridad, hemos generado una contraseña temporal que puedes usar para acceder a tu cuenta.</p>

            <div class="password-box">
                <div class="password-label">Tu contraseña temporal es:</div>
                <div class="password">{{ $temporaryPassword }}</div>
            </div>

            <div class="instructions">
                <h3>📋 Pasos a seguir:</h3>
                <ol>
                    <li>Inicia sesión en la aplicación con esta contraseña temporal</li>
                    <li>Ve a tu perfil o configuración</li>
                    <li>Cambia tu contraseña por una nueva y segura</li>
                    <li>Guarda los cambios</li>
                </ol>
            </div>

            <div class="security-note">
                <h4>🔒 Nota de Seguridad</h4>
                <p>Si no solicitaste este restablecimiento de contraseña, por favor ignora este mensaje. Tu contraseña actual seguirá siendo válida.</p>
            </div>

            <p>Si tienes alguna duda o problema para acceder, no dudes en contactar al soporte técnico.</p>

            <p>Atentamente,<br>
            <strong>Equipo de EPS SOG</strong></p>
        </div>

        <div class="footer">
            <p><span class="logo">EPS CRIS</span> - Sistema de Gestión de Salud</p>
            <p>Este es un mensaje automático generado por el sistema. Por favor, no respondas a este correo.</p>
            <p>&copy; 2025 EPS Cris. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>