<?php
    //Hace la coneión con la base de datos
    require_once "conexion.php";
    //Sesión iniciada
    session_start();
    //Variables que dice si ha pedido o no bocadillos
    //TODO estas variables siempre se poenn en false, tiene que guardarse el valor cambiado antes de una recarga
    $pedido_caliente=false;
    $pedido_frio=false;
    $id_bocata = 0;
    //Para guardar el id del bocata
    $id_bocadillo_caliente = isset($_POST['boton_bocatas_pedir_caliente']) ? $_POST['boton_bocatas_pedir_caliente'] : 0;
    $id_bocadillo_frio = isset($_POST['boton_bocatas_pedir_frio']) ? $_POST['boton_bocatas_pedir_frio'] : 0;
    
    if(isset($_POST['boton_bocatas_retirar_caliente']) || isset($_POST['boton_bocatas_retirar_frio'])){
        //query para eliminar el pedido
        $sql = ('DELETE FROM Pedidos 
        where id_bocadillo = :id_bocadillo and id_usuario = :id_usuario and fecha_pedido = :fecha');
        //Parametro de id del pedido
        if(isset($_POST['boton_bocatas_retirar_caliente'])) {
            $id_bocata = $_POST['boton_bocatas_retirar_caliente'];
        } elseif(isset($_POST['boton_bocatas_retirar_frio'])) {
            $id_bocata = $_POST['boton_bocatas_retirar_frio'];
        }    

        //Parametros
        $param = ['id_bocadillo' => $id_bocata,
        'id_usuario' => $_SESSION['email'],
        'fecha' => date('Y-m-d')];
        //Prepara la query
        $stmt = $pdo->prepare($sql);
        //Ejecuta la query con los parametros
        $stmt->execute($param);
        //Cambia los botones
        $pedido_caliente=false;
        $pedido_frio=false;
    }

    if($pedido_caliente == false && $pedido_frio == false) {
        if(!empty($_POST['boton_bocatas_pedir_caliente'])) {
            //-----BUsca el id mas alto------
            $sql = ('SELECT max(id) as id FROM pedidos');
            //Prepara la query
            $stmt = $pdo->prepare($sql);
            //Ejecuta la consulta
            $stmt->execute();
            //Mete el id mas alto en esta variable
            $id_maximo = $stmt->fetch();
            //Recoge el id mas alto y suma 1 para que no se repita
            //TODO hacer autoIncremental
            $id = $id_maximo['id'] + 1;
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
            $pedido_caliente=true;
        }

        if(!empty($_POST['boton_bocatas_pedir_frio'])) {
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
            'id_bocadillo' => $id_bocadillo_frio
            ];
            //$sql = ('INSERT INTO pedidos set ');
            $sql_insertar_bocata = ('INSERT INTO pedidos (id, estado, fecha_pedido, id_usuario, id_bocadillo) VALUES (:id, :estado, :fecha, :id_usuario, :id_bocadillo)');
            //Prepara la query para insertar el pedido
            $stmt = $pdo->prepare($sql_insertar_bocata);
            //Ejecuta los parametros y la query
            $stmt->execute($param);
            //Cambia el estadod el boton
            $pedido_frio=true;
        }
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
                WHERE (temperatura = 'CALIENTE' OR temperatura = 'FRIO') AND dia = date_format(now(), '%W')";
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

                                if($row['temperatura'] == "CALIENTE") {
                                    echo '<h3 class="titulo_bocata">'.$row['nombre'].' (CALIENTE)</h3>';
                                } else {
                                    echo '<h3 class="titulo_bocata">'.$row['nombre'].' (FRIO)</h3>';
                                }
                                
                                echo '<h3>Descripción del bocata:</h3>';

                                echo '<div class="div_parrafo_bocata">';
                                    echo '<p class="parrafo_bocata">'. $row['descripcion'] .'</p>';
                                echo '</div>';    

                                echo '<h3>Alérgenos:</h3>';
                                    //SQL para los alergenos del bocata
                                    $sql_bocata = "SELECT a.nombre AS nombre
                                    FROM alergenos a
                                    LEFT JOIN bocadillos_alergenos ba ON a.id = ba.id_alergenos
                                    WHERE ba.id_bocadillos = :id_bocata;";
                                    //repara la consulta
                                    $stmt = $pdo->prepare($sql_bocata);
                                    $stmt->execute(['id_bocata' => $id_bocata]);
                                    $alergias_bocata = $stmt->fetchAll();
                                    if ($alergias_bocata != null) {
                                        echo '<ul>';
                                        foreach($alergias_bocata as $alergeno){
                                            echo '<li>'.$alergeno['nombre'].'</li>';
                                        }
                                        echo '</ul>';   
                                    }
                            echo '</div>';
                            //SQL para comprobar si tiene un pedido hoy
                            $sql_comprobar_pedidos = "SELECT b.temperatura as temperatura_bocadillo, p.id_bocadillo as id_bocadillo
                            FROM pedidos p, bocadillos b
                            WHERE p.id_bocadillo = b.id and id_usuario = :id_usuario and fecha_pedido = :fecha";
                            //Parametros para hacer el sql
                            $param = ['id_usuario' => $_SESSION['email'],
                            'fecha' => date('Y-m-d')];
                            //Prepara el sql
                            $stmt = $pdo->prepare($sql_comprobar_pedidos);
                            //Ejecuta la consulta
                            $stmt->execute($param);
                            //Coge los parametros en una variable
                            $comprobar_pedido = $stmt->fetch();
                            //Si no existe se combierte en null
                            if ($comprobar_pedido == false) {
                                $temperatura_bocadillo = null;
                            } else {
                                $temperatura_bocadillo = $comprobar_pedido['temperatura_bocadillo'];
                            }
                            //Si no ha pedido un bocadillo le aparece el boton de pedir bocata caliente
                            if($pedido_caliente == false && $row['temperatura'] == "CALIENTE") {
                                //Recoje el id del bocadillo
                                $id_bocadillo_caliente = $row['id'];
                                //Boton de pedir bocata caliente
                                echo '<div class="div_boton">';
                                    echo '<form action="inicio_user.php" method="post">';
                                        echo '<button type="submit" name="boton_bocatas_pedir_caliente" class="boton_bocatas_pedir" value="'.$row['id'].'">Pedir bocata</button>';
                                    echo '</form>';
                                echo '</div>';
                            //Si a pedido un bocadillo o ya lo pidio antes le aparece el boton de retirar    
                            } elseif (($pedido_caliente == true && $row['temperatura'] == "CALIENTE")){
                                //Comprueba si se pidio un bocata anteriormente
                                if($temperatura_bocadillo == "CALIENTE") {
                                    $id_bocadillo_caliente = $comprobar_pedido['id_bocadillo'];
                                    $pedido_caliente = true;
                                }
                                //Boton de retirar bocata caliente
                                echo '<div class="div_boton">';
                                    echo '<form action="inicio_user.php" method="post">';
                                        echo '<button type="submit" name="boton_bocatas_retirar_caliente" class="boton_bocatas_retirar" value="'.$id_bocadillo_caliente.'">Retirar bocata</button>';
                                    echo '</form>';
                                echo '</div>';
                            //Si no ha pedido un bocadillo le aparece el boton de pedir bocata frio
                            } elseif($pedido_frio == false && $row['temperatura'] == "FRIO") {
                                //id del bocata frio
                                $id_bocadillo_frio = $row['id'];
                                //boton para pedir un bocata frio
                                echo '<div class="div_boton">';
                                    echo '<form action="inicio_user.php" method="post">';
                                        echo '<button type="submit" name="boton_bocatas_pedir_frio" class="boton_bocatas_pedir" value="'.$row['id'].'">Pedir bocata</button>';
                                    echo '</form>';
                                echo '</div>';
                            //Si a pedido un bocadillo o ya lo pidio antes le aparece el boton de retirar 
                            } elseif (($pedido_frio == true && $row['temperatura'] == "FRIO")){
                                //Comprueba si se pidio un bocata anteriormente
                                if($temperatura_bocadillo == "FRIO") {
                                    $id_bocadillo_frio = $comprobar_pedido['id_bocadillo'];
                                    $pedido_frio = true;
                                }
                                //Boton de retirar
                                echo '<div class="div_boton">';
                                    echo '<form action="inicio_user.php" method="post">';
                                        echo '<button type="submit" name="boton_bocatas_retirar_frio" class="boton_bocatas_retirar" value="'.$id_bocadillo_frio.'">Retirar bocata</button>';
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