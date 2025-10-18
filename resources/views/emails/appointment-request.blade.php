<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Solicitud de Cita Médica</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #0c82eaff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 0 0 5px 5px;
        }
        .appointment-details {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #0c82eaff;
        }
        .button {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
        .button.reject {
            background-color: #dc3545;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>EPS SOG</h1>
        <h2>Nueva Solicitud de Cita Médica</h2>
    </div>

    <div class="content">
        <p>Estimado Dr. {{ $doctor->nombres }} {{ $doctor->apellidos }},</p>

        <p>Ha recibido una nueva solicitud de cita médica que requiere su aprobación o rechazo.</p>

        <div class="appointment-details">
            <h3>Detalles de la Cita:</h3>
            <p><strong>Paciente:</strong> {{ $paciente->nombres }} {{ $paciente->apellidos }}</p>
            <p><strong>Fecha y Hora:</strong> {{ $cita->fechaHora->format('d/m/Y H:i') }}</p>
            <p><strong>Consultorio:</strong> {{ $consultorio->nombre }}</p>
            <p><strong>Estado:</strong> {{ $cita->estado }}</p>
            <p><strong>Novedad:</strong> {{ $cita->novedad }}</p>
        </div>

        <p>Por favor, revise la solicitud y tome la acción correspondiente:</p>

        <div style="text-align: center; margin: 20px 0;">
            <a href="#" class="button">Aprobar Cita</a>
            <a href="#" class="button reject">Rechazar Cita</a>
        </div>

        <p>Si tiene alguna pregunta o necesita más información, no dude en contactar al paciente.</p>

        <p>Atentamente,<br>
        Sistema de Gestión EPS SOG</p>
    </div>

    <div class="footer">
        <p>Este es un mensaje automático. Por favor, no responda a este correo.</p>
        <p>EPS SOG - Sistema de Gestión de Citas Médicas</p>
    </div>
</body>
</html>