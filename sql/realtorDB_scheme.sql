SET client_encoding = 'UTF8';

CREATE TYPE district AS enum (
	'Василеостровский',
	'Выборгский',
	'Калининский',
	'Кировский',
	'Колпинский',
	'Красногвардейский',
	'Красносельский',
	'Кронштадтский',
	'Курортный',
	'Московский',
	'Невский',
	'Петроградский',
	'Петродворцовый',
	'Приморский',
	'Пушкинский',
	'Фрунзенский',
	'Центральный'
);

CREATE TYPE user_type AS enum ('admin','realtor');
CREATE TYPE realty_type AS enum ('Квартира','Комната','Дом','Участок');
CREATE TYPE material AS enum ('Кирпич','Блоки','Панель','Дерево','Монолит','Другое');

CREATE TABLE address (
	address_id serial PRIMARY KEY,
	flat_number int,
	building_number int NOT NULL,
	corpus_number int,
	street varchar(70) NOT NULL,
	district district
);

CREATE TABLE personal_data (
	personal_data_id serial PRIMARY KEY,
	surname varchar(50) NOT NULL,
	name varchar(50) NOT NULL,
	father_name varchar(50),
	phone varchar(30),
	email varchar(50) UNIQUE, -- по этому полю будет проверяться, существует ли этот владелец в БД
	date_of_birth date
);

CREATE TABLE realtor (
	realtor_id serial PRIMARY KEY,
	login varchar(50) NOT NULL,
	password varchar(255) NOT NULL,
	personal_data_id int REFERENCES personal_data(personal_data_id) UNIQUE,
	firm_id int,
	is_manager boolean DEFAULT false,
	address_id int REFERENCES address(address_id),
	creation_time timestamp,
	user_type user_type DEFAULT 'realtor'
);

CREATE TABLE firm (
	director_id int REFERENCES realtor(realtor_id) PRIMARY KEY,
	name varchar(100) NOT NULL,
	address_id int REFERENCES address(address_id),
	phone varchar(30),
	email varchar(50)
);

ALTER TABLE realtor ADD FOREIGN KEY (firm_id) REFERENCES firm(director_id);

CREATE TYPE subway AS enum (
	'Автово',
	'Адмиралтейская',
	'Академическая',
	'Балтийская',
	'Бухарестская',
	'Василеостровская',
	'Владимирская',
	'Волковская',
	'Выборгская',
	'Горьковская',
	'Гостиный двор',
	'Гражданский проспект',
	'Девяткино',
	'Достоевская',
	'Елизаровская',
	'Звёздная',
	'Звенигородская',
	'Кировский завод',
	'Комендантский проспект',
	'Крестовский остров',
	'Купчино',
	'Ладожская',
	'Ленинский проспект',
	'Лесная',
	'Лиговский проспект',
	'Ломоносовская',
	'Маяковская',
	'Международная',
	'Московская',
	'Московские ворота',
	'Нарвская',
	'Невский проспект',
	'Новочеркасская',
	'Обводный канал',
	'Обухово',
	'Озерки',
	'Парк Победы',
	'Парнас',
	'Петроградская',
	'Пионерская',
	'Площадь Александра Невского 1',
	'Площадь Александра Невского 2',
	'Площадь Восстания',
	'Площадь Ленина',
	'Площадь Мужества',
	'Политехническая',
	'Приморская',
	'Пролетарская',
	'Проспект Большевиков',
	'Проспект Ветеранов',
	'Проспект Просвещения',
	'Пушкинская',
	'Рыбацкое',
	'Садовая',
	'Сенная площадь',
	'Спасская',
	'Спортивная',
	'Старая Деревня',
	'Технологический институт 1',
	'Технологический институт 2',
	'Удельная',
	'Улица Дыбенко',
	'Фрунзенская',
	'Чёрная речка',
	'Чернышевская',
	'Чкаловская',
	'Электросила'
);

CREATE TABLE realty (
	is_removed boolean DEFAULT false,
	address_id int REFERENCES address(address_id) PRIMARY KEY, -- одна и та же собственность не может быть в продаже дважды
	realtor_id int NOT NULL REFERENCES realtor(realtor_id),
	money numeric(20,2),
	offer_creation_time timestamp NOT NULL,
	is_sold boolean NOT NULL DEFAULT false, -- продана ли собственность
	views int DEFAULT 0,
	hide boolean DEFAULT false NOT NULL,
	description text,

	square numeric(10,2),
	floor smallint,
	number_of_floors smallint CHECK (number_of_floors >= floor),
	number_of_rooms smallint,
	with_decoration boolean DEFAULT false,

	subway subway,
	time_from_subway smallint, -- удаленность от метро по времени
	realty_type realty_type NOT NULL,

	is_new boolean DEFAULT true, -- вид жилья "вторичное" и "новое"
	has_electricity boolean DEFAULT true,
	has_gas boolean DEFAULT true,

	building_age smallint, -- возраст здания
	apartment_complex varchar(100),
	building_material material,

	around_square numeric(10,2) -- величина участка вокруг
);

CREATE TABLE propreitor (
	propreitor_id int REFERENCES personal_data(personal_data_id) PRIMARY KEY,
	number_of_reviews int NOT NULL DEFAULT 0,
	number_of_bad_reviews int NOT NULL DEFAULT 0  -- последние два показателя определят уровень надёжности
);

CREATE TABLE realty_propreitor (
	propreitor_id int REFERENCES propreitor(propreitor_id),
	realty_id int REFERENCES realty(address_id),
	PRIMARY KEY(propreitor_id, realty_id)
);

CREATE TABLE logs (
	log_id serial PRIMARY KEY,
	address_id int REFERENCES address(address_id) NOT NULL,
	realtor_id int REFERENCES realtor(realtor_id) NOT NULL,
	propreitor_id int REFERENCES propreitor(propreitor_id) NOT NULL,
	money numeric(20,2) NOT NULL,
	offer_end_time timestamp NOT NULL
);