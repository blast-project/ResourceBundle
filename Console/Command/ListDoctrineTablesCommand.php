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
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Description of ListDoctrineTablesCommand.
 *
 * @author glenn
 */
class ListDoctrineTablesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('blast:doctrine:list-tables')
            ->setDescription('List doctrine tables')
            ->addOption('names-only', null, InputOption::VALUE_NONE, 'show table names only')
            ->addArgument('preg_filter', InputArgument::OPTIONAL,
                'table name filter used by preg_match', '.*');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filter = $input->getArgument('preg_filter');
        $namesOnly = $input->hasOption('names-only');

        $command = $this->getApplication()->find('doctrine:schema:create');
        $arguments = array('--dump-sql' => true);
        $cmdInput = new ArrayInput($arguments);
        $cmdOutput = new BufferedOutput();
        $returnCode = $command->run($cmdInput, $cmdOutput);

        $content = $cmdOutput->fetch();
        preg_match_all(
            '#create\s*table\s* (\w+)\s*\((.*)\) #i', $content, $matches);
        $tableNames = $matches[1];
        $columns = $matches[2];
        $tables = [];

        for ($i = 0; $i < count($columns); $i++) {
            $cols = explode(',', $columns[$i]);

            $cols = array_map(function ($c) {
                return (explode(' ', trim($c)))[0];
            }, $cols);
            $tables[$tableNames[$i]] = $cols;
        }

        ksort($tables);

        foreach ($tables as $tableName => $cols) {
            if (!preg_match('/.*' . $filter . '.*/', $tableName)) {
                continue;
            }
            $table = new Table($output);
            $table->setHeaders([$tableName]);

            if (false === $namesOnly) {
                foreach ($cols as $colName) {
                    $table->addRow([$colName]);
                }
            }

            $table->render();
        }
    }
}
