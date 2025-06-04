<?php

$mensaje_para_usuario = '';
$tipo_mensaje = 'error';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_ingresado = $_POST['email_del_formulario'];

    if (filter_var($email_ingresado, FILTER_VALIDATE_EMAIL)) {
        $mensaje_para_usuario = "¡Genial! El correo se envió un correo a " . htmlspecialchars($email_ingresado) . ". Revisa tu bandeja de entrada .";
        $tipo_mensaje = 'exito';

    } else {
        $mensaje_para_usuario = "Error: El correo electrónico que ingresaste no es válido. Por favor, verifique el formato.";
        $tipo_mensaje = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="contrasena_olvidada.css">
    <title>Recuperar Contraseña</title>
</head>
<body>
    <div class="caja">
        <img class="candado" src="img/candado.jpg" alt="candado">
        
        <h3>¿Olvidaste tu contraseña?</h3>
        <h5>Ingresa tu correo electrónico para que podamos ayudarte a recuperarla.</h5>
        
        <form action="" method="POST">
            <input class="rellenar" type="text" name="email_del_formulario" value="" placeholder="Tu dirección de correo">
            <input class="button" type="submit" name="boton_enviar" value="Enviar">
        </form>

        <?php
        if (!empty($mensaje_para_usuario)) {
            $clase_mensaje = ($tipo_mensaje == 'correcto') ? 'mensaje-exito' : 'mensaje-error';
            
            echo "<p class='" . $clase_mensaje . "'>" . $mensaje_para_usuario . "</p>";
        }
        ?>
    </div>
</body>
</html>