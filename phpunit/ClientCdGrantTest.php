<?php
namespace phpunit\Gap\Open\Server;

use PHPUnit\Framework\TestCase;
use Gap\Db\Pdo\Param\ParamBase;
use Gap\Db\MySql\Cnn;
use Gap\Open\Server\OpenServer;

class ClientCdGrantTest extends TestCase
{
    public function testClientCredentials(): void
    {
        $this->initParamIndex();

        $pdo = $this->createMock('PDO');
        $stmt = $this->createMock('PDOStatement');
        $stmt->method('execute')->will($this->returnValue(true));
        $pdo->method('prepare')->will($this->returnValue($stmt));
        $serverId = 'xdfsa';
        $cnn = new Cnn($pdo, $serverId);

        $stmt->method('fetch')->will(
            $this->returnValue([
                'appId' => 'fake-app-id',
                'appSecret' => 'fake-app-secret'
            ])
        );

        $openServer = new OpenServer($cnn);
        $clientCdGrant = $openServer->clientCdGrant();

        $appId = 'fake-app-id';
        $appSecret = 'fake-app-secret';

        $accessToken = $clientCdGrant->accessToken(
            $appId,
            $appSecret
        );

        if (is_null($accessToken)) {
            return;
        }
        
        $this->assertEquals(
            'fake-app-id',
            $accessToken->appId
        );

        $stmts = $cnn->executed();
        $fetchAppStmt = $stmts[0];
        $insertStmt = $stmts[1];

        $this->assertEquals(
            'SELECT appId, appSecret, appName, redirectUrl, privilege, scope, created, changed '
            . 'FROM open_app WHERE appId = :k1 LIMIT 1',
            $fetchAppStmt->sql()
        );

        $this->assertEquals(
            'INSERT INTO '
            . 'open_access_token (token, appId, userId, refresh, scope, created, expired) '
            . 'VALUES (:k2, :k3, :k4, :k5, :k6, :k7, :k8)',
            $insertStmt->sql()
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
