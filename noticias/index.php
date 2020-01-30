<?php session_start(); ?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Material Design for Bootstrap fonts and icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons">

    <!-- Material Design for Bootstrap CSS -->
    <link rel="stylesheet"
        href="https://unpkg.com/bootstrap-material-design@4.1.1/dist/css/bootstrap-material-design.min.css"
        integrity="sha384-wXznGJNEXNG1NFsbm0ugrLFMQPWswR3lds2VeinahP8N0zJw9VWSopbjv2x7WCvX" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css"
        integrity="sha256-+N4/V/SbAFiW1MPBCXnfnP9QSN3+Keu+NlB+0ev/YKQ=" crossorigin="anonymous" />
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="icon" href="../img/favicon.ico">
    <title>Mueveme</title>
</head>

<body>

    <?php
        require __DIR__ . '/../comunes/auxiliar.php';
        require __DIR__ . '/auxiliar.php';

        comprobarLogueoAutomatico();

        if (isset($_SESSION['login'])) {
            if (esAdmin()) {
                header('Location: /admin/index.php');
                return;
            }
        }

        nav(true);

        $pag = recogerNumPag();
        $pdo = conectar();
        if (isset($_GET['search']) && $_GET['search'] !== '') {
            $sql = "SELECT n.titulo, n.link, n.cuerpo, n.categoria_id, n.usuario_id, n.created_at
                      FROM noticias n
                      JOIN usuarios u
                        ON n.usuario_id = u.id
                      JOIN categorias c
                        ON n.categoria_id = c.id
                     WHERE n.titulo ILIKE :search
                        OR n.link ILIKE :search
                        OR n.cuerpo ILIKE :search
                        OR c.categoria ILIKE :search
                        OR u.login ILIKE :search";
            $sent = $pdo->prepare($sql);
            $sent->execute(['search' => '%' . trim($_GET['search']) . '%']);
            $nfilas = $sent->rowCount();
            $sql .= ' LIMIT ' . FPP . ' OFFSET ' . ($pag - 1) * FPP;
            $sent = $pdo->prepare($sql);
            $sent->execute(['search' => '%' . trim($_GET['search']) . '%']);
            $npags = ceil($nfilas / FPP);
            if ($nfilas == 0) {
                alert('No se han encontrado resultados. <a class="orange" href="/index.php">Volver a la página de inicio</a>.', DANGER_COLOR);
            }
        } else {
            $sql = 'FROM NOTICIAS';
            $nfilas = contarConsulta($sql, $pdo);
            $sent = $pdo->query('SELECT * FROM noticias');
            $sql .= ' ORDER BY id LIMIT ' . FPP
                  . ' OFFSET ' . ($pag - 1) * FPP;
            $sent = ejecutarConsulta($sql, $pdo);
            $npags = ceil($nfilas / FPP);
        }

        if (hayAvisos()) {
            alert();
        }
    ?>


    <div class="container-fluid">
    <!-- todo entre esto -->
        <?= mostrarCookies(); ?>
        <?= pintarNoticias($sent); ?>
        <?php paginador($pag, $npags); ?>
    <!-- todo entre esto -->
    </div>

    <!-- footer generico -->

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>
    <script src="https://unpkg.com/popper.js@1.12.6/dist/umd/popper.js"
        integrity="sha384-fA23ZRQ3G/J53mElWqVJEGJzU0sTs+SvzG8fXVWP+kJQ1lwFAOkcUOysnlKJC33U" crossorigin="anonymous">
    </script>
    <script src="https://unpkg.com/bootstrap-material-design@4.1.1/dist/js/bootstrap-material-design.js"
        integrity="sha384-CauSuKpEqAFajSpkdjv3z9t8E7RlpJ1UP0lKM/+NdtSarroVKu069AlsRPKkFBz9" crossorigin="anonymous">
    </script>
    <script>
        $(document).ready(function () {
            $('body').bootstrapMaterialDesign();
        });
    </script>
</body>

</html>