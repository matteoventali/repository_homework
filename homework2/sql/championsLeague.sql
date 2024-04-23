drop database if exists champions_league;
create database champions_league;
use champions_league;

create table SQUADRE(
    id      integer auto_increment not null,
    nome    varchar(50) not null,
    unique (nome),
    primary key(id)
);

create table PARTITE(
    id              integer auto_increment not null,
    data            DATE not null,
    squadra_casa    integer not null,
    squadra_ospite  integer not null,
    gol_casa        integer unsigned not null,
    gol_ospite      integer unsigned not null,
    primary key (id),
    unique (squadra_casa, squadra_ospite, data),
    constraint fk_partite_squadra_casa foreign key (squadra_casa)
            references SQUADRE(id),
    constraint fk_partite_squadra_ospite foreign key (squadra_ospite)
            references SQUADRE(id)
);

create table UTENTI(
    id          integer auto_increment not null,
    nome        varchar(50) not null,
    cognome     varchar(50) not null,
    mail        varchar(50) not null,
    password    char(64) not null,
    primary key (id),
    unique (mail)
);

insert into SQUADRE(nome) values ('Real Madrid');
insert into SQUADRE(nome) values ('Inter');
insert into SQUADRE(nome) values ('Milan');
insert into SQUADRE(nome) values ('Manchester United');
insert into SQUADRE(nome) values ('Barcellona');
insert into SQUADRE(nome) values ('Juventus');
insert into SQUADRE(nome) values ('Bayer Monaco');
insert into SQUADRE(nome) values ('Liverpool');