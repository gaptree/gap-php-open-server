<?php
namespace phpunit\Gap\Open\Server;

use PHPUnit\Framework\TestCase;
use Gap\Db\Pdo\Param\ParamBase;
use Gap\Db\MySql\Cnn;
use Gap\Open\Server\OpenServer;

class BearerAuthorizeTest extends TestCase
{
    public function testAuthorize(): void
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

        $token = 'fake-token-str';

        $stmt->method('fetch')->will(
            $this->returnValue([
                'token' => $token
            ])
        );

        $openServer = new OpenServer($cnn);

        $this->assertTrue(
            $openServer->accessTokenService()
                ->bearerAuthorize('Bearer ' . $token)
        );

        $stmts = $cnn->executed();
        $fetchStmt = $stmts[0];

        $this->assertEquals(
            'SELECT token, appId, userId, refresh, scope, created, expired '
            . 'FROM open_access_token WHERE token = :k1 LIMIT 1',
            $fetchStmt->sql()
        );

        $this->assertEquals(
            $token,
            $fetchStmt->vals()[':k1']
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
