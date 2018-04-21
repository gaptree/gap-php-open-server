# Gap Open Server

## Database Schema

```sql
CREATE TABLE `open_access_token` (
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `appId` varbinary(64) NOT NULL,
  `userId` varbinary(64) NOT NULL,
  `refresh` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scope` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `diff` int(10) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `expired` datetime NOT NULL,
  PRIMARY KEY (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `open_app` (
  `appId` varbinary(64) NOT NULL,
  `appSecret` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `appName` varchar(21) COLLATE utf8mb4_unicode_ci NOT NULL,
  `redirectUrl` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `privilege` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `scope` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `changed` datetime NOT NULL,
  PRIMARY KEY (`appId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `open_auth_code` (
  `code` varbinary(64) NOT NULL DEFAULT '',
  `appId` varbinary(64) NOT NULL DEFAULT '',
  `userId` varbinary(64) NOT NULL DEFAULT '',
  `redirectUrl` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `scope` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `expired` datetime NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `open_refresh_token` (
  `refresh` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `appId` varbinary(64) NOT NULL DEFAULT '',
  `userId` varbinary(64) NOT NULL DEFAULT '',
  `scope` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `expired` datetime NOT NULL,
  PRIMARY KEY (`refresh`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `open_user` (
  `userId` varbinary(64) NOT NULL DEFAULT '',
  `nick` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `zcode` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `avt` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `logined` datetime NOT NULL,
  `created` datetime NOT NULL,
  `changed` datetime NOT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```
