<?php
namespace Gap\Open\Server\Service;

use Gap\Dto\DateTime;
use Gap\Open\Dto\AppDto;
use Gap\Open\Server\Repo\Contract\AppRepoInterface;

class AppService extends ServiceBase
{
    protected $ttl;

    public function create(AppDto $app): void
    {
        $this->cacheSet($app->appId, $app);
        $this->getRepo()->create($app);
    }

    public function fetch(string $appId): ?AppDto
    {
        if ($app = $this->cacheGet($appId)) {
            return $app;
        }

        if ($app = $this->getRepo()->fetch($appId)) {
            $this->cacheSet($appId, $app);
            return $app;
        }

        return null;
    }

    public function disable(AppDto $app): void
    {
        $this->getRepo()->disable($app);
        $this->cacheDelete($app->appId);
    }

    protected function getRepo(): AppRepoInterface
    {
        return $this->repoManager->getAppRepo();
    }
}
