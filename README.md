# Gap Open Server

## Install

```
$ composer require gap/open-server
```

## API

Gap\Open\Server\OpenServer
- __construct(?CnnInterface $cnn = null, ?CacheInterface $cache = null, array $opts = [])
- authCodeGrant(): Grant\AuthCodeGrant
- openIdGrant(): Grant\OpenIdGrant
- clientCdGrant(): Grant\ClientCdGrant
- appService(): Service\AppService
- accessTokenService(): Service\AccessTokenService

Gap\Open\Server\Grant\AuthCodeGrant
- authCode(string $appId, string $userId, string $redirectUrl, string $scope = ''): ?AuthCodeDto
- accessToken($appId, $code): ?AccessTokenDto 

Gap\Open\Server\Grant\ClientCdGrant
- accessToken(string $appId, string $appSecret): ?AccessTokenDto

Gap\Open\Server\Grant\OpenIdGrant
- idToken(string $userId) // todo
- accessToken(string $appId, string $token): ?AccessTokenDto

Gap\Open\Server\Service\AppService
- fetch(string $appId): ?AppDto
- create(AppDto $app): void
- disable(AppDto $app): void

Gap\Open\Server\Service\AccessTokenService
- bearerAuthorize(string $bearerToken): bool

## Usage

### Auth Code

```php
$cnn = new Cnn($pdo, $serverId);
$openServer = new OpenServer($cnn);
$authCodeGrant = $openServer->authCodeGrant();

$appId = 'fake-app-id';
$userId = 'fake-user-id';
$redirectUrl = 'fake-redirect-url';
$scope = '';

$authCode = $authCodeGrant->authCode(
    $appId,
    $userId,
    $redirectUrl,
    $scope
);

$accessToken = $authCodeGrant->accessToken(
    $appId,
    $authCode->code
);

if (is_null($accessToken)) {
    return;
}
```
### Client Credentials

```php
$clientCdGrant = $openServer->clientCdGrant();
$appId = 'fake-app-id';
$appSecret = 'fake-app-secret';
$accessToken = $clientCdGrant->accessToken(
    $appId,
    $appSecret
);
```

### OpenId

```php
$publicKey =
    '-----BEGIN PUBLIC KEY-----' . "\n"
    . 'xxx'
    . 'xxx' . "\n"
    . '-----END PUBLIC KEY-----';
$privateKey =
    '------BEGIN RSA PRIVATE KEY----' . "\n"
    . 'xxxx'
    . 'xxx' . "\n"
    . '------END RSA PRIVATE KEY----';

$openServer = new OpenServer($cnn, $cache, [
    'publicKey' => $publicKey,
    'privateKey' => $privateKey
]);

$openIdGrant = $openServer->openIdGrant();

$idToken = $openIdGrant->idToken($userId);
$tokenStr = (string) $idToken;
$accessToken = $openIdGrant->accessToken($appId, $tokenStr);
```

### Authorization

```php
$token = 'Bearer xxxxx';
$openServer->accessTokenService()
    ->bearerAuthorize($token)
```

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
  `appSecret` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `appName` varchar(21) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
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
  `status` enum('ok','destroyed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ok',
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
