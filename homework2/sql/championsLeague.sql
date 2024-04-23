drop database if exists champions_league;
create database champions_league;
use champions_league;

create table SQUADRE(
    id integer auto_increment not null,
    nome varchar(50) not null,
    unique (nome),
    primary key(id)
);

