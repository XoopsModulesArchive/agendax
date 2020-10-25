#
# Table structure for table `agendax_cat`
#

CREATE TABLE agendax_cat (
    cat_id   INT(11)      NOT NULL AUTO_INCREMENT,
    cat_name VARCHAR(150) NOT NULL DEFAULT '',
    PRIMARY KEY (cat_id)
)
    ENGINE = ISAM;

INSERT INTO agendax_cat
VALUES (1, 'General');

# --------------------------------------------------------

#
# Table structure for table `agendax_event_repeats`
#

CREATE TABLE agendax_event_repeats (
    event_id            INT(11) NOT NULL DEFAULT '0',
    event_rpt_type      VARCHAR(20)      DEFAULT NULL,
    event_end           INT(11)          DEFAULT NULL,
    frequency           INT(11)          DEFAULT '1',
    event_repeaton_days VARCHAR(7)       DEFAULT NULL,
    PRIMARY KEY (event_id)
)
    ENGINE = ISAM;
# --------------------------------------------------------

#
# Table structure for table `agendax_event_repeats_not`
#

CREATE TABLE agendax_event_repeats_not (
    event_id   INT(11) NOT NULL DEFAULT '0',
    event_date INT(11) NOT NULL DEFAULT '0'
)
    ENGINE = ISAM;
# --------------------------------------------------------

#
# Table structure for table `agendax_events`
#

CREATE TABLE agendax_events (
    id          INT(11)               NOT NULL AUTO_INCREMENT,
    group_id    INT(11)                        DEFAULT NULL,
    ext_for_id  INT(11)                        DEFAULT NULL,
    title       VARCHAR(255)          NOT NULL DEFAULT '',
    description TEXT                  NOT NULL,
    contact     TEXT                  NOT NULL,
    url         VARCHAR(100)          NOT NULL DEFAULT '',
    email       VARCHAR(120)          NOT NULL DEFAULT '',
    picture     VARCHAR(100)          NOT NULL DEFAULT '',
    cat         TINYINT(2)            NOT NULL DEFAULT '0',
    date        INT(11)               NOT NULL DEFAULT '0',
    time        INT(11)                        DEFAULT NULL,
    modif_date  INT(11)                        DEFAULT NULL,
    modif_time  INT(11)                        DEFAULT NULL,
    duration    INT(11)               NOT NULL DEFAULT '0',
    priority    TINYINT(4)                     DEFAULT '2',
    day         TINYINT(2)            NOT NULL DEFAULT '0',
    month       SMALLINT(2)           NOT NULL DEFAULT '0',
    year        SMALLINT(4)           NOT NULL DEFAULT '0',
    approved    TINYINT(1)            NOT NULL DEFAULT '0',
    submit_by   MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
    type        CHAR(1)               NOT NULL DEFAULT 'E',
    access      CHAR(1)                        DEFAULT 'P',
    PRIMARY KEY (id)
)
    ENGINE = ISAM;
# --------------------------------------------------------

#
# Table structure for table `agendax_mcalurl`
#

CREATE TABLE agendax_mcalurl (
    mc_id       INT(7)          NOT NULL DEFAULT '0',
    mc_isscript INT(1) UNSIGNED NOT NULL DEFAULT '0',
    mc_url      VARCHAR(150)    NOT NULL DEFAULT '',
    UNIQUE KEY mc_id (mc_id)
)
    ENGINE = ISAM;

INSERT INTO agendax_mcalurl
VALUES (0, 0, '');
