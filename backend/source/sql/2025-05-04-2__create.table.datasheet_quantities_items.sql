create table oml_datasheet_quantities_items (
    `id` int unsigned not null auto_increment primary key,
    `quantityId` int unsigned not null,
    `name` varchar(255) not null,
    `position` int unsigned not null,

    constraint `oml_datasheet_quatities_items__quantityId` foreign key (`quantityId`) references `oml_datasheet_quatities` (`id`) on delete cascade,
    constraint `oml_datasheet_quatities_items__name` unique (`quantityId`, `name`)
);