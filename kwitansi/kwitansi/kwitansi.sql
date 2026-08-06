CREATE DATABASE IF NOT EXISTS db_kwitansi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_kwitansi;

CREATE TABLE IF NOT EXISTS users (
    username  VARCHAR(30)  NOT NULL,
    password  VARCHAR(50)  NOT NULL,
    m1        TINYINT(1)   DEFAULT 1,
    m2        TINYINT(1)   DEFAULT 0,
    PRIMARY KEY (username)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO users (username, password, m1, m2) VALUES
('DANY', '1277', 1, 0),
('FINA', 'MIMI', 1, 0),
('MITA', 'MITA', 1, 0),
('RERE', 'RERE', 1, 0);

CREATE TABLE IF NOT EXISTS brg (
    kd_brg  INT          NOT NULL,
    nm_brg  VARCHAR(50)  NOT NULL,
    PRIMARY KEY (kd_brg)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO brg (kd_brg, nm_brg) VALUES
( 1, 'LABORAT'), ( 2, 'ECG'), ( 3, 'RONTGEN'), ( 4, 'USG'),
( 5, 'CT SCAN'), ( 6, 'TREADMIL'), ( 7, 'POLI KLINIK'), ( 8, 'POLI SPESIALIS'),
( 9, 'POLI GIGI'), (10, 'GIGI PALSU'), (11, 'FISIOTERAPI'), (12, 'BEDAH'),
(13, 'FARMASI'), (14, 'UGD'), (15, 'TU'), (16, 'DOKTER');

CREATE TABLE IF NOT EXISTS kwitansi (
    id                INT           NOT NULL AUTO_INCREMENT,
    no_kwitansi       VARCHAR(20)   NOT NULL,
    no_faktur         VARCHAR(30)   DEFAULT NULL,
    terima_dari       VARCHAR(100)  DEFAULT NULL,
    uang_sejumlah     TEXT          DEFAULT NULL,
    untuk_pembayaran  TEXT          DEFAULT NULL,
    keterangan        VARCHAR(200)  DEFAULT NULL,
    jumlah            DECIMAL(15,2) DEFAULT 0,
    tgl               DATE          DEFAULT NULL,
    kasir             VARCHAR(30)   DEFAULT NULL,
    stat              VARCHAR(10)   DEFAULT 'OPEN',
    PRIMARY KEY (id),
    UNIQUE KEY uk_no_kwitansi (no_kwitansi),
    KEY idx_terima_dari (terima_dari(50)),
    KEY idx_no_faktur   (no_faktur),
    KEY idx_tgl         (tgl)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS detail_kwitansi (
    id           INT           NOT NULL AUTO_INCREMENT,
    no_kwitansi  VARCHAR(20)   NOT NULL,
    no_faktur    VARCHAR(30)   DEFAULT NULL,
    kd_brg       INT           DEFAULT NULL,
    nama         VARCHAR(150)  DEFAULT NULL,
    jumlah       DECIMAL(15,2) DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_no_kwitansi (no_kwitansi),
    KEY fk_detail_brg (kd_brg)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
