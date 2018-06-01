<?php
namespace Framework\Core;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Application Controller Class
 */
class Controller {
    use SingletonTrait;

    /**
     * @var TemplateParser
     */
    protected $templateParser;

    /**
     * Initialize helper classes
     */
    protected function init()
    {
        session_start();
        $this->templateParser = new TemplateParser();
    }

    /**
     * @var string Path to templates directory
     */
    protected $templatePath = BASEPATH . 'templates/';

    /**
     * Basic template renderer
     *
     * @param string $fileName
     * @param array $vars
     * @throws \Exception
     */
    protected function renderTemplate(string $fileName, array $vars = []): void
    {
        $content = \file_get_contents($this->templatePath . $fileName);
        $viewVars = array_merge([
            'hasError' => false,
            'error' => ''
        ], $vars);

        $parsedContent = $this->templateParser->parse($content, $viewVars);

        echo($parsedContent);
    }
}
