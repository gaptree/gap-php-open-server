<?php
namespace Gap\Open\Server\Service;

use Gap\Dto\DateTime;
use Gap\Open\Dto\AccessTokenDto;
use Gap\Open\Server\Repo\Contract\AccessTokenRepoInterface;

class AccessTokenService extends ServiceBase
{
    private $ttl;

    /*
    public function generate(
        string $appId,
        string $userId = '',
        string $scope = ''
    ): AccessTokenDto {
        $created = new DateTime();
        $expired = (new DateTime())->add($this->getTtl());

        $accessToken = new AccessTokenDto([
            'token' => $this->randomCode(),
            'refresh' => '',
            'appId' => $appId,
            'userId' => $userId,
            'scope' => $scope,
            'created' => $created,
            'expired' => $expired
        ]);

        return $accessToken;
    }
    public function create(AccessTokenDto $accessToken): void
    {
        if ($this->cache) {
            $this->cache->set($accessToken->token, $accessToken, $this->getTtl());
        }

        $this->getRepo()->create($accessToken);
    }
     */

    public function create(array $opts): AccessTokenDto
    {
        $created = new DateTime();
        $expired = (new DateTime())->add($this->getTtl());

        $appId = $opts['appId'] ?? '';
        $userId = $opts['userId'] ?? '';
        $scope = $opts['score'] ?? '';
        $refresh = $opts['refresh'] ?? '';

        if (empty($appId)) {
            throw new \Exception('appId cannot be empty');
        }

        $accessToken = new AccessTokenDto([
            'token' => $this->randomCode(),
            'refresh' => $refresh,
            'appId' => $appId,
            'userId' => $userId,
            'scope' => $scope,
            'created' => $created,
            'expired' => $expired
        ]);

        $this->cacheSet($accessToken->token, $accessToken, $this->getTtl());
        $this->getRepo()->create($accessToken);
        return $accessToken;
    }

    public function fetch(string $token): ?AccessTokenDto
    {
        if ($accessToken = $this->cacheGet($token)) {
            return $accessToken;
        }

        if ($accessToken = $this->getRepo()->fetch($token)) {
            $this->cacheSet($accessToken->token, $accessToken, $this->getTtl());
        }

        return $accessToken;
    }

    public function bearerAuthorize(string $bearerToken): bool
    {
        if (0 !== strpos($bearerToken, 'Bearer ')) {
            return false;
        }

        $token = substr($bearerToken, 7);
        if ($this->fetch($token)) {
            return true;
        }

        return false;
    }

    protected function getTtl(): \DateInterval
    {
        if ($this->ttl) {
            return $this->ttl;
        }

        $this->ttl = new \DateInterval('PT1H');
        return $this->ttl;
    }

    protected function getRepo(): AccessTokenRepoInterface
    {
        return $this->repoManager->getAccessTokenRepo();
    }
}
