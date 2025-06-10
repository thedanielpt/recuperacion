<?php
    //Conexión a la base de datos
    require_once("conexion.php");
    //Crear una sesión
    session_start();
    //Variables para las querys
    $email = isset($_POST['email']) ? $_POST['email'] : null;
    $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : null;
    $contrasena_repetir = isset($_POST['repetir_contrasena']) ? $_POST['repetir_contrasena'] : null; 
    $rol = isset($_POST['usuario']) ? $_POST['usuario'] : null;
    $nombre = isset($_POST['nombre_apellidos']) ? $_POST['nombre_apellidos'] : null;
    $alta = isset($_POST['alta']) ? $_POST['alta'] : null;
    $curso = isset($_POST['curso']) ? $_POST['curso'] : null;
    $alergias = isset($_POST['alergias']) ? $_POST['alergias'] : null;
    $usuarioRepetidoEnLaBD = null;

    try {
        if(isset($_POST['crear'])) {
        
            //Query que se va a ejecutar
            $sql = ("INSERT INTO usuario (email, password, rol) VALUES (:email, :password, :rol)");

            //parametros que se quiere pasar
            $param = ['rol' => $rol,
            'password'=> $contrasena,
            'email' => $email];

            //Prepara la consulta
            $stmt = $pdo->prepare($sql);
            
            //ejecuta la consulta con los parametros
            $stmt->execute($param);

            //Si el rol es Alumno se mete aquí para crear en la tabla alumno un alumno
            if($rol == "Alumno"){
                //Query que se va a ejecutar
                $sql_alumo = ("INSERT INTO alumno (email, nombre, alta, curso) VALUES (:email, :nombre, :alta, :curso)");

                //parametros para que se ejecute
                $param_alumno = [ 'email' => $email,
                'nombre' => $nombre,
                'alta' => $alta,
                'curso' => $curso
                ];

                //Prepara la query
                $stmt = $pdo->prepare($sql_alumo);

                //Ejecuta la query con los parametros
                $stmt->execute($param_alumno);
            }
        }
    } catch ( PDOException $e) {
        $usuarioRepetidoEnLaBD = "No se pueden repetir el email";
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
<body>
    <header>
        <div id="div_user">
            <h2 id="nombre_user">Administrador</h2>
         </div>
    
         <nav>
            <ul>
                <li><a href="admin_usuarios.php">Usuarios</a></li>
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

            <form action="admin_usuarios_anadir.php" method="POST" class="div_formulario">
                <div class="dos_div">
                    <div class="formulario_div">
                        <label for="usuario" class="label_crear_modificar">Tipo de usuario</label>
                        <select name="usuario" class="seleccion" onchange="ocultarAtributosAlumno()" id="usuario" required>
                            <option value="Alumno">Alumno</option>
                            <option value="Cocina">Cocina</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>

                    <div class="formulario_div">
                        <label for="email" class="label_crear_modificar">Email:</label>
                        <div class="div_input_crear">
                            <input type="email" name="email" placeholder="daniel@elCampico.com" class="input_crear" required>
                        </div>
                        <p><?php echo $usuarioRepetidoEnLaBD; ?></p>
                    </div>
                    
                </div>

                
                <div class="dos_div">
                    <div class="formulario_div">
                        <label for="contrasena" class="label_crear_modificar">Contraseña:</label>
                        <div class="div_input_crear">
                            <input type="password" name="contrasena" placeholder="pepito_123" class="input_crear" required>
                        </div>
                    </div>

                    <div class="formulario_div" id="nombre_apellido">
                        <label for="nombre_apellidos" class="label_crear_modificar">Nombre y apellidos del usuario:</label>
                        <div class="div_input_crear">
                            <input type="text" name="nombre_apellidos" placeholder="Daniel Pamies Teruel" class="input_crear">
                        </div>
                    </div>
        
                </div>
                
                <div class="dos_div">

                    <div class="formulario_div" id="curso">
                        <label for="curso" class="label_crear_modificar">Curso:</label>
                        <select name="curso" class="seleccion">
                            <option value="1ºESO">1ºESO</option>
                            <option value="2ºESO">2ºESO</option>
                            <option value="3ºESO">3ºESO</option>
                            <option value="4ºESO">4ºESO</option>
                            <option value="Grado Medio 1º año">Grado Medio 1º año</option>
                            <option value="Grado Medio 2º año">Grado Medio 2º año</option>
                        </select>
                    </div>

                    <div class="formulario_div" id="alta">
                        <label for="alta" class="label_crear_modificar">Dado de alta o baja:</label>
                        <select name="alta" class="seleccion">
                            <option value="true" default>Alta</option>
                            <option value="false">Baja</option>
                        </select>
                    </div>
                </div>    

                <div id="div_boton_crear">
                        <button type="submit" name="crear" class="boton_crear_modificar">Crear usuario</button>
                </div>
            </form>
        </article>
    </section>
</body>
</html>