DROP TABLE IF EXISTS categorias CASCADE;

CREATE TABLE categorias
(
    id        bigserial    PRIMARY KEY
  , categoria varchar(255) NOT NULL UNIQUE
);

DROP TABLE IF EXISTS usuarios CASCADE;

CREATE TABLE usuarios
(
    id BIGSERIAL PRIMARY KEY
  , login        varchar(255) NOT NULL UNIQUE
  , password     varchar(255) NOT NULL
  , email        varchar(255) NOT NULL
  , created_at   timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP
  , token        varchar(255) UNIQUE
  , admin        bool         NOT NULL DEFAULT false
);

DROP TABLE IF EXISTS noticias CASCADE;

CREATE TABLE noticias
(
    id bigserial PRIMARY KEY
  , titulo       varchar(255)  NOT NULL
  , link         varchar(255)  NOT NULL
  , cuerpo       varchar(1000) NOT NULL
  , categoria_id bigint        NOT NULL references categorias (id)
  , usuario_id   bigint        NOT NULL references usuarios (id)
  , created_at   timestamp     NOT NULL DEFAULT CURRENT_TIMESTAMP
);


INSERT INTO usuarios (login, password, email, admin)
VALUES ('pepe', crypt('pepe', gen_salt('bf', 12)), 'pepe@pepe.com', false),
       ('ji', crypt('ji', gen_salt('bf', 12)), 'ji@ji.com', false),
       ('ale', crypt('ale', gen_salt('bf', 12)), 'ale@ale.com', true);

INSERT INTO categorias (categoria)
VALUES  ('Programación')
      , ('Juegos')
      , ('Piscinas');

INSERT INTO noticias (titulo, link, cuerpo, categoria_id, usuario_id)
VALUES  ('Nuevo juego', 'https://www.google.es', 'Lorem ipsum dolor sit amet consectetur adipiscing elit facilisi quis class,
sem sapien laoreet mauris varius fusce habitant enim nascetur.Sodales in fringilla tristique etiam ultricies torquent dignissim imperdiet ultrices potenti sociosqu augue,
primis.', 2, 2)
      , ('Nuevo lenguaje de programación', 'https://www.google.es', 'Lorem ipsum dolor sit amet consectetur adipiscing elit facilisi quis class,
sem sapien laoreet mauris varius fusce habitant enim nascetur.Sodales in fringilla tristique etiam ultricies torquent dignissim imperdiet ultrices potenti sociosqu augue,
primis.', 2, 2)
      , ('Nuevo lenguaje de programación', 'https://www.google.es', 'Lorem ipsum dolor sit amet consectetur adipiscing,
elit porttitor hendrerit quam aptent condimentum facilisi,
litora maecenas facilisis iaculis nisl.Justo nisi sodales natoque accumsan ante tellus non venenatis quis,
imperdiet himenaeos phasellus mattis laoreet vivamus curabitur turpis purus sociis,
pellentesque montes nisl consequat pulvinar fermentum viverra blandit.Odio euismod magna pretium ridiculus nascetur ultrices,
imperdiet litora sociis hendrerit malesuada dapibus torquent,
eleifend justo vehicula tristique primis.Magnis molestie lectus eleifend felis leo fusce interdum dis id ac condimentum class nostra inceptos,
maecenas volutpat ornare tempus orci tellus dapibus mauris cursus malesuada nullam dui.Libero class potenti conubia lectus tempor habitasse enim phasellus ultrices curabitur,
nisi mi magna torquent nunc metus scelerisque cras maecenas,
diam nullam dis duis est ultricies purus cubilia neque.Neque convallis commodo volutpat vitae tristique,
non phasellus euismo.', 3, 2);



do $$
BEGIN
  FOR counter IN 1..50 LOOP
    INSERT INTO noticias (titulo, link, cuerpo, categoria_id, usuario_id)
    VALUES  (concat('Noticia ', counter), 'https://www.google.es', 'Lorem ipsum dolor sit amet consectetur adipiscing elit facilisi quis class,
    sem sapien laoreet mauris varius fusce habitant enim nascetur.Sodales in fringilla tristique etiam ultricies torquent dignissim imperdiet ultrices potenti sociosqu augue,
    primis.', 2, 2);
  END LOOP;
END;
$$;
