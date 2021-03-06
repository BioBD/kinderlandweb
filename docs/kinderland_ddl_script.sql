--Use these lines to drop all tables
--DROP SCHEMA public CASCADE;
--CREATE SCHEMA public;


CREATE TABLE user_type (
    user_type integer PRIMARY KEY,
    description character varying(50) NOT NULL
);
INSERT INTO user_type VALUES 
(1, 'common user'), 
(2, 'system admin'), 
(3, 'director'), 
(4, 'secretary'), 
(5, 'coordinator'),
(6, 'doctor'),
(7, 'monitor-instructor'),
(0, 'public');

CREATE TABLE address (
    address_id serial PRIMARY KEY,
    street character varying(100) NOT NULL,
    place_number integer,
    complement character varying(255),
    city character varying(40),
    cep character varying(9),
    uf character varying(2),
    neighborhood character varying(80)
);

CREATE TABLE age_group (
    age_group_id INTEGER NOT NULL PRIMARY KEY,
    description VARCHAR NOT NULL
);
INSERT INTO age_group VALUES (1, '0-5 anos'), (2, '6-17 anos'), (3, '+18 anos');
  
CREATE TABLE person (
    person_id serial PRIMARY KEY,
    fullname character varying(80) NOT NULL,
    date_created timestamp without time zone default current_timestamp,
    date_updated timestamp without time zone,
    gender character(1),
    email character varying(120),
    address_id integer REFERENCES address,
    CHECK (gender = 'M' OR gender = 'F')
);
  
CREATE TABLE telephone (
    phone_number character varying(25) NOT NULL,
    person_id integer REFERENCES person,
    PRIMARY KEY (phone_number, person_id)
);
  
CREATE TABLE person_user (
    person_id integer REFERENCES person PRIMARY KEY,
    cpf character varying(20) UNIQUE,
    login character varying(120) UNIQUE NOT NULL,
    password character varying(300) NOT NULL,
    occupation character varying(40)
);

