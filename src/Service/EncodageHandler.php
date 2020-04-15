<?php


namespace App\Service;


use Symfony\Component\Yaml\Yaml;

class EncodageHandler
{

    public $fileID;
    private $errors = [];
    private $customer;
    private $customer_dir;
    private $filetype;
    private $mediatype;
    private $filename;
    private $extension;
    private $destfolder;
    private $srcfolder = 'D:/node_file_system/';
    private $repository;
    private $syncOri;

    public function __construct($customer, $filename, $mediatype)
    {

    }


    private function getCustomerDirectory($customer) {
        $conf = Yaml::parse(file_get_contents(__DIR__ . '/../config/parameters.yml'));
        $data_dir = 'data_' . $customer;
        // Exceptions
        switch($customer) {
            case 'quick':
                $data_dir = 'data';
                break;
            case 'domtom':
                $data_dir = 'data_dt';
            case 'generique':
                $data_dir = 'data_gen';
                break;
        }
        return $conf['sys_path']['datas'] . '/' . $data_dir . '/PLAYER INFOWAY WEB/';
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

}