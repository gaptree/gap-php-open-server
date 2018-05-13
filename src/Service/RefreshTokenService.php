<?php
namespace Gap\Open\Server\Service;

use Gap\Dto\DateTime;
use Gap\Open\Dto\RefreshTokenDto;
use Gap\Open\Server\Repo\Contract\RefreshTokenRepoInterface;

class RefreshTokenService extends ServiceBase
{
    protected $ttl;

    /*
    public function generate(
        string $appId,
        string $userId = '',
        string $scope = ''
    ): RefreshTokenDto {
        $created = new DateTime();
        $expired = (new DateTime())->add($this->getTtl());

        $refreshToken = new RefreshTokenDto([
            'refresh' => $this->randomCode(),
            'appId' => $appId,
            'userId' => $userId,
            'scope' => $scope,
            'created' => $created,
            'expired' => $expired
        ]);

        return $refreshToken;
    }

    public function create(RefreshTokenDto $refreshToken): void
    {
        if ($this->cache) {
            $this->cache->set($refreshToken->refresh, $refreshToken, $this->getTtl());
        }

        $this->getRepo()->create($refreshToken);
    }
    */

    public function create(array $opts): RefreshTokenDto
    {
        $appId = $opts['appId'] ?? '';
        $userId = $opts['userId'] ?? '';
        $scope = $opts['scope'] ?? '';
        $created = new DateTime();
        $expired = (new DateTime())->add($this->getTtl());

        if (empty($appId)) {
            throw new \Exception('appId cannot be empty');
        }

        $refreshToken = new RefreshTokenDto([
            'refresh' => $this->randomCode(),
            'appId' => $appId,
            'userId' => $userId,
            'scope' => $scope,
            'created' => $created,
            'expired' => $expired
        ]);

        $this->cacheSet($refreshToken->refresh, $refreshToken, $this->getTtl());
        $this->getRepo()->create($refreshToken);

        return $refreshToken;
    }

    public function fetch(string $refresh): ?RefreshTokenDto
    {
        if ($this->cache) {
            if ($refreshToken = $this->cache->get($refresh)) {
                return $refreshToken;
            }
        }
        return $this->getRepo()->fetch($refresh);
    }

    protected function getTtl(): \DateInterval
    {
        if ($this->ttl) {
            return $this->ttl;
        }

        $this->ttl = new \DateInterval('P1M');
        return $this->ttl;
    }

    protected function getRepo(): RefreshTokenRepoInterface
    {
        return $this->repoManager->getRefreshTokenRepo();
    }
}
