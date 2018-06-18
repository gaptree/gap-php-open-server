<?php
namespace Gap\Open\Server\Service;

use Gap\Dto\DateTime;
use Gap\Open\Dto\AccessTokenDto;
use Gap\Open\Server\Repo\Contract\AccessTokenRepoInterface;

class AccessTokenService extends ServiceBase
{
    private $ttl;

    public function create(array $opts): AccessTokenDto
    {
        $created = new DateTime();
        $expired = (new DateTime())->add($this->getTtl());

        $appId = $opts['appId'] ?? '';
        $userId = $opts['userId'] ?? '';
        $scope = $opts['scope'] ?? '';
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

    public function extractToken(string $query): string
    {
        if (0 === strpos($query, 'Bearer ')) {
            return substr($query, 7);
        }

        return '';
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

    private function getTtl(): \DateInterval
    {
        if ($this->ttl) {
            return $this->ttl;
        }

        $this->ttl = new \DateInterval('PT1H');
        return $this->ttl;
    }

    private function getRepo(): AccessTokenRepoInterface
    {
        return $this->repoManager->getAccessTokenRepo();
    }
}
