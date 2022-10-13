drop table user_sortie;

drop table sortie;

drop table etat;

drop table lieu;

drop table user;

drop table site;

drop table ville;


-- CREATION DE TABLE


create table etat
(
    id INTEGER not null
        primary key autoincrement,
    libelle VARCHAR(30) not null
);

create table site
(
    id INTEGER not null
        primary key autoincrement,
    nom VARCHAR(50) not null
);

create table user
(
    id INTEGER not null
        primary key autoincrement,
    site_id INTEGER default NULL
        constraint FK_8D93D649F6BD1646
            references site,
    email VARCHAR(100) not null,
    pseudo VARCHAR(30) not null,
    nom VARCHAR(40) not null,
    prenom VARCHAR(30) not null,
    telephone VARCHAR(10) not null,
    actif BOOLEAN not null,
    roles CLOB not null,
    password VARCHAR(255) not null,
    photo VARCHAR(255) default NULL
);

create index IDX_8D93D649F6BD1646
    on user (site_id);

create unique index UNIQ_8D93D64986CC499D
    on user (pseudo);

create unique index UNIQ_8D93D649E7927C74
    on user (email);

create table ville
(
    id INTEGER not null
        primary key autoincrement,
    nom VARCHAR(100) not null,
    code_postal VARCHAR(5) not null
);

create table lieu
(
    id INTEGER not null
        primary key autoincrement,
    ville_id INTEGER not null
        constraint FK_2F577D59A73F0036
            references ville,
    nom VARCHAR(50) not null,
    rue VARCHAR(30) default NULL,
    latitude DOUBLE PRECISION default NULL,
    longitude DOUBLE PRECISION default NULL
);

create index IDX_2F577D59A73F0036
    on lieu (ville_id);

create table sortie
(
    id INTEGER not null
        primary key autoincrement,
    user_id INTEGER default NULL
        constraint FK_3C3FD3F2A76ED395
            references user,
    site_id INTEGER default NULL
        constraint FK_3C3FD3F2F6BD1646
            references site,
    etat_id INTEGER default NULL
        constraint FK_3C3FD3F2D5E86FF
            references etat,
    lieu_id INTEGER not null
        constraint FK_3C3FD3F26AB213CC
            references lieu,
    nom VARCHAR(30) not null,
    date_debut DATETIME not null,
    duree INTEGER default NULL,
    date_cloture DATETIME not null,
    nb_inscriptions_max INTEGER not null,
    descriptions_infos CLOB default NULL,
    url_photo VARCHAR(255) default NULL
);

create index IDX_3C3FD3F26AB213CC
    on sortie (lieu_id);

create index IDX_3C3FD3F2A76ED395
    on sortie (user_id);

create index IDX_3C3FD3F2D5E86FF
    on sortie (etat_id);

create index IDX_3C3FD3F2F6BD1646
    on sortie (site_id);

create table user_sortie
(
    user_id INTEGER not null
        constraint FK_596DC8CFA76ED395
            references user
            on delete cascade,
    sortie_id INTEGER not null
        constraint FK_596DC8CFCC72D953
            references sortie
            on delete cascade,
    primary key (user_id, sortie_id)
);

create index IDX_596DC8CFA76ED395
    on user_sortie (user_id);

create index IDX_596DC8CFCC72D953
    on user_sortie (sortie_id);






-- INSERTION DES DONNEES


INSERT INTO user (site_id,email, pseudo, nom, prenom, telephone, actif, roles, password)
VALUES

    (1,'rose@flower.fr','rose','rosa','hong','0240987582',1,'[]','$2y$13$2S0Rbadd7zQNoQhC/2BeruZi6VBnGzAuI2mEei0em7FF3iKQMxvYW'),
    (1,'lila@flower.fr','lila','lila','lililala','0240987582',1,'[]','$2y$13$2S0Rbadd7zQNoQhC/2BeruZi6VBnGzAuI2mEei0em7FF3iKQMxvYW'),
    (1,'lys@flower.fr','lys','lys','hue','0240987582',1,'[]','$2y$13$2S0Rbadd7zQNoQhC/2BeruZi6VBnGzAuI2mEei0em7FF3iKQMxvYW'),
    (1,'pivoine@blanche.fr','pivoine','peony','mudan','0240987582',true,'[]','$2y$13$2S0Rbadd7zQNoQhC/2BeruZi6VBnGzAuI2mEei0em7FF3iKQMxvYW'),
    (1,'tbs@gmail.com', 'tbs', 'Wick', 'John', '0123456987',true,'["ROLE_USER"]','$2y$13$mH1cu9cw6wTh7sbIrqm4XOEDFFIDiN5hBB1yFkhCbgb9Wfg.CjMBa')
