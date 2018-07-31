<?php

namespace Seftomsk\BracketsApp\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Seftomsk\Brackets\Data;
use Seftomsk\Brackets\DataValidate;
use Seftomsk\Brackets\Research;

/**
 * Class BracketsCommand
 * @package Seftomsk\BracketsApp
 */
class BracketsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('check-brackets')
            ->setDescription('Check brackets.')
            ->setHelp('This command allows you to check the input file.')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to file');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        try {
            $path = $input->getArgument('path');
            $file = new \SplFileObject($path);
            if ($file->getSize() <= 0) {
                $output->writeln('<error>File is empty!</error>');
                return;
            }
            $content = $file->fread($file->getSize());
            $data = new Data($content, ['(', ')']);
            $dataValidate = new DataValidate($data);
            $dataValidate->validate();
            $research = new Research($data);
            $research->setOpenBracket('(');
            $research->setCloseBracket(')');
            $result = $research->isValid();
            if ($result === true) {
                $output->writeln('<info>WoW! Valid!</info>');
            } else {
                $output->writeln('<error>Invalid!</error>');
            }
        } catch (\RuntimeException $e) {
            $output->writeln('<error>File not found</error>');
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }
}
