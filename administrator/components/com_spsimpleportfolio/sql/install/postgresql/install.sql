CREATE TABLE IF NOT EXISTS "#__spsimpleportfolio_items" (
  "id" SERIAL PRIMARY KEY,
  "title" VARCHAR(255) NOT NULL,
  "alias" VARCHAR(55) NOT NULL,
  "catid" INT NOT NULL,
  "image" TEXT NOT NULL,
  "thumbnail" TEXT NOT NULL,
  "video" TEXT NOT NULL,
  "description" TEXT,
  "client" VARCHAR(100) NOT NULL DEFAULT '',
  "client_avatar" TEXT NOT NULL,
  "tagids" TEXT NOT NULL,
  "url" TEXT NOT NULL,
  "published" SMALLINT NOT NULL DEFAULT 1,
  "language" VARCHAR(255) NOT NULL DEFAULT '*',
  "access" INT NOT NULL DEFAULT 1,
  "ordering" INT NOT NULL DEFAULT 0,
  "created_by" BIGINT NOT NULL DEFAULT 0,
  "created" TIMESTAMP NOT NULL,
  "modified_by" BIGINT NOT NULL DEFAULT 0,
  "modified" TIMESTAMP NOT NULL,
  "checked_out" BIGINT NULL,
  "checked_out_time" TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS "#__spsimpleportfolio_tags" (
  "id" SERIAL PRIMARY KEY,
  "title" VARCHAR(255) NOT NULL,
  "alias" VARCHAR(55) NOT NULL
);
