drop table user_sortie;

drop table sortie;
drop table reset_password_request;

drop table etat;

drop table lieu;

drop table user;

drop table site;

drop table ville;


-- CREATION DE TABLE
create table etat
(
    id      INTEGER     not null
        primary key autoincrement,
    libelle VARCHAR(30) not null
);

create table site
(
    id  INTEGER     not null
        primary key autoincrement,
    nom VARCHAR(50) not null
);

create table user
(
    id        INTEGER      not null
        primary key autoincrement,
    site_id   INTEGER      default NULL
        constraint FK_8D93D649F6BD1646
            references site,
    email     VARCHAR(100) not null,
    pseudo    VARCHAR(30)  not null,
    nom       VARCHAR(40)  default NULL,
    prenom    VARCHAR(30)  default NULL,
    telephone VARCHAR(10)  default NULL,
    actif     BOOLEAN      not null,
    roles     CLOB         not null,
    password  VARCHAR(255) not null,
    photo     VARCHAR(255) default NULL
);

create table reset_password_request
(
    id           INTEGER      not null
        primary key autoincrement,
    user_id      INTEGER      not null
        constraint FK_7CE748AA76ED395
            references user,
    selector     VARCHAR(20)  not null,
    hashed_token VARCHAR(100) not null,
    requested_at DATETIME     not null,
    expires_at   DATETIME     not null
);

create index IDX_7CE748AA76ED395
    on reset_password_request (user_id);

create index IDX_8D93D649F6BD1646
    on user (site_id);

create unique index UNIQ_8D93D64986CC499D
    on user (pseudo);

create unique index UNIQ_8D93D649E7927C74
    on user (email);

create table ville
(
    id          INTEGER      not null
        primary key autoincrement,
    nom         VARCHAR(100) not null,
    code_postal VARCHAR(5)   not null
);

create table lieu
(
    id        INTEGER     not null
        primary key autoincrement,
    ville_id  INTEGER     not null
        constraint FK_2F577D59A73F0036
            references ville,
    nom       VARCHAR(50) not null,
    rue       VARCHAR(50)      default NULL,
    latitude  DOUBLE PRECISION default NULL,
    longitude DOUBLE PRECISION default NULL
);

create index IDX_2F577D59A73F0036
    on lieu (ville_id);

