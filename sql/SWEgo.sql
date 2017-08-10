-- Project:	SWEgo
-- Author:	Bertolini Luca
--			Bonolo Marco
--			Carlin Mauro
--			Tintorri Nicola

-- Create database
create database swego;

-- Use clause
use swego;

-- Table: users
create table if not exists users (
	id int auto_increment primary key,
	groupname varchar(100) not null unique,
	projectname varchar(100) not null unique,
	password varchar(100) not null,
	type varchar(20) not null
)engine=INNODB;

-- Table: login
create table if not exists login (
	userid int not null,
	date_time datetime not null,
	primary key(userid, date_time),
	foreign key(userid) references users(id) on delete cascade on update cascade
)engine=INNODB;

-- Table: usecase
create table if not exists usecase (
	id int auto_increment primary key,
	usecaseid varchar(50) not null,
	name varchar(200) not null,
	description varchar(1000) not null,
	precondition varchar(1000) not null,
	postcondition varchar(1000) not null,
	mainscenario varchar(1000) not null,
	alternativescenario varchar(1000),
	generalization boolean not null,
	parent int,
	projectid int,
	foreign key(parent) references usecase(id) on delete cascade on update cascade,
	foreign key(projectid) references users(id) on delete cascade on update cascade
)engine=INNODB;

-- Table: sources
create table if not exists sources (
	id int auto_increment primary key,
	name varchar(100) not null,
	description varchar(1000) not null,
	projectid int,
	foreign key(projectid) references users(id) on delete cascade on update cascade
)engine=INNODB;

-- Table: requirements
create table if not exists requirements (
	id int auto_increment primary key,
	requirementid varchar(50) not null,
	description varchar(1000) not null,
	type varchar(100) not null,
	importance varchar(100) not null,
	satisfied varchar(100) not null,
	parent int,
	source int,
	projectid int,
	foreign key(parent) references requirements(id) on delete cascade on update cascade,
	foreign key(source) references sources(id) on delete cascade on update cascade,
	foreign key(projectid) references users(id) on delete cascade on update cascade
)engine=INNODB;

-- Table: actors
create table if not exists actors (
	id int auto_increment primary key,
	name varchar(100) not null,
	description varchar(1000) not null,
	projectid int,
	foreign key(projectid) references users(id) on delete cascade on update cascade
)engine=INNODB;

-- Table: usecaseactors
create table if not exists usecaseactors (
	usecaseid int,
	actorsid int,
	primary key(usecaseid, actorsid),
	foreign key(usecaseid) references usecase(id) on delete cascade on update cascade,
	foreign key(actorsid) references actors(id) on delete cascade on update cascade
)engine=INNODB;

-- Table usecaseextensions
create table if not exists usecaseextensions (
	usecaseid int,
	extendedusecaseid int,
	primary key(usecaseid, extendedusecaseid),
	foreign key(usecaseid) references usecase(id) on delete cascade on update cascade,
	foreign key(extendedusecaseid) references usecase(id) on delete cascade on update cascade
)engine=INNODB;

-- Table usecaseinclusions
create table if not exists usecaseinclusions (
	usecaseid int,
	includedusecaseid int,
	primary key(usecaseid, includedusecaseid),
	foreign key(usecaseid) references usecase(id) on delete cascade on update cascade,
	foreign key(includedusecaseid) references usecase(id) on delete cascade on update cascade
)engine=INNODB;

-- Table: usecaserequirements
create table if not exists usecaserequirements (
	usecaseid int,
	requirementid int,
	primary key(usecaseid, requirementid),
	foreign key(usecaseid) references usecase(id) on delete cascade on update cascade,
	foreign key(requirementid) references requirements(id) on delete cascade on update cascade
)engine=INNODB;

-- Dump
insert into users values (1, 'admin', 'admin', 'bb017ba90f714a59da19ebcfe41ff68db41c5ab40e4eb0d17f40b2db3cd4cbbf', 'admin');