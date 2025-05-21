create table oml__enumerator (
    `createdAt` timestamp not null default current_timestamp,
    `updatedAt` timestamp not null default current_timestamp on update current_timestamp,
    `id` int(11) unsigned not null auto_increment primary key,
    
    `name` varchar(255) not null,
    `description` varchar(8192) not null,
    `typeId` int(11) unsigned not null,
    `quantityId` int(11) unsigned,

    constraint `oml__enumerator__name` unique (`name`),
    constraint `oml__enumerator__quantityId` foreign key (`quantityId`) references `oml__quantity` (`id`) on delete cascade,
    constraint `oml__enumerator__typeId` foreign key (`typeId`) references `oml__type` (`id`)
);