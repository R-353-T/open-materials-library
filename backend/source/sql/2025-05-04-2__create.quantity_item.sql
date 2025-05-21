create table oml__quantity_item (
    `createdAt` timestamp not null default current_timestamp,
    `updatedAt` timestamp not null default current_timestamp on update current_timestamp,
    `id` int(11) unsigned not null auto_increment primary key,

    `value` varchar(255) not null,
    `position` int(11) unsigned not null,
    `quantityId` int(11) unsigned not null,

    constraint `oml__quantity_item__quantityId` foreign key (`quantityId`) references `oml__quantity` (`id`) on delete cascade,
    constraint `oml__quantity_item__value` unique (`quantityId`, `value`),
    constraint `oml__quantity_item__position` unique (`quantityId`, `position`)
);