;

INSERT INTO site (nom)
VALUES
    ('LYON'),
    ('PARIS'),
    ('LILLE')
;

INSERT INTO sortie (user_id, site_id, etat_id, lieu_id, nom, date_debut, duree, date_cloture, nb_inscriptions_max, descriptions_infos)
VALUES
    (1,1,1,1,'Soirée Jacky & Michelle','2022-11-08 17:13:00',1,'2022-10-28 17:13:00',10,'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ipsum nibh, ornare et mauris sed, pharetra gravida sapien. Etiam accumsan sollicitudin metus vitae rutrum. Donec id euismod orci, efficitur dictum massa. Sed auctor euismod efficitur. Maecenas accumsan, neque viverra imperdiet iaculis, risus augue interdum nulla, in molestie augue turpis in odio. Nunc fermentum justo lacus. Maecenas lectus orci, sagittis at feugiat nec, porttitor sed nisi. Donec non sodales neque. Sed ultrices euismod semper. Aliquam vel diam nec orci pulvinar tristique a finibus nisl. Aenean vitae rhoncus neque, sed finibus ipsum. Sed cursus auctor ligula eu gravida. Quisque pharetra commodo suscipit.'),
    (1,1,1,1,'Attrapper les Pokemons','2022-11-03 17:13:00',1,'2022-10-27 17:13:00',10,'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ipsum nibh, ornare et mauris sed, pharetra gravida sapien. Etiam accumsan sollicitudin metus vitae rutrum. Donec id euismod orci, efficitur dictum massa. Sed auctor euismod efficitur. Maecenas accumsan, neque viverra imperdiet iaculis, risus augue interdum nulla, in molestie augue turpis in odio. Nunc fermentum justo lacus. Maecenas lectus orci, sagittis at feugiat nec, porttitor sed nisi. Donec non sodales neque. Sed ultrices euismod semper. Aliquam vel diam nec orci pulvinar tristique a finibus nisl. Aenean vitae rhoncus neque, sed finibus ipsum. Sed cursus auctor ligula eu gravida. Quisque pharetra commodo suscipit.'),
    (2,1,1,1,'Butter le crud','2022-11-27 17:13:00',1,'2022-10-12 14:13:00',3,'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ipsum nibh, ornare et mauris sed, pharetra gravida sapien. Etiam accumsan sollicitudin metus vitae rutrum. Donec id euismod orci, efficitur dictum massa. Sed auctor euismod efficitur. Maecenas accumsan, neque viverra imperdiet iaculis, risus augue interdum nulla, in molestie augue turpis in odio. Nunc fermentum justo lacus. Maecenas lectus orci, sagittis at feugiat nec, porttitor sed nisi. Donec non sodales neque. Sed ultrices euismod semper. Aliquam vel diam nec orci pulvinar tristique a finibus nisl. Aenean vitae rhoncus neque, sed finibus ipsum. Sed cursus auctor ligula eu gravida. Quisque pharetra commodo suscipit.'),
    (2,1,1,1,'Admirer les lapins','2022-10-30 17:13:00',1,'2022-10-12 17:13:00',10,'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ipsum nibh, ornare et mauris sed, pharetra gravida sapien. Etiam accumsan sollicitudin metus vitae rutrum. Donec id euismod orci, efficitur dictum massa. Sed auctor euismod efficitur. Maecenas accumsan, neque viverra imperdiet iaculis, risus augue interdum nulla, in molestie augue turpis in odio. Nunc fermentum justo lacus. Maecenas lectus orci, sagittis at feugiat nec, porttitor sed nisi. Donec non sodales neque. Sed ultrices euismod semper. Aliquam vel diam nec orci pulvinar tristique a finibus nisl. Aenean vitae rhoncus neque, sed finibus ipsum. Sed cursus auctor ligula eu gravida. Quisque pharetra commodo suscipit.'),
    (2,1,1,1,'Chasser les pythons','2022-10-27 17:13:00',1,'2022-10-12 17:13:00',10,'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ipsum nibh, ornare et mauris sed, pharetra gravida sapien. Etiam accumsan sollicitudin metus vitae rutrum. Donec id euismod orci, efficitur dictum massa. Sed auctor euismod efficitur. Maecenas accumsan, neque viverra imperdiet iaculis, risus augue interdum nulla, in molestie augue turpis in odio. Nunc fermentum justo lacus. Maecenas lectus orci, sagittis at feugiat nec, porttitor sed nisi. Donec non sodales neque. Sed ultrices euismod semper. Aliquam vel diam nec orci pulvinar tristique a finibus nisl. Aenean vitae rhoncus neque, sed finibus ipsum. Sed cursus auctor ligula eu gravida. Quisque pharetra commodo suscipit.'),
    (2,1,1,1,'Contempler les papillions','2022-11-02 17:13:00',1,'2022-10-28 17:13:00',10,'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ipsum nibh, ornare et mauris sed, pharetra gravida sapien. Etiam accumsan sollicitudin metus vitae rutrum. Donec id euismod orci, efficitur dictum massa. Sed auctor euismod efficitur. Maecenas accumsan, neque viverra imperdiet iaculis, risus augue interdum nulla, in molestie augue turpis in odio. Nunc fermentum justo lacus. Maecenas lectus orci, sagittis at feugiat nec, porttitor sed nisi. Donec non sodales neque. Sed ultrices euismod semper. Aliquam vel diam nec orci pulvinar tristique a finibus nisl. Aenean vitae rhoncus neque, sed finibus ipsum. Sed cursus auctor ligula eu gravida. Quisque pharetra commodo suscipit.'),
    (3,1,1,1,'Caresser les loutres','2022-11-28 17:13:00',1,'2022-11-27 17:13:00',10,'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ipsum nibh, ornare et mauris sed, pharetra gravida sapien. Etiam accumsan sollicitudin metus vitae rutrum. Donec id euismod orci, efficitur dictum massa. Sed auctor euismod efficitur. Maecenas accumsan, neque viverra imperdiet iaculis, risus augue interdum nulla, in molestie augue turpis in odio. Nunc fermentum justo lacus. Maecenas lectus orci, sagittis at feugiat nec, porttitor sed nisi. Donec non sodales neque. Sed ultrices euismod semper. Aliquam vel diam nec orci pulvinar tristique a finibus nisl. Aenean vitae rhoncus neque, sed finibus ipsum. Sed cursus auctor ligula eu gravida. Quisque pharetra commodo suscipit.'),
    (1,2,1,1,'AMUSEMENT','2022-10-27 10:13:00',4,'2022-10-07 17:13:00',5,'ceci est la description de la sortie'),
    (2,2,2,2,'SOIREE DANSANTE','2022-11-27 20:13:00',4,'2022-10-27 17:13:00',5,'ceci est la description de la sortie')
;

INSERT INTO ville (nom, code_postal)
VALUES
    ('Nantes','44000'),
    ('Savenay','44260')
;

INSERT INTO lieu (ville_id, nom, rue)
VALUES
    (2,'cafe des arts','rue Paul Taylor'),
    (1,'place du belvedere','boulevard des féministes')
;

INSERT INTO etat (libelle)
VALUES
    ('Créée'),
    ('Ouverte'),
    ('Clôturée'),
    ('Activité en cours'),
    ('Passée'),
    ('Annulée')
;

INSERT INTO user_sortie (user_id, sortie_id)
VALUES
    (2,1),
    (2,2),
    (2,3),
    (3,4),
    (2,5),
    (1,6),
    (1,7)
;

