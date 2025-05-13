create table oml_type (
    `id` int(11) unsigned not null primary key,
    `name` varchar(255) not null,
    `column` varchar(255) not null,
    
    `inputId` int(11) unsigned not null,

    constraint `oml_type__name` unique (`name`),
    constraint `oml_type__inputId` foreign key (`inputId`) references `oml_type_input`(`id`) on delete cascade
);