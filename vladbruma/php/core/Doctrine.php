<?php
namespace Framework\Core;

defined('BASEPATH') OR exit('No direct script access allowed');

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

/**
 * Doctrine Class
 *
 * Handles Database
 *
 * Run to create schema vendor/bin/doctrine orm:schema-tool:create
 * Run to update schema vendor/bin/doctrine orm:schema-tool:update --force
 */
class Doctrine {
    use SingletonTrait;

    const DB_HOST = 'chat-mysql';
    const DB_DATABASE = 'chat';
    const DB_USERNAME = 'chat';
    const DB_PASSWORD = 'chat';

    // Entity Manager
    private $entityManager;

    protected $config;

    protected function init()
    {
        if (!class_exists(EntityManager::class)) {
            show_error('Class Doctrine\ORM\EntityManager does not exist');
        }

        // database configuration parameters
        $conn = array(
            'driver' => 'pdo_mysql',
            'host' => self::DB_HOST,
            'user' => self::DB_USERNAME,
            'password' => self::DB_PASSWORD,
            'dbname' => self::DB_DATABASE,
        );

        // Create a simple "default" Doctrine ORM configuration for Annotations
        $isDevMode = ENVIRONMENT == 'development' ? true : false;
        $paths = array(
            BASEPATH . 'php/models',
        );
        $proxyDir = BASEPATH . 'cache';
        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, null, null, false);
        // or if you prefer yaml or XML
        //$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
        //$config = Setup::createYAMLMetadataConfiguration($paths, $isDevMode, $proxyDir);

        // obtaining the entity manager
        $this->entityManager = EntityManager::create($conn, $config);
    }

    public static function getEntityManager() {
        $doctrine = self::getInstance();

        return $doctrine->entityManager;
    }
}
