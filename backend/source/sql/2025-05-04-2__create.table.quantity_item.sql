create table oml_quantity_item (
    `id` int(11) unsigned not null auto_increment primary key,
    `quantityId` int unsigned not null,
    `name` varchar(255) not null,
    `position` int(11) unsigned not null,

    constraint `oml_quantity_item__quantityId` foreign key (`quantityId`) references `oml_quantity` (`id`) on delete cascade,
    constraint `oml_quantity_item__name` unique (`quantityId`, `name`),
    constraint `oml_quantity_item__position` unique (`quantityId`, `position`)
);