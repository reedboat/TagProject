DROP TABLE IF EXISTS tbl_tag ;
CREATE TABLE tbl_tag
(
    id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(128) NOT NULL,
    category INT NOT NULL DEFAULT 0,
    frequency INTEGER DEFAULT 1,
    create_time INT UNSIGNED NOT NULL DEFAULT 0,
    metadata TEXT NOT NULL DEFAULT '',
    UNIQUE (name)
)ENGINE=INNODB DEFAULT CHARSET=UTF8;
CREATE INDEX idx_name_fequency ON tbl_tag (name, frequency);

DROP TABLE IF EXISTS tbl_article_tags;
CREATE  TABLE tbl_article_tags 
(
    site_id INT NOT NULL DEFAULT 0,
    news_id   CHAR(15) NOT NULL DEFAULT '',
    tags CHAR(255) NOT NULL DEFAULT '',
    create_time INT UNSIGNED NOT NULL DEFAULT 0,
    update_time INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (site_id, news_id) 
)ENGINE=INNODB DEFAULT CHARSET=UTF8;

DROP TABLE IF EXISTS tbl_tag_articles;
CREATE TABLE tbl_tag_articles
(
    id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,   
    tag_id INT NOT NULL DEFAULT 0,
    site_id INT NOT NULL DEFAULT 0,
    type TINYINT NOT NULL DEFAULT 0,
    time INT UNSIGNED NOT NULL DEFAULT 0,
    news_id CHAR(15) NOT NULL DEFAULT ''
)ENGINE=INNODB DEFAULT CHARSET=UTF8;

CREATE INDEX idx_tag_time ON tbl_tag_articles (tag_id, time);
CREATE INDEX idx_tag_site_time ON tbl_tag_articles (tag_id, site_id, time);
CREATE INDEX idx_tag_type_time ON tbl_tag_articles (tag_id, type, time);
CREATE INDEX idx_tag_site_type_time ON tbl_tag_articles (tag_id, site_id, type, time);

DROP TABLE IF EXISTS tbl_article_mini;
CREATE TABLE tbl_article_mini
(
    Fsite_id INT NOT NULL DEFAULT 0,
    Farticle_id CHAR(15) NOT NULL DEFAULT '',
    Ftitle VARCHAR(128) NOT NULL DEFAULT '',
    Fpub_time DATETIME NOT NULL,
    Ftype VARCHAR(128) NOT NULL DEFAULT '',
    Fmeta TEXT NOT NULL DEFAULT '',
    create_at INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (Fsite_id, Farticle_id)
)ENGINE=INNODB DEFAULT CHARSET=UTF8;
