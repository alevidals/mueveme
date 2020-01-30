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

        nav();

        if (hayAvisos()) {
            alert();
        }

        ?>


    <div class="container-fluid vertical-center">
    <!-- todo entre esto -->

        <div class="row justify-content-center">
            <div class="col-2">
                <div class="card">
                    <img class="card-img-top admimg mx-auto" src="https://cdn.pixabay.com/photo/2013/07/12/19/16/newspaper-154444_960_720.png" alt="Card image cap">
                    <div class="dropdown-divider mt-4"></div>
                    <div class="card-body">
                        <a class="btn btn-raised btn-warning btn-block my-auto " href="/admin/admnoticias.php" role="button">Administrar noticias</a>
                    </div>
                </div>
            </div>
            <div class="col-2">
                <div class="card">
                    <img class="card-img-top admimg mx-auto pt-2" src="https://cdn.pixabay.com/photo/2016/11/18/23/38/child-1837375_960_720.png" alt="Card image cap">
                    <div class="dropdown-divider mt-4"></div>
                    <div class="card-body">
                        <a class="btn btn-raised btn-warning btn-block my-auto" href="/admin/admusuarios.php" role="button">Administrar usuarios</a>
                    </div>
                </div>
            </div>
        </div>

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