<?php

const REQ_GET = 'GET';
const REQ_POST = 'POST';

const FPP = 10;

const TIPO_ENTERO = 0;
const TIPO_CADENA = 1;
const TIPO_PASSWORD = 2;
const TIPO_TEXTAREA = 3;

const PRIMARY_COLOR = 'primary';
const SECONDARY_COLOR = 'secondary';
const SUCCESS_COLOR = 'success';
const DANGER_COLOR = 'danger';
const WARNING_COLOR = 'warning';
const INFO_COLOR = 'info';
const LIGHT_COLOR = 'light';
const DARK_COLOR = 'dark';
const LINK_COLOR = 'link';


function mostrarCookies()
{
    if (!isset($_COOKIE['aceptar'])) {
        alert('Este sitio usa cookies. <a href="/comunes/cookies.php" class="orange">Estoy de acuerdo</a>', WARNING_COLOR);
    }
}

function comprobarParametros($parametros, $request, &$errores)
{
    $res = [];
    foreach ($parametros as $key => $value) {
        if (isset($value['defecto'])) {
            $res[$key] = $value['defecto'];
        }
    }
    $peticion = peticion($request);
    if (es_GET($request) && !empty($peticion) || es_POST($request)) { // poner para GET tambien
        if ((es_GET($request) || es_POST($request) && !empty($peticion))
            && empty(array_diff_key($res, $peticion))
            && empty(array_diff_key($peticion, $res))) {
            $res = array_map('trim', $peticion);
        } else {
            $errores[] = 'Los parámetros recibidos no son los correctos.';
        }
    }
    return $res;
}

function formatDate($date)
{
//    $d = new DateTime($date);
    return (new DateTime($date))->format('d-m-Y');
}

function formatTime($date)
{
    return (new DateTime($date))->setTimezone(new DateTimeZone('Europe/Madrid'))->format('h:m:s');
}

function nav($search = false)
{
    ?>
        <nav class="navbar navbar-expand navbar-dark bg-orange">
            <a class="navbar-brand" href="/">
                <img src="/img/logo.png" width="128" height="30" alt="">
            </a>
            <div class="v-divider-white"></div>
            <span class="nav-text ml-3 mr-auto">
                EDICIÓN GENERAL
            </span>
            <?php if ($search): ?>
                <div class="bmd-form-group bmd-collapse-inline">
                    <button class="btn bmd-btn-icon" for="search" data-toggle="collapse" data-target="#collapse-search" aria-expanded="false" aria-controls="collapse-search">
                        <i class="material-icons">search</i>
                    </button>
                    <span id="collapse-search" class="collapse">
                        <form action="" method="get">
                            <input type="search" class="form-control" name="search" id="search">
                        </form>
                    </span>
                </div>
            <?php endif ?>
            <?php if (isset($_SESSION['login']) && esAdmin()): ?>
                <a class="btn btn-light my-auto" href="/admin/admnoticias.php" role="button">Administrar noticias</a>
                <a class="btn btn-light my-auto" href="/admin/admusuarios.php" role="button">Administrar usuarios</a>
            <?php else: ?>
                <a class="btn btn-light my-auto" href="/noticias/misnoticias.php" role="button">Mis noticias</a>
            <?php endif ?>
            <div class="v-divider-white mx-3"></div>
            <?php if (logueado()): ?>
                <span class="mr-2 white f-14">
                    <form action="" method="post" class="d-inline">
                        <input type="hidden" name="id" value="<?= h(getIdByLogin($_SESSION['login'])) ?>">
                        <a class="btn btn-light my-auto" href="/usuarios/modificar.php?id=<?= getIdByLogin($_SESSION['login']) ?>" role="button"><?= mb_strtoupper(logueado()) ?></a>
                    </form>
                </span>
                <form class="form-inline my-2 my-lg-0" action="/usuarios/logout.php" method="post">
                    <button class="btn btn-light my-auto" type="submit">Logout</button>
                </form>
            <?php else: ?>
                <a class="btn btn-light my-auto" href="/usuarios/login.php" role="button">Login</a>
                <a class="btn btn-light my-auto" href="/usuarios/insertar.php" role="button">Registrarse</a>
            <?php endif ?>
        </nav>
        <ul class="nav bg-gray">
            <li>
                <a class="btn btn-primary my-auto " href="/noticias/insertar.php" role="button">
                    <i class="material-icons orange align-middle">add</i>
                    <span class="orange align-middle">Publicar</span>
                </a>
            </li>
        </ul>
    <?php
}

