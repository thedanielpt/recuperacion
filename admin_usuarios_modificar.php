<?php 
    //Conexión a la base de datos
    require_once("conexion.php");
    //Inicio de sesión
    session_start();

    //Variable de la url
    $email_url = $_GET['email'];
    //Variable del email que se utilizara para buscar el email que se quiera modificar
    $search_email = isset($_POST['email_modificado']) ? $_POST['email_modificado'] : $email_url;
    //Variables de usuario
    $email = isset($_POST['email_new']) ? $_POST['email_new'] : null;
    $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : null;
    $rol = isset($_POST['usuario']) ? $_POST['usuario'] : null;
    //variables de Alumno
    $nombre = isset ($_POST['nombre_apellidos']) ? $_POST['nombre_apellidos'] : null;
    $curso = isset ($_POST['curso']) ? $_POST['curso'] : null;
    $alta = isset ($_POST['alta']) ? $_POST['alta'] : null;

    if(isset($_POST['modificar'])) {
        //Query para modificar
        $sql = ('UPDATE usuario 
        set email = :email_modificar, password = :contrasena, rol = :rol
        WHERE email = :email');
        //Parametros del usuario
        $param = ['email' => $search_email,
        'email_modificar' => $email, 
        'contrasena' =>$contrasena, 
        'rol' => $rol];
        //Preparar la query de modificar
        $stmt = $pdo->prepare($sql);
        //Ejecuta la query de modificar
        $stmt->execute($param);

        //Si es alumno tambien cambia los parametros de alumno
        if ($rol == "Alumno"){
            //Query para cambiar los valores del alumno
            $sql_alumno = ('UPDATE alumno
            set nombre = :nombre, alta = :alta, curso = :curso
            where email = :email');
            //Parametros del alumno
            $param = ['nombre' => $nombre,
            'alta' => $alta,
            'curso' => $curso,
            'email' => $search_email];
            //Prepara la query del alumno
            $stmt = $pdo->prepare($sql_alumno);
            //Ejecuta la query del alumno
            $stmt->execute($param);
        }
    }

    $sql = ('SELECT * 
    FROM usuario 
    where email = :email');
    //Parametro para el email
    if ($email == null) {
        $param = ['email' => $email_url];
    } else {
        $param = ['email' => $email];
    }
    //Prepara la query
    $stmt = $pdo->prepare($sql);
    //Ejecuta la query con los parametros
    $stmt->execute($param);
    //recoje la variable
    $usuario = $stmt->fetch();
    //Email que se puede cambiar
    $email = $usuario['email'];
    //Para que $email y $search_email se han iguales
    if($search_email != $email) {
        $search_email = $email;
    }
    //Variables que se peuden cambiar
    $contrasena = $usuario['password'];
    //Variable de rol 
    $rol = $usuario['rol'];

    //Si es alumno lo recoje
    if($rol == "Alumno") {
        //query para cojer la información del email del alumno
        $sql_alumno = ('SELECT * from alumno where email = :email');
        //Prepara la sql de alumno
        $stmt = $pdo->prepare($sql_alumno);
        //Ejecuta el sql de alumno
        $stmt->execute($param);
        //Recoje al usuario
        $usuario_alumno = $stmt->fetch();

        //Agregar a las variables ls datos
        $nombre = $usuario_alumno['nombre'];
        $curso = $usuario_alumno['curso'];
        $alta = $usuario_alumno['alta'];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>añadir usuario</title>
    <link rel="stylesheet" href="user_admin.css">
    <script>
        function ocultarAtributosAlumno(){
            var nombre_apellido = document.getElementById('nombre_apellido');
            var curso = document.getElementById('curso');   
            var alta = document.getElementById('alta');
            var usuario = document.getElementById('usuario').value;

            if (usuario != "Alumno") {
                nombre_apellido.style.display = "none";
                curso.style.display = "none";
                alta.style.display = "none";
            } else {
                nombre_apellido.style.display = "block";
                curso.style.display = "block";
                alta.style.display = "block";
            }
        }
    </script>
</head>
<body onload="ocultarAtributosAlumno()">
    <header>
        <div id="div_user">
            <h2 id="nombre_user">Administrador</h2>
         </div>
    
         <nav>
            <ul>
                <li><a href="admin_usuarios.php">Usuarios</a></li>
                <li><a href="logout.php">Cerrar sesión</a></li>
            </ul>
        </nav>

        <div id="div_logo">
            <img src="img/login-logo.png" id="img_logo">
        </div>
    </header>

    <section id="section_crearModificarUser">
        
        <article class="article_crearModificarUser">

            <div id="div_titulo">
                <h2 id="añadir_usuario_titulo">AÑADIR USUARIO</h2>
            </div>

            <form action="admin_usuarios_modificar.php?email=<?php echo $_GET['email']; ?>" method="POST" class="div_formulario">

                <input type="email" name="email_modificado" value="<?php echo $search_email; ?>" hidden>

                <div class="dos_div">
                    <div class="formulario_div">
                        <label for="usuario" class="label_crear_modificar">Tipo de usuario</label>
                        <select name="usuario" class="seleccion" onchange="ocultarAtributosAlumno()" id="usuario" required>
                            <option value="Alumno" <?php if($rol == "Alumno") {echo 'selected';} ?>>Alumno</option>
                            <option value="Cocina" <?php if($rol == "Cocina") {echo 'selected';} ?>>Cocina</option>
                            <option value="Admin" <?php if($rol == "Admin") {echo 'selected';} ?>>Admin</option>
                        </select>
                    </div>

                    <div class="formulario_div">
                        <label for="email_new" class="label_crear_modificar">Email:</label>
                        <div class="div_input_crear">
                            <input type="email" name="email_new" value="<?php echo $email; ?>" class="input_crear" required>
                        </div>
                    </div>
                </div>
                
                <div class="dos_div">
                    <div class="formulario_div">
                        <label for="contrasena" class="label_crear_modificar">Contraseña:</label>
                        <div class="div_input_crear">
                            <input type="text" name="contrasena" placeholder="pepito_123" class="input_crear" value="<?php echo $contrasena;?>" required>
                        </div>
                    </div>

                    <div class="formulario_div" id="nombre_apellido">
                        <label for="nombre_apellidos" class="label_crear_modificar">Nombre y apellidos del usuario:</label>
                        <div class="div_input_crear">
                            <input type="text" name="nombre_apellidos" placeholder="Daniel Pamies Teruel" value="<?php echo $nombre; ?>" class="input_crear">
                        </div>
                    </div>
        
                </div>
                
                <div class="dos_div">

                    <div class="formulario_div" id="curso">
                        <label for="curso" class="label_crear_modificar">Curso:</label>
                        <select name="curso" class="seleccion">
                            <option value="1ºESO" <?php if($curso == "1ºESO"){echo 'selected';} ?>>1ºESO</option>
                            <option value="2ºESO" <?php if($curso == "2ºESO"){echo 'selected';} ?>>2ºESO</option>
                            <option value="3ºESO" <?php if($curso == "3ºESO"){echo 'selected';} ?>>3ºESO</option>
                            <option value="4ºESO" <?php if($curso == "4ºESO"){echo 'selected';} ?>>4ºESO</option>
                            <option value="Grado Medio 1º año" <?php if($curso == "Grado Medio 1º año"){echo 'selected';} ?>>Grado Medio 1º año</option>
                            <option value="Grado Medio 2º año" <?php if($curso == "Grado Medio 2º año"){echo 'selected';} ?>>Grado Medio 2º año</option>
                        </select>
                    </div>

                    <div class="formulario_div" id="alta">
                        <label for="alta" class="label_crear_modificar">Dado de alta o baja:</label>
                        <select name="alta" class="seleccion">
                            <option value="true" <?php if($alta == "true"){echo 'selected';} ?> >Alta</option>
                            <option value="false" <?php if($alta == "false"){echo 'selected';} ?>>Baja</option>
                        </select>
                    </div>
                </div>    

                <div id="div_boton_crear">
                    <button type="submit" name="modificar" class="boton_crear_modificar">Modificar usuario</button>
                </div>
            </form>
        </article>
    </section>
</body>
</html>