<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Blast\Bundle\ResourceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use InvalidArgumentException;

/**
 * Description of RegisterResourcesPass
 *
 * @author glenn
 */
class RegisterResourcesPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {

        try {
            $resources = $container->getParameter('blast.resources');
            $registry = $container->findDefinition('blast.resource_registry');
        } catch ( InvalidArgumentException $exception ) {
            return;
        }

        foreach ( $resources as $alias => $parameters ) {
            $this->validateBlastResource($parameters['classes']['entity']);
            $registry->addMethodCall('addFromAliasAndParameters',
                    [$alias, $parameters]);
        }
    }

    /**
     * @param string $class
     */
    private function validateBlastResource(string $class)
    {
        /* if (!in_array(ResourceInterface::class, class_implements($class), true)) {
          throw new InvalidArgumentException(sprintf(
          'Class "%s" must implement "%s" to be registered as a Sylius resource.',
          $class,
          ResourceInterface::class
          ));
          } */
    }

}
