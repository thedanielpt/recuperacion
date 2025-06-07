<?php
    //Sesión iniciada
    session_start();
    //Hace la coneión con la base de datos
    require_once "conexion.php";
    //Variables que dice si ha pedido o no bocadillos
    $pedido_caliente = false;
    $pedido_frio = false;
    $id_bocata = 0;
    //Se guarda en al sesion para si el usuario quiere retirar el pedido
    $id_pedido = $_SESSION['id_pedido'] ?? 0;
    //Para guardar el id del bocata
    $id_bocadillo_caliente = isset($_POST['boton_bocatas_pedir_caliente']) ? $_POST['boton_bocatas_pedir_caliente'] : 0;
    $id_bocadillo_frio = isset($_POST['boton_bocatas_pedir_frio']) ? $_POST['boton_bocatas_pedir_frio'] : 0;
    
    if(!empty($_POST['boton_bocatas_pedir_caliente']) && $pedido_caliente == true){
        //query para eliminar el pedido
        $sql = ('DELETE FROM Pedidos where id = :id_pedido');
        //Parametro de id del pedido
        $param = ['id_pedido' => $id_pedido];
        echo $id_bocata;
        //Prepara la query
        $stmt = $pdo->prepare($sql);
        //Ejecuta la query con los parametros
        $stmt->execute($param);
    }

    if ($id_bocata == 0){
        $pedido_caliente == false;
    }

    if(!empty($_POST['boton_bocatas_pedir_caliente']) && $pedido_caliente == false) {
        //-----BUsca el id mas alto------
        $sql = ('SELECT max(id) as id FROM pedidos');
        //Prepara la query
        $stmt = $pdo->prepare($sql);
        //Ejecuta la consulta
        $stmt->execute();
        //Mete el id mas alto en esta variable
        $id_maximo = $stmt->fetch();
        //Recoge el id mas alto y suma 1 para que no se repita
        $id = $id_maximo['id'] + 1;
        $_SESSION['id_pedido'] = $id;
        //Variabeld e los parametros
        $param = [
        //ID se mete en el array de param
        'id' => $id,
        //el email se mete en el array del usuario
        'id_usuario' => $_SESSION['email'],
        //fecha de hoy dentro del array
        'fecha' => date('Y-m-d'),
        //Estado del pedido
        'estado' => "Preparado",
        //ID del bocata
        'id_bocadillo' => $id_bocadillo_caliente
        ];
        //$sql = ('INSERT INTO pedidos set ');
        $sql_insertar_bocata = ('INSERT INTO pedidos (id, estado, fecha_pedido, id_usuario, id_bocadillo) VALUES (:id, :estado, :fecha, :id_usuario, :id_bocadillo)');
        //Prepara la query para insertar el pedido
        $stmt = $pdo->prepare($sql_insertar_bocata);
        //Ejecuta los parametros y la query
        $stmt->execute($param);
        //Cambia el estadod el boton
        $pedido_caliente = true;
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio_user</title>
    <link rel="stylesheet" href="user.css">
    
</head>
<body>
    <header>
        <div id="div_user">
            <img src="img/usuario.jpg" id="imagen_user">
            <!-- Nombre de la persona que se ha logueado-->
            <h2 id="nombre_user"><?php echo $_SESSION['nombre'] ?></h2>
        </div>

        <nav>
            <ul class="ul_nav">
                <li><a href="inicio_user.php">Inicio</a></li>
                <li><a href="historial_pedidos.php">Historial de pedidos</a></li>
                <li><a href="logout.php">Cerrar sesión</a></li>
            </ul>
        </nav>

        <div id="div_horario">
            <h3>HORARIO</h3>
            <h3>9:00 - 10:00</h3>
        </div>

        <div id="div_logo">
            <img src="img/login-logo.png" id="img_logo">
        </div>
    </header>
        <article> 
            <?php
                //SQL del bocata caliente
                $sql = "SELECT * 
                FROM bocadillos 
                WHERE (estado = 'CALIENTE' OR estado = 'FRIO') AND dia = date_format(now(), '%W')";
                //Prepara la consulta
                $stmt = $pdo->prepare($sql);
                //Ejecuta la consulta
                $stmt->execute();
                //Recoje los valores del array
                $bocatas = $stmt->fetchAll();
                //Variable del id del bocata
                if($bocatas) {
                    foreach($bocatas as $row){
                        $id_bocata = $row['id'];
                        echo '<section>';       
                            echo '<div class="section_div_img">';
                                echo '<img src="img/bocata_inicio.jpg" alt="" class="img_bocatas">';
                            echo '</div>';

                            echo '<div class="div_descripcion">';
                                echo '<h3 class="titulo_bocata">'.$row['nombre'].' (CALIENTE)</h3>';

                                echo '<h3>Descripción del bocata:</h3>';

                                echo '<div class="div_parrafo_bocata">';
                                    echo '<p class="parrafo_bocata">'. $row['descripcion'] .'</p>';
                                echo '</div>';    

                                echo '<h3>Alérgenos:</h3>';
                                //SQL para los alergenos del bocata
                                $sql_bocata = "SELECT GROUP_CONCAT(a.nombre SEPARATOR ', ') AS alergenos
                                FROM alergenos a
                                LEFT JOIN bocadillos_alergenos ba ON a.id = ba.id_alergenos
                                WHERE ba.id_bocadillos = :id_bocata;";
                                //repara la consulta
                                $stmt = $pdo->prepare($sql_bocata);
                                $stmt->execute(['id_bocata' => $id_bocata]);
                                $alergias_bocata = $stmt->fetch();

                                // Ahora accedes al valor correcto:
                                $cadena_alergenos = explode(", ", $alergias_bocata['alergenos']);
                                echo '<ul>';
                                foreach($cadena_alergenos as $alergeno){
                                    echo '<li>'.htmlspecialchars($alergeno).'</li>';
                                }
                                echo '</ul>';       
                            echo '</div>';

                            if($pedido_caliente==false && $row['estado'] == "CALIENTE") {
                                $id_bocadillo_caliente = $row['id'];
                                echo '<div class="div_boton">';
                                    echo '<form action="inicio_user.php" method="post">';
                                        echo '<button type="submit" name="boton_bocatas_pedir_caliente" class="boton_bocatas_pedir" value="'.$row['id'].'">Pedir bocata</button>';
                                    echo '</form>';
                                echo '</div>';
                            } elseif ($pedido_caliente==true && $row['estado'] == "CALIENTE"){
                                echo '<div class="div_boton">';
                                    echo '<form action="inicio_user.php" method="post">';
                                        echo '<button type="submit" name="boton_bocatas_retirar_caliente" class="boton_bocatas_retirar" value="'.$row['id'].'">Retirar bocata</button>';
                                    echo '</form>';
                                echo '</div>';
                            }elseif($pedido_frio==false && $row['estado'] == "FRIO") {
                                $id_bocadillo_frio = $row['id'];
                                echo '<div class="div_boton">';
                                    echo '<form action="inicio_user.php" method="post">';
                                        echo '<button type="submit" name="boton_bocatas_pedir_frio" class="boton_bocatas_pedir" value="'.$row['id'].'">Pedir bocata</button>';
                                    echo '</form>';
                                echo '</div>';
                            } elseif ($pedido_frio==true && $row['estado'] == "FRIO"){
                                echo '<div class="div_boton">';
                                    echo '<form action="inicio_user.php" method="post">';
                                        echo '<button type="submit" name="boton_bocatas_retirar_frio" class="boton_bocatas_retirar" value="'.$row['id'].'">Retirar bocata</button>';
                                    echo '</form>';
                                echo '</div>';
                            }
                        echo '</section>';    
                    }  
                }
            ?>
        </section>
    </article>
</body>
</html>