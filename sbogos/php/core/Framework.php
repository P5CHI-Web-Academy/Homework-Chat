<?php
namespace Framework\Core;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * An example of a project-specific implementation.
 *
 * After registering this autoload function with SPL, the following line
 * would cause the function to attempt to load the \Foo\Bar\Baz\Qux class
 * from /path/to/project/src/Baz/Qux.php:
 *
 *      new \Foo\Bar\Baz\Qux;
 *
 * @param string $class The fully-qualified class name.
 *
 * @return void
 */
function autoload_class ($class) {

    // project-specific namespace prefix
    $prefix = 'Framework\\';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = 'php\\' . substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, lowercase
    // directory path append with .php
    $parts = explode(DIRECTORY_SEPARATOR, str_replace('\\', DIRECTORY_SEPARATOR, $relative_class));
    $name = array_pop($parts) . '.php';

    foreach (array(BASEPATH) as $dir) {
        $file = $dir . strtolower(implode(DIRECTORY_SEPARATOR, $parts)) . DIRECTORY_SEPARATOR . $name;

        // if the file exists, require it
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
};

spl_autoload_register(__NAMESPACE__  . '\autoload_class');

/** Composer autoloader */
if (file_exists($_autoload_path = BASEPATH . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php')) {
    require_once($_autoload_path);
} else {
    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo 'System Composer autoload path ' . $_autoload_path . ' was not found.';
    exit(1); // EXIT_ERROR
}

function runController($controller, $method = null, $params = null) {
    if (class_exists($controller)) {
        $controller = $controller::getInstance();
        $method = $method ?? 'indexAction';
        if (method_exists($controller, $method)) {
            if (!empty($params) AND is_array($params)) {
                return call_user_func_array(array(&$controller, $method), $params);
            }
            else {
                return call_user_func(array(&$controller, $method));
            }
        }
        else
            show404();
    }
    else
        show404();
}

function show404($message = 'Not Found')
{
    header('HTTP/1.0 404 Not Found', true, 404);
    exit($message);
}

function showError($message = 'Framework Error')
{
    header('HTTP/1.0 500 Bad Request', true, 500);
    exit($message);
}

require_once BASEPATH . 'php/core/Routing.php';