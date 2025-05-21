create table oml__media (
    `id` int(11) unsigned not null auto_increment primary key,
    `name` varchar(255) not null,
    `description` varchar(8192) not null,
    `path` varchar(2048) not null,

    constraint `oml_media__name` unique (`name`),
    constraint `oml_media__path` unique (`path`)
);