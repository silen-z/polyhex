<?php

namespace Silenz\App\Plugin\Example;

use Cycle\ORM\EntityManager;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\ORM;
use Cycle\ORM\RepositoryInterface;
use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SilenZ\App\Database\Entity\User;

final class PluginHandler
{

    private RepositoryInterface $user_Repo;

    public function __construct(ORM $orm)
    {
        $this->user_Repo= $orm->getRepository(User::class);
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $r = new Response();

        /** @var User $first_user */
        $first_user = $this->user_Repo->findOne();

        $r->getBody()->write("getting first user from plugin: {$first_user->name}");

        return $r;
    }

}