-- Table: public.roles

-- DROP TABLE IF EXISTS public."roles";

CREATE TABLE IF NOT EXISTS public."roles"
(
    id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name character varying(10) UNIQUE NOT NULL -- 'admin' / 'user'
    )

    TABLESPACE pg_default;

INSERT INTO public.roles (name) VALUES ('admin'), ('user');

ALTER TABLE IF EXISTS public."roles"
    OWNER to devuser;

-- Table: public.user

-- DROP TABLE IF EXISTS public."user";

CREATE TABLE IF NOT EXISTS public."user"
(
    id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    firstname character varying(50) COLLATE pg_catalog."default",
    lastname character varying(100) COLLATE pg_catalog."default",
    email character varying(320) COLLATE pg_catalog."default" NOT NULL,
    pwd character varying(255) COLLATE pg_catalog."default" NOT NULL,
    is_active boolean DEFAULT false,
    date_created date NOT NULL,
    date_updated date,
    role_id integer,
    CONSTRAINT fk_role FOREIGN KEY (role_id) REFERENCES public."roles"(id)
    )

    TABLESPACE pg_default;

ALTER TABLE IF EXISTS public."user"
    OWNER to devuser;

-- Table: public.user_tokens

-- DROP TABLE IF EXISTS public."user_tokens";

CREATE TABLE IF NOT EXISTS public."user_tokens"
(
    id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    user_id integer NOT NULL,
    token character varying(255) NOT NULL,
    type character varying(10) NOT NULL, -- 'validation' / 'reset'
    expiry timestamp,
    created_at date DEFAULT CURRENT_DATE,
    CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES public."user"(id) ON DELETE CASCADE

    )

    TABLESPACE pg_default;

ALTER TABLE IF EXISTS public."user_tokens"
    OWNER to devuser;

-- Table: public.pages

-- DROP TABLE IF EXISTS public."pages";

CREATE TABLE IF NOT EXISTS public."pages"
(
    id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    title character varying(60) NOT NULL,
    slug character varying(200) UNIQUE NOT NULL,
    content text,
    meta_description character varying(158),
    is_published boolean DEFAULT FALSE,
    created_at date DEFAULT CURRENT_DATE,
    updated_at date,
    author_id integer,
    CONSTRAINT fk_author FOREIGN KEY (author_id) REFERENCES public."user"(id)
    )

    TABLESPACE pg_default;

ALTER TABLE IF EXISTS public."pages"
    OWNER to devuser;
