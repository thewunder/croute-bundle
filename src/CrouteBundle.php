<?php

namespace Croute\CrouteBundle;

use Croute\Attributes\HttpMethodHandler;
use Croute\Attributes\SecureHandler;
use Croute\Router;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

final class CrouteBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()->children()
            ->arrayNode('namespaces')
                ->prototype('scalar')->isRequired()->cannotBeEmpty()->end()
            ->end()
            ->arrayNode('attribute_handlers')
                ->prototype('scalar')->defaultValue(['croute.http_method_handler', 'croute.secure_handler'])->end()
            ->end()
        ->end();
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->services()->set('croute.http_method_handler', HttpMethodHandler::class);
        $container->services()->set('croute.secure_handler', SecureHandler::class);

        $routerConfig = $container->services()->set('croute.router', Router::class);
        $routerConfig->factory([Router::class, 'create'])
            ->args([service('event_dispatcher'), $config['namespaces'], service('service_container')]);

        foreach ($config['attribute_handlers'] as $handler) {
            $routerConfig->call('addAttributeHandler', [service($handler)]);
        }

        $container->services()->set('croute.kernel', CrouteKernel::class)
            ->args([service('croute.router')])
            ->public();
    }
}
