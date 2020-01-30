<?php

function comprobarValoresLogin(&$args, $pdo, &$errores)
{
    if (!empty($errores) || empty($_POST)) {
        return;
    }
    extract($args);
    if (isset($args['login'])) {
        if ($login === '') {
            $errores['login'] = 'El nombre de usuario es obligatorio.';
        } elseif (mb_strlen($login) > 255) {
            $errores['login'] = 'El nombre de usuario no puede tener más de 255 caracteres.';
        } else {
            // Comprobar si el usuario existe
            $sent = $pdo->prepare('SELECT *
                                     FROM usuarios
                                    WHERE login = :login');
            $sent->execute(['login' => $login]);
            if (($fila = $sent->fetch()) === false) {
                $errores['login'] = 'Usuario o contraseña incorrecta.';
            }
        }
    }
    if (isset($args['password'])) {
        if ($password === '') {
            $errores['password'] = 'La contraseña es obligatoria.';
        } elseif ($fila !== false) {
            // Comprobar contraseña
            if (!password_verify($password, $fila['password'])) {
                $args['password'] = '';
                $errores['password'] = 'Usuario o contraseña incorrecta.';
            }
        }
    }
}

function comprobarValoresInsertar(&$args, $pdo, &$errores)
{
    if (!empty($errores) || empty($_POST)) {
        return;
    }
    extract($args);

    if (isset($args['login'])) {
        if ($login === '') {
            $errores['login'] = 'El nombre de usuario es obligatorio.';
        } elseif (mb_strlen($login) > 255) {
            $errores['login'] = 'El nombre de usuario no puede tener más de 255 caracteres.';
        } else {
            // Comprobar si el usuario existe
            $sent = $pdo->prepare('SELECT *
                                     FROM usuarios
                                    WHERE login = :login');
            $sent->execute(['login' => $login]);
            if (($sent->fetch()) !== false) { // Si devuelve algo disinto de false es que existe
                $errores['login'] = 'El usuario ya existe.';
            }
        }
    }

    if (isset($args['email'])) {
        if ($email === '') {
            $errores['email'] = 'El email de usuario es obligatorio.';
        } elseif (mb_strlen($email) > 255) {
            $errores['email'] = 'El email del usuario no puede tener más de 255 caracteres.';
        } else {
            $sent = $pdo->prepare('SELECT *
                                     FROM usuarios
                                    WHERE email = :email');
            $sent->execute(['email' => $email]);
            if (($sent->fetch()) !== false) { // Si devuelve algo disinto de false es que existe
                $errores['email'] = 'El email ya existe.';
            }
        }
    }

    if (isset($args['password'])) {
        if ($password === '') {
            $errores['password'] = 'La contraseña es obligatoria.';
        }
    }

    if (isset($args['cpassword'])) {
        if ($cpassword === '') {
            $errores['cpassword'] = 'La contraseña es obligatoria.';
        }
    }

    if ($args['password'] !== $args['cpassword']) {
        $errores['password'] = 'Las contraseñas no coinciden';
        $errores['cpassword'] = 'Las contraseñas no coinciden';
    }
}


function comprobarValoresModificar(&$args, $pdo, &$errores, &$sql, &$execute, $user = null)
{
    if (!empty($errores) || empty($_POST)) {
        return;
    }

    extract($args);

    $user = $user ?: $_SESSION['login'];

    if (isset($args['login'])) {
        $sent = $pdo->prepare('SELECT login
                                 FROM usuarios
                                WHERE login = :login');
        $sent->execute(['login' => $user]);
        $fila = $sent->fetch(PDO::FETCH_ASSOC);
        if ($fila['login'] != $args['login']) {
            if (mb_strlen($login) > 255) {
                $errores['login'] = 'El nombre de usuario no puede tener más de 255 caracteres.';
            } else {
                // Comprobar si el usuario existe
                $sent = $pdo->prepare('SELECT *
                                        FROM usuarios
                                        WHERE login = :login');
                $sent->execute(['login' => $login]);
                if (($fila = $sent->fetch()) !== false) {
                    $errores['login'] = 'El usuario ya existe.';
                } else {
                    $sql[] = 'login = :login';
                    $execute['login'] = $args['login'];
                }
            }
        }
    }

    if (isset($args['email'])) {
        $sent = $pdo->prepare('SELECT email
                                 FROM usuarios
                                WHERE login = :login');
        $sent->execute(['login' => $user]);
        $fila = $sent->fetch(PDO::FETCH_ASSOC);
        if ($fila['email'] != $args['email']) {
            if (mb_strlen($email) > 255) {
                $errores['email'] = 'El email del usuario no puede tener más de 255 caracteres.';
            } else {
                $sent = $pdo->prepare('SELECT *
                                         FROM usuarios
                                        WHERE email = :email');
                $sent->execute(['email' => $email]);
                if (($sent->fetch()) !== false) {
                    $errores['email'] = 'El email ya existe';
                } else {
                    $sql[] = 'email = :email';
                    $execute['email'] = $args['email'];
                }
            }
        }
    }

    if (isset($args['password']) && isset($args['cpassword'])) {
        if ($password !== '' || $cpassword !== '') {
            if ($args['password'] !== $args['cpassword']) {
                $errores['password'] = 'Las contraseñas no coinciden';
                $errores['cpassword'] = 'Las contraseñas no coinciden';
            } else {
                $sql[] = "password = crypt(:password, gen_salt('bf', 12))";
                $execute['password'] = $args['password'];
            }
        }
    }
}
