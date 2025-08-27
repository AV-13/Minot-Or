create table company
(
    id              int auto_increment
        primary key,
    company_name    varchar(50) not null,
    company_siret   varchar(50) not null,
    company_contact varchar(50) not null
)
    collate = utf8mb4_unicode_ci;

create table doctrine_migration_versions
(
    version        varchar(191) not null
        primary key,
    executed_at    datetime     null,
    execution_time int          null
)
    collate = utf8mb3_unicode_ci;

create table pricing
(
    id                int auto_increment
        primary key,
    fixed_fee         double   not null,
    modification_date datetime not null,
    cost_per_km       double   not null
)
    collate = utf8mb4_unicode_ci;

create table sales_list
(
    id              int auto_increment
        primary key,
    status          enum ('pending', 'preparing_products', 'awaiting_delivery') not null comment '(DC2Type:sales_status_enum)',
    products_price  double                                                      not null,
    global_discount int                                                         not null,
    issue_date      datetime                                                    not null,
    expiration_date datetime                                                    not null,
    order_date      datetime                                                    null
)
    collate = utf8mb4_unicode_ci;

create table delivery
(
    id               int auto_increment
        primary key,
    sales_list_id    int                                                 not null,
    delivery_date    date                                                not null,
    delivery_address varchar(50)                                         not null,
    delivery_number  varchar(20)                                         not null,
    delivery_status  enum ('in_preparation', 'in_progress', 'delivered') not null comment '(DC2Type:delivery_status_enum)',
    driver_remark    longtext                                            null,
    qr_code          longtext                                            not null,
    constraint UNIQ_3781EC1033576AEB
        unique (sales_list_id),
    constraint FK_3781EC1033576AEB
        foreign key (sales_list_id) references sales_list (id)
)
    collate = utf8mb4_unicode_ci;

create table invoice
(
    id              int auto_increment
        primary key,
    sales_list_id   int        null,
    pricing_id      int        not null,
    total_amount    double     not null,
    issue_date      date       not null,
    due_date        date       not null,
    payment_status  tinyint(1) not null,
    acceptance_date date       not null,
    constraint UNIQ_9065174433576AEB
        unique (sales_list_id),
    constraint FK_9065174433576AEB
        foreign key (sales_list_id) references sales_list (id),
    constraint FK_906517448864AF73
        foreign key (pricing_id) references pricing (id)
)
    collate = utf8mb4_unicode_ci;

create index IDX_906517448864AF73
    on invoice (pricing_id);

create table supplier
(
    id               int auto_increment
        primary key,
    supplier_name    varchar(255) not null,
    supplier_address varchar(255) not null
)
    collate = utf8mb4_unicode_ci;

create table truck_cleaning
(
    id                  int auto_increment
        primary key,
    cleaning_start_date date     not null,
    cleaning_end_date   date     not null,
    observations        longtext not null
)
    collate = utf8mb4_unicode_ci;

create table user
(
    id         int auto_increment
        primary key,
    company_id int                                                                                                      not null,
    email      varchar(180)                                                                                             not null,
    roles      json                                                                                                     not null,
    password   varchar(255)                                                                                             not null,
    last_name  varchar(50)                                                                                              not null,
    first_name varchar(50)                                                                                              not null,
    role       enum ('WaitingForValidation', 'Baker', 'Sales', 'Driver', 'OrderPreparer', 'Maintenance', 'Procurement') not null comment '(DC2Type:user_role_enum)',
    constraint UNIQ_IDENTIFIER_EMAIL
        unique (email),
    constraint FK_8D93D649979B1AD6
        foreign key (company_id) references company (id)
)
    collate = utf8mb4_unicode_ci;

create table evaluate
(
    sales_list_id  int        not null,
    reviewer_id    int        not null,
    quote_accepted tinyint(1) not null,
    primary key (sales_list_id, reviewer_id),
    constraint FK_8E840A8833576AEB
        foreign key (sales_list_id) references sales_list (id),
    constraint FK_8E840A8870574616
        foreign key (reviewer_id) references user (id)
)
    collate = utf8mb4_unicode_ci;

create index IDX_8E840A8833576AEB
    on evaluate (sales_list_id);

create index IDX_8E840A8870574616
    on evaluate (reviewer_id);

create index IDX_8D93D649979B1AD6
    on user (company_id);

create table warehouse
(
    id                int auto_increment
        primary key,
    warehouse_address varchar(255) not null,
    storage_capacity  int          not null
)
    collate = utf8mb4_unicode_ci;

