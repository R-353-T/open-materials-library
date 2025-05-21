create table oml__datasheet_category (
    `id` int(11) unsigned not null auto_increment primary key,
    `name` varchar(255) not null,
    `description` varchar(8192) not null,
    `parentId` int(11) unsigned,

    constraint `oml_datasheet_category__name` unique (`name`),
    constraint `oml_datasheet_category__parentId` foreign key (`parentId`) references `oml__datasheet_category` (`id`)
);