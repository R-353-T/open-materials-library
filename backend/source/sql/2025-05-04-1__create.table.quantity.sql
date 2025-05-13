create table oml_quantity (
    `id` int(11) unsigned not null auto_increment primary key,
    `name` varchar(255) not null,
    `description` varchar(8192) not null,

    constraint `oml_quatity__name` unique (`name`)
);