<?php
namespace Gap\Open\Server\Service;

use Gap\Dto\DateTime;
use Gap\Open\Dto\AuthCodeDto;
use Gap\Open\Server\Repo\Contract\AuthCodeRepoInterface;

class AuthCodeService extends ServiceBase
{
    protected $ttl;

    public function generate(
        string $appId,
        string $userId,
        string $redirectUrl = '',
        string $scope = ''
    ): AuthCodeDto {
        $created = new DateTime();
        $expired = (new DateTime())->add($this->getTtl());

        $authCode = new AuthCodeDto([
            'code' => $this->randomCode(),
            'appId' => $appId,
            'userId' => $userId,
            'redirectUrl' => $redirectUrl,
            'scope' => $scope,
            'created' => $created,
            'expired' => $expired
        ]);

        return $authCode;
    }

    public function create(AuthCodeDto $authCode): void
    {
        if ($this->cache) {
            $this->cache->set($authCode->code, $authCode, $this->getTtl());
        }

        $this->getRepo()->create($authCode);
    }

    public function destroy(AuthCodeDto $authCode): void
    {
        if ($this->cache) {
            $this->cache->delete($authCode->code);
        }
        $this->getRepo()->destroy($authCode);
    }

    public function fetch(string $code): ?AuthCodeDto
    {
        if ($this->cache) {
            if ($authCode = $this->cache->get($code)) {
                return $authCode;
            }
        }
        return $this->getRepo()->fetch($code);
    }

    protected function getTtl(): \DateInterval
    {
        if ($this->ttl) {
            return $this->ttl;
        }

        $this->ttl = new \DateInterval('PT10M');
        return $this->ttl;
    }

    protected function getRepo(): AuthCodeRepoInterface
    {
        return $this->repoManager->getAuthCodeRepo();
    }
}
