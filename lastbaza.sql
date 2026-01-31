--
-- PostgreSQL database dump
--

\restrict zF7Z9d1QfcbFa3HnYPwxnchgLCgfdKAwRx8yud0XBoYiXObGYLvZVNGYfaAN57H

-- Dumped from database version 18.0 (Debian 18.0-1.pgdg13+3)
-- Dumped by pg_dump version 18.0

-- Started on 2026-01-31 05:05:05 UTC

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 229 (class 1255 OID 16467)
-- Name: set_created_at(); Type: FUNCTION; Schema: public; Owner: docker
--

CREATE FUNCTION public.set_created_at() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    NEW.created_at = NOW();
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.set_created_at() OWNER TO docker;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 226 (class 1259 OID 16444)
-- Name: habit_logs; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.habit_logs (
    id integer NOT NULL,
    habit_id integer NOT NULL,
    was_watered boolean DEFAULT true,
    date date DEFAULT CURRENT_DATE
);


ALTER TABLE public.habit_logs OWNER TO docker;

--
-- TOC entry 225 (class 1259 OID 16443)
-- Name: habit_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.habit_logs_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.habit_logs_id_seq OWNER TO docker;

--
-- TOC entry 3506 (class 0 OID 0)
-- Dependencies: 225
-- Name: habit_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.habit_logs_id_seq OWNED BY public.habit_logs.id;