create table sortie
(
    id                  INTEGER     not null
        primary key autoincrement,
    user_id             INTEGER      default NULL
        constraint FK_3C3FD3F2A76ED395
            references user,
    site_id             INTEGER      default NULL
        constraint FK_3C3FD3F2F6BD1646
            references site,
    etat_id             INTEGER      default NULL
        constraint FK_3C3FD3F2D5E86FF
            references etat,
    lieu_id             INTEGER     not null
        constraint FK_3C3FD3F26AB213CC
            references lieu,
    nom                 VARCHAR(30) not null,
    date_debut          DATETIME    not null,
    duree               INTEGER      default NULL,
    date_cloture        DATETIME    not null,
    nb_inscriptions_max INTEGER     not null,
    descriptions_infos  CLOB         default NULL,
    url_photo           VARCHAR(255) default NULL
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
    user_id   INTEGER not null
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


INSERT INTO user (site_id,photo,email, pseudo, nom, prenom, telephone, actif, roles, password)
VALUES


    (3,'uploads/pikachu.jpg','rose@flower.fr','rose','rosa','hong','0240987582',true,'["ROLE_ADMIN"]','$2y$13$2S0Rbadd7zQNoQhC/2BeruZi6VBnGzAuI2mEei0em7FF3iKQMxvYW'),
    (3,'uploads/pikachu.jpg','pivoine@blanche.fr','pivoine','peony','mudan','0240987582',true,'["ROLE_USER"]','$2y$13$2S0Rbadd7zQNoQhC/2BeruZi6VBnGzAuI2mEei0em7FF3iKQMxvYW'),
    (3,'uploads/pikachu.jpg','passiflore@flower.fr','passiflore','passiflora','Passiflorette','0941987582',1,'["ROLE_USER"]','$2y$13$2S0Rbadd7zQNoQhC/2BeruZi6VBnGzAuI2mEei0em7FF3iKQMxvYW'),
    (3,'uploads/pikachu.jpg','petunia@flower.fr','petunia','petuniana','petuniala','0741997782',1,'["ROLE_USER"]','$2y$13$2S0Rbadd7zQNoQhC/2BeruZi6VBnGzAuI2mEei0em7FF3iKQMxvYW'),
    (1,'uploads/pikachu.jpg','lila@flower.fr','lila','lila','lililala','0240987582',1,'["ROLE_USER"]','$2y$13$2S0Rbadd7zQNoQhC/2BeruZi6VBnGzAuI2mEei0em7FF3iKQMxvYW'),
    (1,'uploads/pikachu.jpg','lys@flower.fr','lys','lys','hue','0240987582',1,'["ROLE_USER"]','$2y$13$2S0Rbadd7zQNoQhC/2BeruZi6VBnGzAuI2mEei0em7FF3iKQMxvYW'),
    (1,'uploads/pikachu.jpg','lavande@flower.fr','lavande','lavander','lavandala','0950987582',1,'["ROLE_USER"]','$2y$13$2S0Rbadd7zQNoQhC/2BeruZi6VBnGzAuI2mEei0em7FF3iKQMxvYW'),
    (1,'uploads/pikachu.jpg','lotus@flower.fr','lotus','lotusa','lotusala','0658987582',1,'["ROLE_USER"]','$2y$13$2S0Rbadd7zQNoQhC/2BeruZi6VBnGzAuI2mEei0em7FF3iKQMxvYW'),
    (2,'uploads/pikachu.jpg','marguerite@flower.fr','marguerite','margo','marguerita','0928987582',1,'["ROLE_USER"]','$2y$13$2S0Rbadd7zQNoQhC/2BeruZi6VBnGzAuI2mEei0em7FF3iKQMxvYW'),
    (2,'uploads/pikachu.jpg','test@eni.fr','Nihilix','letoto','Nils','0928987582',1,'["ROLE_USER"]','$2y$13$2S0Rbadd7zQNoQhC/2BeruZi6VBnGzAuI2mEei0em7FF3iKQMxvYW'),
    (2,'uploads/pikachu.jpg','muguet@flower.fr','muguet','mugueti','mugueta','0928965582',1,'["ROLE_USER"]','$2y$13$2S0Rbadd7zQNoQhC/2BeruZi6VBnGzAuI2mEei0em7FF3iKQMxvYW'),
    (2,'uploads/pikachu.jpg','magnolia@flower.fr','magnolia','magnolii','magnoliana','0928765582',1,'["ROLE_USER"]','$2y$13$2S0Rbadd7zQNoQhC/2BeruZi6VBnGzAuI2mEei0em7FF3iKQMxvYW'),
    (1,'uploads/pikachu.jpg','refectory@yopmail.com','refectory','Chang','mulan','0240987582',true,'["ROLE_USER"]','$2y$13$2S0Rbadd7zQNoQhC/2BeruZi6VBnGzAuI2mEei0em7FF3iKQMxvYW'),
    (1,'uploads/pikachu.jpg','admin@admin.fr','admin','admin','admin','0240987582',true,'["ROLE_ADMIN"]','$2y$13$dHAGOhvy.49jcArSAy5BieeVWMmLBYBvnNRLIhKzh4WImkYlLUvAu'),
    (1,'uploads/loup-634826e5c0fe4.jpg','tbs@gmail.com', 'tbs', 'Wick', 'John', '0123456987',true,'["ROLE_ADMIN"]','$2y$13$mH1cu9cw6wTh7sbIrqm4XOEDFFIDiN5hBB1yFkhCbgb9Wfg.CjMBa')

;

INSERT INTO site (nom)
VALUES
    ('Nantes'),
    ('Lyon'),
    ('Paris')
;

INSERT INTO sortie (user_id, site_id, etat_id, lieu_id, nom, date_debut, duree, date_cloture, nb_inscriptions_max, descriptions_infos)
VALUES

    (1,3,3,1,'Soirée Jacky & Michelle','2022-10-20 17:58:00',10,'2022-10-20 17:13:00',3,'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'),
    (1,3,2,1,'Attraper les Pokemons','2022-10-30 17:13:00',1,'2022-10-27 17:13:00',10,'Lorem ipsum dolor sit amet, consectetur adipiscing elit. '),
    (2,3,1,2,'Butter le crud','2022-11-27 18:13:00',1,'2022-11-12 14:13:00',3,'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'),
    (2,3,7,2,'Admirer les lapins','2021-10-30 17:13:00',1,'2021-10-12 17:13:00',10,'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'),
    (2,3,3,1,'Chasser les pythons','2021-10-27 17:13:00',1,'2022-10-12 17:13:00',10,'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'),
    (2,3,2,1,'Contempler les papillons','2022-11-02 17:13:00',1,'2022-10-28 17:13:00',10,'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'),
    (3,3,3,1,'Caresser les loutres','2022-11-28 17:13:00',1,'2022-10-10 17:13:00',10,'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'),
    (3,3,3,2,'AMUSEMENT','2022-10-27 10:13:00',4,'2022-10-07 17:13:00',5,'ceci est la description de la sortie'),
    (1,3,5,1,'SORTIE PASSEE','2022-10-14 10:13:00',2,'2022-10-07 17:13:00',5,'ceci est la description de la sortie'),
    (1,3,5,1,'Voyager chez les liliputes','2022-10-14 10:13:00',2,'2022-10-01 17:13:00',5,'ceci est la description de la sortie'),
    (2,3,2,2,'SOIREE DANSANTE','2021-10-30 20:13:00',4,'2022-10-27 17:13:00',5,'ceci est la description de la sortie'),
    (5,1,7,3,'Dormir avec Ronflex','2021-10-27 20:13:00',4,'2021-10-27 17:13:00',3,'ceci est la description de la sortie'),
    (6,1,1,4,'Speed dating avec Lipoutou','2023-10-27 20:13:00',4,'2023-10-15 17:13:00',3,'ceci est la description de la sortie'),
    (6,1,2,4,'Retro gamming avec Evoli','2022-11-30 20:13:00',4,'2022-11-21 17:13:00',3,'ceci est la description de la sortie'),
    (5,1,2,3,'Dîner avec Caninos','2022-11-25 20:13:00',4,'2022-11-15 17:13:00',3,'ceci est la description de la sortie'),
    (12,2,7,5,'Soirée bisous avec Excellangue','2021-10-25 20:13:00',4,'2021-09-27 17:13:00',3,'ceci est la description de la sortie'),
    (12,2,5,5,'Halloween avec Mimigui','2022-10-01 20:13:00',4,'2022-09-25 17:13:00',3,'ceci est la description de la sortie'),
    (9,2,6,6,'Déjeuner avec Canarticho','2022-10-25 20:13:00',4,'2022-10-31 17:13:00',3,'ceci est la description de la sortie'),
    (9,2,6,6,'Bal des pompiers avec Salamèche','2022-10-25 20:13:00',4,'2022-10-31 17:13:00',3,'ceci est la description de la sortie'),
    (1,3,7,2,'Soirée retour dans le passé','2022-09-18 17:13:00',10,'2021-09-17 17:13:00',1,'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'),
    (2,3,3,1,'Soirée Past pasta','2022-10-21 17:13:00',10,'2022-10-20 16:18:00',1,'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'),
    (10,2,7,6,'Soirée Past Passée','2021-09-29 17:13:00',10,'2021-09-28 17:13:00',1,'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'),
    (7,1,2,4,'Past party','2022-10-21 18:13:00',10,'2022-10-20 18:30:00',1,'Lorem ipsum dolor sit amet, consectetur adipiscing elit.')
;

INSERT INTO ville (nom, code_postal)
VALUES
    ('Lyon','69000'),
    ('Villeurbanne','69100'),
    ('Nantes','44000'),
    ('Savenay','44260'),
    ('Paris','75000'),
    ('Vincennes','94300')
;

INSERT INTO lieu (ville_id, nom, rue, latitude, longitude)
VALUES

    (6,'Liberty II','rue des anges', 48.847759, 2.4394969),
    (5,'Maison des cookies','rue titou', 48.853873, 2.346954),
    (4,'Espace Symfony','bd des codeurs', 47.319974, -1.869392),
    (3,'Maison des Digimons','rue Alex LeFa', 47.223299, -1.547699),
    (2,'cafe des arts','rue Paul Taylor', 45.666846, 4.972687),
    (1,'place du belvedere','boulevard des féministes', 45.761775, 4.825058)
;

INSERT INTO etat (libelle)
VALUES
    ('Créée'),
    ('Ouverte'),
    ('Clôturée'),
    ('Activité en cours'),
    ('Passée'),
    ('Annulée'),
    ('Archivée')
;

INSERT INTO user_sortie (user_id, sortie_id)
VALUES
    (2,2),
    (2,3),
    (3,4),
    (2,5),
    (1,6),
    (1,7),
    (3,1),
    (4,1),
    (11,16),
    (10,16),
    (9,16),
    (9,17),
    (10,17),
    (10,18),
    (12,18),
    (7,12),
    (6,15)
;

