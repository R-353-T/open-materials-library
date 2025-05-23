create table oml__category (
    `createdAt` timestamp not null default current_timestamp,
    `updatedAt` timestamp not null default current_timestamp on update current_timestamp,
    `id` int(11) unsigned not null auto_increment primary key,

    `position` int(11) unsigned not null,
    `name` varchar(255) not null,
    `description` varchar(8192) not null,
    `parentId` int(11) unsigned,

    constraint `oml__category__parentId` foreign key (`parentId`) references `oml__category` (`id`),
    constraint `oml__category__name` unique (`parentId`, `name`),
    constraint `oml__category__position` unique (`parentId`, `position`)
);