<?php
/**
 * @author  Stepan Zolotarev <zsl88.logging@gmail.com>
 */

declare(strict_types = 1);

namespace Demo\App\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection;

class ApplicationExtension extends DependencyInjection\Extension\Extension
{
    /**
     * @inheritDoc
     */
    public function load(array $configs, DependencyInjection\ContainerBuilder $container): void
    {
        $regexIterator = new \RegexIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(__DIR__ . '/../Resources/config')
            )
            , '/\.yml/i'
        );

        $loader = new DependencyInjection\Loader\YamlFileLoader($container, new FileLocator());

        foreach ($regexIterator as $yamlFile) {
            $loader->load((string)$yamlFile);
        }
    }
}
