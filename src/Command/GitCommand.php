<?php
namespace AOE\Tagging\Command;

use AOE\Tagging\Vcs\Driver\GitDriver;
use AOE\Tagging\Vcs\Version;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @package AOE\Tagging\Command
 */
class GitCommand extends Command
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('git')
            ->setDescription('Tagging a GIT Repository')
            ->addArgument(
                'url',
                InputArgument::REQUIRED,
                'The URL to the repository'
            )
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'The path to the cloned repository'
            )
            ->addOption(
                'version-type',
                'vt',
                InputOption::VALUE_REQUIRED,
                'define the version type which will be used to increment (major, minor or patch)',
                Version::INCREASE_PATCH
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $git = $this->getDriver($input->getArgument('url'));
        $version = new Version();

        $latest = $git->getLatestTag();
        $next = $version->increase($latest, $input->getOption('version-type'));

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln('<info>Latest Tag number is "' . $latest . '"</info>');
            $output->writeln('<info>Next Tag number is "' . $next . '"</info>');
        }

        $git->tag($next, $input->getArgument('path'));
    }

    /**
     * @param string $url
     * @return GitDriver
     */
    protected function getDriver($url)
    {
        return new GitDriver($url);
    }
}