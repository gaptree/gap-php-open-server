<?php
namespace phpunit\Gap\Open\Server;

use PHPUnit\Framework\TestCase;
use Gap\Db\Pdo\Param\ParamBase;
use Gap\Db\MySql\Cnn;
use Gap\Open\Server\OpenServer;

class OpenIdGrantTest extends TestCase
{
    public function testAuthCode(): void
    {
        $this->initParamIndex();

        $pdo = $this->createMock('PDO');
        $stmt = $this->createMock('PDOStatement');
        $stmt->method('execute')->will($this->returnValue(true));
        $pdo->method('prepare')->will($this->returnValue($stmt));
        $pdo->method('beginTransaction')->will(
            $this->returnValue(true)
        );
        $pdo->method('commit')->will(
            $this->returnValue(true)
        );
        $serverId = 'xdfsa';
        $cnn = new Cnn($pdo, $serverId);

        $publicKey =
            '-----BEGIN PUBLIC KEY-----' . "\n"
            . 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDozCTehrdWbeYvhASW3kyIpZnh'
            . 'FumsSHnoIVxlSXv3eMbBSGJ/urhL/A+F18gL4NpTcaU03C5qoZTR20QwHk73pZXE'
            . '9dZtyFKg5xjxdKvqHhYGDXy5T/tSeD7QwIAnmdVjwvKydITcY1e3NUggRDhu9VYj'
            . '4c50TXeg/V25GxFoiQIDAQAB' . "\n"
            . '-----END PUBLIC KEY-----';
        $openServer = new OpenServer([
            'cnn' => $cnn,
            'privateKey' => 'file://' . __DIR__ . '/private.pem',
            'publicKey' => $publicKey
        ]);

        $openIdGrant = $openServer->openIdGrant();

        $userId = 'fake-user-id';
        $stmt->method('fetch')->will(
            $this->returnValue([
                'userId' => 'fake-user-id',
                'zcode' => 'fake-user-zcode',
                'nick' => 'fake-user-nick'
            ])
        );


        $idToken = $openIdGrant->idToken($userId);
        $tokenStr = (string) $idToken;
        $appId = 'fake-app-id';

        // or $accessToken = $openIdGrant->accessToken($appId, $tokenStr, 'file://' . __DIR__ . '/public.pem');
        $accessToken = $openIdGrant->accessToken($appId, $tokenStr);

        if (is_null($accessToken)) {
            return;
        }

        $this->assertEquals(
            'fake-user-id',
            $accessToken->userId
        );

        $stmts = $cnn->executed();
        $this->assertEquals(
            'SELECT userId, nick, zcode, avt, logined, created, changed FROM open_user WHERE userId = :k1 LIMIT 1',
            $stmts[0]->sql()
        );

        $this->assertEquals(
            'INSERT INTO open_access_token '
            . '(token, appId, userId, refresh, scope, created, expired) '
            . 'VALUES (:k2, :k3, :k4, :k5, :k6, :k7, :k8)',
            $stmts[1]->sql()
        );
    }

    /**
     * @SuppressWarnings(PHPMD)
     */
    protected function initParamIndex(): void
    {
        ParamBase::initIndex();
    }
}
