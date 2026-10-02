-- enable_manage_title --
CREATE TABLE `phppos_people_name_prefixes` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
    PRIMARY KEY (id) USING BTREE
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci;

INSERT INTO
    `phppos_people_name_prefixes` (`id`, `name`)
VALUES
    (1, 'common_mr.'),
    (2, 'common_mrs.'),
    (3, 'common_dr.'),
    (4, 'common_miss'),
    (5, 'common_ms'),
    (6, 'common_hon.'),
    (7, 'common_prof.'),
    (8, 'common_rev.'),
    (9, 'common_rt_hon.'),
    (10, 'common_sr.'),
    (11, 'common_jr.'),
    (12, 'common_st.');
    