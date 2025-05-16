create table oml_migration (
    `id` int unsigned not null auto_increment primary key,
    `name` varchar(1024) not null,
    `migratedAt` timestamp not null default current_timestamp,

    constraint `oml_migrations__name` unique (`name`)
);