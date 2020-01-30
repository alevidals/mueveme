<?php
const NEWS = [
    'titulo' => [
        'defecto' => '',
        'tipo' => TIPO_CADENA,
        'etiqueta' => 'Título',
    ],
    'link' => [
        'defecto' => '',
        'tipo' => TIPO_CADENA,
        'etiqueta' => 'Link',
    ],
    'cuerpo' => [
        'defecto' => '',
        'tipo' => TIPO_CADENA,
        'etiqueta' => 'Cuerpo',
    ],
    'categoria_id' => [
        'defecto' => '',
        'etiqueta' => 'Categoría',
        'tipo' => TIPO_ENTERO,
        'relacion' => [
            'tabla' => 'categorias',
            'ajena' => 'id',
            'visualizar' => 'categoria'
        ]
    ],
    'usuario_id' => [
        'defecto' => '',
        'etiqueta' => 'Usuario',
        'tipo' => TIPO_ENTERO,
        'relacion' => [
            'tabla' => 'usuarios',
            'ajena' => 'id',
            'visualizar' => 'login',
        ]
    ],
    'created_at' => [
        'defecto' => '',
        'tipo' => TIPO_CADENA,
        'etiqueta' => 'Fecha de creación',
    ],
];

const USERS = [
    'login' => [
        'defecto' => '',
        'tipo' => TIPO_CADENA,
        'etiqueta' => 'Login',
    ],
    'email' => [
        'defecto' => '',
        'tipo' => TIPO_CADENA,
        'etiqueta' => 'Email',
    ],
    'created_at' => [
        'defecto' => '',
        'tipo' => TIPO_CADENA,
        'etiqueta' => 'Fecha de creación',
    ],
    'admin' => [
        'defecto' => '',
        'tipo' => TIPO_ENTERO,
        'etiqueta' => 'Admin',
    ],
];

function dibujarFormularioFiltro($args, $par, $pdo, $errores)
{ ?>
    <div class="row mt-3">
        <div class="col-4 offset-4">
            <form action="" method="get">
                <?php dibujarElementoFormulario($args, $par, $pdo, $errores) ?>
                <button type="submit" class="btn btn-raised btn-warning">
                    Buscar
                </button>
                <button type="reset" class="btn btn-raised  btn-secondary">
                    Limpiar
                </button>
            </form>
        </div>
    </div>
    <?php
}

function insertarFiltro(&$sql, &$execute, $campo, $args, $par, $errores)
{
    if (isset($par[$campo]['defecto']) && $args[$campo] !== '' && !isset($errores[$campo])) {
        if ($par[$campo]['tipo'] === TIPO_ENTERO) {
            $sql .= " AND $campo = :$campo";
            $execute[$campo] = $args[$campo];
        } else {
            $sql .= " AND $campo::varchar ILIKE :$campo";
            $execute[$campo] = '%' . $args[$campo] . '%';
        }
    }
}

function pintarTabla($sent, $count, $par, $orden, $direccion, $errores, $ruta, $total = null)
{
    $filtro = paramsFiltro(); ?>
    <?php if ($count == 0): ?>
        <?php alert('No se han encontrado noticias.', DANGER_COLOR) ?>
    <?php elseif (isset($errores[0])): ?>
        <?php alert($errores[0], DANGER_COLOR); ?>
    <?php else: ?>
        <div class="row mt-4">
            <div class="col-10 offset-1">
                <table class="table">
                    <thead>
                        <?php $simbolo = ($direccion === 'asc' ? '⬆' : '⬇'); ?>
                        <?php $direccion = ($direccion === 'asc' ? 'desc' : 'asc'); ?>
                        <?php foreach ($par as $k => $v): ?>
                            <!-- <th scope="col"><?= $par[$k]['etiqueta'] ?></th> -->
                            <th scope="col">
                                <?php if ($orden !== $k): ?>
                                    <a href="<?= "?$filtro&orden=$k&direccion=asc" ?>">
                                        <?= $par[$k]['etiqueta'] ?>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= "?$filtro&orden=$k&direccion=$direccion" ?>">
                                        <?= $par[$k]['etiqueta'] ?>
                                    </a>
                                <?php endif ?>
                                <?= ($k === $orden) ? "$simbolo" : '' ?>
                            </th>
                        <?php endforeach ?>
                        <th scope="col">Acciones</th>
                        <?php if (isset($total)): ?>
                            <th scope="col">Nº noticias</th>
                        <?php endif ?>
                    </thead>
                    <tbody>
                        <?php foreach ($sent as $fila): ?>
                            <tr scope="row">
                                <?php foreach ($par as $k => $v): ?>
                                    <?php if (isset($par[$k]['relacion'])): ?>
                                        <?php $visualizar = $par[$k]['relacion']['visualizar'] ?>
                                        <td><?= $fila[$visualizar] ?></td>
                                    <?php else: ?>
                                        <td><?= h($fila[$k]) ?></td>
                                    <?php endif ?>
                                <?php endforeach ?>
                                <td>
                                    <form action="" method="post">
                                        <input type="hidden" name="id" value="<?= $fila['id'] ?>">
                                        <?= token_csrf() ?>
                                        <button type="submit" class="btn btn-sm btn-danger">Borrar</button>
                                        <a href="/<?= $ruta ?>/modificar.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-info" role="button">
                                            Modificar
                                        </a>
                                    </form>
                                </td>
                                <?php if (isset($total)): ?>
                                    <td>
                                        <?= getNumNoticias($fila['id']) ?>
                                    </td>
                                <?php endif ?>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif ?>
    <?php
}