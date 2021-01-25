<!-- cierre del head
    apertura del body
    barra de navegacion -->

</head>
<body>

<nav>
    <div class="nav-container">
        <div class="brand"><img src="img/podioOscuro.png" alt=""></div>

        <div class="burger-container">
            <div id="burger">
                <div class="burger-line"></div>
                <div class="burger-line"></div>
                <div class="burger-line"></div>
            </div>
        </div>

        <div class="nav-links" id="menu">
            <div id="cerrarMenu">
                <div id="cerrarMenu-line1"></div>
                <div id="cerrarMenu-line2"></div>
            </div>
            <ul>
                <?php
                if ($_SESSION['tipo_usuario']=='responsable') {
                ?>
                <li><a class="links" href="areaResponsables.php">area responsables</a></li>
                <?php
                }
                if ($_SESSION['tipo_usuario'] != 'admin') {
                ?>
                <li><a class="links" href="datosPersonales.php">mis datos</a></li>
                <li><a class="links" href="cambiarClave.php">cambiar contraseña</a></li>
                <?php
                } else {
                ?>
                <li><a class="links" href="admTorneos.php">torneos</a></li>
                <li><a class="links" href="admIndex.php">equipos</a></li>
                <li><a class="links" href="admDatosJugadora.php">personas</a></li>
                <?php
                }
                ?>
                <li><a id="logout" href="cerrarSesion.php">cerrar sesión</a></li>
            </ul>
        </div>
    </div>
</nav>