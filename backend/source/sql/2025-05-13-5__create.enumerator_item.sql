create table oml__enumerator_item (
    `createdAt` timestamp not null default current_timestamp,
    `updatedAt` timestamp not null default current_timestamp on update current_timestamp,
    `id` int(11) unsigned not null auto_increment primary key,

    `position` int(11) unsigned not null,
    `enumeratorId` int(11) unsigned not null,
    `quantityItemId` int(11) unsigned,
    `text` varchar(255), -- VALUE COLUMN
    `number` decimal(10, 6), -- VALUE COLUMN

    constraint `oml__enumerator_item__enumeratorId` foreign key (`enumeratorId`) references `oml__enumerator` (`id`) on delete cascade,
    constraint `oml__enumerator_item__quantityItemId` foreign key (`quantityItemId`) references `oml__quantity_item` (`id`) on delete cascade,
    constraint `oml__enumerator_item__text` unique (`enumeratorId`, `text`),
    constraint `oml__enumerator_item__number` unique (`enumeratorId`, `number`),
    constraint `oml__enumerator_item__position` unique (`enumeratorId`, `position`)
);