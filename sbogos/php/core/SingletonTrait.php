<?php
namespace Framework\Core;

defined('BASEPATH') OR exit('No direct script access allowed');

trait SingletonTrait {

    protected static $instance;

    final public static function getInstance() {
        return isset(static::$instance)
            ? static::$instance
            : static::$instance = new static;
    }

    final private function __construct() {
        static::init();
    }

    protected function init() {}
}