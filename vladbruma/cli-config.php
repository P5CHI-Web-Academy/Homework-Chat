<?php
namespace Framework;

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Framework\Core\Doctrine;

define('NO_CONTROLLER_RUN', true);

require_once 'public/index.php';

$em = Doctrine::getEntityManager();
return ConsoleRunner::createHelperSet($em);