<?php
namespace Gap\Open\Server\Grant;

use Gap\Open\Dto\AccessTokenDto;
use Gap\Open\Server\Service\AppService;

class ClientCdGrant extends GrantBase
{
    protected $appService;

    public function accessToken(string $appId, string $appSecret): ?AccessTokenDto
    {
        $app = $this->getAppService()->fetch($appId);
        if (!$app) {
            throw new \Exception('Cannot find app');
        }

        if ($app->appSecret !== $appSecret) {
            throw new \Exception('app secret not match');
        }

        $accessTokenService = $this->getAccessTokenService();
        $accessToken = $accessTokenService->generate($appId);
        $accessTokenService->create($accessToken);
        return $accessToken;
    }

    protected function getAppService(): AppService
    {
        if ($this->appService) {
            return $this->appService;
        }

        $this->appService = new AppService($this->repoManager, $this->cache);
        return $this->appService;
    }
}
