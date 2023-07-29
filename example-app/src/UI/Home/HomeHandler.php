<?php

declare(strict_types=1);

namespace SilenZ\App\UI\Home;

use Cycle\ORM\EntityManager;
use Cycle\ORM\ORM;
use Exception;
use SilenZ\App\Database\Entity\User;
use Polyhex\Web\Routing\JsonResponse;

final class HomeHandler
{

    public function __invoke(
        HomeRequest|Exception $request,
        // ORM $orm,
    ): JsonResponse {
        if ($request instanceof Exception) {
            return new JsonResponse(['error' => $request->getMessage()], code: 400);
        }

        // $manager = new EntityManager($orm);
        // $user = new User(id: 123, name: "John");

        // $manager->persist($user);

        // $user->name = "Paul {$request->language}";

        // $manager->run();

        // $userRepo = $orm->getRepository(User::class);
        // $user = $userRepo->findByPK(123);

        return new JsonResponse(['language' => $request->language ]);
    }
}
