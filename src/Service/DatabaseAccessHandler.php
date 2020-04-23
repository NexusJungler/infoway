<?php


namespace App\Service;


use App\Entity\Admin\Customer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\CannotWriteFileException;
use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\Query\ResultSetMapping;


class DatabaseAccessHandler
{

    private ParameterBagInterface  $__parameterBag;

    private EntityManagerInterface $__entityManager;

    private string $__dsn;

    private string $__serverVersion;

    private ArraySearchRecursiveService $__arraySearchRecursive;

    public function __construct(ParameterBagInterface $parameterBag, EntityManagerInterface $entityManager)
    {
        $this->__parameterBag = $parameterBag;
        $this->__entityManager = $entityManager;

        // if database info is registered as parameter (services.yaml)
        //$this->__dsn = $parameterBag->get('db_driver') . "://" . $parameterBag->get('db_user') . ":" . $parameterBag->get('db_password') . "@" . $parameterBag->get('db_host') . ":" . $parameterBag->get('db_port');
        //$this->__serverVersion = $parameterBag->get('db_version');

        $this->__dsn = "mysql://root:root@localhost:3306";
        $this->__serverVersion = "5.7";
        $this->__arraySearchRecursive = new ArraySearchRecursiveService();
    }


    public function customerExist(string $customerName): bool
    {
        return $this->__entityManager->getRepository(Customer::class)->findOneByName($customerName) !== null;
    }


    public function databaseExist(string $databaseName): bool
    {
        $statement = $this->__entityManager->getConnection()->prepare("SHOW DATABASES");
        $statement->execute();
        $databaseNames = $statement->fetchAll();

        return $this->__arraySearchRecursive->search($databaseName, $databaseNames) !== false;
    }


    public function createDatabase(string $databaseName): bool
    {
        $statement = $this->__entityManager->getConnection()->prepare("CREATE DATABASE " . $databaseName);
        return $statement->execute(); // TRUE on success or FALSE on failure
    }


    public function registerDatabaseConnexion(string $databaseName)
    {

        $envVariables = DotEnvParser::envToArray($this->__parameterBag->get('env_file_path'));
        $databaseAccessUrlName = 'DATABASE_'. strtoupper($databaseName) . '_URL';

        if(!array_key_exists($databaseAccessUrlName, $envVariables))
        {

            // check if the first character is number
            // if yes, change it, because of .env don't accept that in first position of variable name
            if(is_numeric($databaseName[0]) !== false)
            {

                if(!$this->replaceNumberByCardinal($databaseName[0]))
                    throw new \Exception(sprintf("Error : Attempt to get cardinal value of '%s' but its cardinal value cannot be found !", $databaseName[0]));

                $temp = $this->replaceNumberByCardinal(intval($databaseName[0])) . substr($databaseName, 1);

                $url = strtoupper($temp . '_DATABASE_URL') . "=" . $this->__dsn . "/" . $databaseName . "?serverVersion=" . $this->__serverVersion;
            }

            else
                $url = strtoupper($databaseName . '_DATABASE_URL') . "=" . $this->__dsn . "/" . $databaseName . "?serverVersion=" . $this->__serverVersion;

            $file_content = file_get_contents($this->__parameterBag->get('env_file_path'));

            $file_content .= PHP_EOL . $url;

            if(!is_writable($this->__parameterBag->get('env_file_path')))
                throw new CannotWriteFileException(sprintf("Error : The '%s' file is not writable !", $this->__parameterBag->get('env_file_path')));

            file_put_contents($this->__parameterBag->get('env_file_path'), $file_content);

            $this->addConnexionInDoctrineFile($databaseName, $databaseAccessUrlName);

        }

        return true;

    }


    private function addConnexionInDoctrineFile(string $databaseName, string $connectionUrlName)
    {

        if(!is_readable($this->__parameterBag->get('doctrine_file_path')))
            throw new \Exception(sprintf("Internal Error : '%s' file is not readable !", $this->__parameterBag->get('doctrine_file_path')));

        $doctrineConfig = Yaml::parseFile($this->__parameterBag->get('doctrine_file_path'));

        if(!array_key_exists($databaseName, array_keys($doctrineConfig['doctrine']['dbal']['connections'])))
        {
            $doctrineConfig['doctrine']['dbal']['connections'][$databaseName] = [
                'url' => '%env(resolve:' . $connectionUrlName . ')%',
                'driver' => 'pdo_mysql',
                'server_version' => '5.7',
                'charset' => 'utf8mb4'
            ];
        }

        if(!array_key_exists(ucfirst($databaseName), array_keys($doctrineConfig['doctrine']['orm']['entity_managers'])))
        {
            $doctrineConfig['doctrine']['orm']['entity_managers'][ucfirst($databaseName)] = [
                'connection' => $databaseName,
                'mappings' => [
                    $databaseName => [
                        "is_bundle" => false,
                        "type" => "annotation",
                        "dir" => "%kernel.project_dir%/src/Entity/Customer",
                        "prefix" => "App\Entity\Customer",
                        "alias" => ucfirst($databaseName)
                    ]
                ]
            ];
        }

        if(!is_writable($this->__parameterBag->get('doctrine_file_path')))
            throw new CannotWriteFileException(sprintf("Internal Error : '%s' file is not writable !", $this->__parameterBag->get('doctrine_file_path')));

        file_put_contents($this->__parameterBag->get('doctrine_file_path'), Yaml::dump($doctrineConfig,10));

        return true;

    }


    /**
     * @param int $number
     * @return bool|string
     */
    private function replaceNumberByCardinal($number)
    {

        $number_to_cardinal = [
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten'
        ];

        if(array_key_exists($number, $number_to_cardinal))
            return $number_to_cardinal[$number];

        return false;

    }
    
    
}