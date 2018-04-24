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
        return str_replace(
            str_split('{}()/\@:'),
            '-',
            base64_encode(random_bytes($length))
        );
    }

    protected function cacheGet(string $key, $default = null)
    {
        if (!$this->cache) {
            return null;
        }

        $this->cache->get($key, $default);
    }

    protected function cacheSet(string $key, $value, $ttl = null): bool
    {
        if (!$this->cache) {
            return false;
        }

        return $this->cache->set($key, $value, $ttl);
    }

    protected function cacheDelete(string $key): bool
    {
        if (!$this->cache) {
            return false;
        }

        return $this->cache->delete($key);
    }
}
