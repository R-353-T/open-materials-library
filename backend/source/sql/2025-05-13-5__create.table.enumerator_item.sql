create table oml_enumerator_item (
    `id` int(11) unsigned not null auto_increment primary key,
    `position` int(11) unsigned not null,
    `text` varchar(255) not null,
    `number` decimal(10, 6) not null,
    
    `enumeratorId` int(11) unsigned not null,
    `quantityItemId` int(11) unsigned,

    constraint `oml_enumerator_item__enumeratorId` foreign key (`enumeratorId`) references `oml_enumerator` (`id`) on delete cascade,
    constraint `oml_enumerator_item__quantityItemId` foreign key (`quantityItemId`) references `oml_quantity_item` (`id`) on delete cascade,
    constraint `oml_enumerator_item__value` unique (`enumeratorId`, `text`, `number`),
    constraint `oml_enumerator_item__position` unique (`enumeratorId`, `position`)
);