create table oml__migration (
    `createdAt` timestamp not null default current_timestamp,
    `updatedAt` timestamp not null default current_timestamp on update current_timestamp,
    `id` int(11) unsigned not null auto_increment primary key,

    `name` varchar(1024) not null,

    constraint `oml__migration__name` unique (`name`)
);