<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Autenticación</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2 style="color: #333333; text-align: center;">Código de Verificación</h2>
        <p style="color: #666666; font-size: 16px; line-height: 1.5;">Hola,</p>
        <p style="color: #666666; font-size: 16px; line-height: 1.5;">Estás intentando iniciar sesión. Por favor ingresa el siguiente código para continuar:</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <span style="font-size: 32px; font-weight: bold; color: #4F46E5; letter-spacing: 5px; background-color: #EEF2FF; padding: 10px 20px; border-radius: 8px;">
                {{ $code }}
            </span>
        </div>
        
        <p style="color: #666666; font-size: 14px; line-height: 1.5;">Este código expirará en 10 minutos.</p>
        <p style="color: #666666; font-size: 14px; line-height: 1.5;">Si no solicitaste este código, puedes ignorar este correo.</p>
    </div>
</body>
</html>
