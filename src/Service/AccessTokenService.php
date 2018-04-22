<?php
namespace Gap\Open\Server\Service;

use Gap\Dto\DateTime;
use Gap\Open\Dto\AccessTokenDto;
use Gap\Open\Server\Repo\Contract\AccessTokenRepoInterface;

class AccessTokenService extends ServiceBase
{
    protected $ttl;

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

    public function fetch(string $token): AccessTokenDto
    {
        if ($this->cache) {
            if ($accessToken = $this->cache->get($token)) {
                return $accessToken;
            }
        }
        return $this->getRepo()->fetch($token);
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
