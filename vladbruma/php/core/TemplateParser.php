<?php

namespace Framework\Core;


class TemplateParser
{
    /**
     * Parse twig style template annotations
     *
     * @param string $content
     * @param array $vars
     * @return string
     * @throws \Exception
     */
    public function parse(string $content, array $vars = []): string
    {
        $content = $this->parseForCondition($content, $vars);

        return $content;
    }

    /**
     * @param string $content
     * @param array $vars
     * @return string
     * @throws \Exception
     */
    private function parseEchoCondition(string $content, array $vars = []): string
    {
        $echoVars = [];

        if (preg_match_all("/{{[\s]*([\w\[\]\']+)[\s]*}}/", $content, $echoVars) != 0) {

            foreach ($echoVars[1] as $key => $templateVar) {

                $varArrayKey = null;
                if (strpos($templateVar, '[') !== false) {
                    $templateVarArr = explode('[', $templateVar);
                    $templateVar = $templateVarArr[0];
                    $varArrayKey = str_replace(['[', ']', '\''], '', $templateVarArr[1]);
                }

                if (!isset($vars[$templateVar])) {
                    throw new \Exception('Undefined template variable: '.$templateVar);
                }

                $replaceValue = $varArrayKey ? $vars[$templateVar][$varArrayKey] : $vars[$templateVar];
                $content = str_replace($echoVars[0][$key], $replaceValue, $content);
            }
        }

        return $content;
    }

    /**
     * @param string $content
     * @param array $vars
     * @return string
     * @throws \Exception
     */
    private function parseIfCondition(string $content, array $vars = []): string
    {
        $ifVars = [];

        if (preg_match_all("/\{\%\s*if[^\%\}]*\s*\%\}(.*?)\{\%\s*endif[^\%\}]*\s*\%\}/si", $content, $ifVars) != 0) {
            foreach ($ifVars[0] as $key => $conditionBlock) {
                $variable = [];
                preg_match("/\{\%\s*if\s*([^\%\}]*)\s*\%\}/", $conditionBlock, $variable);

                $foundCondition = trim($variable[1]);
                if (strpos($foundCondition, '==') !== false) {
                    $foundCondition = str_replace('===', '==', $foundCondition);
                    $foundConditionArr = explode('==', $foundCondition);
                    $leftSide = trim($foundConditionArr[0]);
                    $rightSide = trim($foundConditionArr[1]);
                } else {
                    $leftSide = trim($foundCondition);
                    $rightSide = true;
                }

                if (strpos($leftSide, '[') !== false) {
                    $leftSideArr = explode('[', $leftSide);
                    $leftSide = [
                        'subArray' => $leftSideArr[0],
                        'subKey'   => str_replace(['[', ']', '\''], '', $leftSideArr[1])
                    ];
                }

                if ( (!is_array($leftSide) && !isset($vars[$leftSide])) ||
                    (is_array($leftSide) && !isset($vars[$leftSide['subArray']][$leftSide['subKey']])) ) {
                    throw new \Exception('Undefined template variable: ' . $variable[1]);
                }

                if ( (!is_array($leftSide) && $vars[$leftSide] == $rightSide) ||
                    (is_array($leftSide) && $vars[$leftSide['subArray']][$leftSide['subKey']] == $rightSide) ) {
                    $content = str_replace($ifVars[0][$key], $ifVars[1][$key], $content);
                } else {
                    $content = str_replace($ifVars[0][$key], '', $content);
                }
            }
        }

        return $content;
    }

    /**
     * @param string $content
     * @param array $vars
     * @return string
     * @throws \Exception
     */
    private function parseForCondition(string $content, array $vars = []): string
    {
        $forVars = [];

        if (preg_match("/\{\%\s*for[^\%\}]*\s*\%\}(.*?)\{\%\s*endfor[^\%\}]*\s*\%\}/si", $content, $forVars) != 0) {

            $arrayVars = [];
            preg_match("/\{\%\s*for\s*([^\%\s*\}]*)\sin\s([^\%\s*\}]*)\s*\%\}/", $forVars[0], $arrayVars);

            if ( empty($arrayVars[2]) || !isset($vars[$arrayVars[2]]) ) {
                throw new \Exception('Invalid condition: ' . $arrayVars[0]);
            }

            $iterativelyParsedContent = '';
            foreach($vars[$arrayVars[2]] as $item) {
                $scopeVars = array_merge($vars, [$arrayVars[1] => $item]);

                $iterativelyParsedContent .= $this->parseForCondition($forVars[1], $scopeVars);
            }
            $content = str_replace($forVars[0], $iterativelyParsedContent, $content);
        }

        $content = $this->parseIfCondition($content, $vars);
        $content = $this->parseEchoCondition($content, $vars);

        return $content;
    }
}
