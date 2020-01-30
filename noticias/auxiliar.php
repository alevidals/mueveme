<?php
const PAR = [
    'titulo' => [
        'defecto' => '',
        'etiqueta' => 'Título',
    ],
    'link' => [
        'defecto' => '',
        'etiqueta' => 'Link',
    ],
    'cuerpo' => [
        'defecto' => '',
        'etiqueta' => 'Cuerpo',
        'tipo' => TIPO_TEXTAREA,
    ],
    'categoria_id' => [
        'defecto' => '',
        'etiqueta' => 'Categoría',
        'relacion' => [
            'tabla' => 'categorias',
            'ajena' => 'id',
            'visualizar' => 'categoria'
        ],
    ]
];

function comprobarValores(&$args, $id, $pdo, &$errores)
{
    if (!empty($errores) || empty($_POST)) {
        return;
    }

    extract($args);

    if (isset($args['titulo'])) {
        if ($titulo === '') {
            $errores['titulo'] = 'El título es obligatorio.';
        } elseif (mb_strlen($titulo) > 1000) {
            $errores['titulo'] = 'El título no puede tener más de 1000 caracteres.';
        }
    }

    if (isset($args['link'])) {
        if ($link === '') {
            $errores['link'] = 'El link es obligatorio.';
        } elseif (mb_strlen($link) > 1000) {
            $errores['link'] = 'El link no puede tener más de 1000 caracteres.';
        } elseif (!filter_var($link, FILTER_VALIDATE_URL)) {
            $errores['link'] = 'La url no tiene un formato correcto. Por ejemplo: http://www.meneate.com.';
        }
    }

    if (isset($args['cuerpo'])) {
        if ($cuerpo === '') {
            $errores['cuerpo'] = 'El cuerpo es obligatorio.';
        } elseif (mb_strlen($cuerpo) > 1000) {
            $errores['cuerpo'] = 'El cuerpo no puede tener más de 1000 caracteres.';
        }
    }

    if (isset($args['categoria_id'])) {
        if ($categoria_id === '') {
            $errores['categoria_id'] = 'La categoría es oligatoria.';
        } elseif (!ctype_digit($categoria_id)) {
            $errores['categoria_id'] = 'El departamento no tiene el formato correcto.';
        } else {
            $sent = $pdo->prepare('SELECT COUNT(*)
                                     FROM categorias
                                    WHERE id = :id');
            $sent->execute(['id' => $categoria_id]);
            if ($sent->fetchColumn() === 0) {
                $errores['categoria_id'] = 'La categoria no existe.';
            }
        }
    }

    $args['usuario_id'] = strval(getIdByLogin($_SESSION['login']));
}

function pintarNoticias($sent, $editable = false)
{
    ?>
        <div class="row mt-5">
            <?php foreach ($sent as $fila): ?>
                <div class="col-8 offset-2 mb-5">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <h5>
                                    <a href="<?= h($fila['link']) ?>" class="orange" target="_blank"><?= h($fila['titulo']) ?></a>
                                        <?php if ($editable): ?>
                                            <form action="" method="post" class="d-inline">
                                                <input type="hidden" name="id" value="<?= h($fila['id']) ?>">
                                                <?= token_csrf() ?>
                                                <button type="submit" class="btn btn-secondary bmd-btn-icon">
                                                    <i class="material-icons orange my-auto">delete</i>
                                                </button>
                                                <a class="btn btn-secondary bmd-btn-icon" href="modificar.php?id=<?= h($fila['id']) ?>" role="button">
                                                    <i class="material-icons orange mt-1">edit</i>
                                                </a>
                                            </form>
                                        <?php endif; ?>
                                </h5>
                            </div>
                            <div class="card-subtitle">
                                <span class="badge badge-warning mb-3"><?= getCategoria($fila['categoria_id']) ?></span>
                                <small>por <?= h(getLoginById($fila['usuario_id']))?> a <a class="orange" href="<?= $fila['link'] ?>" target="_blank"><?= parse_url($fila['link'], PHP_URL_HOST) ?></a> el <?= formatDate($fila['created_at']) ?> a las <?= formatTime($fila['created_at']) ?></small>
                            </div>
                            <p class="card-text">
                                <?= h($fila['cuerpo']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    <?php
}