--
-- TOC entry 224 (class 1259 OID 16426)
-- Name: habits; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.habits (
    id integer NOT NULL,
    user_id integer NOT NULL,
    name character varying(255) NOT NULL,
    target_days_per_week integer DEFAULT 7,
    current_health integer DEFAULT 50,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.habits OWNER TO docker;

--
-- TOC entry 223 (class 1259 OID 16425)
-- Name: habits_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.habits_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.habits_id_seq OWNER TO docker;

--
-- TOC entry 3507 (class 0 OID 0)
-- Dependencies: 223
-- Name: habits_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.habits_id_seq OWNED BY public.habits.id;


--
-- TOC entry 220 (class 1259 OID 16386)
-- Name: roles; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.roles (
    id integer NOT NULL,
    name text NOT NULL
);


ALTER TABLE public.roles OWNER TO docker;

--
-- TOC entry 219 (class 1259 OID 16385)
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.roles_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.roles_id_seq OWNER TO docker;

--
-- TOC entry 3508 (class 0 OID 0)
-- Dependencies: 219
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- TOC entry 222 (class 1259 OID 16399)
-- Name: users; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.users (
    id integer NOT NULL,
    login text NOT NULL,
    email text NOT NULL,
    password_hash text NOT NULL,
    role_id integer NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    is_active boolean DEFAULT true NOT NULL
);


ALTER TABLE public.users OWNER TO docker;

--
-- TOC entry 221 (class 1259 OID 16398)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO docker;

--
-- TOC entry 3509 (class 0 OID 0)
-- Dependencies: 221
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 228 (class 1259 OID 16469)
-- Name: v_user_plant_stats; Type: VIEW; Schema: public; Owner: docker
--

CREATE VIEW public.v_user_plant_stats AS
 SELECT u.id,
    u.login,
    u.email,
    ( SELECT r.name
           FROM public.roles r
          WHERE (r.id = u.role_id)) AS role_name,
    count(h.id) AS total_plants
   FROM (public.users u
     LEFT JOIN public.habits h ON ((u.id = h.user_id)))
  GROUP BY u.id, u.login, u.email, u.role_id;


ALTER VIEW public.v_user_plant_stats OWNER TO docker;

--
-- TOC entry 227 (class 1259 OID 16462)
-- Name: v_user_stats; Type: VIEW; Schema: public; Owner: docker
--

CREATE VIEW public.v_user_stats AS
 SELECT u.id,
    u.login,
    count(h.id) AS total_plants,
    avg(h.current_health) AS avg_health
   FROM (public.users u
     LEFT JOIN public.habits h ON ((u.id = h.user_id)))
  GROUP BY u.id, u.login;


ALTER VIEW public.v_user_stats OWNER TO docker;

--
-- TOC entry 3321 (class 2604 OID 16447)
-- Name: habit_logs id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.habit_logs ALTER COLUMN id SET DEFAULT nextval('public.habit_logs_id_seq'::regclass);


--
-- TOC entry 3317 (class 2604 OID 16429)
-- Name: habits id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.habits ALTER COLUMN id SET DEFAULT nextval('public.habits_id_seq'::regclass);


--
-- TOC entry 3313 (class 2604 OID 16389)
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- TOC entry 3314 (class 2604 OID 16402)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 3500 (class 0 OID 16444)
-- Dependencies: 226
-- Data for Name: habit_logs; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.habit_logs (id, habit_id, was_watered, date) FROM stdin;
120	26	t	2026-01-31
69	10	t	2026-01-30
74	18	t	2026-01-31
\.


--
-- TOC entry 3498 (class 0 OID 16426)
-- Dependencies: 224
-- Data for Name: habits; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.habits (id, user_id, name, target_days_per_week, current_health, created_at) FROM stdin;
10	8	wysłac 100 CV	7	90	2025-01-02 20:57:43.690664
18	6	100lp na plus	7	100	2026-01-25 00:07:01.211493
26	6	testbazy	7	100	2026-01-20 03:57:39.25616
11	8	ugotowac burgery	7	100	2026-01-30 23:11:32.389546
\.


--
-- TOC entry 3494 (class 0 OID 16386)
-- Dependencies: 220
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.roles (id, name) FROM stdin;
1	USER
2	ADMIN
\.


--
-- TOC entry 3496 (class 0 OID 16399)
-- Dependencies: 222
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.users (id, login, email, password_hash, role_id, created_at, is_active) FROM stdin;
6	dromeczek2004@gmail.com	dromeczek2004@gmail.com	$2y$10$rN8rTQaWd2UUD1.zmNnyuOoFOQlfngLB4HDgeoo2NkIMhgiEGwZ3q	2	2026-01-30 05:33:15.611526	t
7	igor@example.com	igor@example.com	$2y$10$Zf9jhCtUXPMQscbQhC4WbegpBNuD.p26hY4hExjuGOZmai0XfVVbS	1	2026-01-30 20:56:42.59812	t
9	igor.drohomirecki@student.pk.edu.pl	igor.drohomirecki@student.pk.edu.pl	$2y$10$fnscWnM1DT2daHNMP5yn0OzAQZASTVCTO5Ov.5AuXyF6728jn9IQO	1	2026-01-31 04:51:06.102236	t
8	igi@example.com	igi@example.com	$2y$10$QeOERbzAwwauI//fLZQXs.35jHiQl2ftZpGld3nhv/jAP8StfABZy	1	2026-01-30 20:57:20.713864	t
\.


--
-- TOC entry 3510 (class 0 OID 0)
-- Dependencies: 225
-- Name: habit_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.habit_logs_id_seq', 121, true);


--
-- TOC entry 3511 (class 0 OID 0)
-- Dependencies: 223
-- Name: habits_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.habits_id_seq', 27, true);


--
-- TOC entry 3512 (class 0 OID 0)
-- Dependencies: 219
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.roles_id_seq', 2, true);


--
-- TOC entry 3513 (class 0 OID 0)
-- Dependencies: 221
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.users_id_seq', 9, true);


--
-- TOC entry 3337 (class 2606 OID 16455)
-- Name: habit_logs habit_logs_habit_id_date_key; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.habit_logs
    ADD CONSTRAINT habit_logs_habit_id_date_key UNIQUE (habit_id, date);


--
-- TOC entry 3339 (class 2606 OID 16453)
-- Name: habit_logs habit_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.habit_logs
    ADD CONSTRAINT habit_logs_pkey PRIMARY KEY (id);


--
-- TOC entry 3335 (class 2606 OID 16437)
-- Name: habits habits_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.habits
    ADD CONSTRAINT habits_pkey PRIMARY KEY (id);


--
-- TOC entry 3325 (class 2606 OID 16397)
-- Name: roles roles_name_key; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_name_key UNIQUE (name);


--
-- TOC entry 3327 (class 2606 OID 16395)
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- TOC entry 3329 (class 2606 OID 16419)
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- TOC entry 3331 (class 2606 OID 16417)
-- Name: users users_login_key; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_login_key UNIQUE (login);


--
-- TOC entry 3333 (class 2606 OID 16415)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 3343 (class 2620 OID 16468)
-- Name: habits tr_habits_created; Type: TRIGGER; Schema: public; Owner: docker
--

CREATE TRIGGER tr_habits_created BEFORE INSERT ON public.habits FOR EACH ROW EXECUTE FUNCTION public.set_created_at();


--
-- TOC entry 3342 (class 2606 OID 16456)
-- Name: habit_logs fk_habit; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.habit_logs
    ADD CONSTRAINT fk_habit FOREIGN KEY (habit_id) REFERENCES public.habits(id) ON DELETE CASCADE;


--
-- TOC entry 3341 (class 2606 OID 16438)
-- Name: habits fk_user; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.habits
    ADD CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- TOC entry 3340 (class 2606 OID 16420)
-- Name: users users_role_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_role_id_fkey FOREIGN KEY (role_id) REFERENCES public.roles(id);


-- Completed on 2026-01-31 05:05:05 UTC

--
-- PostgreSQL database dump complete
--

\unrestrict zF7Z9d1QfcbFa3HnYPwxnchgLCgfdKAwRx8yud0XBoYiXObGYLvZVNGYfaAN57H

