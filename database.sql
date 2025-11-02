-- Создание базы данных для управления магазинами
-- Включаем поддержку внешних ключей
PRAGMA foreign_keys = ON;

-- Таблица магазинов
CREATE TABLE IF NOT EXISTS shop (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    address TEXT NOT NULL
);

-- Таблица клиентов
CREATE TABLE IF NOT EXISTS client (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    phone TEXT NOT NULL
);

-- Таблица продуктов
CREATE TABLE IF NOT EXISTS product (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    price REAL NOT NULL CHECK(price > 0),
    count INTEGER NOT NULL CHECK(count >= 0),
    shop_id INTEGER NOT NULL,
    FOREIGN KEY (shop_id) REFERENCES shop(id) ON DELETE CASCADE
);

-- Таблица заказов
CREATE TABLE IF NOT EXISTS "order" (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    client_id INTEGER NOT NULL,
    shop_id INTEGER NOT NULL,
    FOREIGN KEY (client_id) REFERENCES client(id) ON DELETE CASCADE,
    FOREIGN KEY (shop_id) REFERENCES shop(id) ON DELETE CASCADE
);

-- Таблица связи заказов и продуктов
CREATE TABLE IF NOT EXISTS order_product (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    price REAL NOT NULL CHECK(price > 0),
    quantity INTEGER NOT NULL DEFAULT 1 CHECK(quantity > 0),
    FOREIGN KEY (order_id) REFERENCES "order"(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE
);

-- Заполнение таблицы магазинов (минимум 5 записей)
INSERT INTO shop (name, address) VALUES
    ('Магазин "Продукты 24"', 'ул. Ленина, д. 10'),
    ('Супермаркет "Пятерочка"', 'пр. Мира, д. 45'),
    ('Магазин "Свежесть"', 'ул. Советская, д. 23'),
    ('Гипермаркет "Лента"', 'ул. Кирова, д. 88'),
    ('Магазин "У дома"', 'ул. Пушкина, д. 5'),
    ('Магазин "Эконом"', 'ул. Гагарина, д. 15');

-- Заполнение таблицы клиентов (минимум 5 записей)
INSERT INTO client (name, phone) VALUES
    ('Иванов Иван Иванович', '+7-900-123-45-67'),
    ('Петрова Мария Сергеевна', '+7-901-234-56-78'),
    ('Сидоров Петр Александрович', '+7-902-345-67-89'),
    ('Козлова Анна Дмитриевна', '+7-903-456-78-90'),
    ('Смирнов Алексей Николаевич', '+7-904-567-89-01'),
    ('Васильева Елена Игоревна', '+7-905-678-90-12');

-- Заполнение таблицы продуктов (минимум 5 записей)
-- Продукты для магазина 1
INSERT INTO product (name, price, count, shop_id) VALUES
    ('Хлеб белый', 45.50, 50, 1),
    ('Молоко 1л', 85.00, 30, 1),
    ('Яйца 10шт', 120.00, 40, 1),
    ('Масло сливочное', 250.00, 20, 1),
    ('Сахар 1кг', 95.00, 25, 1);

-- Продукты для магазина 2
INSERT INTO product (name, price, count, shop_id) VALUES
    ('Сыр российский', 450.00, 15, 2),
    ('Колбаса докторская', 380.00, 20, 2),
    ('Кефир 1л', 75.00, 35, 2),
    ('Творог 200г', 95.00, 25, 2),
    ('Сметана 500г', 145.00, 18, 2);

-- Продукты для магазина 3
INSERT INTO product (name, price, count, shop_id) VALUES
    ('Яблоки 1кг', 120.00, 100, 3),
    ('Бананы 1кг', 95.00, 80, 3),
    ('Апельсины 1кг', 150.00, 60, 3),
    ('Картофель 1кг', 35.00, 150, 3),
    ('Морковь 1кг', 45.00, 120, 3);

-- Продукты для магазина 4
INSERT INTO product (name, price, count, shop_id) VALUES
    ('Рис 1кг', 110.00, 50, 4),
    ('Гречка 1кг', 135.00, 45, 4),
    ('Макароны 500г', 65.00, 70, 4),
    ('Мука 1кг', 55.00, 60, 4),
    ('Соль 1кг', 25.00, 100, 4);

-- Заполнение таблицы заказов (минимум 5 записей)
INSERT INTO "order" (created_at, client_id, shop_id) VALUES
    ('2025-10-25 10:30:00', 1, 1),
    ('2025-10-26 14:15:00', 2, 2),
    ('2025-10-27 09:45:00', 3, 3),
    ('2025-10-28 16:20:00', 4, 1),
    ('2025-10-29 11:00:00', 5, 2),
    ('2025-10-29 13:30:00', 1, 3);

-- Заполнение таблицы связи заказов и продуктов (минимум 5 записей)
-- Заказ 1: клиент 1 в магазине 1
INSERT INTO order_product (order_id, product_id, price, quantity) VALUES
    (1, 1, 45.50, 2),   -- 2 хлеба
    (1, 2, 85.00, 1),   -- 1 молоко
    (1, 3, 120.00, 1);  -- 1 упаковка яиц

-- Заказ 2: клиент 2 в магазине 2
INSERT INTO order_product (order_id, product_id, price, quantity) VALUES
    (2, 6, 450.00, 1),  -- 1 сыр
    (2, 7, 380.00, 2),  -- 2 колбасы
    (2, 8, 75.00, 2);   -- 2 кефира

-- Заказ 3: клиент 3 в магазине 3
INSERT INTO order_product (order_id, product_id, price, quantity) VALUES
    (3, 11, 120.00, 3), -- 3 кг яблок
    (3, 12, 95.00, 2),  -- 2 кг бананов
    (3, 14, 35.00, 5);  -- 5 кг картофеля

-- Заказ 4: клиент 4 в магазине 1
INSERT INTO order_product (order_id, product_id, price, quantity) VALUES
    (4, 4, 250.00, 1),  -- 1 масло
    (4, 5, 95.00, 2);   -- 2 кг сахара

-- Заказ 5: клиент 5 в магазине 2
INSERT INTO order_product (order_id, product_id, price, quantity) VALUES
    (5, 9, 95.00, 3),   -- 3 творога
    (5, 10, 145.00, 2); -- 2 сметаны

-- Заказ 6: клиент 1 в магазине 3
INSERT INTO order_product (order_id, product_id, price, quantity) VALUES
    (6, 13, 150.00, 2), -- 2 кг апельсинов
    (6, 15, 45.00, 3);  -- 3 кг моркови
