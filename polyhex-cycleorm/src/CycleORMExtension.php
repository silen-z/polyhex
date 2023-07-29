<?php declare(strict_types=1);

namespace Polyhex\Integration\CycleORM;

use Cycle\Database\DatabaseManager;
use Cycle\ORM\Schema;
use Polyhex\Application\Builder;
use Polyhex\Application\Extension;
use Spiral\Core\FactoryInterface;
use Spiral\Tokenizer\ClassesInterface;

/**
 * @psalm-api
 */
final class CycleORMExtension implements Extension
{

    public const ENTITY_PROVIDER = 'cycle_orm.entity_provider';

    public function __construct(private readonly ClassesInterface $entity_provider)
    {

    }

    /**
     * @psalm-api
     * @param string[] $entity_paths
     */
    public static function entity_paths(array $entity_paths): self
    {
        $finder = (new \Symfony\Component\Finder\Finder())->files()->in($entity_paths); // __DIR__ here is folder with entities
        $entity_provider = new \Spiral\Tokenizer\ClassLocator($finder);

        return new self($entity_provider);

    }

    /**
     * @psalm-api
     * @param class-string[] $classes
     */
    public static function entity_classes(array $classes): self
    {
        $entity_provider = new StaticClassProvider($classes);

        return new self($entity_provider);
    }

    public function register(Builder $builder): void
    {
        $builder->with_config([
            CycleORMExtension::ENTITY_PROVIDER => \DI\value($this->entity_provider),

            Schema::class => \DI\factory(function (DatabaseManager $dbal, ClassesInterface $entity_classes) {

                // autoload annotations
//                \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');

                $schema = (new \Cycle\Schema\Compiler())->compile(new \Cycle\Schema\Registry($dbal), [
                    new \Cycle\Schema\Generator\ResetTables(),        // re-declared table schemas (remove columns)
                    new \Cycle\Annotated\Embeddings($entity_classes),        // register embeddable entities
                    new \Cycle\Annotated\Entities($entity_classes),          // register annotated entities
                    new \Cycle\Annotated\TableInheritance(),               // register STI/JTI
                    new \Cycle\Annotated\MergeColumns(),                   // add @Table column declarations
                    new \Cycle\Schema\Generator\GenerateRelations(),       // generate entity relations
                    new \Cycle\Schema\Generator\GenerateModifiers(),       // generate changes from schema modifiers
                    new \Cycle\Schema\Generator\ValidateEntities(),        // make sure all entity schemas are correct
                    new \Cycle\Schema\Generator\RenderTables(),            // declare table schemas
                    new \Cycle\Schema\Generator\RenderRelations(),         // declare relation keys and indexes
                    new \Cycle\Schema\Generator\RenderModifiers(),         // render all schema modifiers
                    new \Cycle\Annotated\MergeIndexes(),                   // add @Table column declarations
                    new \Cycle\Schema\Generator\SyncTables(),              // sync table changes to database
                    new \Cycle\Schema\Generator\GenerateTypecast(),        // typecast non string columns
                ]);

                return new \Cycle\ORM\Schema($schema);
            })->parameter('entity_classes', \DI\get(CycleORMExtension::ENTITY_PROVIDER)),

            \Cycle\ORM\ORM::class => \DI\factory(function (DatabaseManager $dbal, Schema $schema, \DI\FactoryInterface $factory) {

                $spiralFactory = new class($factory) implements FactoryInterface {

                    public function __construct(private readonly \DI\FactoryInterface $factory)
                    {
                    }

                    /**
                     * {@inheritDoc}
                     * @param class-string|string $alias
                     */
                    public function make(string $alias, array $parameters = []): mixed
                    {
                        return $this->factory->make($alias, $parameters);
                    }
                };

                return new \Cycle\ORM\ORM(
                    factory: new \Cycle\ORM\Factory($dbal, null, $spiralFactory),
                    schema: $schema,
                );
            }),
        ]);
    }
}