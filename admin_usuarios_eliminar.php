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
                <li><a href="admin_usuarios.html">Usuarios</a></li>
                <li><a href="admin_cocina.html">Bocatas</a></li>
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

        <div id="div_enlaces_gestion_usuario">
            <div class="div_enlaces">
                <button type="button" id="eliminar_usuarios">Eliminar</button>   
            </div>
        </div>

        <div id="div_tabla">
            <table border="1" id="tabla_usuarios">
                <thead>
                    <tr>
                        <th>Nombre y apellidos</th>
                        <th>Curso</th>
                        <th>Gmail</th>
                        <th>Contraseña</th>

                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Daniel Pamies teruel</td>
                        <td>1ºESO</td>
                        <td>ani@elcampico.com</td>
                        <td>Alfonso1_Fernandez</td>
                     <td><input type="checkbox" name="eliminar_usuarios"></td>
                    </tr>
                    <?php 
                        $sql = ('select * from usuario');
                        $stmt = execute($sql);
                        while()
                    ?> 
                </tbody>
            </table>
        </div>
    </section>
</body>
</html>