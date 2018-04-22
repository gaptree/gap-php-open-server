<?php
namespace Gap\Open\Server\Repo;

use Gap\Db\Contract\CnnInterface;

abstract class RepoBase
{
    protected $cnn;

    public function __construct(CnnInterface $cnn)
    {
        $this->cnn = $cnn;
    }
}
