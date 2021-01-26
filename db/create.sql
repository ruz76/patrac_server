--
-- Name: locations; Type: TABLE; Schema: public; Owner: patrac
--

CREATE TABLE public.locations (
    sysid integer NOT NULL,
    sessionid character varying(50),
    lat double precision,
    lon double precision,
    searchid character varying(20),
    dt_updated timestamp without time zone DEFAULT now() NOT NULL,
    locid integer,
    ts timestamp without time zone
);


ALTER TABLE public.locations OWNER TO patrac;

--
-- Name: locations_sysid_seq; Type: SEQUENCE; Schema: public; Owner: patrac
--

CREATE SEQUENCE public.locations_sysid_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.locations_sysid_seq OWNER TO patrac;

--
-- Name: locations_sysid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: patrac
--

ALTER SEQUENCE public.locations_sysid_seq OWNED BY public.locations.sysid;


--
-- Name: locations sysid; Type: DEFAULT; Schema: public; Owner: patrac
--

ALTER TABLE ONLY public.locations ALTER COLUMN sysid SET DEFAULT nextval('public.locations_sysid_seq'::regclass);


--
-- Name: locations locations_pkey; Type: CONSTRAINT; Schema: public; Owner: patrac
--

ALTER TABLE ONLY public.locations
    ADD CONSTRAINT locations_pkey PRIMARY KEY (sysid);

--
-- Name: messages; Type: TABLE; Schema: public; Owner: patrac
--

CREATE TABLE public.messages (
    sysid integer NOT NULL,
    id integer,
    from_id character varying(50),
    to_id character varying(50),
    message character varying(255),
    file character varying(255),
    searchid character varying(20),
    dt_created timestamp without time zone DEFAULT now(),
    readed integer DEFAULT 0,
    shared integer DEFAULT 0
);


ALTER TABLE public.messages OWNER TO patrac;

--
-- Name: messages_sysid_seq; Type: SEQUENCE; Schema: public; Owner: patrac
--

CREATE SEQUENCE public.messages_sysid_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.messages_sysid_seq OWNER TO patrac;

--
-- Name: messages_sysid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: patrac
--

ALTER SEQUENCE public.messages_sysid_seq OWNED BY public.messages.sysid;


--
-- Name: messages sysid; Type: DEFAULT; Schema: public; Owner: patrac
--

ALTER TABLE ONLY public.messages ALTER COLUMN sysid SET DEFAULT nextval('public.messages_sysid_seq'::regclass);


--
-- Name: messages messages_pkey; Type: CONSTRAINT; Schema: public; Owner: patrac
--

ALTER TABLE ONLY public.messages
    ADD CONSTRAINT messages_pkey PRIMARY KEY (sysid);

--
-- Name: searches; Type: TABLE; Schema: public; Owner: patrac
--

CREATE TABLE public.searches (
    sysid integer NOT NULL,
    searchid character varying(20),
    dt_created timestamp without time zone DEFAULT now(),
    status character varying(20),
    description character varying(255),
    region character varying(2),
    name character varying(255),
    version integer,
    accesskey character varying(50)
);


ALTER TABLE public.searches OWNER TO patrac;

--
-- Name: searches_sysid_seq; Type: SEQUENCE; Schema: public; Owner: patrac
--

CREATE SEQUENCE public.searches_sysid_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.searches_sysid_seq OWNER TO patrac;

--
-- Name: searches_sysid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: patrac
--

ALTER SEQUENCE public.searches_sysid_seq OWNED BY public.searches.sysid;


--
-- Name: searches sysid; Type: DEFAULT; Schema: public; Owner: patrac
--

ALTER TABLE ONLY public.searches ALTER COLUMN sysid SET DEFAULT nextval('public.searches_sysid_seq'::regclass);


--
-- Name: searches searches_pkey; Type: CONSTRAINT; Schema: public; Owner: patrac
--

ALTER TABLE ONLY public.searches
    ADD CONSTRAINT searches_pkey PRIMARY KEY (sysid);

--
-- Name: users; Type: TABLE; Schema: public; Owner: patrac
--

CREATE TABLE public.users (
    sysid integer NOT NULL,
    sessionid character varying(50),
    id character varying(50),
    name character varying(50),
    searchid character varying(20),
    status character varying(20),
    lat double precision,
    lon double precision,
    arrive character varying(10),
    firebase text,
    dt_updated timestamp without time zone DEFAULT now() NOT NULL,
    status_requested character varying(20),
    role character varying(10)
);


ALTER TABLE public.users OWNER TO patrac;

--
-- Name: users_sysid_seq; Type: SEQUENCE; Schema: public; Owner: patrac
--

CREATE SEQUENCE public.users_sysid_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_sysid_seq OWNER TO patrac;

--
-- Name: users_sysid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: patrac
--

ALTER SEQUENCE public.users_sysid_seq OWNED BY public.users.sysid;


--
-- Name: users sysid; Type: DEFAULT; Schema: public; Owner: patrac
--

ALTER TABLE ONLY public.users ALTER COLUMN sysid SET DEFAULT nextval('public.users_sysid_seq'::regclass);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: patrac
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (sysid);
