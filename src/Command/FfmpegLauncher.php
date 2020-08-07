<?php


namespace App\Command;


use App\Service\FfmpegSchedule;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

date_default_timezone_set('Europe/Paris');

/**
 * Class FfmpegLauncher
 * @package App\Command
 *
 * This file will be execute by CRON (script .bat) which will call symfony console command (see property $defaultName for command name)
 *
 */
class FfmpegLauncher extends Command
{

    // the name of the command (the part after "bin/console")
    // @see Symfony\Component\Console\Command\Command::validateName() for valid name regex
    protected static $defaultName = 'cron:encode-file';

    /**
     * @var FfmpegSchedule
     */
    private FfmpegSchedule $__ffmpegSchedule;

    /**
     *
     * @param FfmpegSchedule $ffmpegSchedule
     */
    public function __construct(FfmpegSchedule $ffmpegSchedule)
    {

        $this->__ffmpegSchedule = $ffmpegSchedule;

        parent::__construct();
    }

    protected function configure()
    {

        $this
            // command name
            // which will be used for execute script (php bin/console COMMAND_NAME)
            // use this setter if you don't specify COMMAND_NAME in parent constructor
            //->setName('cron:encode-file')

        // the short description shown while running "php bin/console list"
            ->setDescription("Get the next file encoding request in queue and run it with Ffmpeg")

        // the full command description shown when running the command with
        // the "--help" option
            ->setHelp("This command allows you to get the next file encoding request in queue (stored in db) and run it with Ffmpeg")

            ->setHidden(false)

        ;

    }

    public function execute (InputInterface $input, OutputInterface $output)
    {

        $this->__ffmpegSchedule->runTasks();

        return 0;
    }

}