--
-- PostgreSQL database dump
--

\restrict Q0bpI8YAqSYk6WK79OuVs4HxLBaOA52Gmqu9dra5uHinMbEGtb7SadReJgO2SHC

-- Dumped from database version 14.19 (Homebrew)
-- Dumped by pg_dump version 14.19 (Homebrew)

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

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: anecdote; Type: TABLE; Schema: public; Owner: Nikita
--

CREATE TABLE public.anecdote (
    id integer NOT NULL,
    text text NOT NULL,
    topic_id integer NOT NULL,
    user_id integer NOT NULL
);


ALTER TABLE public.anecdote OWNER TO "Nikita";

--
-- Name: anecdote_id_seq; Type: SEQUENCE; Schema: public; Owner: Nikita
--

CREATE SEQUENCE public.anecdote_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.anecdote_id_seq OWNER TO "Nikita";

--
-- Name: anecdote_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: Nikita
--

ALTER SEQUENCE public.anecdote_id_seq OWNED BY public.anecdote.id;


--
-- Name: anectodes_topics; Type: TABLE; Schema: public; Owner: Nikita
--

CREATE TABLE public.anectodes_topics (
    id_anecdote integer NOT NULL,
    id_topic integer NOT NULL
);


ALTER TABLE public.anectodes_topics OWNER TO "Nikita";

--
-- Name: app_user; Type: TABLE; Schema: public; Owner: Nikita
--

CREATE TABLE public.app_user (
    id integer NOT NULL,
    email character varying(180) NOT NULL,
    roles json NOT NULL,
    password character varying(255) NOT NULL
);


ALTER TABLE public.app_user OWNER TO "Nikita";

--
-- Name: app_user_id_seq; Type: SEQUENCE; Schema: public; Owner: Nikita
--

CREATE SEQUENCE public.app_user_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.app_user_id_seq OWNER TO "Nikita";

--
-- Name: app_user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: Nikita
--

ALTER SEQUENCE public.app_user_id_seq OWNED BY public.app_user.id;


--
-- Name: doctrine_migration_versions; Type: TABLE; Schema: public; Owner: Nikita
--

CREATE TABLE public.doctrine_migration_versions (
    version character varying(191) NOT NULL,
    executed_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    execution_time integer
);


ALTER TABLE public.doctrine_migration_versions OWNER TO "Nikita";

--
-- Name: no; Type: TABLE; Schema: public; Owner: Nikita
--

CREATE TABLE public.no (
    id integer NOT NULL
);


ALTER TABLE public.no OWNER TO "Nikita";

--
-- Name: no_id_seq; Type: SEQUENCE; Schema: public; Owner: Nikita
--

CREATE SEQUENCE public.no_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.no_id_seq OWNER TO "Nikita";

--
-- Name: no_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: Nikita
--

ALTER SEQUENCE public.no_id_seq OWNED BY public.no.id;


--
-- Name: topics; Type: TABLE; Schema: public; Owner: Nikita
--

CREATE TABLE public.topics (
    id integer NOT NULL,
    name character varying(255) NOT NULL
);


ALTER TABLE public.topics OWNER TO "Nikita";

--
-- Name: vote; Type: TABLE; Schema: public; Owner: Nikita
--

CREATE TABLE public.vote (
    id integer NOT NULL,
    user_id integer NOT NULL,
    anecdote_id integer NOT NULL
);


ALTER TABLE public.vote OWNER TO "Nikita";

--
-- Name: vote_id_seq; Type: SEQUENCE; Schema: public; Owner: Nikita
--

CREATE SEQUENCE public.vote_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.vote_id_seq OWNER TO "Nikita";

--
-- Name: vote_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: Nikita
--

ALTER SEQUENCE public.vote_id_seq OWNED BY public.vote.id;


--
-- Name: anecdote id; Type: DEFAULT; Schema: public; Owner: Nikita
--

ALTER TABLE ONLY public.anecdote ALTER COLUMN id SET DEFAULT nextval('public.anecdote_id_seq'::regclass);


--
-- Name: app_user id; Type: DEFAULT; Schema: public; Owner: Nikita
--

ALTER TABLE ONLY public.app_user ALTER COLUMN id SET DEFAULT nextval('public.app_user_id_seq'::regclass);


--
-- Name: no id; Type: DEFAULT; Schema: public; Owner: Nikita
--

