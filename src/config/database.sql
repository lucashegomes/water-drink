CREATE DATABASE waterdrinken;

CREATE USER 'waterdrinken'@'localhost' IDENTIFIED BY 'waterdrinken';
GRANT ALL PRIVILEGES ON waterdrinken.* TO 'waterdrinken'@'localhost';

use waterdrinken;

CREATE TABLE USER (
    ID_USER_USR int(11) not null auto_increment primary key, 
    ST_EMAIL_USR varchar(255) not null,
    ST_NAME_USR varchar(255) not null,
    ST_PASSWORD_USR varchar(255) not null,
    ST_TOKEN_USR varchar(255) default null,
    DT_LASTLOGIN_USR timestamp not null default CURRENT_TIMESTAMP
);

CREATE TABLE DRINKS_BY_USER (
    ID_DRINKS_DKS INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    ID_USER_USR INT(11) NOT NULL, 
     INT(11) DEFAULT NULL,
    DT_REGISTER_DKS TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT FK_DRINKSBYUSER_IDUSER FOREIGN KEY(ID_USER_USR) REFERENCES USER(ID_USER_USR)
);