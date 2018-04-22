<?php
namespace Gap\Open\Server\Repo;

use Gap\Open\Dto\AuthCodeDto;

class AuthCodeRepo extends RepoBase implements Contract\AuthCodeRepoInterface
{
    protected $table = 'open_auth_code';

    public function create(AuthCodeDto $authCode): void
    {
        $this->cnn->isb()
            ->insert($this->table)
            ->field(
                'code',
                'appId',
                'userId',
                'redirectUrl',
                'scope',
                'created',
                'expired'
            )->value()
                ->addStr($authCode->code)
                ->addStr($authCode->appId)
                ->addStr($authCode->userId)
                ->addStr($authCode->redirectUrl)
                ->addStr($authCode->scope)
                ->addDateTime($authCode->created)
                ->addDateTime($authCode->expired)
            ->end()
            ->execute();
    }

    public function fetch(string $code): ?AuthCodeDto
    {
        return $this->cnn->ssb()
            ->select(
                'code',
                'appId',
                'userId',
                'redirectUrl',
                'scope',
                'created',
                'expired'
            )
            ->from($this->table)->end()
            ->where()
                ->expect('code')->equal()->str($code)
                ->andExpect('status')->equal()->str('ok')
            ->end()
            ->fetch(AuthCodeDto::class);
    }

    public function destroy(AuthCodeDto $authCode): void
    {
        $this->cnn->usb()
            ->update($this->table)->end()
                ->set('status')->str('destroyed')
            ->where()
                ->expect('code')->equal()->str($authCode->code)
            ->end()
            ->execute();
    }
}
