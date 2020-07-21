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

    private ResultSetMapping $__rsm;

    private ArraySearchRecursiveService $__arraySearchRecursive;

    private string $_project_dir;

    public function __construct(ParameterBagInterface $parameterBagInterface, EntityManagerInterface $entityManager, string $project_dir)
    {
        $this->__parameterBag = $parameterBagInterface;
        $this->__entityManager = $entityManager;
        $this->__rsm = new ResultSetMapping();
        $this->__arraySearchRecursive = new ArraySearchRecursiveService();
        $this->_project_dir = $project_dir ;
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

        return $this->__arraySearchRecursive->search($databaseName, $databaseNames) ;
    }


    public function createDatabase(string $databaseName): bool
    {
        $statement = $this->__entityManager->getConnection()->prepare("CREATE DATABASE " . $databaseName);
        return $statement->execute(); // TRUE on success or FALSE on failure
    }

    public function hydrateDb($databaseName){
        exec( "cd $this->_project_dir/bin  && php console doctrine:schema:update -f --dump-sql --em=$databaseName", $output) ;

        $success  = strpos(json_encode( $output ) ,'[OK] Database schema updated successfully!'  ) ;

        return $success ;
    }


    public function registerDatabaseConnexion(string $databaseName)
    {

        $envVariables = DotEnvParser::envToArray($this->__parameterBag->get('env_file_path'));

        $databaseAccessUrlName = 'DATABASE_'. strtoupper($databaseName) . '_URL';

        if(!array_key_exists($databaseAccessUrlName, $envVariables))
        {

            $databaseAccessUrlName  = $databaseAccessUrlName. "=mysql://root:root@localhost:3306/" . $databaseName . "?serverVersion=5.7";

            $file_content = file_get_contents($this->__parameterBag->get('env_file_path'));

            $file_content .= PHP_EOL . $databaseAccessUrlName;

            if(!is_writable($this->__parameterBag->get('env_file_path')))
                throw new CannotWriteFileException(sprintf("Error : The '%s' file is not writable !", $this->__parameterBag->get('env_file_path')));

            file_put_contents($this->__parameterBag->get('env_file_path'), $file_content);

            $this->addConnexionInDoctrineFile($databaseName, 'DATABASE_'. strtoupper($databaseName) . '_URL');

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

        if(!array_key_exists(strtolower($databaseName), array_keys($doctrineConfig['doctrine']['orm']['entity_managers'])))
        {
            $doctrineConfig['doctrine']['orm']['entity_managers'][strtolower($databaseName)] = [
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