<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Blast\Bundle\ResourceBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Description of ListDoctrineTablesCommand
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
                ->addArgument('preg_filter', InputArgument::OPTIONAL,
                        'table name filter used by preg_match', '.*');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filter = $input->getArgument('preg_filter');
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

        for ( $i = 0; $i < count($columns); $i++ ) {
            $cols = explode(',', $columns[$i]);

            $cols = array_map(function($c) {
                return (explode(' ', trim($c)))[0];
            }, $cols);
            $tables[$tableNames[$i]] = $cols;
        }



        asort($tables);



        foreach ( $tables as $tableName => $cols ) {
            if ( !preg_match('/.*' . $filter . '.*/', $tableName) ) {
                continue;
            }
            $table = new Table($output);
            $table->setHeaders([$tableName]);

            foreach ( $cols as $colName ) {
                $table->addRow([$colName]);
            }
            $table->render();
        }
    }

}
