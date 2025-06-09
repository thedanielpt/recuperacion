<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>login</title>
</head>
<body>

    <?php
    session_start();
    //Conexión al servidor
    require_once "conexion.php";

    //Variables
    $email = isset($_POST['correo']) ? $_POST['correo']:null;
    $passg = isset($_POST['password']) ? $_POST['password']:null;
    $rol = null;
    $errorBusqueda = null;

    //Si no ha sido nada mandado por POST 
    if(!empty($_POST)) {
        //Query que se quiere ejecutar
        $sql = ("SELECT * FROM usuario where email = :email and password = :passg");
        
        //Array que tiene los parametros
        $param['email'] = $email;
        $param['passg'] = $passg;

        //Prepara la query
        $stmt = $pdo->prepare($sql);
        //Ejecuta el array para que cambie los parametros por la contraseña y el email
        $stmt -> execute($param);
        //Coge los atributos del ususario
        $usuario = $stmt->fetch();
        
        //Si usuario existe
        if($usuario) {
            //Coge el rol del usuario
            $rol = $usuario['rol'];
            //Comprueba si su ral es de Alumno
            if ($rol == "Alumno"){
                //Si el usuario esta de altapasa, si no, no pasa
                $sql_alumno = ('SELECT * from alumno where email = :email');
                //Prepara la query de alumno
                $stmt = $pdo->prepare($sql_alumno);
                //Parametro del email
                $param = ['email' => $email];
                //Ejecuta los parametros
                $stmt->execute($param);
                //Coge el array asociativo del alumno encontrado
                $alumno = $stmt->fetch();
                //Recoje el alta del alumno
                $alta = $alumno['alta'];
                if($alta){
                    $_SESSION['nombre'] = $alumno['nombre'];
                    $_SESSION['email'] = $email;
                    header("Location: inicio_user.php");
                    exit();
                } else {
                    $errorBusqueda = "Usuario dado de baja";
                }
            //Comprueba si su ral es de Cocina
            }else if($rol == "Cocina"){
                $_SESSION['nombre'] = $rol;
                header("Location: cocina.html");
                exit();
            //Comprueba si su ral es de Admin    
            }else if($rol == "Admin"){
                $_SESSION['nombre'] = $rol;
                header("Location: admin_usuarios.php");
                exit();
            }
        } else {
            //Muestra el error si no se ha encontrado el usuario
            $errorBusqueda = "Usuario o contraseña mal escrita";
        }
    }
    ?>

    <div id="base-login">

        <div id="base-logo">
            <img src="img/login-logo.png" id="logo">
        </div>

        <form action="" method="post" id="formulario">
            
            <label for="correo">Correo electrónico</label>
            
            <div class="formulario-registros">
                <input type="text" class="input-registro" name="correo" placeholder="ejemplo@elcampico.com" value="<?php echo $email ?>" required>
            </div>
            
            <label for="password">Contraseña</label>
            
            <div class="formulario-registros">
                <input type="password" class="input-registro" name="password" value="<?php echo $passg ?>" required>
            </div>
        
            <button type="submit" name="acceder" id="form-submit">Acceder</button>
            
            <?php echo "<p id='error'>$errorBusqueda</p>"; ?>

            <a href="restablecer_contrasena.html">Recuperar contraseña</a>
        
        </form>
    </div>

</body>
</html>