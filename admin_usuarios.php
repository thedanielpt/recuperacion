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

    //Variables de Paginado
    $registros = isset($_POST['registros']) ? $_POST['registros'] : 0;
    $numero_pagina = isset($_POST['numero_pagina']) ? $_POST['numero_pagina']:1;

    //Comprobar los registros para el paginado
    if(($registros / 10) != $numero_pagina -1) {
        $numero_pagina = ($registros /10) -1;
    }

    if(isset($_POST['subir'])){
        $registros +=  10;
        $numero_pagina += 1;
    }

    if(isset($_POST['bajar'])) {
        $registros -= 10;
        $numero_pagina -= 1;

        if ($registros < 0) {
            $registros= 0;
            $numero_pagina = 1;
        }
    }

    if(isset($_POST["eliminar_boton"])) {
        
        //Query utilizada
        $sql = ("DELETE FROM usuario 
        where email = :email");
        
        //Prepara la query
        $stmt = $pdo->prepare($sql);

        //ejecuta la query con los parametros
        if($usuarios != null) {
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
    <title>admin_usuarios</title>
    <link rel="stylesheet" href="user_admin.css">
</head>
<body>
    <header>
        <div id="div_user">
            <h2 id="nombre_user">Administrador</h2>
         </div>
    
        <nav>
            <a href="admin_usuarios.php">Usuarios</a>
            <a href="logout.php">Cerrar sesión</a>
        </nav>  
        

        <div id="div_logo">
            <img src="img/login-logo.png" id="img_logo">
        </div>
    </header>

    <section class="section_usuario">
        <div id="div_titulo">
            <h2>GESTION DE USUARIOS</h2>
        </div>

        <form action="admin_usuarios.php" method="post">
            
            <div id="div_enlaces_gestion_usuario">    

                <div class="filtros">

                    <label for="filtrar_rol">Filtrar emial:</label>
                    <select name="filtrar_rol">
                        <option value="" <?php if ($filtro_rol == "") echo 'selected'; ?>>Todos</option>
                        <option value="Alumno" <?php if ($filtro_rol == "Alumno") echo 'selected'; ?>>Alumno</option>
                        <option value="Cocina" <?php if ($filtro_rol == "Cocina") echo 'selected'; ?>>Cocina</option>
                        <option value="Admin" <?php if ($filtro_rol == "Admin") echo 'selected'; ?>>Admin</option>
                    </select> 

                    <label for="email">Filtrar emial:</label>
                    <input type="text" name="email" value='<?php echo $filtro_email; ?>'> 
                    
                </div>

                <div class="div_enlaces">
                    <button type="submit" name="filtrar" id="filtrar">Filtrar</button>
                </div>

                <div class="div_enlaces">
                    <button type="submit" id="eliminar_usuarios" name="eliminar_boton">Eliminar</button>  
                </div>
                <p><?php echo $errorArrayVacio ?></p> 
        
                <div class="div_enlaces">
                    <a href="admin_usuarios_anadir.php" id="anadir_enlace">Añadir</a>
                </div>
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
                            //Query que quiero ejcutar para buscar
                            $sql = ('SELECT u.email as email, u.password as password, rol, alta, nombre, curso
                            FROM usuario u
                            LEFT JOIN alumno a ON u.email = a.email
                            where true ');

                            //TODO terminar de hacer el cuento de registros
                            //Query para limitar el maximo de paginas
                            $sql_paginacion = ('SELECT count(*) as total_registros
                            FROM usuario');

                            //Prepara la query de paginado
                            $stmt_paginacion = $pdo->prepare($sql_paginacion);

                            //Ejecuta la query que cuenta los registros
                            $stmt_paginacion->execute();
                            $total = $stmt_paginacion->fetch();
                            $total_registros = $total['total_registros'];

                            $param = null;

                            if($total_registros < (10 + $registros)) {
                                while($registros > $total_registros) {
                                    $registros -= 10;
                                    $numero_pagina -=1;
                                }
                            }
                            
                            if (isset($_POST['filtrar']) || isset($_POST['bajar']) || isset($_POST['subir'])) {

                                if(!empty($filtro_rol)){
                                    $sql .= " AND u.rol = :rol";
                                    $param = ['rol' => $filtro_rol];
                                    $filtro_email = null;
                                }
                            }

                            if (isset($_POST['filtrar'])) {
                                if (!empty($filtro_email) && $filtro_rol == ""){
                                    $sql .= " AND u.email = :email";
                                    $param = ['email' => $filtro_email];
                                    $registros = 0;
                                    $numero_pagina = 1;
                                }
                            }
                            $sql .= ' LIMIT ' . ($registros) . ', 10;';
                            //Prepara la ejecucón de la query que muestra a los usuaiors
                            $stmt = $pdo->prepare($sql);
                            //Ejecuta la query que muestra a los usuaiors y si hay un error ejecuta la quey sin los filtros
                            try {
                                $stmt->execute($param);
                            } catch (PDOException $e) {
                                //Query para buscar usuarios
                                $sql = ('SELECT u.email as email, u.password as password, rol, alta, nombre, curso
                                FROM usuario u
                                LEFT JOIN alumno a ON u.email = a.email
                                where true ');
                                //Paginación
                                $sql .= ' LIMIT ' . ($registros) . ', 10;';
                                //Prepara la consulta de buscar usuario
                                $stmt = $pdo->prepare($sql);
                                //Ejecuta la consulta
                                $stmt->execute();
                                $filtro_email = null;
                            }
                            
                            //Pasar los registros a la variable que muestra a los usuaiors
                            $usuarios = $stmt->fetchAll();
                            
                            try {
                                foreach($usuarios as $usuario){
                                    echo '<tr>';
                                    if($usuario['rol'] == "Alumno") {
                                        echo '<td>'.$usuario['email'].'</td>';
                                        echo '<td>'.$usuario['password'].'</td>';
                                        echo '<td>'.$usuario['nombre'].'</td>';
                                        echo '<td>'.$usuario['curso'].'</td>';
                                        echo '<td>'.$usuario['rol'].'</td>';
                                        echo '<td>'.$usuario['alta'].'</td>';
                                        echo '<td><a href="admin_usuarios_modificar.php?email='.$usuario['email'].'" class="modificar">Modificar</a></td>';
                                        echo '<td><input type="checkbox" name="eliminar_usuarios[]" value='.$usuario['email'].'></td>';
                                    } else {
                                        echo '<td>'.$usuario['email'].'</td>';
                                        echo '<td>'.$usuario['password'].'</td>';
                                        echo '<td>  </td>';
                                        echo '<td>  </td>';
                                        echo '<td>'.$usuario['rol'].'</td>';
                                        echo '<td>  </td>';
                                         echo '<td><a href="admin_usuarios_modificar.php?email='.$usuario['email'].'" class="modificar">Modificar</a></td>';
                                        echo '<td><input type="checkbox" name="eliminar_usuarios[]" value='.$usuario['email'].'></td>';
                                    }
                                    echo '</tr>';
                                }
                            } catch (PDOException $e) {
                                echo "Usuario no encontrado";
                            }
                            
                        ?>
                    </tbody>
                </table>
            </div>
            <div id="div_paginado">
                <div id="div_bajar">
                    <button type="submit" name="bajar" id="boton_bajar_subir"><</button>
                </div>
                <div id="div_registro">
                    <input type="number" name="registros" id="input_registros" value="<?php echo $registros; ?>" readonly hidden>
                </div>
                
                <div id="div_pagina">
                    <label for="numero_pagina">Pagina: </label>
                    <input type="number" name="numero_pagina" id="input_pagina" value="<?php echo $numero_pagina; ?>" readonly>
                </div>
                
                <div id="div_subir">
                    <button type="submit" name="subir" id="boton_bajar_subir">></button>
                </div>
            </div>
        </form>    
    </section>
</body>
</html>