create table product
(
    id             int auto_increment
        primary key,
    warehouse_id   int                                                                                                    not null,
    product_name   varchar(50)                                                                                            not null,
    quantity       double                                                                                                 not null,
    net_price      double                                                                                                 not null,
    gross_price    double                                                                                                 not null,
    unit_weight    double                                                                                                 not null,
    category       enum ('flour', 'oil', 'egg', 'yeast', 'salt', 'sugar', 'butter', 'milk', 'seed', 'chocolate', 'bread') not null comment '(DC2Type:product_category_enum)',
    stock_quantity int                                                                                                    not null,
    constraint FK_D34A04AD5080ECDE
        foreign key (warehouse_id) references warehouse (id)
)
    collate = utf8mb4_unicode_ci;

create table contains
(
    sales_list_id    int not null,
    product_id       int not null,
    product_quantity int not null,
    product_discount int not null,
    primary key (sales_list_id, product_id),
    constraint FK_8EFA6A7E33576AEB
        foreign key (sales_list_id) references sales_list (id),
    constraint FK_8EFA6A7E4584665A
        foreign key (product_id) references product (id)
)
    collate = utf8mb4_unicode_ci;

create index IDX_8EFA6A7E33576AEB
    on contains (sales_list_id);

create index IDX_8EFA6A7E4584665A
    on contains (product_id);

create index IDX_D34A04AD5080ECDE
    on product (warehouse_id);

create table product_supplier
(
    product_id  int not null,
    supplier_id int not null,
    primary key (product_id, supplier_id),
    constraint FK_509A06E92ADD6D8C
        foreign key (supplier_id) references supplier (id),
    constraint FK_509A06E94584665A
        foreign key (product_id) references product (id)
)
    collate = utf8mb4_unicode_ci;

create index IDX_509A06E92ADD6D8C
    on product_supplier (supplier_id);

create index IDX_509A06E94584665A
    on product_supplier (product_id);

create table supplier_product
(
    supplier_id int not null,
    product_id  int not null,
    primary key (supplier_id, product_id),
    constraint FK_522F70B22ADD6D8C
        foreign key (supplier_id) references supplier (id)
            on delete cascade,
    constraint FK_522F70B24584665A
        foreign key (product_id) references product (id)
            on delete cascade
)
    collate = utf8mb4_unicode_ci;

create index IDX_522F70B22ADD6D8C
    on supplier_product (supplier_id);

create index IDX_522F70B24584665A
    on supplier_product (product_id);

create table truck
(
    id                  int auto_increment
        primary key,
    delivery_id         int                                            null,
    warehouse_id        int                                            not null,
    driver_id           int                                            null,
    registration_number varchar(50)                                    not null,
    truck_type          enum ('monocuve', 'porteur_palettes', 'autre') not null comment '(DC2Type:truck_category_enum)',
    is_available        tinyint(1)                                     not null,
    delivery_count      int                                            not null,
    transport_distance  double                                         not null,
    transport_fee       double                                         not null,
    constraint FK_CDCCF30A12136921
        foreign key (delivery_id) references delivery (id),
    constraint FK_CDCCF30A5080ECDE
        foreign key (warehouse_id) references warehouse (id),
    constraint FK_CDCCF30AC3423909
        foreign key (driver_id) references user (id)
)
    collate = utf8mb4_unicode_ci;

create table clean
(
    truck_cleaning_id int not null,
    truck_id          int not null,
    primary key (truck_cleaning_id, truck_id),
    constraint FK_F1B0AD491FEE3B9
        foreign key (truck_cleaning_id) references truck_cleaning (id),
    constraint FK_F1B0AD49C6957CCE
        foreign key (truck_id) references truck (id)
)
    collate = utf8mb4_unicode_ci;

create index IDX_F1B0AD491FEE3B9
    on clean (truck_cleaning_id);

create index IDX_F1B0AD49C6957CCE
    on clean (truck_id);

create table restock
(
    supplier_id               int         not null,
    truck_id                  int         not null,
    product_id                int         not null,
    supplier_product_quantity int         not null,
    order_number              varchar(20) not null,
    order_date                date        not null,
    order_status              varchar(50) not null,
    primary key (supplier_id, truck_id, product_id),
    constraint FK_33B621E82ADD6D8C
        foreign key (supplier_id) references supplier (id),
    constraint FK_33B621E84584665A
        foreign key (product_id) references product (id),
    constraint FK_33B621E8C6957CCE
        foreign key (truck_id) references truck (id)
)
    collate = utf8mb4_unicode_ci;

create index IDX_33B621E82ADD6D8C
    on restock (supplier_id);

create index IDX_33B621E84584665A
    on restock (product_id);

create index IDX_33B621E8C6957CCE
    on restock (truck_id);

create index IDX_CDCCF30A12136921
    on truck (delivery_id);

create index IDX_CDCCF30A5080ECDE
    on truck (warehouse_id);

create index IDX_CDCCF30AC3423909
    on truck (driver_id);

