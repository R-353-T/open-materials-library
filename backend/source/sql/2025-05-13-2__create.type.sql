create table oml__type (
    `id` int(11) unsigned not null primary key,

    `name` varchar(255) not null,
    `column` varchar(255) not null,
    `inputId` int(11) unsigned not null,

    constraint `oml__type__inputId` foreign key (`inputId`) references `oml__type_input` (`id`) on delete cascade,
    constraint `oml__type__name` unique (`name`)
);