ALTER TABLE ONLY public.no ALTER COLUMN id SET DEFAULT nextval('public.no_id_seq'::regclass);


--
-- Name: vote id; Type: DEFAULT; Schema: public; Owner: Nikita
--

ALTER TABLE ONLY public.vote ALTER COLUMN id SET DEFAULT nextval('public.vote_id_seq'::regclass);


--
-- Data for Name: anecdote; Type: TABLE DATA; Schema: public; Owner: Nikita
--

COPY public.anecdote (id, text, topic_id, user_id) FROM stdin;
42	чилдрен	10	8
35	шестой 	1	8
36	седьмой 	1	8
37	восьмой	1	8
38	девятый	1	8
39	десятый	1	8
40	одиннадцатый	1	8
41	двенадцатый	1	8
28	первый	1	8
31	второй	1	8
32	третий	1	8
43	ahsgvdjbasbdas	1	8
34	пятый	1	8
\.


--
-- Data for Name: anectodes_topics; Type: TABLE DATA; Schema: public; Owner: Nikita
--

COPY public.anectodes_topics (id_anecdote, id_topic) FROM stdin;
28	1
28	2
30	1
31	2
\.


--
-- Data for Name: app_user; Type: TABLE DATA; Schema: public; Owner: Nikita
--

COPY public.app_user (id, email, roles, password) FROM stdin;
8	adminka@example.com	["ROLE_ADMIN"]	$2y$13$/6Ruouczc0In2fikh/UArOBMHZsQLJRFy0S3S2pPWTQiPF2fiH1gu
9	artemburmak2012@gmail.com	["ROLE_USER"]	$2y$13$G4HlYIX17V7T5ObZx1.AO.9Idr.gN8M6wHTADBPl7eHI3d7y2IaH6
10	hsadvajsvda@gmail.com	["ROLE_USER"]	$2y$13$ojeezgrGXMlC/0yQr18QkucmtwX8mldDYSZhIQW7f6LB.pSd4IvdK
\.


--
-- Data for Name: doctrine_migration_versions; Type: TABLE DATA; Schema: public; Owner: Nikita
--

COPY public.doctrine_migration_versions (version, executed_at, execution_time) FROM stdin;
DoctrineMigrations\\Version20251014104316	2025-10-14 10:43:37	7
DoctrineMigrations\\Version20251014142036	2025-10-14 14:21:00	1
DoctrineMigrations\\Version20251015124952	2025-10-15 12:49:58	11
DoctrineMigrations\\Version20251015125648	2025-10-15 12:56:53	1
DoctrineMigrations\\Version20251015131906	2025-10-15 13:19:11	12
DoctrineMigrations\\Version20251015160741	2025-10-15 16:07:45	12
DoctrineMigrations\\Version20251016111616	2025-10-16 11:16:29	11
DoctrineMigrations\\Version20251016174911	2025-10-16 17:49:24	4
DoctrineMigrations\\Version20251016180232	\N	\N
DoctrineMigrations\\Version20251016180606	\N	\N
DoctrineMigrations\\Version20251016180939	\N	\N
DoctrineMigrations\\Version20251016181544	\N	\N
DoctrineMigrations\\Version20251016181421	2025-10-21 12:55:10	3
DoctrineMigrations\\Version20251021114715	\N	\N
DoctrineMigrations\\Version20251021124722	\N	\N
DoctrineMigrations\\Version20251021125500	\N	\N
DoctrineMigrations\\Version20251119114341	\N	\N
DoctrineMigrations\\Version20251119135018	\N	\N
DoctrineMigrations\\Version20251119135059	\N	\N
DoctrineMigrations\\Version20251119140316	\N	\N
DoctrineMigrations\\Version20251119141225	\N	\N
\.


--
-- Data for Name: no; Type: TABLE DATA; Schema: public; Owner: Nikita
--

COPY public.no (id) FROM stdin;
\.


--
-- Data for Name: topics; Type: TABLE DATA; Schema: public; Owner: Nikita
--

COPY public.topics (id, name) FROM stdin;
4	Work
5	Family
6	Medicine
7	Politics
8	Army
9	Vacation
10	Children
11	Animals
12	Travel
13	Technology
14	Friends
15	Relationships
16	Teachers
17	Internet
18	Police
19	Neighbors
20	Restaurant
21	Marriage
22	Mother-in-law
1	All topics
2	School
3	Programming
\.


