<?php

/**
 * DiServer application
 *
 * @author  Stepan Zolotarev <zsl88.logging@gmail.com>
 */

declare(strict_types = 1);

namespace Demo\App\DependencyInjection;

use ServiceBus\Sagas\Module\SqlSchemaCreator;
use ServiceBus\Storage\Common\DatabaseAdapter;
use Symfony\Component\DependencyInjection;

/**
 *
 */
class SchemaCompilerPass implements DependencyInjection\Compiler\CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(DependencyInjection\ContainerBuilder $container): void
    {
        if (true === $container->hasDefinition(DatabaseAdapter::class)) {
            $container->setDefinition(
                SqlSchemaCreator::class,
                (new DependencyInjection\Definition(
                    SqlSchemaCreator::class, [
                        new DependencyInjection\Reference(DatabaseAdapter::class),
                        __DIR__ . '/../../vendor/php-service-bus/sagas'
                    ]
                ))->setPublic(true)
            );
        }
    }
}
