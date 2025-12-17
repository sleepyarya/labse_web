--
-- PostgreSQL database dump
--

\restrict HahbUlY5abDZbMTH6TF57QDFdhDZaUyOWPwHdEvPZtlsQwGcN4Y3rtfdALsfjKL

-- Dumped from database version 15.14
-- Dumped by pg_dump version 15.14

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

ALTER TABLE IF EXISTS ONLY public.produk DROP CONSTRAINT IF EXISTS produk_personil_id_fkey;
ALTER TABLE IF EXISTS ONLY public.produk DROP CONSTRAINT IF EXISTS produk_kategori_id_fkey;
ALTER TABLE IF EXISTS ONLY public.pengabdian DROP CONSTRAINT IF EXISTS pengabdian_personil_id_fkey;
ALTER TABLE IF EXISTS ONLY public.penelitian DROP CONSTRAINT IF EXISTS penelitian_mahasiswa_id_fkey;
ALTER TABLE IF EXISTS ONLY public.mahasiswa DROP CONSTRAINT IF EXISTS mahasiswa_dosen_pembimbing_id_fkey;
ALTER TABLE IF EXISTS ONLY public.komentar_penelitian DROP CONSTRAINT IF EXISTS komentar_penelitian_user_id_fkey;
ALTER TABLE IF EXISTS ONLY public.komentar_penelitian DROP CONSTRAINT IF EXISTS komentar_penelitian_penelitian_id_fkey;
ALTER TABLE IF EXISTS ONLY public.hasil_penelitian DROP CONSTRAINT IF EXISTS hasil_penelitian_personil_id_fkey;
ALTER TABLE IF EXISTS ONLY public.hasil_penelitian DROP CONSTRAINT IF EXISTS hasil_penelitian_kategori_id_fkey;
ALTER TABLE IF EXISTS ONLY public.produk DROP CONSTRAINT IF EXISTS fk_produk_personil;
ALTER TABLE IF EXISTS ONLY public.produk DROP CONSTRAINT IF EXISTS fk_produk_kategori;
ALTER TABLE IF EXISTS ONLY public.pengabdian DROP CONSTRAINT IF EXISTS fk_pengabdian_personil;
ALTER TABLE IF EXISTS ONLY public.hasil_penelitian DROP CONSTRAINT IF EXISTS fk_penelitian_personil;
ALTER TABLE IF EXISTS ONLY public.hasil_penelitian DROP CONSTRAINT IF EXISTS fk_penelitian_kategori;
ALTER TABLE IF EXISTS ONLY public.mahasiswa DROP CONSTRAINT IF EXISTS fk_mahasiswa_approved_by;
ALTER TABLE IF EXISTS ONLY public.artikel DROP CONSTRAINT IF EXISTS fk_artikel_personil;
ALTER TABLE IF EXISTS ONLY public.activity_logs DROP CONSTRAINT IF EXISTS fk_activity_personil;
DROP TRIGGER IF EXISTS update_users_updated_at ON public.users;
DROP TRIGGER IF EXISTS update_produk_updated_at ON public.produk;
DROP TRIGGER IF EXISTS update_personil_updated_at ON public.personil;
DROP TRIGGER IF EXISTS update_pengabdian_updated_at ON public.pengabdian;
DROP TRIGGER IF EXISTS update_penelitian_updated_at ON public.hasil_penelitian;
DROP TRIGGER IF EXISTS update_lab_profile_updated_at ON public.lab_profile;
DROP INDEX IF EXISTS public.idx_users_username;
DROP INDEX IF EXISTS public.idx_users_role;
DROP INDEX IF EXISTS public.idx_users_reference;
DROP INDEX IF EXISTS public.idx_users_email;
DROP INDEX IF EXISTS public.idx_recruitment_settings_is_open;
DROP INDEX IF EXISTS public.idx_produk_tahun;
DROP INDEX IF EXISTS public.idx_produk_personil_id;
DROP INDEX IF EXISTS public.idx_produk_kategori_id;
DROP INDEX IF EXISTS public.idx_produk_kategori;
DROP INDEX IF EXISTS public.idx_personil_email;
DROP INDEX IF EXISTS public.idx_pengabdian_tanggal;
DROP INDEX IF EXISTS public.idx_pengabdian_personil_id;
DROP INDEX IF EXISTS public.idx_penelitian_tahun;
DROP INDEX IF EXISTS public.idx_penelitian_personil_id;
DROP INDEX IF EXISTS public.idx_penelitian_mahasiswa;
DROP INDEX IF EXISTS public.idx_penelitian_kategori_id;
DROP INDEX IF EXISTS public.idx_mahasiswa_status;
DROP INDEX IF EXISTS public.idx_mahasiswa_dosen;
DROP INDEX IF EXISTS public.idx_mahasiswa_approved_by;
DROP INDEX IF EXISTS public.idx_komentar_penelitian;
DROP INDEX IF EXISTS public.idx_kategori_produk_is_active;
DROP INDEX IF EXISTS public.idx_kategori_penelitian_is_active;
DROP INDEX IF EXISTS public.idx_kategori_artikel_is_active;
DROP INDEX IF EXISTS public.idx_jurusan_is_active;
DROP INDEX IF EXISTS public.idx_artikel_personil;
DROP INDEX IF EXISTS public.idx_admin_users_updated_at;
DROP INDEX IF EXISTS public.idx_admin_users_foto;
DROP INDEX IF EXISTS public.idx_activity_logs_target;
DROP INDEX IF EXISTS public.idx_activity_logs_personil;
DROP INDEX IF EXISTS public.idx_activity_logs_created_at;
DROP INDEX IF EXISTS public.idx_activity_logs_action_type;
ALTER TABLE IF EXISTS ONLY public.users DROP CONSTRAINT IF EXISTS users_username_key;
ALTER TABLE IF EXISTS ONLY public.users DROP CONSTRAINT IF EXISTS users_pkey;
ALTER TABLE IF EXISTS ONLY public.users DROP CONSTRAINT IF EXISTS users_email_key;
ALTER TABLE IF EXISTS ONLY public.recruitment_settings DROP CONSTRAINT IF EXISTS recruitment_settings_pkey;
ALTER TABLE IF EXISTS ONLY public.produk DROP CONSTRAINT IF EXISTS produk_pkey;
ALTER TABLE IF EXISTS ONLY public.personil DROP CONSTRAINT IF EXISTS personil_pkey;
ALTER TABLE IF EXISTS ONLY public.pengabdian DROP CONSTRAINT IF EXISTS pengabdian_pkey;
ALTER TABLE IF EXISTS ONLY public.penelitian DROP CONSTRAINT IF EXISTS penelitian_pkey;
ALTER TABLE IF EXISTS ONLY public.mahasiswa DROP CONSTRAINT IF EXISTS mahasiswa_pkey;
ALTER TABLE IF EXISTS ONLY public.landing_page_content DROP CONSTRAINT IF EXISTS landing_page_content_section_name_key_name_key;
ALTER TABLE IF EXISTS ONLY public.landing_page_content DROP CONSTRAINT IF EXISTS landing_page_content_pkey;
ALTER TABLE IF EXISTS ONLY public.komentar_penelitian DROP CONSTRAINT IF EXISTS komentar_penelitian_pkey;
ALTER TABLE IF EXISTS ONLY public.kategori_produk DROP CONSTRAINT IF EXISTS kategori_produk_slug_key;
ALTER TABLE IF EXISTS ONLY public.kategori_produk DROP CONSTRAINT IF EXISTS kategori_produk_pkey;
ALTER TABLE IF EXISTS ONLY public.kategori_produk DROP CONSTRAINT IF EXISTS kategori_produk_nama_kategori_key;
ALTER TABLE IF EXISTS ONLY public.kategori_penelitian DROP CONSTRAINT IF EXISTS kategori_penelitian_slug_key;
ALTER TABLE IF EXISTS ONLY public.kategori_penelitian DROP CONSTRAINT IF EXISTS kategori_penelitian_pkey;
ALTER TABLE IF EXISTS ONLY public.kategori_penelitian DROP CONSTRAINT IF EXISTS kategori_penelitian_nama_kategori_key;
ALTER TABLE IF EXISTS ONLY public.kategori_artikel DROP CONSTRAINT IF EXISTS kategori_artikel_pkey;
ALTER TABLE IF EXISTS ONLY public.kategori_artikel DROP CONSTRAINT IF EXISTS kategori_artikel_nama_kategori_key;
ALTER TABLE IF EXISTS ONLY public.jurusan DROP CONSTRAINT IF EXISTS jurusan_pkey;
ALTER TABLE IF EXISTS ONLY public.jurusan DROP CONSTRAINT IF EXISTS jurusan_nama_jurusan_key;
ALTER TABLE IF EXISTS ONLY public.hasil_penelitian DROP CONSTRAINT IF EXISTS hasil_penelitian_pkey;
ALTER TABLE IF EXISTS ONLY public.artikel DROP CONSTRAINT IF EXISTS artikel_pkey;
ALTER TABLE IF EXISTS ONLY public.admin_users DROP CONSTRAINT IF EXISTS admin_users_username_key;
ALTER TABLE IF EXISTS ONLY public.admin_users DROP CONSTRAINT IF EXISTS admin_users_pkey;
ALTER TABLE IF EXISTS ONLY public.admin_users DROP CONSTRAINT IF EXISTS admin_users_email_key;
ALTER TABLE IF EXISTS ONLY public.activity_logs DROP CONSTRAINT IF EXISTS activity_logs_pkey;
ALTER TABLE IF EXISTS public.users ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.recruitment_settings ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.produk ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.personil ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.pengabdian ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.penelitian ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.mahasiswa ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.landing_page_content ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.lab_profile ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.komentar_penelitian ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.kategori_produk ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.kategori_penelitian ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.kategori_artikel ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.jurusan ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.hasil_penelitian ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.artikel ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.admin_users ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.activity_logs ALTER COLUMN id DROP DEFAULT;
DROP VIEW IF EXISTS public.view_lab_profile_summary;
DROP SEQUENCE IF EXISTS public.users_id_seq;
DROP TABLE IF EXISTS public.users;
DROP SEQUENCE IF EXISTS public.recruitment_settings_id_seq;
DROP TABLE IF EXISTS public.recruitment_settings;
DROP SEQUENCE IF EXISTS public.produk_id_seq;
DROP TABLE IF EXISTS public.produk;
DROP SEQUENCE IF EXISTS public.personil_id_seq;
DROP TABLE IF EXISTS public.personil;
DROP SEQUENCE IF EXISTS public.pengabdian_id_seq;
DROP TABLE IF EXISTS public.pengabdian;
DROP SEQUENCE IF EXISTS public.penelitian_id_seq;
DROP TABLE IF EXISTS public.penelitian;
DROP SEQUENCE IF EXISTS public.mahasiswa_id_seq;
DROP TABLE IF EXISTS public.mahasiswa;
DROP SEQUENCE IF EXISTS public.landing_page_content_id_seq;
DROP TABLE IF EXISTS public.landing_page_content;
DROP SEQUENCE IF EXISTS public.lab_profile_id_seq;
DROP TABLE IF EXISTS public.lab_profile;
DROP SEQUENCE IF EXISTS public.komentar_penelitian_id_seq;
DROP TABLE IF EXISTS public.komentar_penelitian;
DROP SEQUENCE IF EXISTS public.kategori_produk_id_seq;
DROP TABLE IF EXISTS public.kategori_produk;
DROP SEQUENCE IF EXISTS public.kategori_penelitian_id_seq;
DROP TABLE IF EXISTS public.kategori_penelitian;
DROP SEQUENCE IF EXISTS public.kategori_artikel_id_seq;
DROP TABLE IF EXISTS public.kategori_artikel;
DROP SEQUENCE IF EXISTS public.jurusan_id_seq;
DROP TABLE IF EXISTS public.jurusan;
DROP SEQUENCE IF EXISTS public.hasil_penelitian_id_seq;
DROP TABLE IF EXISTS public.hasil_penelitian;
DROP SEQUENCE IF EXISTS public.artikel_id_seq;
DROP TABLE IF EXISTS public.artikel;
DROP SEQUENCE IF EXISTS public.admin_users_id_seq;
DROP TABLE IF EXISTS public.admin_users;
DROP SEQUENCE IF EXISTS public.activity_logs_id_seq;
DROP TABLE IF EXISTS public.activity_logs;
DROP FUNCTION IF EXISTS public.update_updated_at_column();
DROP FUNCTION IF EXISTS public.get_student_statistics(student_id integer);
DROP FUNCTION IF EXISTS public.approve_student_research(research_id integer, reviewer_id integer);
--
-- Name: approve_student_research(integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.approve_student_research(research_id integer, reviewer_id integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
    -- Update status penelitian
    UPDATE penelitian 
    SET status = 'approved', 
        updated_at = NOW()
    WHERE id = research_id;
    
    -- Bisa ditambahkan logika lain di sini, misal insert ke tabel log notifikasi
    -- Untuk saat ini kita raise notice saja
    RAISE NOTICE 'Research ID % approved by Reviewer ID %', research_id, reviewer_id;
END;
$$;


ALTER FUNCTION public.approve_student_research(research_id integer, reviewer_id integer) OWNER TO postgres;

--
-- Name: get_student_statistics(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.get_student_statistics(student_id integer) RETURNS TABLE(total_submissions bigint, approved_submissions bigint, pending_submissions bigint)
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN QUERY
    SELECT 
        COUNT(*) as total,
        COUNT(*) FILTER (WHERE status = 'approved') as approved,
        COUNT(*) FILTER (WHERE status = 'pending') as pending
    FROM penelitian
    WHERE mahasiswa_id = student_id;
END;
$$;


ALTER FUNCTION public.get_student_statistics(student_id integer) OWNER TO postgres;

--
-- Name: update_updated_at_column(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.update_updated_at_column() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.update_updated_at_column() OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: activity_logs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.activity_logs (
    id integer NOT NULL,
    personil_id integer NOT NULL,
    personil_nama character varying(255) NOT NULL,
    action_type character varying(100) NOT NULL,
    action_description text NOT NULL,
    target_type character varying(50),
    target_id integer,
    ip_address character varying(45),
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.activity_logs OWNER TO postgres;

--
-- Name: TABLE activity_logs; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.activity_logs IS 'Tabel untuk menyimpan riwayat aktivitas personil';


--
-- Name: COLUMN activity_logs.action_type; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.activity_logs.action_type IS 'Tipe aktivitas: LOGIN, LOGOUT, CREATE_ARTICLE, EDIT_ARTICLE, DELETE_ARTICLE, dll';


--
-- Name: COLUMN activity_logs.action_description; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.activity_logs.action_description IS 'Deskripsi detail aktivitas dalam bahasa Indonesia';


--
-- Name: COLUMN activity_logs.target_type; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.activity_logs.target_type IS 'Jenis target: artikel, penelitian, pengabdian, produk, profile';


--
-- Name: COLUMN activity_logs.target_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.activity_logs.target_id IS 'ID dari target yang dimanipulasi';


--
-- Name: COLUMN activity_logs.ip_address; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.activity_logs.ip_address IS 'IP address saat aktivitas dilakukan';


--
-- Name: activity_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.activity_logs_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.activity_logs_id_seq OWNER TO postgres;

--
-- Name: activity_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.activity_logs_id_seq OWNED BY public.activity_logs.id;


--
-- Name: admin_users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.admin_users (
    id integer NOT NULL,
    username character varying(100) NOT NULL,
    password character varying(255) NOT NULL,
    nama_lengkap character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    last_login timestamp without time zone,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    foto character varying(255),
    updated_at timestamp without time zone
);


ALTER TABLE public.admin_users OWNER TO postgres;

--
-- Name: COLUMN admin_users.foto; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.admin_users.foto IS 'Filename of admin profile photo';


--
-- Name: COLUMN admin_users.updated_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.admin_users.updated_at IS 'Last profile update timestamp';


--
-- Name: admin_users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.admin_users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.admin_users_id_seq OWNER TO postgres;

--
-- Name: admin_users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.admin_users_id_seq OWNED BY public.admin_users.id;


--
-- Name: artikel; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.artikel (
    id integer NOT NULL,
    judul character varying(255) NOT NULL,
    isi text NOT NULL,
    penulis character varying(255) NOT NULL,
    gambar character varying(255),
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    personil_id integer
);


ALTER TABLE public.artikel OWNER TO postgres;

--
-- Name: artikel_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.artikel_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.artikel_id_seq OWNER TO postgres;

--
-- Name: artikel_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.artikel_id_seq OWNED BY public.artikel.id;


--
-- Name: hasil_penelitian; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.hasil_penelitian (
    id integer NOT NULL,
    judul character varying(255) NOT NULL,
    deskripsi text NOT NULL,
    tahun integer NOT NULL,
    kategori character varying(100),
    kategori_id integer,
    abstrak text,
    gambar character varying(255),
    file_pdf character varying(255),
    link_publikasi text,
    personil_id integer,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.hasil_penelitian OWNER TO postgres;

--
-- Name: hasil_penelitian_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.hasil_penelitian_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.hasil_penelitian_id_seq OWNER TO postgres;

--
-- Name: hasil_penelitian_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.hasil_penelitian_id_seq OWNED BY public.hasil_penelitian.id;


--
-- Name: jurusan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.jurusan (
    id integer NOT NULL,
    nama_jurusan character varying(255) NOT NULL,
    kode_jurusan character varying(20),
    deskripsi text,
    is_active boolean DEFAULT true,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.jurusan OWNER TO postgres;

--
-- Name: jurusan_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jurusan_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.jurusan_id_seq OWNER TO postgres;

--
-- Name: jurusan_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jurusan_id_seq OWNED BY public.jurusan.id;


--
-- Name: kategori_artikel; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kategori_artikel (
    id integer NOT NULL,
    nama_kategori character varying(100) NOT NULL,
    slug character varying(100),
    deskripsi text,
    warna character varying(20) DEFAULT '#0d6efd'::character varying,
    is_active boolean DEFAULT true,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.kategori_artikel OWNER TO postgres;

--
-- Name: kategori_artikel_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.kategori_artikel_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.kategori_artikel_id_seq OWNER TO postgres;

--
-- Name: kategori_artikel_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.kategori_artikel_id_seq OWNED BY public.kategori_artikel.id;


--
-- Name: kategori_penelitian; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kategori_penelitian (
    id integer NOT NULL,
    nama_kategori character varying(100) NOT NULL,
    slug character varying(100) NOT NULL,
    deskripsi text,
    warna character varying(20) DEFAULT '#0d6efd'::character varying,
    is_active boolean DEFAULT true,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.kategori_penelitian OWNER TO postgres;

--
-- Name: kategori_penelitian_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.kategori_penelitian_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.kategori_penelitian_id_seq OWNER TO postgres;

--
-- Name: kategori_penelitian_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.kategori_penelitian_id_seq OWNED BY public.kategori_penelitian.id;


--
-- Name: kategori_produk; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kategori_produk (
    id integer NOT NULL,
    nama_kategori character varying(100) NOT NULL,
    slug character varying(100) NOT NULL,
    deskripsi text,
    warna character varying(20) DEFAULT '#0d6efd'::character varying,
    is_active boolean DEFAULT true,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.kategori_produk OWNER TO postgres;

--
-- Name: kategori_produk_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.kategori_produk_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.kategori_produk_id_seq OWNER TO postgres;

--
-- Name: kategori_produk_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.kategori_produk_id_seq OWNED BY public.kategori_produk.id;


--
-- Name: komentar_penelitian; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.komentar_penelitian (
    id integer NOT NULL,
    penelitian_id integer NOT NULL,
    user_id integer NOT NULL,
    isi text NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.komentar_penelitian OWNER TO postgres;

--
-- Name: komentar_penelitian_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.komentar_penelitian_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.komentar_penelitian_id_seq OWNER TO postgres;

--
-- Name: komentar_penelitian_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.komentar_penelitian_id_seq OWNED BY public.komentar_penelitian.id;


--
-- Name: lab_profile; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.lab_profile (
    id integer NOT NULL,
    judul character varying(255) NOT NULL,
    konten text NOT NULL,
    kategori character varying(100) NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.lab_profile OWNER TO postgres;

--
-- Name: lab_profile_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.lab_profile_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.lab_profile_id_seq OWNER TO postgres;

--
-- Name: lab_profile_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.lab_profile_id_seq OWNED BY public.lab_profile.id;


--
-- Name: landing_page_content; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.landing_page_content (
    id integer NOT NULL,
    section_name character varying(50) NOT NULL,
    key_name character varying(50) NOT NULL,
    content_value text,
    content_type character varying(20) DEFAULT 'text'::character varying,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.landing_page_content OWNER TO postgres;

--
-- Name: landing_page_content_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.landing_page_content_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.landing_page_content_id_seq OWNER TO postgres;

--
-- Name: landing_page_content_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.landing_page_content_id_seq OWNED BY public.landing_page_content.id;


--
-- Name: mahasiswa; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.mahasiswa (
    id integer NOT NULL,
    nama character varying(255) NOT NULL,
    nim character varying(50) NOT NULL,
    jurusan character varying(100) NOT NULL,
    email character varying(255) NOT NULL,
    alasan text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    status_approval character varying(20) DEFAULT 'pending'::character varying,
    approved_by integer,
    approved_at timestamp without time zone,
    rejection_reason text,
    dosen_pembimbing_id integer
);


ALTER TABLE public.mahasiswa OWNER TO postgres;

--
-- Name: COLUMN mahasiswa.status_approval; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.mahasiswa.status_approval IS 'Status persetujuan: pending, approved, rejected';


--
-- Name: COLUMN mahasiswa.approved_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.mahasiswa.approved_by IS 'ID admin yang menyetujui/menolak';


--
-- Name: COLUMN mahasiswa.approved_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.mahasiswa.approved_at IS 'Waktu persetujuan/penolakan';


--
-- Name: COLUMN mahasiswa.rejection_reason; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.mahasiswa.rejection_reason IS 'Alasan penolakan (jika ditolak)';


--
-- Name: mahasiswa_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.mahasiswa_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.mahasiswa_id_seq OWNER TO postgres;

--
-- Name: mahasiswa_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.mahasiswa_id_seq OWNED BY public.mahasiswa.id;


--
-- Name: penelitian; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.penelitian (
    id integer NOT NULL,
    mahasiswa_id integer NOT NULL,
    judul character varying(255) NOT NULL,
    file_path character varying(255),
    link_drive text,
    keterangan text,
    status character varying(50) DEFAULT 'submitted'::character varying,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.penelitian OWNER TO postgres;

--
-- Name: penelitian_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.penelitian_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.penelitian_id_seq OWNER TO postgres;

--
-- Name: penelitian_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.penelitian_id_seq OWNED BY public.penelitian.id;


--
-- Name: pengabdian; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pengabdian (
    id integer NOT NULL,
    judul character varying(255) NOT NULL,
    deskripsi text NOT NULL,
    tanggal date NOT NULL,
    lokasi character varying(255) NOT NULL,
    penyelenggara character varying(255) NOT NULL,
    gambar character varying(255),
    personil_id integer,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.pengabdian OWNER TO postgres;

--
-- Name: pengabdian_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.pengabdian_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.pengabdian_id_seq OWNER TO postgres;

--
-- Name: pengabdian_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.pengabdian_id_seq OWNED BY public.pengabdian.id;


--
-- Name: personil; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.personil (
    id integer NOT NULL,
    nama character varying(255) NOT NULL,
    jabatan character varying(100) NOT NULL,
    deskripsi text,
    foto character varying(255),
    email character varying(255),
    password character varying(255),
    is_member boolean DEFAULT false,
    last_login timestamp without time zone,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.personil OWNER TO postgres;

--
-- Name: personil_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.personil_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.personil_id_seq OWNER TO postgres;

--
-- Name: personil_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.personil_id_seq OWNED BY public.personil.id;


--
-- Name: produk; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.produk (
    id integer NOT NULL,
    nama_produk character varying(255) NOT NULL,
    deskripsi text NOT NULL,
    kategori character varying(100),
    kategori_id integer,
    tahun integer NOT NULL,
    gambar character varying(255),
    link_demo text,
    link_repository text,
    teknologi text,
    personil_id integer,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.produk OWNER TO postgres;

--
-- Name: produk_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.produk_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.produk_id_seq OWNER TO postgres;

--
-- Name: produk_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.produk_id_seq OWNED BY public.produk.id;


--
-- Name: recruitment_settings; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.recruitment_settings (
    id integer NOT NULL,
    is_open boolean DEFAULT true NOT NULL,
    message text DEFAULT 'Maaf, Lab SE sedang tidak membuka recruitment saat ini. Silakan cek kembali nanti.'::text,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_by character varying(255) DEFAULT 'System'::character varying
);


ALTER TABLE public.recruitment_settings OWNER TO postgres;

--
-- Name: TABLE recruitment_settings; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.recruitment_settings IS 'Tabel untuk mengatur status recruitment (buka/tutup) dan pesan custom';


--
-- Name: COLUMN recruitment_settings.is_open; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.recruitment_settings.is_open IS 'Status recruitment: TRUE = buka, FALSE = tutup';


--
-- Name: COLUMN recruitment_settings.message; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.recruitment_settings.message IS 'Pesan yang ditampilkan ketika recruitment ditutup';


--
-- Name: COLUMN recruitment_settings.updated_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.recruitment_settings.updated_at IS 'Waktu terakhir update setting';


--
-- Name: COLUMN recruitment_settings.updated_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.recruitment_settings.updated_by IS 'Admin yang melakukan update terakhir';


--
-- Name: recruitment_settings_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.recruitment_settings_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.recruitment_settings_id_seq OWNER TO postgres;

--
-- Name: recruitment_settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.recruitment_settings_id_seq OWNED BY public.recruitment_settings.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id integer NOT NULL,
    username character varying(100) NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    role character varying(20) NOT NULL,
    reference_id integer NOT NULL,
    is_active boolean DEFAULT true,
    last_login timestamp without time zone,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT users_role_check CHECK (((role)::text = ANY (ARRAY[('admin'::character varying)::text, ('personil'::character varying)::text, ('mahasiswa'::character varying)::text])))
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Name: TABLE users; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.users IS 'Tabel pusat autentikasi untuk semua pengguna sistem';


--
-- Name: COLUMN users.role; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.users.role IS 'Role pengguna: admin, personil, atau mahasiswa';


--
-- Name: COLUMN users.reference_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.users.reference_id IS 'ID referensi ke tabel asli sesuai role';


--
-- Name: COLUMN users.is_active; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.users.is_active IS 'Status aktif user, untuk soft delete';


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
-- Name: view_lab_profile_summary; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.view_lab_profile_summary AS
 SELECT lab_profile.kategori,
    count(*) AS total_item,
    max(lab_profile.updated_at) AS last_update
   FROM public.lab_profile
  GROUP BY lab_profile.kategori;


ALTER TABLE public.view_lab_profile_summary OWNER TO postgres;

--
-- Name: activity_logs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.activity_logs ALTER COLUMN id SET DEFAULT nextval('public.activity_logs_id_seq'::regclass);


--
-- Name: admin_users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.admin_users ALTER COLUMN id SET DEFAULT nextval('public.admin_users_id_seq'::regclass);


--
-- Name: artikel id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.artikel ALTER COLUMN id SET DEFAULT nextval('public.artikel_id_seq'::regclass);


--
-- Name: hasil_penelitian id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.hasil_penelitian ALTER COLUMN id SET DEFAULT nextval('public.hasil_penelitian_id_seq'::regclass);


--
-- Name: jurusan id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jurusan ALTER COLUMN id SET DEFAULT nextval('public.jurusan_id_seq'::regclass);


--
-- Name: kategori_artikel id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kategori_artikel ALTER COLUMN id SET DEFAULT nextval('public.kategori_artikel_id_seq'::regclass);


--
-- Name: kategori_penelitian id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kategori_penelitian ALTER COLUMN id SET DEFAULT nextval('public.kategori_penelitian_id_seq'::regclass);


--
-- Name: kategori_produk id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kategori_produk ALTER COLUMN id SET DEFAULT nextval('public.kategori_produk_id_seq'::regclass);


--
-- Name: komentar_penelitian id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.komentar_penelitian ALTER COLUMN id SET DEFAULT nextval('public.komentar_penelitian_id_seq'::regclass);


--
-- Name: lab_profile id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lab_profile ALTER COLUMN id SET DEFAULT nextval('public.lab_profile_id_seq'::regclass);


--
-- Name: landing_page_content id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.landing_page_content ALTER COLUMN id SET DEFAULT nextval('public.landing_page_content_id_seq'::regclass);


--
-- Name: mahasiswa id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mahasiswa ALTER COLUMN id SET DEFAULT nextval('public.mahasiswa_id_seq'::regclass);


--
-- Name: penelitian id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penelitian ALTER COLUMN id SET DEFAULT nextval('public.penelitian_id_seq'::regclass);


--
-- Name: pengabdian id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengabdian ALTER COLUMN id SET DEFAULT nextval('public.pengabdian_id_seq'::regclass);


--
-- Name: personil id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personil ALTER COLUMN id SET DEFAULT nextval('public.personil_id_seq'::regclass);


--
-- Name: produk id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.produk ALTER COLUMN id SET DEFAULT nextval('public.produk_id_seq'::regclass);


--
-- Name: recruitment_settings id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.recruitment_settings ALTER COLUMN id SET DEFAULT nextval('public.recruitment_settings_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: activity_logs; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (1, 3, 'Budi Santoso, Ph.D', 'LOGIN', 'Login ke dashboard', NULL, NULL, '::1', '2025-12-03 05:32:35.039678');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (2, 3, 'Budi Santoso, Ph.D', 'CREATE_PRODUK', 'Membuat produk baru: E-Learning Platform', 'produk', 7, '::1', '2025-12-03 05:32:53.126394');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (3, 3, 'Budi Santoso, Ph.D', 'LOGOUT', 'Logout dari dashboard', NULL, NULL, '::1', '2025-12-03 05:32:56.560282');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (4, 3, 'Budi Santoso, Ph.D', 'LOGIN', 'Login ke dashboard', NULL, NULL, '::1', '2025-12-04 08:35:57.235616');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (5, 3, 'Budi Santoso, Ph.D', 'LOGOUT', 'Logout dari dashboard', NULL, NULL, '::1', '2025-12-04 08:36:46.202988');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (6, 3, 'Budi Santoso, Ph.D', 'LOGIN', 'Login ke dashboard', NULL, NULL, '::1', '2025-12-04 08:47:16.586551');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (7, 3, 'Budi Santoso, Ph.D', 'LOGOUT', 'Logout dari dashboard', NULL, NULL, '::1', '2025-12-04 08:51:31.239095');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (8, 3, 'Budi Santoso, Ph.D', 'LOGIN', 'Login ke dashboard', NULL, NULL, '::1', '2025-12-04 08:53:08.604391');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (9, 3, 'Budi Santoso, Ph.D', 'LOGOUT', 'Logout dari dashboard', NULL, NULL, '::1', '2025-12-04 08:53:27.610219');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (10, 3, 'Budi Santoso, Ph.D', 'LOGIN', 'Login ke dashboard', NULL, NULL, '::1', '2025-12-04 08:54:08.046884');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (11, 3, 'Budi Santoso, Ph.D', 'EDIT_ARTICLE', 'Mengedit artikel: Belajar Stored Procedure', 'artikel', 10, '::1', '2025-12-04 08:55:17.542624');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (12, 3, 'Budi Santoso, Ph.D', 'EDIT_ARTICLE', 'Mengedit artikel: Belajar Stored Procedure', 'artikel', 10, '::1', '2025-12-04 08:55:24.198631');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (13, 3, 'Budi Santoso, Ph.D', 'EDIT_ARTICLE', 'Mengedit artikel: Belajar Stored Procedure', 'artikel', 10, '::1', '2025-12-04 08:55:34.941773');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (14, 3, 'Budi Santoso, Ph.D', 'LOGIN', 'Login ke dashboard', NULL, NULL, '::1', '2025-12-04 08:57:28.387927');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (15, 3, 'Budi Santoso, Ph.D', 'CREATE_ARTICLE', 'Membuat artikel baru: Misi 1', 'artikel', 11, '::1', '2025-12-04 09:00:02.821974');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (16, 3, 'Budi Santoso, Ph.D', 'DELETE_ARTICLE', 'Menghapus artikel: Misi 1', 'artikel', 11, '::1', '2025-12-04 09:00:09.637971');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (17, 3, 'Budi Santoso, Ph.D', 'LOGOUT', 'Logout dari dashboard', NULL, NULL, '::1', '2025-12-04 09:01:01.035819');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (18, 3, 'Budi Santoso, Ph.D', 'LOGIN', 'Login ke dashboard', NULL, NULL, '::1', '2025-12-06 06:58:19.361911');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (19, 5, 'Muhammad Rizki, M.Kom', 'LOGIN', 'Login ke dashboard', NULL, NULL, '::1', '2025-12-10 15:54:31.483265');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (20, 5, 'Muhammad Rizki, M.Kom', 'CREATE_PENELITIAN', 'Membuat penelitian baru: ASASASASAASASS', 'penelitian', 7, '::1', '2025-12-10 15:55:18.572424');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (21, 5, 'Muhammad Rizki, M.Kom', 'LOGOUT', 'Logout dari dashboard', NULL, NULL, '::1', '2025-12-10 15:55:22.023584');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (22, 3, 'Budi Santoso, Ph.D', 'LOGIN', 'Login ke dashboard', NULL, NULL, '::1', '2025-12-10 20:56:12.525824');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (23, 3, 'Budi Santoso, Ph.D', 'EDIT_PENELITIAN', 'Mengedit penelitian: Misi', 'penelitian', 1, '::1', '2025-12-10 21:01:44.712716');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (24, 3, 'Budi Santoso, Ph.D', 'EDIT_PENELITIAN', 'Mengedit penelitian: Misi', 'penelitian', 1, '::1', '2025-12-10 21:02:01.288597');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (25, 3, 'Budi Santoso, Ph.D', 'EDIT_PENELITIAN', 'Mengedit penelitian: Misi', 'penelitian', 1, '::1', '2025-12-10 21:03:11.259399');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (26, 3, 'Budi Santoso, Ph.D', 'EDIT_PENELITIAN', 'Mengedit penelitian: Misi', 'penelitian', 1, '::1', '2025-12-10 21:10:15.433499');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (27, 3, 'Budi Santoso, Ph.D', 'EDIT_PENELITIAN', 'Mengedit penelitian: Misi', 'penelitian', 1, '::1', '2025-12-10 21:10:24.471109');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (28, 3, 'Budi Santoso, Ph.D', 'LOGIN', 'Login ke dashboard', NULL, NULL, '::1', '2025-12-11 11:58:53.19969');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (29, 3, 'Budi Santoso, Ph.D', 'EDIT_ARTICLE', 'Mengedit artikel: Belajar Stored Procedure', 'artikel', 10, '::1', '2025-12-11 11:59:05.608622');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (30, 3, 'Budi Santoso, Ph.D', 'EDIT_PENELITIAN', 'Mengedit penelitian: Misi', 'penelitian', 1, '::1', '2025-12-11 11:59:35.451759');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (31, 3, 'Budi Santoso, Ph.D', 'EDIT_PRODUK', 'Mengedit produk: E-Learning Platform', 'produk', 7, '::1', '2025-12-11 11:59:47.985528');
INSERT INTO public.activity_logs (id, personil_id, personil_nama, action_type, action_description, target_type, target_id, ip_address, created_at) VALUES (32, 3, 'Budi Santoso, Ph.D', 'LOGOUT', 'Logout dari dashboard', NULL, NULL, '::1', '2025-12-11 12:13:57.783252');


--
-- Data for Name: admin_users; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.admin_users (id, username, password, nama_lengkap, email, last_login, created_at, foto, updated_at) VALUES (3, 'admin', '$2y$10$ZYD8x/o3NotqN0I5O0xFKef9HUtMEGl3gvHCGTULcZtNkkqLxO0Rq', 'Administrato', 'admin@labse.ac.id', '2025-11-13 09:41:27.86994', '2025-11-03 11:08:02.875079', NULL, '2025-11-18 15:20:37.134911');


--
-- Data for Name: artikel; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.artikel (id, judul, isi, penulis, gambar, created_at, personil_id) VALUES (4, 'Mobile App Development: Flutter vs React Native di Tahun 2024', 'Memilih framework yang tepat untuk mobile app development adalah keputusan penting yang akan mempengaruhi seluruh lifecycle proyek. Flutter dan React Native adalah dua framework cross-platform paling populer saat ini. Artikel ini membandingkan keduanya dari berbagai aspek seperti performance, developer experience, ecosystem, dan community support berdasarkan pengalaman praktis kami dalam berbagai proyek.', 'Dr. Rina Wijaya, M.Sc', 'mobile-dev.jpg', '2025-10-31 23:24:03.897608', 4);
INSERT INTO public.artikel (id, judul, isi, penulis, gambar, created_at, personil_id) VALUES (6, 'Software Testing Strategy: Unit, Integration, dan E2E Testing', 'Testing adalah komponen vital dalam software development lifecycle. Strategi testing yang komprehensif meliputi unit testing, integration testing, dan end-to-end testing. Artikel ini membahas kapan menggunakan masing-masing jenis testing, tools yang dapat digunakan, dan bagaimana mencapai test coverage yang optimal tanpa mengorbankan development velocity.', 'Dewi Lestari, M.T', 'software-testing.jpg', '2025-10-31 23:24:03.897608', 6);
INSERT INTO public.artikel (id, judul, isi, penulis, gambar, created_at, personil_id) VALUES (5, 'Implementasi CI/CD Pipeline untuk Meningkatkan Produktivitas', 'Continuous Integration dan Continuous Deployment (CI/CD) adalah praktik essential dalam modern software development. Dengan CI/CD pipeline yang baik, tim dapat melakukan deployment lebih cepat dengan risiko yang lebih rendah. Artikel ini menjelaskan step-by-step implementasi CI/CD pipeline menggunakan tools seperti Jenkins, GitLab CI, dan GitHub Actions, lengkap dengan contoh konfigurasi dan best.', 'Muhammad, M.Kom', 'artikel_1762393942_690bff5690eac.png', '2025-10-31 23:24:03.897608', NULL);
INSERT INTO public.artikel (id, judul, isi, penulis, gambar, created_at, personil_id) VALUES (1, 'Penerapan Design Patterns dalam Pengembangan Aplikasi Enterprise', 'Design patterns adalah solusi umum yang dapat digunakan kembali untuk masalah yang sering terjadi dalam desain software. Dalam pengembangan aplikasi enterprise, penerapan design patterns seperti Singleton, Factory, dan Observer sangat penting untuk menciptakan kode yang maintainable dan scalable. Artikel ini membahas bagaimana menerapkan berbagai design patterns dalam konteks aplikasi enterprise modern, termasuk contoh implementasi praktis dan best practices yang perlu diperhatikan', 'Dr. Ahmad Fauzi, M.Kom', '6915342444548.png', '2025-10-31 23:24:03.897608', 1);
INSERT INTO public.artikel (id, judul, isi, penulis, gambar, created_at, personil_id) VALUES (3, 'Microservices Architecture: Keuntungan dan Tantangan Implementasinya', 'Microservices architecture telah menjadi pilihan populer untuk aplikasi skala besar. Arsitektur ini menawarkan fleksibilitas, scalability, dan kemudahan dalam deployment. Namun, implementasinya juga membawa tantangan tersendiri seperti distributed system complexity dan data consistency. Artikel ini memberikan panduan komprehensif tentang bagaimana merancang dan mengimplementasikan microservices architecture dengan efektif.', 'Prof. Dr. Siti Nurhaliza, M.T', 'microservices.jpg', '2025-10-31 23:24:03.897608', 2);
INSERT INTO public.artikel (id, judul, isi, penulis, gambar, created_at, personil_id) VALUES (8, 'asasas', 'asasasasaasasasasasasasasasasasdadsdsdasdsdewddasdxzxzcxcasdasddsdasdawdsdsdsgdyastydayhdvvashgdcahgvsdhgcasdchgasdhgcsgdacdcgsasdasdadasd', 'Muhammad Rizki, M.Kom', NULL, '2025-11-13 07:55:07.57639', 5);
INSERT INTO public.artikel (id, judul, isi, penulis, gambar, created_at, personil_id) VALUES (2, 'Optimasi Performance Aplikasi Web dengan Caching Strategy', 'Performance adalah faktor krusial dalam kesuksesan aplikasi web modern. Salah satu teknik yang paling efektif adalah implementasi caching strategy yang tepat. Artikel ini mengeksplorasi berbagai jenis caching mulai dari browser caching, CDN caching, hingga application-level caching menggunakan Redis atau Memcached. Kami juga membahas kapan dan bagaimana mengimplementasikan masing-masing strategi untuk hasil optimal.', 'Budi Santoso, Ph.D', '6926592b7bd4f.png', '2025-10-31 23:24:03.897608', 3);


--
-- Data for Name: hasil_penelitian; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.hasil_penelitian (id, judul, deskripsi, tahun, kategori, kategori_id, abstrak, gambar, file_pdf, link_publikasi, personil_id, created_at, updated_at) VALUES (2, 'Optimasi Algoritma Machine Learning untuk Prediksi Cuaca', 'Penelitian pengembangan model prediksi cuaca menggunakan ensemble learning dengan kombinasi Random Forest dan Neural Networks.', 2024, 'Fundamental', 1, 'Studi ini mengembangkan model hybrid untuk prediksi cuaca jangka pendek dengan menggabungkan Random Forest dan Deep Neural Networks. Model yang dikembangkan mampu meningkatkan akurasi prediksi hingga 15% dibandingkan model konvensional.', NULL, NULL, NULL, NULL, '2025-12-12 19:52:24.936437', '2025-12-12 19:52:24.941121');
INSERT INTO public.hasil_penelitian (id, judul, deskripsi, tahun, kategori, kategori_id, abstrak, gambar, file_pdf, link_publikasi, personil_id, created_at, updated_at) VALUES (4, 'Analisis Sentimen Media Sosial Menggunakan BERT', 'Implementasi model BERT untuk analisis sentimen bahasa Indonesia dengan fine-tuning pada dataset lokal.', 2023, 'Terapan', 2, 'Penelitian menggunakan pre-trained model BERT yang di-fine-tune dengan dataset sentimen bahasa Indonesia. Hasil menunjukkan akurasi 89.7% dalam klasifikasi sentimen positif, negatif, dan netral.', NULL, NULL, 'https://aclanthology.org/example2', NULL, '2025-12-12 19:52:24.936437', '2025-12-12 19:52:24.941121');
INSERT INTO public.hasil_penelitian (id, judul, deskripsi, tahun, kategori, kategori_id, abstrak, gambar, file_pdf, link_publikasi, personil_id, created_at, updated_at) VALUES (1, 'Sistem Deteksi Objek Menggunakan YOLO v8', 'Implementasi algoritma YOLO v8 untuk deteksi objek real-time dengan akurasi tinggi pada berbagai kondisi pencahayaan.', 2024, 'Terapan', 2, 'Penelitian ini mengimplementasikan algoritma YOLO v8 untuk sistem deteksi objek secara real-time. Hasil penelitian menunjukkan akurasi deteksi mencapai 94.5% pada dataset COCO dengan kecepatan processing 60 FPS.', NULL, NULL, 'https://ieeexplore.ieee.org/document/example1', NULL, '2025-12-12 19:52:24.936437', '2025-12-12 19:52:24.941121');
INSERT INTO public.hasil_penelitian (id, judul, deskripsi, tahun, kategori, kategori_id, abstrak, gambar, file_pdf, link_publikasi, personil_id, created_at, updated_at) VALUES (3, 'Pengembangan Framework IoT untuk Smart Home', 'Framework open-source untuk integrasi berbagai perangkat IoT dalam sistem smart home yang efisien dan scalable.', 2023, 'Pengembangan', 3, 'Penelitian ini menghasilkan framework IoT yang memudahkan integrasi berbagai device smart home. Framework mendukung protokol MQTT, HTTP, dan WebSocket dengan arsitektur microservices.', NULL, NULL, 'https://github.com/example/smart-home-framework', NULL, '2025-12-12 19:52:24.936437', '2025-12-12 19:52:24.941121');


--
-- Data for Name: jurusan; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.jurusan (id, nama_jurusan, kode_jurusan, deskripsi, is_active, created_at, updated_at) VALUES (1, 'Teknik Informatika', 'TI', 'Program studi yang mempelajari tentang pengembangan perangkat lunak, algoritma, dan pemrograman', true, '2025-12-12 19:52:24.914154', '2025-12-12 19:52:24.914154');
INSERT INTO public.jurusan (id, nama_jurusan, kode_jurusan, deskripsi, is_active, created_at, updated_at) VALUES (2, 'Sistem Informasi', 'SI', 'Program studi yang mempelajari tentang sistem informasi bisnis dan manajemen teknologi informasi', true, '2025-12-12 19:52:24.914154', '2025-12-12 19:52:24.914154');
INSERT INTO public.jurusan (id, nama_jurusan, kode_jurusan, deskripsi, is_active, created_at, updated_at) VALUES (3, 'Teknik Komputer', 'TK', 'Program studi yang mempelajari tentang hardware, embedded systems, dan arsitektur komputer', true, '2025-12-12 19:52:24.914154', '2025-12-12 19:52:24.914154');
INSERT INTO public.jurusan (id, nama_jurusan, kode_jurusan, deskripsi, is_active, created_at, updated_at) VALUES (4, 'Teknologi Informasi', 'TIN', 'Program studi yang mempelajari tentang infrastruktur TI dan administrasi jaringan', true, '2025-12-12 19:52:24.914154', '2025-12-12 19:52:24.914154');
INSERT INTO public.jurusan (id, nama_jurusan, kode_jurusan, deskripsi, is_active, created_at, updated_at) VALUES (5, 'Manajemen Informatika', 'MI', 'Program studi yang mempelajari tentang manajemen sistem informasi dan e-business', true, '2025-12-12 19:52:24.914154', '2025-12-12 19:52:24.914154');


--
-- Data for Name: kategori_artikel; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.kategori_artikel (id, nama_kategori, slug, deskripsi, warna, is_active, created_at, updated_at) VALUES (1, 'Teknologi', 'teknologi', 'Artikel tentang teknologi, software, dan hardware terbaru', '#0d6efd', true, '2025-12-12 19:52:24.927763', '2025-12-12 19:52:24.927763');
INSERT INTO public.kategori_artikel (id, nama_kategori, slug, deskripsi, warna, is_active, created_at, updated_at) VALUES (2, 'Penelitian', 'penelitian', 'Artikel tentang hasil penelitian dan akademik', '#198754', true, '2025-12-12 19:52:24.927763', '2025-12-12 19:52:24.927763');
INSERT INTO public.kategori_artikel (id, nama_kategori, slug, deskripsi, warna, is_active, created_at, updated_at) VALUES (3, 'Tutorial', 'tutorial', 'Panduan dan tutorial programming', '#ffc107', true, '2025-12-12 19:52:24.927763', '2025-12-12 19:52:24.927763');
INSERT INTO public.kategori_artikel (id, nama_kategori, slug, deskripsi, warna, is_active, created_at, updated_at) VALUES (4, 'Berita', 'berita', 'Berita terbaru seputar lab dan kampus', '#dc3545', true, '2025-12-12 19:52:24.927763', '2025-12-12 19:52:24.927763');
INSERT INTO public.kategori_artikel (id, nama_kategori, slug, deskripsi, warna, is_active, created_at, updated_at) VALUES (5, 'Tips & Trik', 'tips-trik', 'Tips dan trik seputar IT dan pengembangan', '#6f42c1', true, '2025-12-12 19:52:24.927763', '2025-12-12 19:52:24.927763');


--
-- Data for Name: kategori_penelitian; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.kategori_penelitian (id, nama_kategori, slug, deskripsi, warna, is_active, created_at, updated_at) VALUES (1, 'Fundamental', 'fundamental', 'Penelitian dasar untuk memperluas ilmu pengetahuan', '#0d6efd', true, '2025-12-12 19:52:24.934492', '2025-12-12 19:52:24.934492');
INSERT INTO public.kategori_penelitian (id, nama_kategori, slug, deskripsi, warna, is_active, created_at, updated_at) VALUES (2, 'Terapan', 'terapan', 'Penelitian untuk memecahkan masalah praktis', '#198754', true, '2025-12-12 19:52:24.934492', '2025-12-12 19:52:24.934492');
INSERT INTO public.kategori_penelitian (id, nama_kategori, slug, deskripsi, warna, is_active, created_at, updated_at) VALUES (3, 'Pengembangan', 'pengembangan', 'Penelitian dan pengembangan produk atau sistem', '#ffc107', true, '2025-12-12 19:52:24.934492', '2025-12-12 19:52:24.934492');
INSERT INTO public.kategori_penelitian (id, nama_kategori, slug, deskripsi, warna, is_active, created_at, updated_at) VALUES (4, 'Eksperimental', 'eksperimental', 'Penelitian berbasis eksperimen dan uji coba', '#6610f2', true, '2025-12-12 19:52:24.934492', '2025-12-12 19:52:24.934492');
INSERT INTO public.kategori_penelitian (id, nama_kategori, slug, deskripsi, warna, is_active, created_at, updated_at) VALUES (5, 'Studi Kasus', 'studi-kasus', 'Penelitian mendalam tentang suatu kasus spesifik', '#dc3545', true, '2025-12-12 19:52:24.934492', '2025-12-12 19:52:24.934492');


--
-- Data for Name: kategori_produk; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.kategori_produk (id, nama_kategori, slug, deskripsi, warna, is_active, created_at, updated_at) VALUES (1, 'Web Application', 'web-application', 'Aplikasi berbasis web', '#0d6efd', true, '2025-12-12 19:52:24.94633', '2025-12-12 19:52:24.94633');
INSERT INTO public.kategori_produk (id, nama_kategori, slug, deskripsi, warna, is_active, created_at, updated_at) VALUES (2, 'Mobile Application', 'mobile-application', 'Aplikasi mobile (Android/iOS)', '#6610f2', true, '2025-12-12 19:52:24.94633', '2025-12-12 19:52:24.94633');
INSERT INTO public.kategori_produk (id, nama_kategori, slug, deskripsi, warna, is_active, created_at, updated_at) VALUES (3, 'IoT System', 'iot-system', 'Sistem Internet of Things dan Hardware', '#198754', true, '2025-12-12 19:52:24.94633', '2025-12-12 19:52:24.94633');
INSERT INTO public.kategori_produk (id, nama_kategori, slug, deskripsi, warna, is_active, created_at, updated_at) VALUES (4, 'AI & Machine Learning', 'ai-ml', 'Implementasi kecerdasan buatan', '#ffc107', true, '2025-12-12 19:52:24.94633', '2025-12-12 19:52:24.94633');
INSERT INTO public.kategori_produk (id, nama_kategori, slug, deskripsi, warna, is_active, created_at, updated_at) VALUES (5, 'Desktop Application', 'desktop-application', 'Aplikasi desktop', '#dc3545', true, '2025-12-12 19:52:24.94633', '2025-12-12 19:52:24.94633');


--
-- Data for Name: komentar_penelitian; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Data for Name: lab_profile; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (1, 'Tentang Lab Software Engineering', 'Laboratorium Software Engineering adalah pusat keunggulan dalam pengembangan perangkat lunak yang berfokus pada penelitian, pengembangan, dan implementasi praktik terbaik dalam rekayasa perangkat lunak. Kami berkomitmen untuk menghasilkan lulusan yang kompeten dan siap menghadapi tantangan industri teknologi informasi.', 'tentang', '2025-12-12 19:52:24.908117', '2025-12-12 19:52:24.908117');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (2, 'Visi', 'Menjadi laboratorium software engineering terkemuka yang menghasilkan inovasi dan praktisi berkualitas tinggi dalam bidang rekayasa perangkat lunak di tingkat nasional dan internasional pada tahun 2030.', 'visi', '2025-12-12 19:52:24.908117', '2025-12-12 19:52:24.908117');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (3, 'Misi 1', 'Menyelenggarakan pendidikan dan pelatihan berkualitas tinggi dalam bidang software engineering dengan pendekatan praktis dan berbasis industri.', 'misi', '2025-12-12 19:52:24.908117', '2025-12-12 19:52:24.908117');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (4, 'Misi 2', 'Melakukan penelitian dan pengembangan yang inovatif untuk memajukan ilmu pengetahuan dan teknologi di bidang rekayasa perangkat lunak.', 'misi', '2025-12-12 19:52:24.908117', '2025-12-12 19:52:24.908117');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (5, 'Misi 3', 'Membangun kemitraan strategis dengan industri dan institusi pendidikan untuk meningkatkan kualitas pembelajaran dan penelitian.', 'misi', '2025-12-12 19:52:24.908117', '2025-12-12 19:52:24.908117');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (6, 'Focus Area 1', 'Web Development dan Cloud Computing - Mengembangkan aplikasi web modern dengan arsitektur cloud-native dan scalable.', 'focus', '2025-12-12 19:52:24.908117', '2025-12-12 19:52:24.908117');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (7, 'Focus Area 2', 'Mobile Application Development - Penelitian dan pengembangan aplikasi mobile cross-platform dengan performa optimal.', 'focus', '2025-12-12 19:52:24.908117', '2025-12-12 19:52:24.908117');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (8, 'Focus Area 3', 'Software Quality Assurance - Implementasi metode testing dan quality assurance untuk menghasilkan software berkualitas tinggi.', 'focus', '2025-12-12 19:52:24.908117', '2025-12-12 19:52:24.908117');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (9, 'Focus Area 4', 'DevOps dan CI/CD - Otomasi proses development dan deployment untuk meningkatkan efisiensi pengembangan software.', 'focus', '2025-12-12 19:52:24.908117', '2025-12-12 19:52:24.908117');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (1, 'Tentang Lab Software Engineering', 'Laboratorium Software Engineering adalah pusat keunggulan dalam pengembangan perangkat lunak yang berfokus pada penelitian, pengembangan, dan implementasi praktik terbaik dalam rekayasa perangkat lunak. Kami berkomitmen untuk menghasilkan lulusan yang kompeten dan siap menghadapi tantangan industri teknologi informasi.', 'tentang', '2025-10-31 23:23:57.825366', '2025-11-26 06:40:02.726729');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (2, 'Visi', 'Menjadi laboratorium software engineering terkemuka yang menghasilkan inovasi dan praktisi berkualitas tinggi dalam bidang rekayasa perangkat lunak di tingkat nasional dan internasional pada tahun 2030.', 'visi', '2025-10-31 23:23:57.825366', '2025-11-26 06:40:02.726729');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (4, 'Misi 2', 'Melakukan penelitian dan pengembangan yang inovatif untuk memajukan ilmu pengetahuan dan teknologi di bidang rekayasa perangkat lunak.', 'misi', '2025-10-31 23:23:57.825366', '2025-11-26 06:40:02.726729');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (5, 'Misi 3', 'Membangun kemitraan strategis dengan industri dan institusi pendidikan untuk meningkatkan kualitas pembelajaran dan penelitian.', 'misi', '2025-10-31 23:23:57.825366', '2025-11-26 06:40:02.726729');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (6, 'Focus Area 1', 'Web Development dan Cloud Computing - Mengembangkan aplikasi web modern dengan arsitektur cloud-native dan scalable.', 'focus', '2025-10-31 23:23:57.825366', '2025-11-26 06:40:02.726729');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (7, 'Focus Area 2', 'Mobile Application Development - Penelitian dan pengembangan aplikasi mobile cross-platform dengan performa optimal.', 'focus', '2025-10-31 23:23:57.825366', '2025-11-26 06:40:02.726729');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (8, 'Focus Area 3', 'Software Quality Assurance - Implementasi metode testing dan quality assurance untuk menghasilkan software berkualitas tinggi.', 'focus', '2025-10-31 23:23:57.825366', '2025-11-26 06:40:02.726729');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (9, 'Focus Area 4', 'DevOps dan CI/CD - Otomasi proses development dan deployment untuk meningkatkan efisiensi pengembangan software.', 'focus', '2025-10-31 23:23:57.825366', '2025-11-26 06:40:02.726729');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (10, 'Visi Kami', 'Menjadi pusat unggulan dalam pendidikan dan penelitian rekayasa perangkat lunak yang diakui secara internasional, menghasilkan inovasi teknologi yang bermanfaat bagi masyarakat luas.', 'visi', '2025-11-26 06:34:48.484135', '2025-11-26 06:40:02.726729');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (11, 'Pendidikan Berkualitas', 'Menyelenggarakan pendidikan berkualitas tinggi di bidang software engineering dengan kurikulum yang relevan dengan kebutuhan industri.', 'misi', '2025-11-26 06:34:48.534658', '2025-11-26 06:40:02.726729');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (12, 'Penelitian Inovatif', 'Melaksanakan penelitian inovatif yang berkontribusi pada kemajuan teknologi informasi dan komunikasi.', 'misi', '2025-11-26 06:34:48.534658', '2025-11-26 06:40:02.726729');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (13, 'Kolaborasi Global', 'Membangun kerjasama strategis dengan industri, pemerintah, dan institusi pendidikan global untuk meningkatkan daya saing.', 'misi', '2025-11-26 06:34:48.534658', '2025-11-26 06:40:02.726729');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (14, 'Pengabdian Masyarakat', 'Menerapkan solusi teknologi tepat guna untuk memecahkan permasalahan di masyarakat.', 'misi', '2025-11-26 06:34:48.534658', '2025-11-26 06:40:02.726729');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (15, '2024 - Q1', 'Modernisasi Infrastruktur: Upgrade peralatan laboratorium dan implementasi cloud infrastructure untuk mendukung pembelajaran dan penelitian yang lebih efektif.', 'roadmap', '2025-11-26 06:34:48.547368', '2025-11-26 06:40:02.726729');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (16, '2024 - Q2', 'Program Sertifikasi Internasional: Meluncurkan program sertifikasi AWS, Azure, dan Google Cloud untuk meningkatkan kompetensi mahasiswa dan dosen.', 'roadmap', '2025-11-26 06:34:48.547368', '2025-11-26 06:40:02.726729');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (17, '2024 - Q3', 'Kemitraan Industri: Membangun kemitraan strategis dengan perusahaan teknologi untuk proyek kolaborasi dan program magang.', 'roadmap', '2025-11-26 06:34:48.547368', '2025-11-26 06:40:02.726729');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (18, '2024 - Q4', 'Penelitian AI & Machine Learning: Inisiasi program penelitian fokus pada Artificial Intelligence dan Machine Learning dengan publikasi di jurnal internasional.', 'roadmap', '2025-11-26 06:34:48.547368', '2025-11-26 06:40:02.726729');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (19, '2025 - Q1', 'Peluncuran Inkubator Startup: Membuka inkubator untuk mendukung mahasiswa mengembangkan ide bisnis teknologi mereka.', 'roadmap', '2025-11-26 06:34:48.547368', '2025-11-26 06:40:02.726729');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (20, '2025 - Q2', 'Konferensi Internasional: Menyelenggarakan konferensi internasional tentang Software Engineering dan mengundang pembicara dari berbagai negara.', 'roadmap', '2025-11-26 06:34:48.547368', '2025-11-26 06:40:02.726729');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (21, '2025 - Q3', 'Expansion Program: Perluasan fasilitas laboratorium dan penambahan program penelitian baru dalam bidang Blockchain dan IoT.', 'roadmap', '2025-11-26 06:34:48.547368', '2025-11-26 06:40:02.726729');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (22, '2026 - 2030', 'Menjadi Center of Excellence: Mewujudkan Lab SE sebagai Center of Excellence di tingkat nasional dan internasional dengan kontribusi signifikan dalam riset dan industri.', 'roadmap', '2025-11-26 06:34:48.547368', '2025-11-26 06:40:02.726729');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (23, 'Tentang Lab SE', 'Lab Software Engineering adalah pusat inovasi dan pembelajaran yang berdedikasi untuk mencetak talenta terbaik di bidang rekayasa perangkat lunak. Kami menggabungkan kurikulum akademis yang ketat dengan pengalaman praktis melalui proyek-proyek nyata.

Fasilitas kami dilengkapi dengan teknologi terkini untuk mendukung eksplorasi mahasiswa dalam berbagai bidang seperti Web Development, Mobile Apps, Cloud Computing, dan Artificial Intelligence. Kami percaya bahwa kolaborasi dan inovasi adalah kunci untuk menciptakan solusi teknologi masa depan.', 'tentang', '2025-11-26 06:34:48.557852', '2025-11-26 06:40:02.726729');
INSERT INTO public.lab_profile (id, judul, konten, kategori, created_at, updated_at) VALUES (3, 'Misi 1', 'Menyelenggarakan pendidikan dan pelatihan berkualitas tinggi dalam bidang software engineering dengan pendekatan praktis dan berbasis', 'misi', '2025-10-31 23:23:57.825366', '2025-11-26 06:49:22.040895');


--
-- Data for Name: landing_page_content; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.landing_page_content (id, section_name, key_name, content_value, content_type, updated_at) VALUES (1, 'hero', 'title', 'Laboratorium Software Engineering', 'text', '2025-11-26 07:08:38.555529');
INSERT INTO public.landing_page_content (id, section_name, key_name, content_value, content_type, updated_at) VALUES (2, 'hero', 'subtitle', 'Berinovasi, Berkolaborasi, dan Berkembang bersama Teknologi Masa Depan', 'text', '2025-11-26 07:08:38.567741');
INSERT INTO public.landing_page_content (id, section_name, key_name, content_value, content_type, updated_at) VALUES (3, 'about', 'title', 'Tentang Lab SE', 'text', '2025-11-26 07:08:38.56933');
INSERT INTO public.landing_page_content (id, section_name, key_name, content_value, content_type, updated_at) VALUES (4, 'about', 'subtitle', 'Pusat Keunggulan Pengembangan Perangkat Lunak', 'text', '2025-11-26 07:08:38.57042');
INSERT INTO public.landing_page_content (id, section_name, key_name, content_value, content_type, updated_at) VALUES (17, 'navbar', 'logo_path', '/public/img/logo-1764115242.png', 'image', '2025-11-26 07:00:42.6136');
INSERT INTO public.landing_page_content (id, section_name, key_name, content_value, content_type, updated_at) VALUES (5, 'about', 'card1_title', 'Unggul dalam Penelitian', 'text', '2025-11-26 07:08:38.571152');
INSERT INTO public.landing_page_content (id, section_name, key_name, content_value, content_type, updated_at) VALUES (6, 'about', 'card1_desc', 'Melakukan penelitian inovatif dalam bidang rekayasa perangkat lunak dan teknologi informasi terkini.', 'text', '2025-11-26 07:08:38.571739');
INSERT INTO public.landing_page_content (id, section_name, key_name, content_value, content_type, updated_at) VALUES (7, 'about', 'card2_title', 'Tim Berkualitas', 'text', '2025-11-26 07:08:38.572234');
INSERT INTO public.landing_page_content (id, section_name, key_name, content_value, content_type, updated_at) VALUES (8, 'about', 'card2_desc', 'Didukung oleh dosen dan peneliti berpengalaman dengan sertifikasi internasional.', 'text', '2025-11-26 07:08:38.572704');
INSERT INTO public.landing_page_content (id, section_name, key_name, content_value, content_type, updated_at) VALUES (9, 'about', 'card3_title', 'Inovasi Berkelanjutan', 'text', '2025-11-26 07:08:38.573124');
INSERT INTO public.landing_page_content (id, section_name, key_name, content_value, content_type, updated_at) VALUES (10, 'about', 'card3_desc', 'Menghasilkan solusi software inovatif yang memberikan dampak nyata bagi masyarakat.', 'text', '2025-11-26 07:08:38.573658');
INSERT INTO public.landing_page_content (id, section_name, key_name, content_value, content_type, updated_at) VALUES (11, 'navbar', 'brand_title', 'Jurusan Teknologi Informasi', 'text', '2025-11-26 07:08:38.574045');
INSERT INTO public.landing_page_content (id, section_name, key_name, content_value, content_type, updated_at) VALUES (12, 'navbar', 'brand_subtitle', 'Politeknik Negeri Malang', 'text', '2025-11-26 07:08:38.574472');
INSERT INTO public.landing_page_content (id, section_name, key_name, content_value, content_type, updated_at) VALUES (13, 'footer', 'description', 'Pusat keunggulan dalam pengembangan perangkat lunak dan penelitian teknologi informasi.', 'text', '2025-11-26 07:08:38.574938');
INSERT INTO public.landing_page_content (id, section_name, key_name, content_value, content_type, updated_at) VALUES (14, 'footer', 'email', 'labse@university.ac.id', 'text', '2025-11-26 07:08:38.575482');
INSERT INTO public.landing_page_content (id, section_name, key_name, content_value, content_type, updated_at) VALUES (15, 'footer', 'phone', '+62 21 1234 5678', 'text', '2025-11-26 07:08:38.575997');
INSERT INTO public.landing_page_content (id, section_name, key_name, content_value, content_type, updated_at) VALUES (16, 'footer', 'copyright', 'Lab Software Engineering. All rights reserved.', 'text', '2025-11-26 07:08:38.576631');
INSERT INTO public.landing_page_content (id, section_name, key_name, content_value, content_type, updated_at) VALUES (18, 'footer', 'social_facebook', '#', 'link', '2025-11-26 07:08:38.57941');
INSERT INTO public.landing_page_content (id, section_name, key_name, content_value, content_type, updated_at) VALUES (19, 'footer', 'social_twitter', '', 'link', '2025-11-26 07:08:38.581238');
INSERT INTO public.landing_page_content (id, section_name, key_name, content_value, content_type, updated_at) VALUES (20, 'footer', 'social_instagram', '#', 'link', '2025-11-26 07:08:38.5829');
INSERT INTO public.landing_page_content (id, section_name, key_name, content_value, content_type, updated_at) VALUES (21, 'footer', 'social_linkedin', '#', 'link', '2025-11-26 07:08:38.586374');
INSERT INTO public.landing_page_content (id, section_name, key_name, content_value, content_type, updated_at) VALUES (22, 'footer', 'social_youtube', '#', 'link', '2025-11-26 07:08:38.587517');


--
-- Data for Name: mahasiswa; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.mahasiswa (id, nama, nim, jurusan, email, alasan, created_at, status_approval, approved_by, approved_at, rejection_reason, dosen_pembimbing_id) VALUES (6, 'Bisri', '1212121212', 'Teknik Informatika', 'bisri@gmail.com', 'asasa', '2025-11-13 17:13:13.4546', 'approved', 3, '2025-11-13 17:15:46.904186', NULL, NULL);
INSERT INTO public.mahasiswa (id, nama, nim, jurusan, email, alasan, created_at, status_approval, approved_by, approved_at, rejection_reason, dosen_pembimbing_id) VALUES (7, 'Agus', '123131331', 'Teknik Komputer', 'agus@gmail.com', 'hhaghagsahsh', '2025-11-13 17:59:07.797136', 'rejected', 3, '2025-11-13 18:00:05.395967', 'tidak memenuhi syarat', NULL);
INSERT INTO public.mahasiswa (id, nama, nim, jurusan, email, alasan, created_at, status_approval, approved_by, approved_at, rejection_reason, dosen_pembimbing_id) VALUES (8, 'Muhamad Karim', '12345678', 'Sistem Informasi', 'karim@gmail.com', 'gwa hanya inginaslassajsnabsahygsfdytasfdygasd', '2025-11-24 18:26:44.673015', 'approved', 3, '2025-11-24 18:27:18.108838', NULL, 3);


--
-- Data for Name: penelitian; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.penelitian (id, mahasiswa_id, judul, file_path, link_drive, keterangan, status, created_at, updated_at) VALUES (1, 8, 'Penerapan Design Patterns dalam Pengembangan Aplikasi Enterprise', 'penelitian_8_1763984959.png', '', '', 'approved', '2025-11-24 18:49:19.922924', '2025-11-24 18:49:19.922924');


--
-- Data for Name: pengabdian; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.pengabdian (id, judul, deskripsi, tanggal, lokasi, penyelenggara, gambar, personil_id, created_at, updated_at) VALUES (1, 'Pelatihan Web Development untuk UMKM', 'Kegiatan pelatihan pembuatan website untuk pelaku UMKM di wilayah Semarang. Peserta diajarkan cara membuat website sederhana menggunakan WordPress dan dasar-dasar digital marketing.', '2024-10-15', 'Aula Kelurahan Tembalang, Semarang', 'Lab Software Engineering', 'pelatihan-umkm.jpg', NULL, '2025-12-12 19:52:24.943466', '2025-12-12 19:52:24.943466');
INSERT INTO public.pengabdian (id, judul, deskripsi, tanggal, lokasi, penyelenggara, gambar, personil_id, created_at, updated_at) VALUES (2, 'Workshop IoT untuk Siswa SMA', 'Workshop pengenalan Internet of Things (IoT) untuk siswa SMA di Jawa Tengah. Materi meliputi pengenalan Arduino, sensor, dan pembuatan prototype smart home sederhana.', '2024-09-20', 'SMA Negeri 1 Semarang', 'Lab Software Engineering', 'workshop-iot.jpg', NULL, '2025-12-12 19:52:24.943466', '2025-12-12 19:52:24.943466');
INSERT INTO public.pengabdian (id, judul, deskripsi, tanggal, lokasi, penyelenggara, gambar, personil_id, created_at, updated_at) VALUES (3, 'Pengabdian Masyarakat: Digitalisasi Desa', 'Program pengabdian masyarakat untuk membantu digitalisasi administrasi desa. Termasuk pembuatan sistem informasi desa dan pelatihan penggunaan untuk aparat desa.', '2024-08-10', 'Desa Mangunsari, Ungaran', 'Lab Software Engineering', 'digitalisasi-desa.jpg', NULL, '2025-12-12 19:52:24.943466', '2025-12-12 19:52:24.943466');
INSERT INTO public.pengabdian (id, judul, deskripsi, tanggal, lokasi, penyelenggara, gambar, personil_id, created_at, updated_at) VALUES (4, 'Pelatihan Mobile App Development', 'Pelatihan pengembangan aplikasi mobile menggunakan Flutter untuk mahasiswa dan masyarakat umum. Peserta membuat aplikasi sederhana dari nol hingga publish ke Play Store.', '2024-11-05', 'Lab Software Engineering, Kampus UNDIP', 'Lab Software Engineering', 'pelatihan-flutter.jpg', NULL, '2025-12-12 19:52:24.943466', '2025-12-12 19:52:24.943466');


--
-- Data for Name: personil; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.personil (id, nama, jabatan, deskripsi, foto, email, password, is_member, last_login, created_at, updated_at) VALUES (1, 'Dr. Ahmad Fauzi, M.Kom', 'Kepala Laboratorium', 'Memiliki pengalaman lebih dari 15 tahun dalam bidang software engineering dan telah memimpin berbagai proyek penelitian skala nasional dan internasional.', 'ahmad-fauzi.jpg', 'ahmad.fauzi@university.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', true, NULL, '2025-12-12 19:52:24.911765', '2025-12-12 19:52:24.963563');
INSERT INTO public.personil (id, nama, jabatan, deskripsi, foto, email, password, is_member, last_login, created_at, updated_at) VALUES (2, 'Prof. Dr. Siti Nurhaliza, M.T', 'Koordinator Penelitian', 'Ahli dalam bidang software architecture dan design patterns. Telah mempublikasikan lebih dari 50 paper di jurnal internasional bereputasi.', 'siti-nurhaliza.jpg', 'siti.nurhaliza@university.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', true, NULL, '2025-12-12 19:52:24.911765', '2025-12-12 19:52:24.963563');
INSERT INTO public.personil (id, nama, jabatan, deskripsi, foto, email, password, is_member, last_login, created_at, updated_at) VALUES (3, 'Budi Santoso, Ph.D', 'Dosen Senior', 'Spesialisasi dalam web development dan cloud computing. Aktif membimbing mahasiswa dalam proyek-proyek inovatif.', 'budi-santoso.jpg', 'budi.santoso@university.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', true, NULL, '2025-12-12 19:52:24.911765', '2025-12-12 19:52:24.963563');
INSERT INTO public.personil (id, nama, jabatan, deskripsi, foto, email, password, is_member, last_login, created_at, updated_at) VALUES (4, 'Dr. Rina Wijaya, M.Sc', 'Dosen Senior', 'Fokus penelitian pada mobile application development dan user experience design. Memiliki sertifikasi internasional di bidang UX/UI.', 'rina-wijaya.jpg', 'rina.wijaya@university.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', true, NULL, '2025-12-12 19:52:24.911765', '2025-12-12 19:52:24.963563');
INSERT INTO public.personil (id, nama, jabatan, deskripsi, foto, email, password, is_member, last_login, created_at, updated_at) VALUES (5, 'Muhammad Rizki, M.Kom', 'Asisten Laboratorium', 'Mengelola operasional laboratorium dan memberikan support teknis kepada mahasiswa dalam kegiatan praktikum.', 'muhammad-rizki.jpg', 'muhammad.rizki@university.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', true, NULL, '2025-12-12 19:52:24.911765', '2025-12-12 19:52:24.963563');
INSERT INTO public.personil (id, nama, jabatan, deskripsi, foto, email, password, is_member, last_login, created_at, updated_at) VALUES (6, 'Dewi Lestari, M.T', 'Asisten Laboratorium', 'Membantu dalam kegiatan penelitian dan pengembangan serta membimbing mahasiswa dalam proyek akhir.', 'dewi-lestari.jpg', 'dewi.lestari@university.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', true, NULL, '2025-12-12 19:52:24.911765', '2025-12-12 19:52:24.963563');


--
-- Data for Name: produk; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.produk (id, nama_produk, deskripsi, kategori, kategori_id, tahun, gambar, link_demo, link_repository, teknologi, personil_id, created_at, updated_at) VALUES (1, 'Sistem Informasi Akademik', 'Aplikasi untuk mengelola data akademik mahasiswa, dosen, dan jadwal perkuliahan dengan fitur lengkap.', 'Software', NULL, 2023, NULL, NULL, NULL, 'PHP, Laravel, MySQL, Bootstrap', 1, '2025-12-12 19:52:24.949923', '2025-12-12 19:52:24.949923');
INSERT INTO public.produk (id, nama_produk, deskripsi, kategori, kategori_id, tahun, gambar, link_demo, link_repository, teknologi, personil_id, created_at, updated_at) VALUES (2, 'Smart Home Controller', 'Perangkat hardware untuk mengontrol peralatan rumah tangga menggunakan IoT dan aplikasi mobile.', 'Hardware', NULL, 2023, NULL, NULL, NULL, 'Arduino, ESP32, Android, Firebase', 2, '2025-12-12 19:52:24.949923', '2025-12-12 19:52:24.949923');
INSERT INTO public.produk (id, nama_produk, deskripsi, kategori, kategori_id, tahun, gambar, link_demo, link_repository, teknologi, personil_id, created_at, updated_at) VALUES (3, 'E-Learning Platform', 'Platform pembelajaran online dengan fitur video conference, quiz, dan assignment management.', 'Software', NULL, 2024, NULL, NULL, NULL, 'Node.js, React, MongoDB, WebRTC', 1, '2025-12-12 19:52:24.949923', '2025-12-12 19:52:24.949923');
INSERT INTO public.produk (id, nama_produk, deskripsi, kategori, kategori_id, tahun, gambar, link_demo, link_repository, teknologi, personil_id, created_at, updated_at) VALUES (4, 'Traffic Monitoring System', 'Sistem monitoring lalu lintas berbasis AI dengan kamera CCTV dan dashboard real-time.', 'Hardware', NULL, 2024, NULL, NULL, NULL, 'Raspberry Pi, Python, OpenCV, TensorFlow', 2, '2025-12-12 19:52:24.949923', '2025-12-12 19:52:24.949923');


--
-- Data for Name: recruitment_settings; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.recruitment_settings (id, is_open, message, updated_at, updated_by) VALUES (1, true, 'Maaf, Lab SE sedang tidak membuka recruitment saat ini. Silakan cek kembali nanti.', '2025-12-12 19:52:24.964679', 'System');


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.users (id, username, email, password, role, reference_id, is_active, last_login, created_at, updated_at) VALUES (2, 'superadmin', 'superadmin@labse.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 2, true, NULL, '2025-11-13 15:13:16.704752', '2025-11-13 15:13:16.704752');
INSERT INTO public.users (id, username, email, password, role, reference_id, is_active, last_login, created_at, updated_at) VALUES (6, 'rina.wijaya', 'rina.wijaya@university.ac.id', '$2y$10$W//I8cL3SfWRjRW9Hap7/O68Tzpmm3aEWnxijW20AnqO4ITymmQA2', 'personil', 4, true, NULL, '2025-11-13 15:13:16.754199', '2025-11-13 16:02:19.685701');
INSERT INTO public.users (id, username, email, password, role, reference_id, is_active, last_login, created_at, updated_at) VALUES (7, 'muhammad.rizki', 'muhammad.rizki@university.ac.id', '$2y$10$W//I8cL3SfWRjRW9Hap7/O68Tzpmm3aEWnxijW20AnqO4ITymmQA2', 'personil', 5, true, NULL, '2025-11-13 15:13:16.767401', '2025-11-13 16:02:19.686422');
INSERT INTO public.users (id, username, email, password, role, reference_id, is_active, last_login, created_at, updated_at) VALUES (4, 'siti.nurhaliza', 'siti.nurhaliza@university.ac.id', '$2y$10$W//I8cL3SfWRjRW9Hap7/O68Tzpmm3aEWnxijW20AnqO4ITymmQA2', 'personil', 2, true, '2025-11-13 16:14:48.300237', '2025-11-13 15:13:16.727444', '2025-11-13 16:14:48.300237');
INSERT INTO public.users (id, username, email, password, role, reference_id, is_active, last_login, created_at, updated_at) VALUES (8, 'dewi.lestar', 'dewi.lestari@university.ac.id', '$2y$10$W//I8cL3SfWRjRW9Hap7/O68Tzpmm3aEWnxijW20AnqO4ITymmQA2', 'personil', 6, true, NULL, '2025-11-13 15:13:16.776899', '2025-11-13 16:34:38.156946');
INSERT INTO public.users (id, username, email, password, role, reference_id, is_active, last_login, created_at, updated_at) VALUES (3, 'ahmad.fauzi', 'ahmad.fauzi@university.ac.id', '$2y$10$NFxkK1Xl4W.HCfLaziyjQ.BmJxY2raMpAuTsCoiLJ.qeLxbTOW82y', 'personil', 1, true, '2025-11-24 18:50:15.548935', '2025-11-13 15:13:16.720857', '2025-11-24 18:50:15.548935');
INSERT INTO public.users (id, username, email, password, role, reference_id, is_active, last_login, created_at, updated_at) VALUES (1, 'admin', 'admin@labse.ac.id', '$2y$10$ZYD8x/o3NotqN0I5O0xFKef9HUtMEGl3gvHCGTULcZtNkkqLxO0Rq', 'admin', 3, true, '2025-11-26 08:30:04.190398', '2025-11-13 15:13:16.676302', '2025-11-26 08:30:04.190398');
INSERT INTO public.users (id, username, email, password, role, reference_id, is_active, last_login, created_at, updated_at) VALUES (13, 'muhamadkarim', 'karim@gmail.com', '$2y$10$vUMvoCN.7ChuOPIbGASVGu2DZoxfVvMglCib9rY5xDo8JWSBc5syq', 'mahasiswa', 8, true, '2025-11-26 08:33:02.190234', '2025-11-24 18:27:18.108838', '2025-11-26 08:33:02.190234');
INSERT INTO public.users (id, username, email, password, role, reference_id, is_active, last_login, created_at, updated_at) VALUES (5, 'budi.santoso', 'budi.santoso@university.ac.id', '$2y$10$W//I8cL3SfWRjRW9Hap7/O68Tzpmm3aEWnxijW20AnqO4ITymmQA2', 'personil', 3, true, '2025-11-26 08:33:34.231637', '2025-11-13 15:13:16.740471', '2025-11-26 08:33:34.231637');


--
-- Name: activity_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.activity_logs_id_seq', 32, true);


--
-- Name: admin_users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.admin_users_id_seq', 11, true);


--
-- Name: artikel_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.artikel_id_seq', 8, true);


--
-- Name: hasil_penelitian_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.hasil_penelitian_id_seq', 13, true);


--
-- Name: jurusan_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.jurusan_id_seq', 6, true);


--
-- Name: kategori_artikel_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.kategori_artikel_id_seq', 6, true);


--
-- Name: kategori_penelitian_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.kategori_penelitian_id_seq', 6, true);


--
-- Name: kategori_produk_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.kategori_produk_id_seq', 5, true);


--
-- Name: komentar_penelitian_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.komentar_penelitian_id_seq', 1, false);


--
-- Name: lab_profile_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.lab_profile_id_seq', 23, true);


--
-- Name: landing_page_content_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.landing_page_content_id_seq', 22, true);


--
-- Name: mahasiswa_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.mahasiswa_id_seq', 8, true);


--
-- Name: penelitian_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.penelitian_id_seq', 1, true);


--
-- Name: pengabdian_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.pengabdian_id_seq', 6, true);


--
-- Name: personil_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.personil_id_seq', 6, true);


--
-- Name: produk_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.produk_id_seq', 7, true);


--
-- Name: recruitment_settings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.recruitment_settings_id_seq', 1, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 13, true);


--
-- Name: activity_logs activity_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.activity_logs
    ADD CONSTRAINT activity_logs_pkey PRIMARY KEY (id);


--
-- Name: admin_users admin_users_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.admin_users
    ADD CONSTRAINT admin_users_email_key UNIQUE (email);


--
-- Name: admin_users admin_users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.admin_users
    ADD CONSTRAINT admin_users_pkey PRIMARY KEY (id);


--
-- Name: admin_users admin_users_username_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.admin_users
    ADD CONSTRAINT admin_users_username_key UNIQUE (username);


--
-- Name: artikel artikel_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.artikel
    ADD CONSTRAINT artikel_pkey PRIMARY KEY (id);


--
-- Name: hasil_penelitian hasil_penelitian_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.hasil_penelitian
    ADD CONSTRAINT hasil_penelitian_pkey PRIMARY KEY (id);


--
-- Name: jurusan jurusan_nama_jurusan_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jurusan
    ADD CONSTRAINT jurusan_nama_jurusan_key UNIQUE (nama_jurusan);


--
-- Name: jurusan jurusan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jurusan
    ADD CONSTRAINT jurusan_pkey PRIMARY KEY (id);


--
-- Name: kategori_artikel kategori_artikel_nama_kategori_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kategori_artikel
    ADD CONSTRAINT kategori_artikel_nama_kategori_key UNIQUE (nama_kategori);


--
-- Name: kategori_artikel kategori_artikel_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kategori_artikel
    ADD CONSTRAINT kategori_artikel_pkey PRIMARY KEY (id);


--
-- Name: kategori_penelitian kategori_penelitian_nama_kategori_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kategori_penelitian
    ADD CONSTRAINT kategori_penelitian_nama_kategori_key UNIQUE (nama_kategori);


--
-- Name: kategori_penelitian kategori_penelitian_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kategori_penelitian
    ADD CONSTRAINT kategori_penelitian_pkey PRIMARY KEY (id);


--
-- Name: kategori_penelitian kategori_penelitian_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kategori_penelitian
    ADD CONSTRAINT kategori_penelitian_slug_key UNIQUE (slug);


--
-- Name: kategori_produk kategori_produk_nama_kategori_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kategori_produk
    ADD CONSTRAINT kategori_produk_nama_kategori_key UNIQUE (nama_kategori);


--
-- Name: kategori_produk kategori_produk_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kategori_produk
    ADD CONSTRAINT kategori_produk_pkey PRIMARY KEY (id);


--
-- Name: kategori_produk kategori_produk_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kategori_produk
    ADD CONSTRAINT kategori_produk_slug_key UNIQUE (slug);


--
-- Name: komentar_penelitian komentar_penelitian_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.komentar_penelitian
    ADD CONSTRAINT komentar_penelitian_pkey PRIMARY KEY (id);


--
-- Name: landing_page_content landing_page_content_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.landing_page_content
    ADD CONSTRAINT landing_page_content_pkey PRIMARY KEY (id);


--
-- Name: landing_page_content landing_page_content_section_name_key_name_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.landing_page_content
    ADD CONSTRAINT landing_page_content_section_name_key_name_key UNIQUE (section_name, key_name);


--
-- Name: mahasiswa mahasiswa_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mahasiswa
    ADD CONSTRAINT mahasiswa_pkey PRIMARY KEY (id);


--
-- Name: penelitian penelitian_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penelitian
    ADD CONSTRAINT penelitian_pkey PRIMARY KEY (id);


--
-- Name: pengabdian pengabdian_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengabdian
    ADD CONSTRAINT pengabdian_pkey PRIMARY KEY (id);


--
-- Name: personil personil_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personil
    ADD CONSTRAINT personil_pkey PRIMARY KEY (id);


--
-- Name: produk produk_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.produk
    ADD CONSTRAINT produk_pkey PRIMARY KEY (id);


--
-- Name: recruitment_settings recruitment_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.recruitment_settings
    ADD CONSTRAINT recruitment_settings_pkey PRIMARY KEY (id);


--
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: users users_username_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_username_key UNIQUE (username);


--
-- Name: idx_activity_logs_action_type; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_activity_logs_action_type ON public.activity_logs USING btree (action_type);


--
-- Name: idx_activity_logs_created_at; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_activity_logs_created_at ON public.activity_logs USING btree (created_at DESC);


--
-- Name: idx_activity_logs_personil; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_activity_logs_personil ON public.activity_logs USING btree (personil_id);


--
-- Name: idx_activity_logs_target; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_activity_logs_target ON public.activity_logs USING btree (target_type, target_id);


--
-- Name: idx_admin_users_foto; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_admin_users_foto ON public.admin_users USING btree (foto);


--
-- Name: idx_admin_users_updated_at; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_admin_users_updated_at ON public.admin_users USING btree (updated_at);


--
-- Name: idx_artikel_personil; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_artikel_personil ON public.artikel USING btree (personil_id);


--
-- Name: idx_jurusan_is_active; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_jurusan_is_active ON public.jurusan USING btree (is_active);


--
-- Name: idx_kategori_artikel_is_active; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_kategori_artikel_is_active ON public.kategori_artikel USING btree (is_active);


--
-- Name: idx_kategori_penelitian_is_active; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_kategori_penelitian_is_active ON public.kategori_penelitian USING btree (is_active);


--
-- Name: idx_kategori_produk_is_active; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_kategori_produk_is_active ON public.kategori_produk USING btree (is_active);


--
-- Name: idx_komentar_penelitian; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_komentar_penelitian ON public.komentar_penelitian USING btree (penelitian_id);


--
-- Name: idx_mahasiswa_approved_by; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_mahasiswa_approved_by ON public.mahasiswa USING btree (approved_by);


--
-- Name: idx_mahasiswa_dosen; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_mahasiswa_dosen ON public.mahasiswa USING btree (dosen_pembimbing_id);


--
-- Name: idx_mahasiswa_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_mahasiswa_status ON public.mahasiswa USING btree (status_approval);


--
-- Name: idx_penelitian_kategori_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_penelitian_kategori_id ON public.hasil_penelitian USING btree (kategori_id);


--
-- Name: idx_penelitian_mahasiswa; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_penelitian_mahasiswa ON public.penelitian USING btree (mahasiswa_id);


--
-- Name: idx_penelitian_personil_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_penelitian_personil_id ON public.hasil_penelitian USING btree (personil_id);


--
-- Name: idx_penelitian_tahun; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_penelitian_tahun ON public.hasil_penelitian USING btree (tahun);


--
-- Name: idx_pengabdian_personil_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pengabdian_personil_id ON public.pengabdian USING btree (personil_id);


--
-- Name: idx_pengabdian_tanggal; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pengabdian_tanggal ON public.pengabdian USING btree (tanggal);


--
-- Name: idx_personil_email; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_personil_email ON public.personil USING btree (email);


--
-- Name: idx_produk_kategori; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_produk_kategori ON public.produk USING btree (kategori);


--
-- Name: idx_produk_kategori_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_produk_kategori_id ON public.produk USING btree (kategori_id);


--
-- Name: idx_produk_personil_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_produk_personil_id ON public.produk USING btree (personil_id);


--
-- Name: idx_produk_tahun; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_produk_tahun ON public.produk USING btree (tahun);


--
-- Name: idx_recruitment_settings_is_open; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_recruitment_settings_is_open ON public.recruitment_settings USING btree (is_open);


--
-- Name: idx_users_email; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_users_email ON public.users USING btree (email);


--
-- Name: idx_users_reference; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_users_reference ON public.users USING btree (role, reference_id);


--
-- Name: idx_users_role; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_users_role ON public.users USING btree (role);


--
-- Name: idx_users_username; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_users_username ON public.users USING btree (username);


--
-- Name: lab_profile update_lab_profile_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_lab_profile_updated_at BEFORE UPDATE ON public.lab_profile FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- Name: hasil_penelitian update_penelitian_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_penelitian_updated_at BEFORE UPDATE ON public.hasil_penelitian FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- Name: pengabdian update_pengabdian_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_pengabdian_updated_at BEFORE UPDATE ON public.pengabdian FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- Name: personil update_personil_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_personil_updated_at BEFORE UPDATE ON public.personil FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- Name: produk update_produk_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_produk_updated_at BEFORE UPDATE ON public.produk FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- Name: users update_users_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_users_updated_at BEFORE UPDATE ON public.users FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- Name: activity_logs fk_activity_personil; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.activity_logs
    ADD CONSTRAINT fk_activity_personil FOREIGN KEY (personil_id) REFERENCES public.personil(id) ON DELETE CASCADE;


--
-- Name: artikel fk_artikel_personil; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.artikel
    ADD CONSTRAINT fk_artikel_personil FOREIGN KEY (personil_id) REFERENCES public.personil(id) ON DELETE SET NULL;


--
-- Name: mahasiswa fk_mahasiswa_approved_by; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mahasiswa
    ADD CONSTRAINT fk_mahasiswa_approved_by FOREIGN KEY (approved_by) REFERENCES public.admin_users(id) ON DELETE SET NULL;


--
-- Name: hasil_penelitian fk_penelitian_kategori; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.hasil_penelitian
    ADD CONSTRAINT fk_penelitian_kategori FOREIGN KEY (kategori_id) REFERENCES public.kategori_penelitian(id) ON DELETE SET NULL;


--
-- Name: hasil_penelitian fk_penelitian_personil; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.hasil_penelitian
    ADD CONSTRAINT fk_penelitian_personil FOREIGN KEY (personil_id) REFERENCES public.personil(id) ON DELETE CASCADE;


--
-- Name: pengabdian fk_pengabdian_personil; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengabdian
    ADD CONSTRAINT fk_pengabdian_personil FOREIGN KEY (personil_id) REFERENCES public.personil(id) ON DELETE SET NULL;


--
-- Name: produk fk_produk_kategori; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.produk
    ADD CONSTRAINT fk_produk_kategori FOREIGN KEY (kategori_id) REFERENCES public.kategori_produk(id) ON DELETE SET NULL;


--
-- Name: produk fk_produk_personil; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.produk
    ADD CONSTRAINT fk_produk_personil FOREIGN KEY (personil_id) REFERENCES public.personil(id) ON DELETE CASCADE;


--
-- Name: hasil_penelitian hasil_penelitian_kategori_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.hasil_penelitian
    ADD CONSTRAINT hasil_penelitian_kategori_id_fkey FOREIGN KEY (kategori_id) REFERENCES public.kategori_penelitian(id) ON DELETE SET NULL;


--
-- Name: hasil_penelitian hasil_penelitian_personil_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.hasil_penelitian
    ADD CONSTRAINT hasil_penelitian_personil_id_fkey FOREIGN KEY (personil_id) REFERENCES public.personil(id);


--
-- Name: komentar_penelitian komentar_penelitian_penelitian_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.komentar_penelitian
    ADD CONSTRAINT komentar_penelitian_penelitian_id_fkey FOREIGN KEY (penelitian_id) REFERENCES public.penelitian(id) ON DELETE CASCADE;


--
-- Name: komentar_penelitian komentar_penelitian_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.komentar_penelitian
    ADD CONSTRAINT komentar_penelitian_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- Name: mahasiswa mahasiswa_dosen_pembimbing_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mahasiswa
    ADD CONSTRAINT mahasiswa_dosen_pembimbing_id_fkey FOREIGN KEY (dosen_pembimbing_id) REFERENCES public.personil(id);


--
-- Name: penelitian penelitian_mahasiswa_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penelitian
    ADD CONSTRAINT penelitian_mahasiswa_id_fkey FOREIGN KEY (mahasiswa_id) REFERENCES public.mahasiswa(id);


--
-- Name: pengabdian pengabdian_personil_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengabdian
    ADD CONSTRAINT pengabdian_personil_id_fkey FOREIGN KEY (personil_id) REFERENCES public.personil(id);


--
-- Name: produk produk_kategori_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.produk
    ADD CONSTRAINT produk_kategori_id_fkey FOREIGN KEY (kategori_id) REFERENCES public.kategori_produk(id) ON DELETE SET NULL;


--
-- Name: produk produk_personil_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.produk
    ADD CONSTRAINT produk_personil_id_fkey FOREIGN KEY (personil_id) REFERENCES public.personil(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict HahbUlY5abDZbMTH6TF57QDFdhDZaUyOWPwHdEvPZtlsQwGcN4Y3rtfdALsfjKL

