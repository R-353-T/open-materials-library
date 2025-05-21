create table oml__type_input (
    `id` int(11) unsigned not null primary key,

    `name` varchar(255) not null,

    constraint `oml__type_input__name` unique (`name`)
);