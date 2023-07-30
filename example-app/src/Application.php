<?php

namespace SilenZ\App;

use Polyhex\Application\BuildConfig;
use Polyhex\Application\Builder;
use Polyhex\Integration\RoadRunner\RoadRunnerApplication;
use Psr\Http\Message\ServerRequestInterface;
use Sentry\ClientBuilder;
use Sentry\SentrySdk;
use Sentry\State\HubInterface;

final class Application
{

    public static function runWeb(): void
    {
        $sentryHub = self::initSentry();

        $app = \Polyhex\Web\Application::builder()
            ->use(...self::extensions())
            ->with_config([ HubInterface::class => $sentryHub ])
            ->with_config(...self::configs(Environment::Development))
            ->build(self::buildConfig(Environment::Development, false));

        $app->run($app->requestFromGlobals());
    }

    public static function runRoadRunner(): never
    {
        $sentryHub = self::initSentry();

        $app = RoadRunnerApplication::builder()
            ->use(...self::extensions())
            ->with_config([ HubInterface::class => $sentryHub ])
            ->with_config(...self::configs(Environment::Development))
            ->build(self::buildConfig(Environment::Development, false));

        $app->run();
    }

    private static function initSentry(): HubInterface
    {
        $sentryHub = SentrySdk::init();
        $client = ClientBuilder::create([
            'dsn' => 'https://3c23bf098fbc4bf3a018c1ea118ff2a2@o4505054484692992.ingest.sentry.io/4505054485544960',
            'traces_sample_rate' => 1,
        ])->getClient();
        $sentryHub->bindClient($client);

        return $sentryHub;
    }

    private static function extensions(): array {
        return require(__DIR__. '/../config/extensions.php');
    }

    private static function configs(Environment $env): iterable
    {
        if ($env === Environment::Development) {
            yield require(__DIR__ . '/../config/environment/development.php');
        }

        if ($env === Environment::Production) {
            yield require(__DIR__ . '/../config/environment/production.php');
        }

        yield require(__DIR__ . "/../config/config.php");
    }

    public static function buildConfig(Environment $env, bool $debugMode): BuildConfig
    {
        return Builder::buildConfig()
            ->cached($env === Environment::Production ? __DIR__ . '/../temp/di' : null)
            ->addParameters([
                'env' => $env,
                'debug_mode' => $debugMode,
            ]);
    }

    private static function detectDebugMode(ServerRequestInterface $request): bool
    {
        return true;

        if (($_ENV['DEBUG'] ?? false)) {
            return true;
        }

        $debug_secret = $_ENV['DEBUG_SECRET'] ?? null;
        if ($debug_secret !== null && ($request->getCookieParams()['debug'] ?? null) === $debug_secret) {
            return true;
        }

        return false;
    }
}
