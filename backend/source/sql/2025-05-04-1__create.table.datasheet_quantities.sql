create table oml_datasheet_quatities (
    `id` int unsigned not null auto_increment primary key,
    `name` varchar(255) not null,
    `description` varchar(8192) not null,

    constraint `oml_datasheet_quatities__name` unique (`name`)
);