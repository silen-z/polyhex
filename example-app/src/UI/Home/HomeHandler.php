<?php

declare(strict_types=1);

namespace SilenZ\App\UI\Home;

use Exception;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class HomeHandler
{

    public function __invoke(
        HomeRequest|Exception $request,
        LoggerInterface $logger,
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory,
        // ORM $orm,
    ): ResponseInterface {
        // if ($request instanceof Exception) {
        //     return new JsonResponse(['error' => $request->getMessage()], code: 400);
        // }

        // $logger->info('handling homepage request', ['language' => $request->language]);

        return $responseFactory->createResponse(200)
            ->withHeader('Content-Type', 'text/html')
            ->withBody($streamFactory->createStreamFromFile(__DIR__. '/home.html'));

        // return new JsonResponse(['language' => $request->language ]);

        // $manager = new EntityManager($orm);
        // $user = new User(id: 123, name: "John");

        // $manager->persist($user);

        // $user->name = "Paul {$request->language}";

        // $manager->run();

        // $userRepo = $orm->getRepository(User::class);
        // $user = $userRepo->findByPK(123);

    }
}
