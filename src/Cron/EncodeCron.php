<?php


namespace App\Cron;


use App\Service\FfmpegSchedule;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

date_default_timezone_set('Europe/Paris');

class EncodeCron extends Command
{

    use LockableTrait;

    // the name of the command (the part after "bin/console")
    // @see Symfony\Component\Console\Command\Command::validateName() for valid name regex
    protected static $defaultName = 'cron:encode-file';

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameterBag;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;


    /**
     * EncodeCron constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param ParameterBagInterface $parameterBag
     * @param LoggerInterface $cronLogger @see : https://symfony.com/doc/current/logging/channels_handlers.html#how-to-autowire-logger-channels
     */
    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag, LoggerInterface $cronLogger)
    {

        $this->entityManager = $entityManager;
        $this->parameterBag = $parameterBag;
        $this->logger = $cronLogger;

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

        if (!$this->lock())
        {
            //$output->writeln('<erro>The command is already running in another process.</erro>');
            $this->logger->error(sprintf("Log[%s] -- %s : Encode cron is already running in another process !", __CLASS__, date('d/m/Y - G:i:s')));

            return 0;
        }

        $this->logger->info(sprintf("Log[%s] -- %s : Encode cron start !", __CLASS__, date('d/m/Y - G:i:s')));

        $ffmpegSchedule = new FfmpegSchedule($this->entityManager, $this->parameterBag, $this->logger);

        $output->writeln($ffmpegSchedule->runTasks());



        // outputs multiple lines to the console (adding "\n" at the end of each line)
        /*$output->writeln([
            'User Creator',
            '============',
            '',
        ]);*/

        // the value returned by someMethod() can be an iterator (https://secure.php.net/iterator)
        // that generates and returns the messages with the 'yield' PHP keyword
        // $output->writeln($this->someMethod());

        // outputs a message followed by a "\n"
        // $output->writeln('Whoa!');

        // outputs a message without adding a "\n" at the end of the line
        // $output->writeln('');

        $this->release();

        $this->logger->info(sprintf("Log[%s] -- %s : Encode Cron end !", __CLASS__, date('d/m/Y - G:i:s')));

        return 0;
    }

}