--
-- PostgreSQL database dump
--

-- Dumped from database version 13.5 (Debian 13.5-1.pgdg110+1)
-- Dumped by pg_dump version 13.5

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: databases; Type: DATABASE; Schema: -; Owner: postgres
--

CREATE DATABASE databases WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE = 'en_US.utf8';


ALTER DATABASE databases OWNER TO postgres;

\connect databases

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: collect_user_carrier_journal_on_insert(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.collect_user_carrier_journal_on_insert() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
    lastUserCarrierJournalEntry integer;
BEGIN
    SELECT id
    INTO lastUserCarrierJournalEntry
    FROM users_career_journal
    WHERE user_id = NEW.id
    ORDER BY id DESC
    LIMIT 1;

    IF
        (lastUserCarrierJournalEntry > 0)
    THEN
        UPDATE users_career_journal
        SET end_datetime = NOW()::timestamp(0)
        WHERE id = lastUserCarrierJournalEntry;
    END IF;

    INSERT INTO users_career_journal (user_id, department_id, salary, position)
    VALUES (NEW.id, NULL, NEW.salary, NEW.position);
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.collect_user_carrier_journal_on_insert() OWNER TO postgres;

--
-- Name: collect_user_carrier_journal_on_update(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.collect_user_carrier_journal_on_update() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
    lastDepartmentId                    INTEGER;
    DECLARE lastUserCarrierJournalEntry INTEGER;
BEGIN
    SELECT department_id
    INTO lastDepartmentId
    FROM user_department
    WHERE user_id = NEW.id
    ORDER BY department_id DESC
    LIMIT 1;

    IF (NEW.position != OLD.position OR NEW.salary != OLD.salary) THEN
        SELECT id
        INTO lastUserCarrierJournalEntry
        FROM users_career_journal
        WHERE user_id = NEW.id
        ORDER BY id DESC
        LIMIT 1;

        IF (lastUserCarrierJournalEntry > 0) THEN
            UPDATE users_career_journal SET end_datetime = NOW()::timestamp(0) WHERE id = lastUserCarrierJournalEntry;
        END IF;

        INSERT INTO users_career_journal (user_id, department_id, salary, position)
        VALUES (NEW.id, lastDepartmentId, NEW.salary, NEW.position);
    END IF;
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.collect_user_carrier_journal_on_update() OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: departments; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.departments (
    id integer NOT NULL,
    name text NOT NULL,
    head_id integer NOT NULL,
    description text
);


ALTER TABLE public.departments OWNER TO postgres;

--
-- Name: departments_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.departments_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.departments_id_seq OWNER TO postgres;

--
-- Name: departments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.departments_id_seq OWNED BY public.departments.id;


--
-- Name: sql_query_history; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sql_query_history (
    id integer NOT NULL,
    user_id integer NOT NULL,
    sql_query text,
    execution_datetime timestamp without time zone DEFAULT (now())::timestamp(0) without time zone
);


ALTER TABLE public.sql_query_history OWNER TO postgres;

--
-- Name: sql_history_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sql_history_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sql_history_id_seq OWNER TO postgres;

--
-- Name: sql_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sql_history_id_seq OWNED BY public.sql_query_history.id;


--
-- Name: user_department; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.user_department (
    id integer NOT NULL,
    user_id integer,
    department_id integer
);


ALTER TABLE public.user_department OWNER TO postgres;

--
-- Name: user_department_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.user_department_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user_department_id_seq OWNER TO postgres;

--
-- Name: user_department_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.user_department_id_seq OWNED BY public.user_department.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id integer NOT NULL,
    login character varying(255) NOT NULL,
    hashed_password character varying(255) NOT NULL,
    email text NOT NULL,
    name text,
    last_name text,
    patronymic text,
    is_admin boolean DEFAULT false,
    path_to_avatar text,
    "position" text,
    phone text,
    salary integer
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Name: users_career_journal; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users_career_journal (
    id integer NOT NULL,
    user_id integer,
    department_id integer,
    salary integer,
    "position" text,
    start_datetime timestamp without time zone DEFAULT (now())::timestamp(0) without time zone,
    end_datetime timestamp without time zone
);


ALTER TABLE public.users_career_journal OWNER TO postgres;

--
-- Name: users_career_journal_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_career_journal_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_career_journal_id_seq OWNER TO postgres;

--
-- Name: users_career_journal_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_career_journal_id_seq OWNED BY public.users_career_journal.id;


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: departments id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.departments ALTER COLUMN id SET DEFAULT nextval('public.departments_id_seq'::regclass);


--
-- Name: sql_query_history id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sql_query_history ALTER COLUMN id SET DEFAULT nextval('public.sql_history_id_seq'::regclass);


--
-- Name: user_department id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_department ALTER COLUMN id SET DEFAULT nextval('public.user_department_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: users_career_journal id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users_career_journal ALTER COLUMN id SET DEFAULT nextval('public.users_career_journal_id_seq'::regclass);


--
-- Data for Name: departments; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.departments (id, name, head_id, description) VALUES (6, 'Отдел продаж', 10, 'Одно из направлений, отвечающее за сбыт. Состоит из группы специалистов, которые ставят перед собой общие цели в продажах.');
INSERT INTO public.departments (id, name, head_id, description) VALUES (4, 'Отдел разработки', 1, 'Отдел разработки, внедрения и сопровождения информационных систем · создание, внедрение, сопровождение и развитие информационных ресурсов (сайтов)');
INSERT INTO public.departments (id, name, head_id, description) VALUES (7, 'Отдел маркетинга', 19, 'Обеспечивает информационное взаимодействие между компанией и внешней средой.');
INSERT INTO public.departments (id, name, head_id, description) VALUES (5, 'Руководство', 12, 'Команда, которая должна закрывать все ключевые функции в компании');
INSERT INTO public.departments (id, name, head_id, description) VALUES (8, 'Отдел кадров', 14, 'Подбор персонала, подготовка штатного расписания предприятия; оформление личных дел сотрудников, выдача по требованию работников справок и копий документов; проведение операций с трудовыми книжками');
INSERT INTO public.departments (id, name, head_id, description) VALUES (3, 'Бухгалтерия', 11, 'Штатно-структурное подразделение хозяйствующего субъекта, предназначенное для аккумулирования данных о его имуществе и обязательствах.');


--
-- Data for Name: sql_query_history; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.sql_query_history (id, user_id, sql_query, execution_datetime) VALUES (2, 1, 'SELECT * from departments', '2021-11-26 16:39:04');
INSERT INTO public.sql_query_history (id, user_id, sql_query, execution_datetime) VALUES (3, 1, 'UPDATE departments SET NAME = ''Привет мир'' WHERE id = 3', '2021-11-28 08:11:28');
INSERT INTO public.sql_query_history (id, user_id, sql_query, execution_datetime) VALUES (4, 1, 'UPDATE departments SET NAME = ''Бухгалтерия'' WHERE id = 3', '2021-11-28 08:11:45');


--
-- Data for Name: user_department; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.user_department (id, user_id, department_id) VALUES (2, 7, 3);
INSERT INTO public.user_department (id, user_id, department_id) VALUES (3, 9, 4);
INSERT INTO public.user_department (id, user_id, department_id) VALUES (5, 11, 5);
INSERT INTO public.user_department (id, user_id, department_id) VALUES (6, 13, 6);
INSERT INTO public.user_department (id, user_id, department_id) VALUES (7, 15, 6);
INSERT INTO public.user_department (id, user_id, department_id) VALUES (8, 16, 4);
INSERT INTO public.user_department (id, user_id, department_id) VALUES (9, 17, 4);
INSERT INTO public.user_department (id, user_id, department_id) VALUES (10, 18, 7);
INSERT INTO public.user_department (id, user_id, department_id) VALUES (11, 19, 5);
INSERT INTO public.user_department (id, user_id, department_id) VALUES (12, 1, 5);
INSERT INTO public.user_department (id, user_id, department_id) VALUES (13, 10, 5);
INSERT INTO public.user_department (id, user_id, department_id) VALUES (14, 13, 8);


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.users (id, login, hashed_password, email, name, last_name, patronymic, is_admin, path_to_avatar, "position", phone, salary) VALUES (7, 'boris', '$2y$10$iWs1luFR2Rxnyuae17lQE.vq/6b/IgrMblKvY/OTjig5BKY3Y/I5O', 'boris@mosowell.ru', 'Борис', 'Николаев', 'Владимирович', false, '/public/avatars/61a277cca0512pexels-bm-capture-2232981.jpg', 'Помощник бухгалтера', '8 (456) 456 09 73', 400000);
INSERT INTO public.users (id, login, hashed_password, email, name, last_name, patronymic, is_admin, path_to_avatar, "position", phone, salary) VALUES (1, 'mark56716', '$2y$10$QEcodX/fzZyUVwVObic2R.J9MhqP5yCbNXRmi2zcC3KWvQdYHVbB.', 'mark@mosowell.ru', 'Марк', 'Прохоров', 'Андреевич', true, '/public/avatars/61a293e7e4a2apexels-spencer-selover-775358.jpg', 'Администратор', '8 (999) 888 23-34', 120000);
INSERT INTO public.users (id, login, hashed_password, email, name, last_name, patronymic, is_admin, path_to_avatar, "position", phone, salary) VALUES (9, 'vasya', '$2y$10$q1JPAJS6.GeT5gqqAYI58.WtRJkjrthr5wsWmj1rR/MW/ZDz4N14G', 'vasya@mosowell.ru', 'Владимир', 'Колодницкий', 'Вячесловавич', false, '/public/avatars/61a33329483dcpexels-yogendra-singh-3748221.jpg', 'Программист', '8 (961) 629 29 48', 50000);
INSERT INTO public.users (id, login, hashed_password, email, name, last_name, patronymic, is_admin, path_to_avatar, "position", phone, salary) VALUES (11, 'stas', '$2y$10$zXmwFF0VlzdwK1XOsn/p5uEgZNTWGjxjepKkKh6xXE4axxiaWvTDu', 'stas@mosowell.ru', 'Станислав', 'Евграшин', 'Викторович', false, '', 'Главный бухгалтер', '8 (456) 456 09-73', 60000);
INSERT INTO public.users (id, login, hashed_password, email, name, last_name, patronymic, is_admin, path_to_avatar, "position", phone, salary) VALUES (13, 'cc', '$2y$10$68yxs7DCgYhgkzlgPTqz7uwwGcRf4o.635mMopUlivdRSjA98nfZG', 'cc@mosowell.ru', 'Цицерон', 'Братович', 'Кайримович', false, '/public/avatars/61a334746e0a2pexels-saul-joseph-2846602.jpg', 'Телефонист', '8 (333) 484 27-44', 30000);
INSERT INTO public.users (id, login, hashed_password, email, name, last_name, patronymic, is_admin, path_to_avatar, "position", phone, salary) VALUES (14, 'nat', '$2y$10$BP4BvnV/tIk.aG71MZlXZ.MzxTZjEFy0oRWh2XuR5N60bA0UPaXJa', 'nat@mosowell.ru', 'Наталья', 'Шмитова', 'Геннадьевна', false, '/public/avatars/61a3362218032pexels-mentatdgt-1526814.jpg', 'HR', '8 (456) 456 09 73', 50000);
INSERT INTO public.users (id, login, hashed_password, email, name, last_name, patronymic, is_admin, path_to_avatar, "position", phone, salary) VALUES (12, 'jeckich', '$2y$10$sjXgWDaSdbc282dq1eCZ4.VLO.7yApOptgv2ziyp6TTILUnYFbAF2', 'jeckich@mosowell.ru', 'Евгений', 'Дубровский', 'Павлович', false, '/public/avatars/61a3364cb9aeapexels-chloe-1043474.jpg', 'Генеральный директор', '8 (555) 444 27-54', 200000);
INSERT INTO public.users (id, login, hashed_password, email, name, last_name, patronymic, is_admin, path_to_avatar, "position", phone, salary) VALUES (15, 'ar', '$2y$10$Vaw6/Ix/2FUCjhqASF9wxOUFxVHC6XMJo.LA84kPg8K.Z2mOxSIe6', 'ar@mosowell.ru', 'Арина', 'Алексеева', 'Степановна', false, '/public/avatars/61a336a36cac6pexels-juliana-stein-1898555.jpg', 'Менеджер по продажам', '8 (444) 444 27-54', 50000);
INSERT INTO public.users (id, login, hashed_password, email, name, last_name, patronymic, is_admin, path_to_avatar, "position", phone, salary) VALUES (10, 'alex', '$2y$10$PCEBZ3riGnJEYo9mg9OR/uWQ0/CdApK6nmoznLHgED.G3K639XniW', 'alex@mosowell.ru', 'Алексей', 'Абашин', 'Дмитриевич', false, '/public/avatars/61a336db6c4e2pexels-andrea-piacquadio-874158.jpg', 'Менеджер по продажам', '8 (444) 444 27-54', 40000);
INSERT INTO public.users (id, login, hashed_password, email, name, last_name, patronymic, is_admin, path_to_avatar, "position", phone, salary) VALUES (16, 'arti', '$2y$10$TRobQhvAYBmzsnb9Ew0da.XAUjIWB21vg8C1O5J7c/DgVIrC7mneW', 'arti@mosowell.ru', 'Артём', 'Комаров', 'Александрович', false, '/public/avatars/61a33743a5bdcpexels-nathan-cowley-1300402.jpg', 'Техлид', '8 (444) 444 27-54', 250000);
INSERT INTO public.users (id, login, hashed_password, email, name, last_name, patronymic, is_admin, path_to_avatar, "position", phone, salary) VALUES (17, 'egor', '$2y$10$/JpVVa7e/VSdmAyHHJi3melGvz44gQVvDvpbJhdZO3stAXh2gLBhu', 'egor@mosowell.ru', 'Егор', 'Соколов', 'Александрович', false, '/public/avatars/61a337af6c6d7pexels-andrea-piacquadio-839011.jpg', 'Менеджер проектов', '8 (444) 444 27-54', 100000);
INSERT INTO public.users (id, login, hashed_password, email, name, last_name, patronymic, is_admin, path_to_avatar, "position", phone, salary) VALUES (18, 'uaroslava', '$2y$10$AGbOYDOl0PvcH0mQZgrQNOXR5zFXG7I.2/VARdoiYH85N/5DIb4py', 'uaroslava@mosowell.ru', 'Ярослава', 'Давыдова', 'Семёновна', false, '/public/avatars/61a33827120edpexels-anastasiya-lobanovskaya-789296.jpg', 'Маркетолог', '8 (444) 444 27-54', 60000);
INSERT INTO public.users (id, login, hashed_password, email, name, last_name, patronymic, is_admin, path_to_avatar, "position", phone, salary) VALUES (19, 'andrey', '$2y$10$NvCmUjnhK.HkOTw1g62wK.Ofw/gI9hCRKOr2j39yzGeiN6zTmb2L.', 'andrey@mosowell.ru', 'Андрей', 'Сухов', 'Леонидович', false, '/public/avatars/61a3388b844c5pexels-stefan-stefancik-91227.jpg', 'Руководитель отдела маркетинга', '8 (444) 444 27-54', 70000);


--
-- Data for Name: users_career_journal; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.users_career_journal (id, user_id, department_id, salary, "position", start_datetime, end_datetime) VALUES (4, 1, NULL, 120000, 'Администратор', '2021-11-27 18:20:45', NULL);
INSERT INTO public.users_career_journal (id, user_id, department_id, salary, "position", start_datetime, end_datetime) VALUES (7, 7, 3, 400000, 'Помощник бухгалтера', '2021-11-27 18:21:59', NULL);
INSERT INTO public.users_career_journal (id, user_id, department_id, salary, "position", start_datetime, end_datetime) VALUES (8, 9, NULL, 50000, 'Программист', '2021-11-28 07:43:37', NULL);
INSERT INTO public.users_career_journal (id, user_id, department_id, salary, "position", start_datetime, end_datetime) VALUES (9, 10, NULL, 40000, 'Менеджер по продажам', '2021-11-28 07:44:36', NULL);
INSERT INTO public.users_career_journal (id, user_id, department_id, salary, "position", start_datetime, end_datetime) VALUES (10, 11, NULL, 60000, 'Главный бухгалтер', '2021-11-28 07:45:26', NULL);
INSERT INTO public.users_career_journal (id, user_id, department_id, salary, "position", start_datetime, end_datetime) VALUES (11, 12, NULL, 200000, 'Генеральный директор', '2021-11-28 07:46:41', NULL);
INSERT INTO public.users_career_journal (id, user_id, department_id, salary, "position", start_datetime, end_datetime) VALUES (12, 13, NULL, 30000, 'Телефонист', '2021-11-28 07:49:09', NULL);
INSERT INTO public.users_career_journal (id, user_id, department_id, salary, "position", start_datetime, end_datetime) VALUES (13, 14, NULL, 50000, 'HR', '2021-11-28 07:55:25', NULL);
INSERT INTO public.users_career_journal (id, user_id, department_id, salary, "position", start_datetime, end_datetime) VALUES (14, 15, NULL, 50000, 'Менеджер по продажам', '2021-11-28 07:58:28', NULL);
INSERT INTO public.users_career_journal (id, user_id, department_id, salary, "position", start_datetime, end_datetime) VALUES (15, 16, NULL, 250000, 'Техлид', '2021-11-28 08:01:08', NULL);
INSERT INTO public.users_career_journal (id, user_id, department_id, salary, "position", start_datetime, end_datetime) VALUES (16, 17, NULL, 100000, 'Менеджер проектов', '2021-11-28 08:02:56', NULL);
INSERT INTO public.users_career_journal (id, user_id, department_id, salary, "position", start_datetime, end_datetime) VALUES (17, 18, NULL, 60000, 'Маркетолог', '2021-11-28 08:04:55', NULL);
INSERT INTO public.users_career_journal (id, user_id, department_id, salary, "position", start_datetime, end_datetime) VALUES (18, 19, NULL, 70000, 'Руководитель отдела маркетинга', '2021-11-28 08:06:36', NULL);


--
-- Name: departments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.departments_id_seq', 8, true);


--
-- Name: sql_history_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sql_history_id_seq', 4, true);


--
-- Name: user_department_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.user_department_id_seq', 14, true);


--
-- Name: users_career_journal_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_career_journal_id_seq', 18, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 19, true);


--
-- Name: departments departments_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.departments
    ADD CONSTRAINT departments_pk PRIMARY KEY (id);


--
-- Name: sql_query_history sql_history_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sql_query_history
    ADD CONSTRAINT sql_history_pk PRIMARY KEY (id);


--
-- Name: user_department user_department_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_department
    ADD CONSTRAINT user_department_pk PRIMARY KEY (id);


--
-- Name: users_career_journal users_career_journal_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users_career_journal
    ADD CONSTRAINT users_career_journal_pk PRIMARY KEY (id);


--
-- Name: users users_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pk PRIMARY KEY (id);


--
-- Name: users_email_uindex; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX users_email_uindex ON public.users USING btree (email);


--
-- Name: users_login_uindex; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX users_login_uindex ON public.users USING btree (login);


--
-- Name: users collect_user_carrier_journal_on_insert; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER collect_user_carrier_journal_on_insert AFTER INSERT ON public.users FOR EACH ROW EXECUTE FUNCTION public.collect_user_carrier_journal_on_insert();


--
-- Name: users collect_user_carrier_journal_on_update; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER collect_user_carrier_journal_on_update AFTER UPDATE ON public.users FOR EACH ROW EXECUTE FUNCTION public.collect_user_carrier_journal_on_update();


--
-- Name: departments departments_users_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.departments
    ADD CONSTRAINT departments_users_id_fk FOREIGN KEY (head_id) REFERENCES public.users(id);


--
-- Name: sql_query_history sql_history_users_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sql_query_history
    ADD CONSTRAINT sql_history_users_id_fk FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: user_department user_department_departments_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_department
    ADD CONSTRAINT user_department_departments_id_fk FOREIGN KEY (department_id) REFERENCES public.departments(id) ON DELETE CASCADE;


--
-- Name: user_department user_department_users_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_department
    ADD CONSTRAINT user_department_users_id_fk FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: users_career_journal users_career_journal_departments_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users_career_journal
    ADD CONSTRAINT users_career_journal_departments_id_fk FOREIGN KEY (department_id) REFERENCES public.departments(id);


--
-- Name: users_career_journal users_career_journal_users_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users_career_journal
    ADD CONSTRAINT users_career_journal_users_id_fk FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

