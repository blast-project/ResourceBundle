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
                ->setDescription('List doctrine tables');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getApplication()->find('doctrine:schema:create');
        $arguments = array('--dump-sql' => true);
        $cmdInput = new ArrayInput($arguments);
        $cmdOutput = new BufferedOutput();
        $returnCode = $command->run($cmdInput, $cmdOutput);

        $content = $cmdOutput->fetch();
        preg_match_all('#create\s*table\s* (\w+) #i', $content, $matches);
        $tables = $matches[1];
        asort($tables);
        foreach ($tables as $table ) {
            $output->writeln($table);
        }
    }

}
