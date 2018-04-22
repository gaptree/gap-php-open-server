<?php
namespace Gap\Open\Server\Service;

use Psr\SimpleCache\CacheInterface;
use Gap\Open\Server\RepoManager;

abstract class ServiceBase
{
    protected $repoManager;
    protected $cache;

    public function __construct(RepoManager $repoManager, ?CacheInterface $cache = null)
    {
        $this->repoManager = $repoManager;
        $this->cache = $cache;
    }

    protected function randomCode(int $length = 36): string
    {
        return base64_encode(random_bytes($length));
    }
}
