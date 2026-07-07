DROP TABLE consum CASCADE CONSTRAINTS;
DROP TABLE oferta CASCADE CONSTRAINTS;
DROP TABLE pontaj CASCADE CONSTRAINTS;
DROP TABLE material CASCADE CONSTRAINTS;
DROP TABLE furnizor CASCADE CONSTRAINTS;
DROP TABLE utilaj CASCADE CONSTRAINTS;
DROP TABLE lucrare CASCADE CONSTRAINTS;
DROP TABLE client CASCADE CONSTRAINTS;
DROP TABLE angajat CASCADE CONSTRAINTS;
DROP TABLE job CASCADE CONSTRAINTS;

CREATE TABLE job (
    id_job          NUMBER PRIMARY KEY,
    titlu_job       VARCHAR2(75) NOT NULL,
    salariu_lunar   NUMBER(8, 2) NOT NULL,
    CONSTRAINT check_salariu_pozitiv CHECK (salariu_lunar > 0)
);

CREATE TABLE angajat (
    id_angajat      NUMBER PRIMARY KEY,
    cnp             NUMBER(13) UNIQUE,
    nationalitate   VARCHAR2(30) DEFAULT 'Română',
    data_incepere   DATE DEFAULT SYSDATE,    
    nume            VARCHAR2(20) NOT NULL,
    prenume         VARCHAR2(25) NOT NULL,
    iban            VARCHAR2(34) NOT NULL UNIQUE,
    telefon         VARCHAR2(15) NOT NULL,
    id_job          NUMBER NOT NULL,
    CONSTRAINT check_lungime_iban CHECK (length(iban)<=34),
    CONSTRAINT check_telefon_angajat CHECK (substr(telefon,1,1)='+'),
    CONSTRAINT fk_angajat_job FOREIGN KEY (id_job) REFERENCES job(id_job)
);

CREATE TABLE client (
    cui                 VARCHAR2(10) PRIMARY KEY,
    nume_contractor     VARCHAR2(40) NOT NULL,
    numar_reg_com       VARCHAR2(14) NOT NULL UNIQUE,
    email               VARCHAR2(30) NOT NULL,
    telefon             VARCHAR2(10) NOT NULL,
    judet               VARCHAR2(15) NOT NULL,
    oras                VARCHAR2(25) NOT NULL,
    strada              VARCHAR2(50) NOT NULL,
    numar_strada        NUMBER(4),
    CONSTRAINT check_telefon_client CHECK (length(telefon)=10 AND substr(telefon,1,1)='0')
);

CREATE TABLE lucrare (
    id_lucrare      NUMBER PRIMARY KEY,
    nume_lucrare    VARCHAR2(60) NOT NULL,
    buget           NUMBER(15, 2) NOT NULL,
    data_start      DATE DEFAULT SYSDATE,
    status          VARCHAR2(20) DEFAULT 'In asteptare',
    cui             VARCHAR2(10) NOT NULL,
    CONSTRAINT check_status_lucrare CHECK (status IN ('In asteptare', 'Activ', 'Finalizat')),
    CONSTRAINT fk_lucrare_client FOREIGN KEY (cui) REFERENCES client(cui) ON DELETE CASCADE
);

CREATE TABLE pontaj (
    id_pontaj       NUMBER PRIMARY KEY,
    id_angajat      NUMBER NOT NULL,
    id_lucrare      NUMBER NOT NULL,
    data_lucru      DATE NOT NULL,
    nr_ore          NUMBER(3, 1) NOT NULL,
    CONSTRAINT check_ore_pozitiv CHECK (nr_ore > 0 AND nr_ore <= 24),
    CONSTRAINT fk_pontaj_angajat FOREIGN KEY (id_angajat) REFERENCES angajat(id_angajat) ON DELETE CASCADE,
    CONSTRAINT fk_pontaj_lucrare FOREIGN KEY (id_lucrare) REFERENCES lucrare(id_lucrare) ON DELETE CASCADE
);

CREATE TABLE utilaj (
    nr_inmatriculare  VARCHAR2(9) PRIMARY KEY,
    marca             VARCHAR2(15) NOT NULL,
    denumire          VARCHAR2(30) NOT NULL,
    unitate_masura    VARCHAR2(5) DEFAULT 'ora',
    cost_unitate       NUMBER(8, 2) NOT NULL,
    CONSTRAINT check_um_utilaj CHECK (unitate_masura IN ('ora', 'km')),
    CONSTRAINT check_cost_utilaj CHECK (cost_unitate >= 0)
);

CREATE TABLE material (
    id_material     NUMBER PRIMARY KEY,
    nume_material   VARCHAR2(30) NOT NULL,
    unitate_masura  VARCHAR2(10) NOT NULL,
    pret_unitate    NUMBER(8, 2) NOT NULL
);

CREATE TABLE furnizor (
    id_furnizor     NUMBER PRIMARY KEY,
    cui             VARCHAR2(10) NOT NULL UNIQUE, 
    nume_firma      VARCHAR2(40) NOT NULL,
    numar_reg_com   VARCHAR2(14) NOT NULL UNIQUE,
    iban            VARCHAR2(24) UNIQUE NOT NULL,
    nume_banca      VARCHAR2(25) NOT NULL,
    email           VARCHAR2(30) NOT NULL,
    telefon         VARCHAR2(10) NOT NULL,
    judet           VARCHAR2(15) NOT NULL,
    oras            VARCHAR2(25) NOT NULL,
    strada          VARCHAR2(50) NOT NULL,
    numar_strada    NUMBER(4),
    CONSTRAINT check_lungime_iban_furnizor CHECK (length(iban)<=24),
    CONSTRAINT check_telefon_furnizor CHECK (length(telefon)=10 AND substr(telefon,1,1)='0')
);

CREATE TABLE oferta (
    id_furnizor     NUMBER,
    id_material     NUMBER,
    pret_oferit     NUMBER(10, 2) NOT NULL,
    CONSTRAINT pk_oferta PRIMARY KEY (id_furnizor, id_material),
    CONSTRAINT fk_oferta_furnizor FOREIGN KEY (id_furnizor) REFERENCES furnizor(id_furnizor) ON DELETE CASCADE,
    CONSTRAINT fk_oferta_material FOREIGN KEY (id_material) REFERENCES material(id_material) ON DELETE CASCADE
);

CREATE TABLE consum (
    id_consum           NUMBER PRIMARY KEY,
    id_lucrare          NUMBER NOT NULL,
    id_material         NUMBER DEFAULT NULL,
    nr_inmatriculare    VARCHAR2(9) DEFAULT NULL,
    cantitate_material  NUMBER(10, 2) DEFAULT NULL,
    cantitate_utilaj    NUMBER(10, 2) DEFAULT NULL,
    data_consum         DATE DEFAULT SYSDATE,
    CONSTRAINT check_macar_o_cantitate CHECK (
        (cantitate_material IS NOT NULL AND cantitate_material > 0) 
        OR 
        (cantitate_utilaj IS NOT NULL AND cantitate_utilaj > 0)
    ),
    CONSTRAINT fk_consum_lucrare FOREIGN KEY (id_lucrare) REFERENCES lucrare(id_lucrare) ON DELETE CASCADE,
    CONSTRAINT fk_consum_material FOREIGN KEY (id_material) REFERENCES material(id_material) ON DELETE CASCADE,
    CONSTRAINT fk_consum_utilaj FOREIGN KEY (nr_inmatriculare) REFERENCES utilaj(nr_inmatriculare) ON DELETE CASCADE
);

COMMIT;