function borrarFila($pdo, $tabla, $id, $admin = null)
{
    $sent = $pdo->prepare("SELECT DISTINCT true
                                          FROM usuarios
                                         WHERE :id
                                            IN (SELECT DISTINCT usuario_id
                                                           FROM noticias);");
    $sent->execute(['id' => $id]);
    if ($sent->fetchColumn() == false || $admin) {
        $sent = $pdo->prepare("DELETE
                                    FROM $tabla
                                    WHERE id = :id");
        $sent->execute(['id' => $id]);
        if ($sent->rowCount() === 1) {
            aviso('Fila borrada correctamente.');
            if (adminRedirect()) {
                return;
            }
            if (isset($_SESSION['retorno'])) {
                $retorno = $_SESSION['retorno'];
                unset($_SESSION['retorno']);
                header("Location: $retorno");
                return;
            }
            header('Location: /index.php');
        } else {
            aviso('Ha ocurrido un error inesperado.', DANGER_COLOR);
        }
    } else {
        aviso('El usuario tiene noticias y no puede ser borrado.', DANGER_COLOR);
        if (adminRedirect()) {
            return;
        }

    }
}


function dibujarFormulario($args, $par, $accion, $pdo, $errores)
{
    ?>
    <div class="card">
  <div class="card-body">
      <!-- <div class="row mt-5"> -->
          <!-- <div class="col-8 offset-2"> -->
            <form action="" method="post">
                <?php dibujarElementoFormulario($args, $par, $pdo, $errores) ?>
                <?= token_csrf() ?>
                <?php if ($accion === 'Login'): ?>
                    <div class="checkbox">
                        <label style="font-size: 14px">
                            <input type="checkbox" name="remember" id="remember" value="1"> Recuerdame
                        </label>
                    </div>
                <?php endif; ?>
                <button type="submit" class="btn btn-raised btn-warning btn-block">
                    <?= $accion ?>
                </button>
            </form>
          <!-- </div> -->
      <!-- </div> -->
  </div>
</div>
    <?php
}

function dibujarElementoFormulario($args, $par, $pdo, $errores)
{
    foreach ($par as $k => $v): ?>
        <?php if (isset($par[$k]['defecto'])): ?>
            <div class="form-group">
                <label for="<?= $k ?>"><?= $par[$k]['etiqueta'] ?></label>
                <?php if (isset($par[$k]['relacion'])): ?>
                    <?php
                        $tabla = $par[$k]['relacion']['tabla'];
    $visualizar = $par[$k]['relacion']['visualizar'];
    $ajena = $par[$k]['relacion']['ajena'];
    $sent = $pdo->query("SELECT $ajena, $visualizar
                                               FROM $tabla"); ?>
                    <select name="<?= $k ?>" id="<?= $k ?>" class="form-control">
                        <?php foreach ($sent as $fila): ?>
                            <option value="<?= h($fila[0]) ?>"
                            <?= selected($fila[0], $args[$k]) ?>>
                                <?= h($fila[1]) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                <?php elseif (isset($par[$k]['tipo']) && $par[$k]['tipo'] === TIPO_TEXTAREA): ?>
                    <textarea class="form-control <?= valido($k, $errores) ?>" id="<?= $k ?>" name="<?= $k ?>" cols="30" rows="15"><?=trim($args[$k]) ?></textarea>
                <?php elseif (isset($par[$k]['tipo']) && $par[$k]['tipo'] === TIPO_PASSWORD): ?>
                    <input type="password"
                    class="form-control orange <?= valido($k, $errores) ?>"
                    id="<?= $k ?>" name="<?= $k ?>"
                    value="">
                <?php else: ?>
                    <input type="text"
                    class="form-control <?= valido($k, $errores) ?>"
                    id="<?= $k ?>" name="<?= $k ?>"
                    value="<?= h($args[$k]) ?>">
                <?php endif?>
                <?= mensajeError($k, $errores) ?>
            </div>
        <?php endif; ?><?php
    endforeach;
}


function valido($campo, $errores)
{
    $peticion = peticion();
    if (isset($errores[$campo])) {
        return 'is-invalid invalid-form';
    } elseif (!empty($peticion)) {
        return 'is-valid valid-form';
    } else {
        return '';
    }
}

function mensajeError($campo, $errores)
{
    if (isset($errores[$campo])) {
        return <<<EOT
        <div class="invalid-feedback uppercase">
            {$errores[$campo]}
        </div>
        EOT;
    } else {
        return '';
    }
}

function selected($op, $o)
{
    return $op == $o ? 'selected' : '';
}


function conectar()
{
    return new PDO('pgsql:host=localhost;dbname=mueveme', 'usuario', 'usuario');
}

function es_GET($req = null)
{
    return ($req === null) ? metodo() === 'GET' : $req === REQ_GET;
}

function es_POST($req = null)
{
    return ($req === null) ? metodo() === 'POST' : $req === REQ_POST;
}

function metodo()
{
    return $_SERVER['REQUEST_METHOD'];
}

function peticion($req = null)
{
    return es_GET($req) ? $_GET : $_POST;
}

function getCategoria($cat_id)
{
    $pdo = conectar();
    $sent = $pdo->query("SELECT c.categoria
                           FROM noticias n
                           JOIN categorias c
                             ON $cat_id = c.id
                          WHERE true");
    // $sent->execute();
    return $sent->fetchColumn(0);
}

function getLoginById($usuario_id)
{
    $pdo = conectar();
    $sent = $pdo->query("SELECT u.login
                           FROM usuarios u
                           JOIN noticias n
                             ON u.id = $usuario_id
                          WHERE true");
    return $sent->fetchColumn(0);
}

function getIdByLogin($login)
{
    $pdo = conectar();
    $sent = $pdo->prepare('SELECT id
                           FROM usuarios
                          WHERE login = :login');
    $sent->execute(['login' => $login]);
    return $sent->fetchColumn(0);
}

function alert($mensaje = null, $tipo = null)
{
    if ($mensaje === null && $tipo === null) {
        if (hayAvisos()) {
            $aviso = getAviso();
            $mensaje = $aviso['mensaje'];
            $tipo = $aviso['tipo'];
            quitarAvisos();
        } else {
            return;
        }
    } ?>
    <div class="row mt-5">
        <div class="col-8 offset-2">
            <div class="alert alert-<?= $tipo ?> alert-dismissible fade show" role="alert">
                <?= $mensaje ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div><?php
}

function logueado()
{
    return isset($_SESSION['login']) ? $_SESSION['login'] : false;
}

function aviso($mensaje, $tipo = SUCCESS_COLOR)
{
    $_SESSION['aviso'] = [
        'mensaje' => $mensaje,
        'tipo' => $tipo,
    ];
}

function hayAvisos()
{
    return isset($_SESSION['aviso']);
}

function getAviso()
{
    return hayAvisos() ? $_SESSION['aviso'] : [];
}

function quitarAvisos()
{
    unset($_SESSION['aviso']);
}

function loginObligatorio()
{
    if (!logueado()) {
        aviso('Tienes que loguearte antes para realizar esa acción.', DANGER_COLOR);
        $_SESSION['retorno'] = $_SERVER['REQUEST_URI'];
        header('Location: /usuarios/login.php');
    }
}

function h($string)
{
    return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE);
}

function token_csrf()
{
    if (isset($_SESSION['token'])) {
        $token = $_SESSION['token'];
        return <<<EOT
            <input type="hidden" name="_csrf" value="$token">
        EOT;
    }
}

function tokenValido($_csrf)
{
    if ($_csrf !== null) {
        return $_csrf === $_SESSION['token'];
    } else {
        return false;
    }
}

function paginador($pag, $npags, $orden = null, $direccion = null)
{
    $filtro = paramsFiltro();
    $ant = $pag - 1;
    $sig = $pag + 1;
    if (isset($orden, $direccion)) {
        $orden = "orden=$orden";
        $direccion = "direccion=$direccion";
    } else {
        $orden = '';
        $direccion = '';
    }
    ?>
    <div class="row">
        <div class="col-6 mt-3 mx-auto mt-auto">
            <nav aria-label="Page navigation example">
                <ul class="pagination pagination-sm justify-content-center">
                    <li class="page-item <?= ($pag <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= "?pag=$ant&$filtro&$orden&$direccion" ?>">Anterior</a>
                    </li>
                    <?php for ($i = 1; $i <= $npags; $i++): ?>
                        <li class="page-item <?= ($i == $pag) ? 'active' : '' ?>">
                            <a class="page-link" href= <?= "?pag=$i&$filtro&$orden&$direccion" ?>><?= $i ?></a>
                        </li>
                    <?php endfor ?>
                    <li class="page-item <?= ($pag >= $npags) ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= "?pag=$sig&$filtro&$orden&$direccion" ?>">Siguiente</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <?php
}

// function ejecutarConsulta($sql, $execute, $pdo)
function ejecutarConsulta($sql, $pdo, $execute = null)
{
    if (!isset($execute)) {
        $sent = $pdo->query("SELECT * $sql");
        return $sent;
    } else {
        $sent = $pdo->prepare("SELECT * $sql");
        $sent->execute($execute);
        return $sent;
    }
}

// function contarConsulta($sql, $execute, $pdo)
function contarConsulta($sql, $pdo, $execute = null)
{
    if (!isset($execute)) {
        $sent = $pdo->query("SELECT COUNT(*) $sql");
        $count = $sent->fetchColumn();
        return $count;
    } else {
        $sent = $pdo->prepare("SELECT COUNT(*) $sql");
        $sent->execute($execute);
        $count = $sent->fetchColumn();
        return $count;

    }
}

function recogerNumPag()
{
    if (isset($_GET['pag']) && ctype_digit($_GET['pag'])) {
        $pag = trim($_GET['pag']);
        unset($_GET['pag']);
    } else {
        $pag = 1;
    }
    return $pag;
}

function recogerOrden()
{
    if (isset($_GET['orden'])) {
        $orden = trim($_GET['orden']);
        unset($_GET['orden']);
    } else {
        $orden = 'id';
    }

    return $orden;
}

function recogerDireccion()
{
    if (isset($_GET['direccion'])) {
        $direccion = trim($_GET['direccion']);
        unset($_GET['direccion']);
    } else {
        $direccion = 'asc';
    }

    return $direccion;
}


function paramsFiltro()
{
    $filtro = [];

    foreach ($_GET as $k => $v) {
        $filtro[] = "$k=$v";
    }

    return implode('&', $filtro);
}

function comprobarLogueoAutomatico()
{
    if (isset($_COOKIE['username'], $_COOKIE['tokenCookie'])) {
        $pdo = conectar();
        $sent = $pdo->prepare('SELECT token
                          FROM usuarios
                         WHERE login = :login');
        $sent->execute(['login' => $_COOKIE['username']]);
        $tokenBD = $sent->fetchColumn();
        if ($tokenBD === $_COOKIE['tokenCookie']) {
            $_SESSION['login'] = $_COOKIE['username'];
        }
    }
}

function esAdmin()
{
    $pdo = conectar();
    $sent = $pdo->prepare("SELECT admin
                             FROM usuarios
                            WHERE login = :login");
    $sent->execute(['login' => $_SESSION['login']]);
    return $sent->fetchColumn();
}

function adminRedirect()
{
    if (isset($_SESSION['adm'])) {
        $adm = $_SESSION['adm'];
        unset($_SESSION['adm']);
        header("Location: $adm");
        return true;
    }
}


function getNumNoticias($id)
{
    $pdo = conectar();
    $sent = $pdo->prepare('SELECT COUNT(n.usuario_id)
                               AS num_noticias
                             FROM noticias n
                       RIGHT JOIN usuarios u
                               ON n.usuario_id = u.id
                            WHERE u.id = :id
                         GROUP BY u.id');
    $sent->execute(['id' => $id]);
    return $sent->fetchColumn();
}

function esMiNoticia($id)
{
    $pdo = conectar();
    $array = [];
    $sent = $pdo->prepare('SELECT id FROM noticias WHERE usuario_id = :usuario_id');
    $sent->execute(['usuario_id' => getIdByLogin($_SESSION['login'])]);
    foreach ($sent as $fila) {
        $array[] = $fila['id'];
    }
    return in_array($id, $array) || esAdmin();
    // $pdo = conectar();
    // $sent = $pdo->prepare('SELECT id FROM noticias WHERE usuario_id = :usuario_id');
    // $sent->execute(['usuario_id' => getIdByLogin($_SESSION['login'])]);
    // $array = $sent->fetchAll(PDO::FETCH_COLUMN);
    // return in_array($id, $array) || esAdmin();
}

function soyYo($id)
{
    $pdo = conectar();
    $sent = $pdo->prepare('SELECT id
                             FROM usuarios
                            WHERE login = :login');
    $sent->execute(['login' => $_SESSION['login']]);
    $myId = $sent->fetchColumn();
    return $myId == $id || esAdmin();
}