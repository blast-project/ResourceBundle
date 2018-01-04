<?php

/*
 * Copyright (C) 2015-2017 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blast\Bundle\ResourceBundle\Console\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

/**
 * Description of DoctrineResolvedTargetEntitiesCommand.
 *
 * @author glenn
 */
class DoctrineResolvedTargetEntitiesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('blast:doctrine:resolved-target-entities')
            ->setDescription('List of resolved target entities');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrineEntityResolver = $this->getContainer()->get('doctrine.orm.listeners.resolve_target_entity');
        $class = new \ReflectionClass($doctrineEntityResolver);
        $property = $class->getProperty('resolveTargetEntities');
        $property->setAccessible(true);
        $mapping = $property->getValue($doctrineEntityResolver);

        $table = new Table($output);
        $table->setHeaders(['Interface or Abstract Class', 'Target Entity']);
        foreach ($mapping as $key => $value) {
            $table->addRow([$key, $value['targetEntity']]);
        }

        $table->render();
    }
}
