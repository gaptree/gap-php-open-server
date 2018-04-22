<?php
namespace Gap\Open\Server\Repo;

use Gap\Open\Dto\AppDto;
use Gap\Dto\DateTime;

class AppRepo extends RepoBase implements Contract\AppRepoInterface
{
    protected $table = 'open_app';

    public function create(AppDto $app): void
    {
        if (!$app->created) {
            $app->created = new DateTime();
        }
        if (!$app->changed) {
            $app->changed = new DateTime();
        }
        $this->cnn->isb()
            ->insert($this->table)
            ->field(
                'appId',
                'appSecret',
                'appName',
                'redirectUrl',
                'scope',
                'created',
                'changed'
            )->value()
                ->addStr($app->appId)
                ->addStr($app->appSecret)
                ->addStr($app->appName)
                ->addStr($app->redirectUrl)
                ->addStr($app->scope)
                ->addDateTime($app->created)
                ->addDateTime($app->changed)
            ->end()
            ->execute();
    }

    public function fetch(string $appId): AppDto
    {
        return $this->cnn->ssb()
            ->select(
                'appId',
                'appSecret',
                'appName',
                'redirectUrl',
                'privilege',
                'scope',
                'created',
                'expired'
            )
            ->from($this->table)->end()
            ->where()
                ->expect('appId')->equal()->str($appId)
            ->end()
            ->fetch(AppDto::class);
    }
}
