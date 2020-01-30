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
    <title>Modificar noticia</title>
</head>

<body>

    <?php
        require __DIR__ . '/../comunes/auxiliar.php';
        require __DIR__ . '/auxiliar.php';

        nav(true);

        $errores = [];

        $_csrf = (isset($_POST['_csrf'])) ? $_POST['_csrf'] : null;
        unset($_POST['_csrf']);
        $args = comprobarParametros(PAR, REQ_POST, $errores);
        if (!isset($_GET['id'])) {
            aviso('Error al modificar fila.', DANGER_COLOR);
            header('Location: /index.php');
            return;
        } elseif (hayAvisos()) {
            alert();
        }

        $id = trim($_GET['id']);
        if (!esMiNoticia($id)) {
            header('Location: /index.php');
            return;
        }

        $pdo = conectar();
        comprobarValores($args, $id, $pdo, $errores);

        if (es_POST() && empty($errores)) {
            if (!tokenValido($_csrf)) {
                aviso('Ha ocurrido un error interno en el servidor.', DANGER_COLOR);
                header('Location: /index.php');
                return;
            } else {
                $sent = $pdo->prepare('UPDATE noticias
                                        SET titulo = :titulo
                                          , link = :link
                                          , cuerpo = :cuerpo
                                          , categoria_id = :categoria_id
                                      WHERE id = :id');
                $args['id'] = $id;
                $sent->execute([
                    'titulo' => $args['titulo'],
                    'link' => $args['link'],
                    'cuerpo' => $args['cuerpo'],
                    'categoria_id' => $args['categoria_id'],
                    'id' => $args['id'],
                ]);
                aviso('Fila modificada correctamente.');

                if (adminRedirect()) {
                    return;
                }
                header('Location: index.php');
                return;

            }
        }

        if (es_GET()) {
            $sent = $pdo->prepare('SELECT *
                                     FROM noticias
                                    WHERE id = :id');
            $sent->execute(['id' => $id]);
            if (($args = $sent->fetch(PDO::FETCH_ASSOC)) === false) {
                aviso('Error al modificar la fila.', DANGER_COLOR);
                header('Location: /index.php');
                return;
            }
        }
    ?>


    <div class="container-fluid vertical-center mt-5">
    <!-- todo entre esto -->
        <?= mostrarCookies(); ?>
        <?= dibujarFormulario($args, PAR, 'Modificar', $pdo, $errores) ?>
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