<?php
namespace Gap\Open\Server;

use Gap\Db\Contract\CnnInterface;
use Psr\SimpleCache\CacheInterface;

// https://www.php-fig.org/psr/psr-16/

class OpenServer
{
    protected $cnn;
    protected $cache;

    public function __construct(CnnInterface $cnn, CacheInterface $cache)
    {
        $this->cnn = $cnn;
        $this->cache = $cache;
    }
}
