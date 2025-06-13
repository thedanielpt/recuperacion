<?php 
    //Conexión a la base de datos
    require_once "conexion.php";
    //Inicio de sesión
    session_start();
    //Variables utilizadas
    $mes = isset($_POST['mes']) ? $_POST['mes'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="user.css">
    <title>Historial de pedidos</title>
</head>
<body>
   <header>
    <div id="div_user">
        <img src="img/usuario.jpg" id="imagen_user">
        <h2 id="nombre_user"><?php echo $_SESSION['nombre'] ?></h2>
     </div>

    <nav>
        <ul class="ul_nav">
            <li><a href="inicio_user.php">Inicio</a></li>
            <li><a href="historial_pedidos.php">Historial de pedidos</a></li>
            <li><a href="logout.php">Cerrar sesión</a></li>
        </ul>
    </nav>

    <div id="div_horario"></div>

    <div id="div_logo">
        <img src="img/login-logo.png" id="img_logo">
    </div>
</header>

    <h1>Historial de bocadillos</h1>
    <?php 
        echo '<div class="formulario">';
            echo '<form method="post">';
                echo '<label for="mes">Selecciona un mes</label>'; 
                echo '<select id="mes" name="mes">';
                    echo '<option value="null" selected>Mes</option>';
                    echo '<option value="1">Enero</option>';
                    echo '<option value="2">Febrero</option>';
                    echo '<option value="3">Marzo</option>';
                    echo '<option value="4">Abril</option>';
                    echo '<option value="5">Mayo</option>';
                    echo '<option value="6">Junio</option>';
                    echo '<option value="7">Julio</option>';
                    echo '<option value="8">Agosto</option>';
                    echo '<option value="9">Septiembre</option>';
                    echo '<option value="10">Octubre</option>';
                    echo '<option value="11">Noviembre</option>';
                    echo '<option value="12">Diciembre</option>';
                echo '</select>';
                echo '<button type="submit" name="filtro">Filtrar</button>';
            echo '</form>';
        echo '</div>';
        
        echo '<table>';
            echo '<thead>';
                echo '<tr>';
                    echo '<th>Bocadillo</th>';
                    echo '<th>Tipo de bocadillo</th>';
                    echo '<th>Fecha</th>';
                    echo '<th>Precio total</th>';
                    echo '<th>temperatura</th>';
                echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
                $sql = "SELECT b.nombre as nombre_bocata, b.temperatura as temperatura_bocata, p.fecha_pedido as fecha_pedido, 
                b.coste as coste, p.estado as estado_pedido
                FROM pedidos p, bocadillos b
                WHERE p.id_bocadillo = b.id AND p.id_usuario = :usuario ";
                //Parametros
                $param = ['usuario' => $_SESSION['email']];

                if(isset($_POST['filtro'])) {
                    $sql .= " AND MONTH(p.fecha_pedido) = :mes";
                    $param['mes']=$mes;
                }
                //Prepara la consulta
                $stmt = $pdo->prepare($sql);
                //ejecuta la consulta
                $stmt->execute($param);
                //Recoge el los registros encontrados
                $pedidos = $stmt->fetchAll();
                //Bucle que muestra los pedidos
                if ($pedidos == null) {
                    
                } else {
                    foreach($pedidos as $pedido) {
                        echo '<tr>';
                            echo '<td>'.$pedido['nombre_bocata'].'</td>';
                            echo '<td>'.$pedido['temperatura_bocata'].'</td>';
                            echo '<td>'.$pedido['fecha_pedido'].'</td>';
                            echo '<td>'.$pedido['coste'].'</td>';
                            if ($pedido['estado_pedido'] == "PREPARADO") {
                                echo '<td>';
                                echo '<div class="preparado">'.$pedido['estado_pedido'].'</div>';
                                echo '</td>';
                            } elseif($pedido['estado_pedido'] == "RETIRADO") {
                                echo '<td>';
                                echo '<div class="retirado">'.$pedido['estado_pedido'].'</div>';
                                echo '</td>';
                            }
                        echo '</tr>';
                    }
                }
                
           echo '</tbody>';
            echo '</tbody>';
            echo '</table>';
        echo '</table>';    
    ?>    
</body>
</html>