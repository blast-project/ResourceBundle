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
use Blast\Component\Resource\Metadata\MetadataRegistryInterface;
use Blast\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Description of ListDoctrineTablesCommand.
 *
 * @author glenn
 */
class ResourceInfoCommand extends ContainerAwareCommand
{
    /**
     * @var MetadataRegistryInterface
     */
    private $registry;

    /**
     * @param MetadataRegistryInterface $registry
     */
    public function __construct(MetadataRegistryInterface $registry)
    {
        parent::__construct();
        $this->metadataRegistry = $registry;
    }

    protected function configure()
    {
        $this
            ->setName('blast:resource:info')
            ->setDescription('List Blast resources');

        $this->addArgument('resource', InputArgument::OPTIONAL, 'Resource to debug');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $resourceAlias = $input->getArgument('resource');
        if (null === $resourceAlias) {
            $this->listResources($output);

            return;
        }
        $metadata = $this->getMetadataRegistry()->get($resourceAlias);
        $this->debugResource($metadata, $output);
    }

    /**
     * @param OutputInterface $output
     */
    private function listResources(OutputInterface $output)
    {
        $resources = $this->getMetadataRegistry()->getAll();
        ksort($resources);
        $table = new Table($output);
        $table->setHeaders(['Prefix', 'Alias']);
        foreach ($resources as $resource) {
            $table->addRow([$resource->getPrefix(), $resource->getAlias()]);
        }
        $table->render();
    }

    /**
     * @param MetadataInterface $metadataMetadataInterface
     * @param OutputInterface   $output
     */
    private function debugResource(MetadataInterface $metadata, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['Alias', $metadata->getAlias()]);
        $information = [
            'prefix'         => $metadata->getPrefix(),
            'model'          => $metadata->getClassMap()->getModel(),
            'repository'     => $metadata->getClassMap()->getRepository(),
            'interfaces'     => implode("\n", $metadata->getClassMap()->getInterfaces()),
        ];

        foreach ($information as $key => $value) {
            $table->addRow([$key, $value]);
        }
        $table->render();
    }

    /**
     * @param array  $parameters
     * @param array  $flattened
     * @param string $prefix
     *
     * @return array
     */
    private function flattenParameters(array $parameters, array $flattened = [], $prefix = '')
    {
        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                $flattened = $this->flattenParameters($value, $flattened, $prefix . $key . '.');
                continue;
            }
            $flattened[$prefix . $key] = $value;
        }

        return $flattened;
    }

    protected function getMetadataRegistry()
    {
        return $this->metadataRegistry;
    }
}
