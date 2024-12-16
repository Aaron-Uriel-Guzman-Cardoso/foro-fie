SET GLOBAL sql_mode='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION,ANSI_QUOTES,NO_ZERO_IN_DATE,ONLY_FULL_GROUP_BY';

INSERT INTO "category" ("name")
VALUES ('Avisos'), ('Comunidad'), ('Retroalimentación');

INSERT INTO "student" ("id", "first_name", "last_name", "email")
VALUES
    ('2100554h', 'Aaron Uriel', 'Guzman Cardoso', '2100554h@umich.mx'),
    ('2147433d', 'Luis Alberto', 'Galeana Vargas', '2147433d@umich.mx'),
    ('1578636g', 'Samuel Ismael', 'Martínez Villa', '1578636g@umich.mx'),
    ('2100536a', 'Jorge', 'Correa Gutiérrez', '2100536a@umich.mx');

INSERT INTO "group" ("name")
VALUES ('admin'), ('moderator'), ('common');

INSERT INTO "grant" ("name")
VALUES
    ('post'), ('comment'), ('delete_own_post'),
    ('edit_anyone_post'), ('delete_anyone_post'), ('ban_user'),
    ('update_user_group');

INSERT INTO "group_grant" ("group", "grant")
VALUES
    (1, 1),(1, 2),(1, 3),(1, 4),(1, 5),(1, 6),(1, 7),
    (2, 1),(2, 2),(2, 3),(2, 4),(2, 5),(2, 6),
    (3, 1),(3, 2),(3, 3);

INSERT INTO "account" ("nickname", "desc", "hash", "group")
VALUES
    ('auriel', '¡Hola! Soy uno de los creadores de foro.fie',
        '$2y$10$bK0ABuAvtI/X.ypMbl8jNO2dPiTivcdG3RWBjrpcBdWx6XAE0MB.S', 1),
    ('test1', 'test1', 
        '$2y$10$pcIRi5D5MVYWXgh4dNPidukaspHirFkoWqb..KlxUGw2XDC.2NIl2', 2),
    ('test2', 'test2',
        '$2y$10$Sl9JFzHe58Aac9lsu9cjNuWL6ggMLmnx0KD4JR.1c5.REoLWe.gSC', 3);

INSERT INTO "post" ("account", "title", "content", "category")
VALUES
    ('1', 'Publicación de prueba', 'testasdfghjklñ', '1'),
    ('2', 'Probandoooo', 'Hola xdVoy a probar saltos 
    de línea\nHola', '2');
