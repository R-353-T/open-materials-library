create table oml__enumerator (
    `id` int(11) unsigned not null auto_increment primary key,
    `name` varchar(255) not null,
    `description` varchar(8192) not null,
    
    `typeId` int(11) unsigned not null,
    `quantityId` int(11) unsigned,

    constraint `oml_enumerator__name` unique (`name`),
    constraint `oml_enumerator__quantityId` foreign key (`quantityId`) references `oml__quantity` (`id`) on delete cascade,
    constraint `oml_enumerator__typeId` foreign key (`typeId`) references `oml__type` (`id`)
);