create table oml_datasheet_medias (
    `id` int unsigned not null auto_increment primary key,
    `name` varchar(255) not null,
    `description` varchar(8192) not null,
    `path` varchar(2048) not null
);