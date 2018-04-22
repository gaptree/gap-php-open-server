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
        if ($this->cache) {
            $this->cache->set($app->appId, $app);
        }

        $this->getRepo()->create($app);
    }

    public function fetch(string $appId): ?AppDto
    {
        if ($this->cache) {
            if ($app = $this->cache->get($appId)) {
                return $app;
            }
        }
        return $this->getRepo()->fetch($appId);
    }

    protected function getRepo(): AppRepoInterface
    {
        return $this->repoManager->getAppRepo();
    }
}
