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

    if(!empty($_POST['crear'])) {
        
        //Query que se va a ejecutar
        $sql = ("INSERT INTO usuario (email, password, rol) VALUES (:email, :password, :rol)");

        //parametros que se quiere pasar
        $param = ['rol' => $rol,
        'password'=> $contrasena,
        'email' => $email];

        //Prepara la consulta
        $stmt = $pdo->prepare($sql);
        
        //ejecuta la consulta
        $stmt->execute($param);

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
            var alergenos = document.getElementById('alergenos');
            var usuario = document.getElementById('usuario').value;

            if (usuario != "Alumno") {
                nombre_apellido.style.display = "none";
                curso.style.display = "none";
                alergenos.style.display = "none";
            } else {
                nombre_apellido.style.display = "block";
                curso.style.display = "block";
                alergenos.style.display = "block";
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

        <?php echo isset($_POST['crear']); ?>

        <div id="div_logo">
            <img src="img/login-logo.png" id="img_logo">
        </div>
    </header>

    <section id="section_crearModificarUser">
        
        <article class="article_crearModificarUser">

            <div id="div_titulo">
                <h2>AÑADIR USUARIO</h2>
            </div>

            <form action="admin_usuarios_anadir.php" method="POST" class="div_formulario">
                
                <div class="formulario_div">
                    <label for="usuario" class="label_crear_modificar">Tipo de usuario</label>
                    <select name="usuario" class="seleccion" onchange="ocultarAtributosAlumno()" id="usuario" required>
                        <option value="Alumno">Alumno</option>
                        <option value="Cocina">Cocina</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>

                
                <div class="formulario_div" id="nombre_apellido">
                    <label for="Nombre_apellidos_usuario" class="label_crear_modificar">Nombre y apellidos del usuario:</label>
                    <div class="div_input_crear">
                        <input type="text" name="Nombre_apellidos_usuario" placeholder="Daniel Pamies Teruel" class="input_crear">
                    </div>
                </div>
    
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
    
                <div class="formulario_div">
                    <label for="email" class="label_crear_modificar">Email:</label>
                    <div class="div_input_crear">
                        <input type="email" name="email" placeholder="daniel@elCampico.com" class="input_crear" required>
                    </div>
                </div>
    
                <!--<div class="formulario_div">
                    <label for="contrasena" class="label_crear_modificar">Contraseña:</label>
                    <div class="div_input_crear">
                        <input type="text" name="contrasena" placeholder="pepito_123" class="input_crear" required>
                    </div>
                    <p><?php //$prueba ?></p>
                </div>-->

                <div class="formulario_div">
                    <label for="repetir_contrasena" class="label_crear_modificar">Repetir la contraseña:</label>
                    <div class="div_input_crear">
                        <input type="text" name="repetir_contrasena" placeholder="pepito_123" class="input_crear" required>
                    </div>
                </div>
        
                <div class="formulario_div" id="alergenos">
                    <label for="alergias" class="label_crear_modificar">Alergias:</label>
                    <select name="alergias" multiple class="seleccion">
                        <option value="">No tiene alergenos</option>
                        <?php
                        //SQL que se va a ejecutar
                            $sql = ('SELECT *
                            FROM alergenos');
                            //Se prepara la sql
                            $stmt = $pdo->prepare($sql);
                            //Ejecuta la consulta
                            $stmt->execute();
                            $alergias = $stmt->fetchAll();
                            foreach($alergias as $alergia){
                                echo '<option value='.$alergia['id'].'>'.$alergia['nombre'].'</option>';
                            }
                        ?>
                    </select>
                </div>
        
                <div id="div_boton_crear">
                    <button type="submit" name="crear" class="boton_crear_modificar" value="1">Crear usuario</button>
                </div>
            </form>
        </article>
    </section>
</body>
</html>