--
-- Data for Name: vote; Type: TABLE DATA; Schema: public; Owner: Nikita
--

COPY public.vote (id, user_id, anecdote_id) FROM stdin;
24	9	28
\.


--
-- Name: anecdote_id_seq; Type: SEQUENCE SET; Schema: public; Owner: Nikita
--

SELECT pg_catalog.setval('public.anecdote_id_seq', 43, true);


--
-- Name: app_user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: Nikita
--

SELECT pg_catalog.setval('public.app_user_id_seq', 10, true);


--
-- Name: no_id_seq; Type: SEQUENCE SET; Schema: public; Owner: Nikita
--

SELECT pg_catalog.setval('public.no_id_seq', 1, false);


--
-- Name: vote_id_seq; Type: SEQUENCE SET; Schema: public; Owner: Nikita
--

SELECT pg_catalog.setval('public.vote_id_seq', 26, true);


--
-- Name: anecdote anecdote_pkey; Type: CONSTRAINT; Schema: public; Owner: Nikita
--

ALTER TABLE ONLY public.anecdote
    ADD CONSTRAINT anecdote_pkey PRIMARY KEY (id);


--
-- Name: anectodes_topics anectodes_topics_pkey; Type: CONSTRAINT; Schema: public; Owner: Nikita
--

ALTER TABLE ONLY public.anectodes_topics
    ADD CONSTRAINT anectodes_topics_pkey PRIMARY KEY (id_anecdote, id_topic);


--
-- Name: app_user app_user_pkey; Type: CONSTRAINT; Schema: public; Owner: Nikita
--

ALTER TABLE ONLY public.app_user
    ADD CONSTRAINT app_user_pkey PRIMARY KEY (id);


--
-- Name: doctrine_migration_versions doctrine_migration_versions_pkey; Type: CONSTRAINT; Schema: public; Owner: Nikita
--

ALTER TABLE ONLY public.doctrine_migration_versions
    ADD CONSTRAINT doctrine_migration_versions_pkey PRIMARY KEY (version);


--
-- Name: no no_pkey; Type: CONSTRAINT; Schema: public; Owner: Nikita
--

ALTER TABLE ONLY public.no
    ADD CONSTRAINT no_pkey PRIMARY KEY (id);


--
-- Name: topics topics_pkey; Type: CONSTRAINT; Schema: public; Owner: Nikita
--

ALTER TABLE ONLY public.topics
    ADD CONSTRAINT topics_pkey PRIMARY KEY (id);


--
-- Name: vote user_anecdote_unique; Type: CONSTRAINT; Schema: public; Owner: Nikita
--

ALTER TABLE ONLY public.vote
    ADD CONSTRAINT user_anecdote_unique UNIQUE (user_id, anecdote_id);


--
-- Name: vote vote_pkey; Type: CONSTRAINT; Schema: public; Owner: Nikita
--

ALTER TABLE ONLY public.vote
    ADD CONSTRAINT vote_pkey PRIMARY KEY (id);


--
-- Name: uniq_88bdf3e9e7927c74; Type: INDEX; Schema: public; Owner: Nikita
--

CREATE UNIQUE INDEX uniq_88bdf3e9e7927c74 ON public.app_user USING btree (email);


--
-- Name: anecdote fk_anecdote_topic; Type: FK CONSTRAINT; Schema: public; Owner: Nikita
--

ALTER TABLE ONLY public.anecdote
    ADD CONSTRAINT fk_anecdote_topic FOREIGN KEY (topic_id) REFERENCES public.topics(id) ON DELETE SET NULL;


--
-- Name: vote fk_vote_anecdote; Type: FK CONSTRAINT; Schema: public; Owner: Nikita
--

ALTER TABLE ONLY public.vote
    ADD CONSTRAINT fk_vote_anecdote FOREIGN KEY (anecdote_id) REFERENCES public.anecdote(id);


--
-- Name: vote fk_vote_user; Type: FK CONSTRAINT; Schema: public; Owner: Nikita
--

ALTER TABLE ONLY public.vote
    ADD CONSTRAINT fk_vote_user FOREIGN KEY (user_id) REFERENCES public.app_user(id);


--
-- PostgreSQL database dump complete
--

\unrestrict Q0bpI8YAqSYk6WK79OuVs4HxLBaOA52Gmqu9dra5uHinMbEGtb7SadReJgO2SHC

