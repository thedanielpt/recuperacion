<?php 
    //inicio de sesión
    session_start();
    //Conexion a la base de datos
    require_once("conexion.php");

    //Variables utilizadas
    $usuarios = isset($_POST['eliminar_usuarios']) ? $_POST['eliminar_usuarios'] : null;
    $filtro_email = isset($_POST['email']) ? $_POST['email'] : null;
    $filtro_rol = isset($_POST['filtrar_rol']) ? $_POST['filtrar_rol'] : null;
    $errorArrayVacio = null;

    if(isset($_POST["eliminar_boton"])) {
        
        //Query utilizada
        $sql = ("DELETE FROM usuario 
        where email = :email");
        
        //Prepara la query
        $stmt = $pdo->prepare($sql);

        //ejecuta la query con los parametros
        if(is_iterable($usuarios)) {
            foreach($usuarios as $usuario) {
                $stmt->execute(['email' => $usuario]);
            }
        } else {
            $errorArrayVacio = "Usuario no seleccionado";
        }
        
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar_usuario</title>
    <link rel="stylesheet" href="user_admin.css">
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

    <section class="section_usuario">
        <div id="div_titulo">
            <h2>Eliminar usuario</h2>
        </div>

        <form action="admin_usuarios_eliminar.php" method="post">

            <div id="div_enlaces_gestion_usuario">

                <div class="filtros">
                    <label for="filtrar_rol">Filtrar emial:</label>
                    <select name="filtrar_rol">
                        <option value="">Todos</option>
                        <option value="Alumno">Alumno</option>
                        <option value="Cocina">Cocina</option>
                        <option value="Admin">Admin</option>
                    </select> 
                </div>

                <div class="filtros">
                    <label for="email">Filtrar emial:</label>
                    <input type="text" name="email"> 
                </div>

                <div class="div_enlaces">
                    <button type="submit" name="filtrar" id="filtrar">Filtrar</button>  
                </div>
                
                <div class="div_enlaces">
                    <button type="submit" id="eliminar_usuarios" name="eliminar_boton">Eliminar</button>  
                </div>
                <p><?php echo $errorArrayVacio ?></p> 
            </div>
            <div id="div_tabla">
                <table border="1" id="tabla_usuarios">
                    <thead>
                        <tr>
                            <th>Gmail</th>
                            <th>Contraseña</th>
                            <th>Nombre y apellidos</th>
                            <th>Curso</th>
                            <th>rol</th>
                            <th>Alta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            //Query que quiero eejcutar
                            $sql = ('SELECT u.email as email, u.password as password, rol, alta, nombre, curso
                            FROM usuario u
                            LEFT JOIN alumno a ON u.email = a.email
                            where true ');

                            $param = null;
                            if (isset($_POST['filtrar'])) {
                                if (isset($filtro_email) && !empty($filtro_email)){
                                    $sql .= " AND u.email = :email";
                                    $param = ['email' => $filtro_email];
                                }

                                if(isset($filtro_rol) && !empty($filtro_rol)){
                                    $sql .= " AND u.rol = :rol";
                                    $param = ['rol' => $filtro_rol];
                                }
                            }
                            
                            //Prepara la ejecucón de la query
                            $stmt = $pdo->prepare($sql);
                            //Ejecuta la query
                            $stmt->execute($param);
                            //Pasar los registros a la variable
                            $usuarios = $stmt->fetchAll();
                            foreach($usuarios as $usuario){
                                echo '<tr>';
                                if($usuario['rol'] == "Alumno") {
                                    echo '<td>'.$usuario['email'].'</td>';
                                    echo '<td>'.$usuario['password'].'</td>';
                                    echo '<td>'.$usuario['nombre'].'</td>';
                                    echo '<td>'.$usuario['curso'].'</td>';
                                    echo '<td>'.$usuario['rol'].'</td>';
                                    echo '<td>'.$usuario['alta'].'</td>';
                                    echo '<td><input type="checkbox" name="eliminar_usuarios[]" value='.$usuario['email'].'></td>';
                                } else {
                                    echo '<td>'.$usuario['email'].'</td>';
                                    echo '<td>'.$usuario['password'].'</td>';
                                    echo '<td>  </td>';
                                    echo '<td>  </td>';
                                    echo '<td>'.$usuario['rol'].'</td>';
                                    echo '<td>  </td>';
                                    echo '<td><input type="checkbox" name="eliminar_usuarios[]" value='.$usuario['email'].'></td>';
                                }
                                echo '</tr>';
                            }
                        ?> 
                    </tbody>
                </table>
            </div>
        </form>
    </section>
</body>
</html>