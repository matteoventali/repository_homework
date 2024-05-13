drop database if exists champions_league;
create database champions_league;
use champions_league;

create table UTENTI(
    id          integer auto_increment not null,
    nome        varchar(50) not null,
    cognome     varchar(50) not null,
    mail        varchar(50) not null,
    password    char(64) not null,
    tipologia   char(1) default 'U',
    primary key (id),
    unique (mail)
);

insert into UTENTI (nome, cognome, mail, password, tipologia) values ('admin', 'admin', 'admin@admin.it', SHA2('admin', 256), 'A');