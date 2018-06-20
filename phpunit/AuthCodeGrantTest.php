<?php
namespace phpunit\Gap\Open\Server;

use PHPUnit\Framework\TestCase;
use Gap\Db\Pdo\Param\ParamBase;
use Gap\Db\MySql\Cnn;
use Gap\Open\Server\OpenServer;

class AuthCodeGrantTest extends TestCase
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

        $openServer = new OpenServer(['cnn' => $cnn]);
        $authCodeGrant = $openServer->authCodeGrant();

        $appId = 'fake-app-id';
        $userId = 'fake-user-id';
        $redirectUrl = 'fake-redirect-url';
        $scope = 'openId';

        $authCode = $authCodeGrant->authCode(
            $appId,
            $userId,
            $redirectUrl,
            $scope
        );
        if (is_null($authCode)) {
            return;
        }

        $stmt->method('fetch')->will(
            $this->returnValue($authCode->getData())
        );

        $accessToken = $authCodeGrant->accessToken($appId, $authCode->code);
        if (is_null($accessToken)) {
            return;
        }

        $this->assertEquals($scope, $accessToken->scope);

        $stmts = $cnn->executed();

        $createAuthCodeStmt = $stmts[0];
        $this->assertEquals(
            'INSERT INTO open_auth_code '
            . '(code, appId, userId, redirectUrl, scope, created, expired) '
            .'VALUES (:k1, :k2, :k3, :k4, :k5, :k6, :k7)',
            $createAuthCodeStmt->sql()
        );

        $this->assertEquals($appId, $authCode->appId);

        $vals = $createAuthCodeStmt->vals();
        $this->assertEquals($appId, $vals[':k2']);

        $this->assertEquals($authCode->code, $vals[':k1']);

        $this->assertEquals(
            'SELECT code, appId, userId, redirectUrl, scope, created, expired '
            . 'FROM open_auth_code WHERE code = :k8 AND status = :k9 LIMIT 1',
            $stmts[1]->sql()
        );
        $this->assertEquals(
            'INSERT INTO open_refresh_token (refresh, appId, userId, scope, created, expired) '
            . 'VALUES (:k10, :k11, :k12, :k13, :k14, :k15)',
            $stmts[2]->sql()
        );
        $this->assertEquals(
            'INSERT INTO open_access_token (token, appId, userId, refresh, scope, info, created, expired) '
            . 'VALUES (:k16, :k17, :k18, :k19, :k20, :k21, :k22, :k23)',
            $stmts[3]->sql()
        );
        $this->assertEquals(
            'UPDATE open_auth_code SET status = :k24 WHERE code = :k25',
            $stmts[4]->sql()
        );

        $this->assertEquals(
            'fake-app-id',
            $accessToken->appId
        );

        $this->assertEquals(
            $authCode->code,
            $stmts[4]->vals()[':k25']
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
