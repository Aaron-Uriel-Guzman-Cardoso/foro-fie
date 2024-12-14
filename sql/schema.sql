USE "foro_fie";

CREATE OR REPLACE TABLE "account" (
    "id" INTEGER UNSIGNED AUTO_INCREMENT,
    "nickname" VARCHAR(64) NOT NULL,
    "desc" VARCHAR(256) NOT NULL,
    "hash" BINARY(32) NOT NULL, 
    "salt" BINARY(32) NOT NULL,
    UNIQUE ("nickname"),
    PRIMARY KEY ("id")
);

CREATE OR REPLACE TABLE "student" (
    "id" VARCHAR(8),
    "first_name" VARCHAR(128) NOT NULL,
    "last_name" VARCHAR(128) NOT NULL,
    "email" VARCHAR(256) NOT NULL,
    PRIMARY KEY ("id")
);

CREATE OR REPLACE TABLE "student_account" (
    "student" VARCHAR(8),
    "account" INTEGER UNSIGNED,
    UNIQUE ("student"), -- Un usuario tendrá a lo más una cuenta
    PRIMARY KEY ("student", "account"),
    FOREIGN KEY ("student") REFERENCES "student"("id")
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY ("account") REFERENCES "account"("id")
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE OR REPLACE TABLE "group" (
    "id" TINYINT UNSIGNED AUTO_INCREMENT,
    "name" VARCHAR(64) NOT NULL UNIQUE,
    PRIMARY KEY ("id")
);

CREATE OR REPLACE TABLE "account_group" (
    "account" INTEGER UNSIGNED,
    "group" TINYINT UNSIGNED,
    PRIMARY KEY ("account", "group"),
    FOREIGN KEY ("account") REFERENCES "account"("id")
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY ("group") REFERENCES "group"("id")
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE OR REPLACE TABLE "grant" (
    "id" SMALLINT UNSIGNED AUTO_INCREMENT,
    "name" VARCHAR(64) NOT NULL UNIQUE,
    PRIMARY KEY ("id")
);

CREATE OR REPLACE TABLE "group_grant" (
    "group" TINYINT UNSIGNED NOT NULL,
    "grant" SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY ("group", "grant"),
    FOREIGN KEY ("group") REFERENCES "group"("id")
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY ("grant") REFERENCES "grant"("id")
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE OR REPLACE TABLE "post" (
    "id" INTEGER UNSIGNED,
    "account" INTEGER UNSIGNED NOT NULL,
    "title" VARCHAR(128) NOT NULL,
    "content" TEXT NOT NULL,
    "category" INTEGER UNSIGNED NOT NULL,
    PRIMARY KEY ("id"),
    FOREIGN KEY ("account") REFERENCES "account" ("id")
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE OR REPLACE TABLE "ban" (
    "id" INTEGER UNSIGNED AUTO_INCREMENT,
    "account" INTEGER UNSIGNED NOT NULL,
    "start" DATE NOT NULL,
    "end" DATE NOT NULL,
    "reason" TEXT NOT NULL,
    PRIMARY KEY ("id"),
    FOREIGN KEY ("account") REFERENCES "account"("id")
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE OR REPLACE TABLE "comment" (
    "id" INTEGER UNSIGNED AUTO_INCREMENT,
    "post" INTEGER UNSIGNED NOT NULL,
    "account" INTEGER UNSIGNED NOT NULL,
    "publication" DATE NOT NULL,
    "content" TEXT NOT NULL,
    PRIMARY KEY ("id"),
    FOREIGN KEY ("post") REFERENCES "post"("id")
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY ("account") REFERENCES "account"("id")
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE OR REPLACE TABLE "reply" (
    "comment" INTEGER UNSIGNED NOT NULL,
    "parent" INTEGER UNSIGNED NOT NULL,
    PRIMARY KEY ("comment", "parent"),
    FOREIGN KEY ("comment") REFERENCES "comment"("id")
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY ("parent") REFERENCES "comment"("id")
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE OR REPLACE TABLE "category" (
    "id" INTEGER UNSIGNED AUTO_INCREMENT,
    "name" VARCHAR(64) NOT NULL UNIQUE,
    PRIMARY KEY ("id")
);