CREATE TABLE person_user_type
(
  person_id integer NOT NULL,
  user_type integer NOT NULL,
  CONSTRAINT pk_person_user_type PRIMARY KEY (person_id, user_type),
  CONSTRAINT fk_person FOREIGN KEY (person_id)
      REFERENCES person_user (person_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT fk_user_type FOREIGN KEY (user_type)
      REFERENCES user_type (user_type) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
);
  
CREATE TABLE donation_type(
    donation_type integer PRIMARY KEY NOT NULL,
    description character varying(30) NOT NULL,
    minimum_price numeric(7,2) DEFAULT 0.0
);
INSERT INTO donation_type VALUES (1, 'avulsa', 20.00), (2, 'associação', 720.00), (3, 'inscrição', 0.00), (4, 'inscrição colonia', 0.00);
  
CREATE TABLE donation_status(
    donation_status integer PRIMARY KEY NOT NULL,
    description character varying(30)
);
INSERT INTO donation_status VALUES (1, 'aberto'), (2, 'pago'),(-1, 'abandonado'), (3, 'não autorizado');
  
CREATE TABLE donation (
    donation_id serial PRIMARY KEY,
    person_id integer REFERENCES person,
    donation_type integer REFERENCES donation_type,
    date_created timestamp without time zone default current_timestamp,
    date_updated timestamp without time zone,
    donated_value decimal(7, 2) NOT NULL,
    donation_status integer REFERENCES donation_status
);

CREATE TABLE event (
    event_id serial PRIMARY KEY,
    event_name character varying(255) NOT NULL,
    description character varying(500),
    date_created timestamp without time zone DEFAULT current_timestamp,
    date_start timestamp without time zone,
    date_finish timestamp without time zone,
    date_start_show timestamp without time zone,
    date_finish_show timestamp without time zone,
    enabled boolean NOT NULL default false,
    capacity_male integer NOT NULL DEFAULT 0,
    capacity_female integer NOT NULL DEFAULT 0,
    capacity_nonsleeper integer NOT NULL DEFAULT 0
);

CREATE TABLE communication (
    communication_id serial PRIMARY KEY NOT NULL,
    content text NOT NULL,
    date_sent timestamp without time zone DEFAULT now() NOT NULL,
    type character varying(10) NOT NULL,
    successfully_sent boolean NOT NULL
);


CREATE TABLE communication_recipient (
    communication_id integer NOT NULL,
    recipient character varying(200) NOT NULL,
    recipient_type character varying(20) NOT NULL,
    PRIMARY KEY (communication_id, recipient, recipient_type),
    FOREIGN KEY (communication_id) REFERENCES communication(communication_id)
);


CREATE TABLE subscription_status(
    subscription_status integer PRIMARY KEY NOT NULL,
    description character varying(30)
);
INSERT INTO subscription_status VALUES (1, 'pre-inscrito'), (0, 'pre-inscrito incompleto'), (2, 'aguardando pagamento'), (3, 'inscrito'), (-1, 'cancelado'), (-2, 'desistente'), (-3, 'excluido');

CREATE TABLE payment_period (
    payment_period_id SERIAL,
    event_id INTEGER NOT NULL REFERENCES event,
    date_start TIMESTAMP WITHOUT TIME ZONE NOT NULL,
    date_finish TIMESTAMP WITHOUT TIME ZONE NOT NULL,
    full_price decimal(9,2) NOT NULL,
    children_price decimal(9,2) NOT NULL,
    middle_price numeric(9,2) NOT NULL,
    associate_discount decimal(3,2) NOT NULL DEFAULT 0.0,
    portions integer NOT NULL DEFAULT 1,

    PRIMARY KEY(payment_period_id)
);

CREATE TABLE event_subscription (
    person_id integer NOT NULL REFERENCES person, -- id from the person that is going
    event_id integer NOT NULL REFERENCES event,
    person_user_id integer NOT NULL,
    subscription_status integer REFERENCES subscription_status,
    donation_id integer REFERENCES donation,
    date_created timestamp without time zone DEFAULT now(),
    age_group_id integer NOT NULL REFERENCES age_group DEFAULT 3,
    associate boolean default false,
    nonsleeper boolean default false,

    PRIMARY KEY (person_id, event_id),
    CONSTRAINT fk_person_user_event FOREIGN KEY (person_user_id)
      REFERENCES person_user (person_id)
);

CREATE TABLE payment_status(
    payment_status integer PRIMARY KEY NOT NULL,
    description character varying(30)
);

INSERT INTO payment_status VALUES
(0,'Transação Criada'),
(1,'Transação em Andamento'),
(2,'Transação Autenticada'),
(3,'Transação não Autenticada'),
(4,'Transação Autorizada'),
(5,'Transação não Autorizada'),
(6,'Transação Capturada'),
(9,'Transação Cancelada'),
(10,'Transação em Autenticação'),
(12,'Transação em Cancelamento');


CREATE TABLE cielo_transaction (
    tid character varying(50) PRIMARY KEY,
    payment_type character varying(20), -- debito/credito
    cardflag character varying(20), -- amex/visa/mastercard
    payment_portions integer NOT NULL DEFAULT 1,
    donation_id integer REFERENCES donation,
    payment_status integer REFERENCES payment_status,
    date_created timestamp without time zone DEFAULT current_timestamp,
    date_updated timestamp without time zone,
    transaction_value decimal(9,2)
);

CREATE TABLE benemerits (
    person_id integer not null REFERENCES person PRIMARY KEY,
    date_started timestamp without time zone default current_timestamp,
    date_finished timestamp without time zone
);

CREATE TABLE school (
    school_name varchar(70) NOT NULL PRIMARY KEY
);
INSERT INTO school VALUES ('A. Liessin'),
('Andino Arica (Chile)'),
('Andrews'),
('Bahiense'),
('British School'),
('CEAT'),
('Centro Educ. Espaço Integrado (CEI)'),
('Colégio Estilo (Madrid ES)'),
('Colégio PH'),
('Corcovado'),
('EARJ'),
('edem'),
('Educativa'),
('Eliezer Max'),
('Escola Americana'),
('Escola João de Barro Itaipava'),
('Escola Nova'),
('Escola Parque'),
('Escola Vera Cruz (SP)'),
('I.L. Peretz'),
('King Alfred School'),
('Lycee Moliere'),
('Miguel de Cervantes (SP)'),
('Miraflores'),
('ORT'),
('Oswald de Andrade'),
('Pueri Domus'),
('Santo Agostinho'),
('Santo Inácio'),
('Sá Pereira'),
('TTH Bar''Ilan'),
('Único Colégio de Gente Feliz');

CREATE TABLE summer_camp (
    summer_camp_id SERIAL PRIMARY KEY NOT NULL,
    camp_name varchar (150) not null,
    description varchar(500),
    date_created timestamp without time zone DEFAULT now(),
    date_start timestamp without time zone,
    date_finish timestamp without time zone,
    date_start_pre_subscriptions timestamp without time zone,
    date_finish_pre_subscriptions timestamp without time zone,
    date_start_pre_subscriptions_associate timestamp without time zone,
    date_finish_pre_subscriptions_associate timestamp without time zone,
    pre_subscriptions_enabled boolean default false,
    capacity_male integer not null default 0,
    capacity_female integer not null default 0,
    mini_camp boolean not null default false,
    days_to_pay integer not null default 5
);

CREATE TABLE colonist (
    colonist_id SERIAL NOT NULL PRIMARY KEY,
    person_id INTEGER NOT NULL REFERENCES person,
    birth_date timestamp without time zone NOT NULL,
    date_created timestamp without time zone default now(),
    document_number varchar(100),
    document_type varchar(25),
    emergency_phonenumber varchar(30)
);

CREATE TABLE summer_camp_subscription_status (
    status integer not null PRIMARY KEY,
    description varchar(50)
);
INSERT INTO summer_camp_subscription_status VALUES
(0, 'Pré-inscrição em elaboração'), 
(1, 'Pré-inscrição aguardando validação'), 
(2, 'Pré-inscrição validada'),
(3, 'Pré-inscrição na fila de espera'), 
(4, 'Pré-inscrição aguardando doação'), 
(5, 'Inscrito'), 
(6, 'Pré-inscrição não validada'),
(-1, 'Desistente'), 
(-2, 'Excluido'),
(-3, 'Cancelado');

-- criar tabela de razões de desconto

create table discount_reason(
discount_reason_id SERIAL PRIMARY KEY,
discount_reason CHARACTER VARYING(200) UNIQUE NOT NULL
);

INSERT INTO discount_reason VALUES (1, 'Desconto igual ao da escola');
INSERT INTO discount_reason VALUES (2, 'Segundo irmão');
INSERT INTO discount_reason VALUES (3, 'Terceiro irmão');
INSERT INTO discount_reason VALUES (4, 'Lar da criança');

SELECT pg_catalog.setval('discount_reason_discount_reason_id_seq', 6, true);

CREATE TABLE summer_camp_subscription (
    summer_camp_id INTEGER NOT NULL REFERENCES summer_camp,
    colonist_id INTEGER NOT NULL REFERENCES colonist,
    person_user_id INTEGER NOT NULL REFERENCES person_user(person_id),
    situation INTEGER NOT NULL REFERENCES summer_camp_subscription_status(status),
    donation_id integer REFERENCES donation,
    date_created timestamp without time zone,
    school_name varchar(70),
    school_year integer not null,
    accepted_terms boolean default false,
    accepted_travel_terms boolean default false,
    roommate1 character varying(200),
    roommate2 character varying(200),
    roommate3 character varying(200),
    queue_number integer,
    discount integer not null default '0',
    discount_reason_id integer,
    date_payment_limit timestamp without time zone,
    room_number integer,
    CHECK (discount >= 0 and discount <= 100),
    FOREIGN KEY (discount_reason_id) REFERENCES discount_reason(discount_reason_id) ON DELETE RESTRICT
);

ALTER TABLE ONLY summer_camp_subscription
    ADD CONSTRAINT summer_camp_subscription_pkey PRIMARY KEY (summer_camp_id, colonist_id);

CREATE TABLE mini_colonist_observations (
    summer_camp_id integer not null references summer_camp,
    colonist_id integer not null references colonist,
    sleep_out boolean not null,
    wake_up_early boolean not null,
    food_restriction character varying(300),
    eat_by_oneself boolean not null,
    bathroom_freedom boolean not null,
    sleep_routine boolean not null,
    bunk_restriction boolean not null,
    wake_up_at_night boolean not null,
    sleep_enuresis boolean not null,
    sleepwalk boolean not null,
    observation character varying(300),
    responsible_name character varying(100) NOT NULL,
    responsible_number character varying(25) NOT NULL,

    primary key(summer_camp_id, colonist_id)
);

CREATE TABLE camp_payment_period (
    camp_payment_period_id SERIAL,
    summer_camp_id INTEGER NOT NULL REFERENCES event,
    date_start TIMESTAMP WITHOUT TIME ZONE NOT NULL,
    date_finish TIMESTAMP WITHOUT TIME ZONE NOT NULL,
    portions integer NOT NULL DEFAULT 1,

    PRIMARY KEY(camp_payment_period_id)
);

CREATE TABLE document_type (
    document_type SERIAL NOT NULL PRIMARY KEY,
    description character varying(100)
);

INSERT INTO document_type VALUES (1, 'Ficha Médica');
INSERT INTO document_type VALUES (2, 'Autorização de viagem');
INSERT INTO document_type VALUES (3, 'Documento de Identidade');
INSERT INTO document_type VALUES (4, 'Normas Gerais');
INSERT INTO document_type VALUES (5, 'Foto 3x4');


CREATE TABLE document (
    document_id SERIAL NOT NULL PRIMARY KEY,
    summer_camp_id integer,
    colonist_id integer,
    date_created timestamp without time zone DEFAULT now(),
    filename character varying(100) NOT NULL,
    extension character varying(100) NOT NULL,
    document_type integer NOT NULL,
    file bytea NOT NULL,
    user_id integer NOT NULL
);

ALTER TABLE ONLY document
    ADD CONSTRAINT "UK_document" UNIQUE (summer_camp_id, colonist_id, date_created);

ALTER TABLE ONLY document
    ADD CONSTRAINT document_summer_camp_id_fkey FOREIGN KEY (summer_camp_id, colonist_id) REFERENCES summer_camp_subscription(summer_camp_id, colonist_id);

ALTER TABLE ONLY document
    ADD CONSTRAINT document_user_id_fkey FOREIGN KEY (user_id) REFERENCES person_user(person_id);

CREATE TABLE parent_summer_camp_subscription (
    summer_camp_id integer NOT NULL,
    colonist_id integer NOT NULL,
    parent_id integer NOT NULL,
    relation character varying(10)
);

ALTER TABLE ONLY parent_summer_camp_subscription
    ADD CONSTRAINT parent_summer_camp_subscription_pkey PRIMARY KEY (summer_camp_id, colonist_id, parent_id);

ALTER TABLE ONLY parent_summer_camp_subscription
    ADD CONSTRAINT "FK_parent_summer_camp_subscription_person" FOREIGN KEY (parent_id) REFERENCES person(person_id);

CREATE TABLE validation (
    summer_camp_id integer NOT NULL,
    colonist_id bigint NOT NULL,
    colonist_gender_ok boolean,
    colonist_gender_msg character varying(200),
    colonist_picture_ok boolean,
    colonist_picture_msg character varying(200),
    colonist_identity_ok boolean,
    colonist_identity_msg character varying(200),
    colonist_birthday_ok boolean,
    colonist_birthday_msg character varying(200),
    colonist_parents_name_ok boolean,
    colonist_parents_name_msg character varying(200),
    colonist_name_ok boolean,
    colonist_name_msg character varying(200),

    PRIMARY KEY(summer_camp_id, colonist_id)
);
    

CREATE VIEW open_public_events as (SELECT * FROM event 
                WHERE current_timestamp BETWEEN date_start_show AND date_finish_show 
                AND enabled = true);

CREATE VIEW associates AS (
  SELECT pu.person_id,
    pu.cpf,
    pu.login,
    pu.occupation
   FROM person_user pu
     JOIN donation d ON d.person_id = pu.person_id
  WHERE d.donation_type = 2 AND date_part('year'::text, d.date_created) = date_part('year'::text, now()) AND d.donation_status = 2
  UNION 
  SELECT pu.person_id,
    pu.cpf,
    pu.login,
    pu.occupation
   FROM person_user pu
  INNER JOIN benemerits b on b.person_id = pu.person_id
  WHERE b.date_finished is null
);

CREATE VIEW donations_completed AS (
    SELECT *
    FROM donation
    WHERE
        donation_status = 2
);

CREATE VIEW donation_detailed AS (
    SELECT
        d.donation_id,
        d.person_id,
        dt.description as donation_type,
        ds.description as donation_status,
        d.date_created,
        d.donated_value
    FROM
        donation d
    INNER JOIN
        donation_type dt
    ON dt.donation_type = d.donation_type
    INNER JOIN
        donation_status ds
    ON ds.donation_status = d.donation_status
    
);

-- DROP TABLE system_method;

CREATE TABLE system_method
(
  system_method_id serial NOT NULL,
  method_name character varying(50) NOT NULL,
  controller_name character varying(20),
  user_type integer NOT NULL REFERENCES user_type,
  date_inserted timestamp without time zone DEFAULT now(),
  CONSTRAINT pk_system_methods PRIMARY KEY (system_method_id)
);

CREATE TABLE blood_type (
    blood_type_id SERIAL NOT NULL PRIMARY KEY,
    description character(2) NOT NULL
);

Insert into blood_type(blood_type_id,description) values
(1,'A'),
(2,'B'),
(3,'O'),
(4,'AB');

CREATE TABLE medical_file (
    summer_camp_id integer NOT NULL,
    colonist_id integer NOT NULL,
    blood_type integer NOT NULL,
    rh boolean NOT NULL,
    weight numeric NOT NULL,
    height numeric NOT NULL,
    physical_activity_restriction character varying(200),
    vacine_tetanus boolean NOT NULL,
    vacine_mmr boolean NOT NULL,
    vacine_hepatitis boolean NOT NULL,
    infecto_contagious_antecedents character varying(200),
    regular_use_medicine character varying(200),
    medicine_restrictions character varying(200),
    allergies character varying(200),
    analgesic_antipyretic character varying(200),
    doctor_id integer NOT NULL,
    date timestamp without time zone NOT NULL default current_timestamp,
    doctor_observations character varying
    PRIMARY KEY (summer_camp_id, colonist_id),
    FOREIGN KEY (blood_type) REFERENCES blood_type(blood_type_id) ON UPDATE       CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (doctor_id) REFERENCES person(person_id) ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE medical_file_staff (
    summer_camp_id integer NOT NULL,
    person_id integer NOT NULL,
    blood_type integer NOT NULL,
    rh boolean NOT NULL,
    weight numeric NOT NULL,
    height numeric NOT NULL,
    physical_activity_restriction character varying(200),
    vacine_tetanus boolean NOT NULL,
    vacine_mmr boolean NOT NULL,
    vacine_hepatitis boolean NOT NULL,
    infecto_contagious_antecedents character varying(200),
    regular_use_medicine character varying(200),
    medicine_restrictions character varying(200),
    allergies character varying(200),
    analgesic_antipyretic character varying(200),
    doctor_id integer NOT NULL,
    date timestamp without time zone NOT NULL default current_timestamp,
    doctor_observations character varying
    PRIMARY KEY (summer_camp_id, person_id),
    FOREIGN KEY (blood_type) REFERENCES blood_type(blood_type_id) ON UPDATE       CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (doctor_id) REFERENCES person(person_id) ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE summer_camp_payment_period
(
  payment_period_id serial NOT NULL,
  summer_camp_id integer NOT NULL REFERENCES summer_camp,
  date_start timestamp without time zone NOT NULL,
  date_finish timestamp without time zone NOT NULL,
  price numeric(9,2) NOT NULL,
  portions integer NOT NULL DEFAULT 1,
  CONSTRAINT summer_camp_payment_period_pkey PRIMARY KEY (payment_period_id)
);

----------------------------------- Views Section -----------------------------------

CREATE OR REPLACE VIEW donations_pending AS (
    SELECT 
        *
    FROM donation
    WHERE donation_status = 1 
    AND (current_timestamp - donation.date_created) > '1 hour'::interval
);

CREATE OR REPLACE VIEW v_report_user_registered AS 
 SELECT count_users.count_users,
    count_associates.count_associates,
    count_benemerit.count_benemerit,
    count_non_benemerit.count_non_associate
   FROM ( SELECT count(*) AS count_users
           FROM person_user) count_users,
    ( SELECT count(*) AS count_associates
           FROM associates) count_associates,
    ( SELECT count(*) AS count_benemerit
           FROM benemerits b
          WHERE date_finished is null) count_benemerit,
    ( SELECT count(*) AS count_non_associate
           FROM person_user
          WHERE NOT (person_user.person_id IN ( SELECT associates.person_id
                   FROM associates))) count_non_benemerit;

-- Essa view ainda precisa ser aprimorada para exibir que tipo de sócio a pessoa é
CREATE VIEW v_report_all_users AS (
    SELECT 
        p.fullname, p.email, COALESCE((SELECT true from associates where person_id = p.person_id), false) as associate, p.person_id
    FROM
        person p 
    INNER JOIN 
        person_user pu on p.person_id = pu.person_id
);

CREATE VIEW v_report_all_users_association_detailed AS (
    SELECT * FROM (
        SELECT p.fullname,
            p.email,
            'não sócio' as associate,
            p.person_id
        FROM
            person_user pu
        INNER JOIN
            person p on pu.person_id = p.person_id
        WHERE
            pu.person_id not in( SELECT person_id FROM associates )
        UNION
        SELECT p.fullname,
            p.email,
            'contribuinte' AS associate,
            p.person_id
        FROM associates a
        INNER JOIN
            person p ON p.person_id = a.person_id
        WHERE
            a.person_id not in (
                SELECT
                    p.person_id
                FROM
                    benemerits b
                INNER JOIN
                    person p on p.person_id = b.person_id
                WHERE
                    b.date_finished is null
            )
        UNION
        SELECT p.fullname,
            p.email,
            'benemerito' AS associate,
            p.person_id
        FROM
            benemerits b
        INNER JOIN
            person p on p.person_id = b.person_id
        WHERE
            b.date_finished is null
    ) as A
    ORDER BY A.fullname
);

CREATE OR REPLACE VIEW v_rel_associated_campaign AS (
    SELECT vall.fullname,
        vall.email,
        vall.associate,
        vall.person_id,
        CASE
            WHEN vall.associate = 'contribuinte'::text THEN ( SELECT donation.date_created
               FROM donation
              WHERE donation.person_id = vall.person_id AND donation.donation_status = 2 AND donation.donation_type = 2)
            WHEN vall.associate = 'benemerito'::text THEN ( SELECT benemerits.date_started
               FROM benemerits
              WHERE benemerits.person_id = vall.person_id AND benemerits.date_finished IS NULL)
            ELSE NULL::timestamp without time zone
        END AS data_associacao
    FROM v_report_all_users_association_detailed vall
    WHERE vall.associate = 'contribuinte'::text
);

CREATE OR REPLACE VIEW v_report_free_donations AS 
 SELECT c.date_created,
    p.person_id,
    p.fullname,
    p.associate,
    d.donated_value,
    c.payment_type,
    c.cardflag,
    c.payment_portions,
    d.donation_type
   FROM donation d
     JOIN v_report_all_users_association_detailed p ON d.person_id = p.person_id
     JOIN cielo_transaction c ON c.donation_id = d.donation_id
  WHERE c.payment_status = 6;
 
 CREATE OR REPLACE VIEW v_users_permissions AS 
 SELECT DISTINCT p.person_id,
    p.fullname,
    COALESCE(( SELECT 1
           FROM person_user_type
          WHERE person_user_type.person_id = p.person_id AND person_user_type.user_type = 1), 0) AS common_user,
    COALESCE(( SELECT 1
           FROM person_user_type
          WHERE person_user_type.person_id = p.person_id AND person_user_type.user_type = 2), 0) AS system_admin,
    COALESCE(( SELECT 1
           FROM person_user_type
          WHERE person_user_type.person_id = p.person_id AND person_user_type.user_type = 3), 0) AS director,
    COALESCE(( SELECT 1
           FROM person_user_type
          WHERE person_user_type.person_id = p.person_id AND person_user_type.user_type = 4), 0) AS secretary,
    COALESCE(( SELECT 1
           FROM person_user_type
          WHERE person_user_type.person_id = p.person_id AND person_user_type.user_type = 5), 0) AS coordinator,
    COALESCE(( SELECT 1
           FROM person_user_type
          WHERE person_user_type.person_id = p.person_id AND person_user_type.user_type = 6), 0) AS doctor,
    COALESCE(( SELECT 1
           FROM person_user_type
          WHERE person_user_type.person_id = p.person_id AND person_user_type.user_type = 7), 0) AS monitor_instructor
   FROM person_user_type put
     LEFT JOIN person p ON p.person_id = put.person_id
  ORDER BY p.fullname;

-- Additional fields that are already included in the summer_camp_subscription CREATE TABLE sql query;
--ALTER TABLE summer_camp_subscription
--   ADD COLUMN roommate1 character varying(200);

--ALTER TABLE summer_camp_subscription
--   ADD COLUMN roommate2 character varying(200);

--ALTER TABLE summer_camp_subscription
--   ADD COLUMN roommate3 character varying(200);

--ALTER TABLE summer_camp_subscription
--   ADD COLUMN queue_number integer;

CREATE VIEW v_socios_count_inscricoes as (
select p.*, count(scs.colonist_id) total_inscritos from associates a join person p on p.person_id = a.person_id
left outer join summer_camp_subscription scs on scs.person_user_id = a.person_id and summer_camp_id in (
        select summer_camp_id from summer_camp where to_char(date_created, 'YYYY') = to_char(current_timestamp, 'YYYY')
    )
group by p.person_id);

CREATE OR REPLACE VIEW v_colonists_with_responsables_not_parents AS
		(SELECT p.fullname Colonist,p1.fullname as Responsable, DATE_PART('YEAR',sc.date_created) as Ano, c.colonist_id as Colonist_id, scs.person_user_id as Responsable_id, sc.camp_name as camp_name, sc.summer_camp_id as camp_id  FROM colonist c
		INNER JOIN summer_camp_subscription scs on c.colonist_id = scs.colonist_id
		INNER JOIN summer_camp sc on sc.summer_camp_id = scs.summer_camp_id
		INNER JOIN person p on p.person_id = c.person_id
		INNER JOIN person p1 on p1.person_id = scs.person_user_id
		WHERE c.colonist_id not in (SELECT colonist_id FROM parent_summer_camp_subscription));

CREATE OR REPLACE VIEW v_subscriptions_not_submitted as
	(SELECT c.colonist_id as colonist_id, p.fullname as colonist_name, p1.person_id as responsable_id, p1.fullname as responsable_name, scs.situation as situation, 
	scss.description as situation_description, sc.summer_camp_id as camp_id, sc.camp_name as camp_name, DATE_PART('YEAR',sc.date_created) as year FROM person p
	INNER JOIN colonist c on c.person_id = p.person_id
	INNER JOIN summer_camp_subscription scs on c.colonist_id = scs.colonist_id
	INNER JOIN summer_camp sc on sc.summer_camp_id = scs.summer_camp_id
	INNER JOIN person p1 on p1.person_id = scs.person_user_id
	INNER JOIN summer_camp_subscription_status scss on scss.status = scs.situation
	WHERE scs.situation in ('0','6')
	AND c.colonist_id in (SELECT colonist_id FROM medical_file)
	AND c.colonist_id in (SELECT colonist_id FROM summer_camp_subscription WHERE accepted_travel_terms = 'TRUE')
	AND c.colonist_id in (SELECT colonist_id FROM document WHERE document_type = 3)
	AND c.colonist_id in (SELECT colonist_id FROM summer_camp_subscription WHERE accepted_terms = 'TRUE')
	AND c.colonist_id in (SELECT colonist_id FROM document WHERE document_type = 5));

				
CREATE OR REPLACE VIEW v_discount as (
		SELECT c.colonist_id as colonist_id, p.fullname as colonist_name, pr.person_id as responsable_id, pr.fullname as responsable_name, 
		scs.summer_camp_id as camp_id, scs.discount as discount, dr.discount_reason as discount_reason, scs.situation as situation, 
		scss.description as situation_description, DATE_PART('YEAR',sc.date_created) as year
		FROM colonist c 
		INNER JOIN person p on p.person_id = c.person_id
		INNER JOIN summer_camp_subscription scs on scs.colonist_id = c.colonist_id
		INNER JOIN person pr on pr.person_id = scs.person_user_id
		INNER JOIN discount_reason dr on dr.discount_reason_id = scs.discount_reason_id
		INNER JOIN summer_camp_subscription_status scss on scss.status = scs.situation
		INNER JOIN summer_camp sc on sc.summer_camp_id = scs.summer_camp_id);


CREATE OR REPLACE VIEW v_colonists_waiting_payment AS 
 SELECT scs.summer_camp_id,
    scs.colonist_id,
    scs.person_user_id,
    pr.fullname AS responsible_name,
    p.fullname AS colonist_name,
    s.description,
    scs.queue_number,
    scs.situation,
    p.gender
   FROM summer_camp_subscription scs
     JOIN summer_camp_subscription_status s ON s.status = scs.situation
     JOIN colonist c ON c.colonist_id = scs.colonist_id
     JOIN person p ON p.person_id = c.person_id
     JOIN person pr ON pr.person_id = scs.person_user_id
  WHERE scs.situation = 4;
  
  CREATE OR REPLACE VIEW v_captured_transactions AS (
	SELECT ct.payment_type as type, ct.cardflag as cardflag, ct.payment_portions as portions, ct.date_created as date_created,
	ct.date_updated as date_updated, ct.transaction_value as value, d.donation_type as donation_type
	FROM cielo_transaction ct INNER JOIN payment_status ps on ps.payment_status = ct.payment_status
	INNER JOIN donation d on ct.donation_id = d.donation_id
	INNER JOIN donation_type dt on dt.donation_type = d.donation_type
	WHERE ps.payment_status = 6);

CREATE OR REPLACE FUNCTION set_colonist_subscription_waiting_payment(_colonist_id integer, _summer_camp_id integer)
  RETURNS boolean AS
$BODY$
DECLARE
    cSql VARCHAR;
    iDaysToPay INTEGER;
    tsDateLimit TIMESTAMP WITHOUT TIME ZONE;
    iActSituation INTEGER;
    
BEGIN
    SELECT situation into iActSituation
    FROM summer_camp_subscription 
    WHERE summer_camp_id = _summer_camp_id 
    AND colonist_id = _colonist_id;

    IF (iActSituation <> 3) THEN
        RAISE NOTICE 'Camper status is not in queue';
        RETURN FALSE;
    END IF;

    SELECT days_to_pay INTO iDaysToPay 
    FROM summer_camp 
    WHERE summer_camp_id = _summer_camp_id; 

    RAISE NOTICE 'Days to pay: %', iDaysToPay;

    tsDateLimit := (current_timestamp + ( iDaysToPay || ' days')::interval)::date || ' 23:59:59';
    RAISE NOTICE 'Date Limit: %', tsDateLimit;
    
    UPDATE summer_camp_subscription 
    SET date_payment_limit = tsDateLimit,
        situation = 4
    WHERE summer_camp_id = _summer_camp_id 
    AND colonist_id = _colonist_id;

    RETURN TRUE;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;

