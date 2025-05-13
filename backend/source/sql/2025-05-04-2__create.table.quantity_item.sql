create table oml_quantity_item (
    `id` int(11) unsigned not null auto_increment primary key,
    `value` varchar(255) not null,
    `position` int(11) unsigned not null,
    
    `quantityId` int unsigned not null,

    constraint `oml_quantity_item__value` unique (`quantityId`, `value`),
    constraint `oml_quantity_item__position` unique (`quantityId`, `position`),
    constraint `oml_quantity_item__quantityId` foreign key (`quantityId`) references `oml_quantity` (`id`) on delete cascade
);