create table oml__media (
    `createdAt` timestamp not null default current_timestamp,
    `updatedAt` timestamp not null default current_timestamp on update current_timestamp,
    `id` int(11) unsigned not null auto_increment primary key,

    `name` varchar(255) not null,
    `description` varchar(8192) not null,
    `path` varchar(2048) not null,

    constraint `oml__media__name` unique (`name`),
    constraint `oml__media__path` unique (`path`)
);