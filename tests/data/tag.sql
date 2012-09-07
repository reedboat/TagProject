DROP TABLE IF EXISTS tbl_tag ;
CREATE TABLE tbl_tag
(
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(128) NOT NULL,
    category INT NOT NULL DEFAULT 0,
    frequency INTEGER DEFAULT 1,
    create_time INT UNSIGNED NOT NULL DEFAULT 0,
    metadata TEXT NOT NULL DEFAULT '',
    UNIQUE (name)
);
CREATE INDEX idx_name_fequency ON tbl_tag (name, frequency);

DROP TABLE IF EXISTS tbl_article_tags;
CREATE  TABLE tbl_article_tags 
(
    site_id TINYINT NOT NULL DEFAULT 0,
    news_id   CHAR(14) NOT NULL DEFAULT '',
    tags CHAR(255) NOT NULL DEFAULT '',
    create_time INT UNSIGNED NOT NULL DEFAULT 0,
    update_time INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (site_id, news_id) 
);

DROP TABLE IF EXISTS tbl_tag_articles;
CREATE TABLE tbl_tag_articles
(
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,   
    tag_id INT NOT NULL DEFAULT 0,
    site_id TINYINT NOT NULL DEFAULT 0,
    time INT UNSIGNED NOT NULL DEFAULT 0,
    news_id CHAR(14) NOT NULL DEFAULT ''
);

CREATE INDEX idx_tag_site_time ON tbl_tag_articles (tag_id, site_id, time);
CREATE INDEX idx_tag_time ON tbl_tag_articles (tag_id, time);

DROP TABLE IF EXISTS tbl_article_mini;
CREATE TABLE tbl_article_mini
(
    --id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    Fsite VARCHAR(64) NOT NULL DEFAULT '',
    Farticle_id CHAR(14) NOT NULL DEFAULT '',
    Ftitle VARCHAR(128) NOT NULL DEFAULT '',
    Fpub_time DATETIME NOT NULL DEFAULT '',
    Fabstract VARCHAR(255) NOT NULL DEFAULT '',
    Fthumbnail VARCHAR(128) NOT NULL DEFAULT '',
    create_at INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (Fsite, Farticle_id)
);
