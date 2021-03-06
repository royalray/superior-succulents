<?php


namespace Neoan3\Apps;


class TemplateFunctions
{
    private static $registeredClosures = [];
    private static $registeredDelimiters = ['{{', '}}'];

    static function registerClosure($name, $function)
    {
        self::$registeredClosures[$name] = $function;
    }

    static function setDelimiter($opening, $closing)
    {
        self::$registeredDelimiters = [$opening, $closing];
    }

    static function getDelimiters()
    {
        return self::$registeredDelimiters;
    }

    static function tryClosures($substitutions, $content, $executePure = true)
    {
        foreach (self::$registeredClosures as $name => $closure) {
            $content = self::executeClosure($content, $name, $closure, $substitutions, $executePure);
        }
        return $content;
    }

    static function executeClosure($content, $callBackName, $closure, $valueArray, $pure = true)
    {
        $pattern = self::retrieveClosurePattern($pure, $callBackName);
        $replacement = preg_replace_callback(
            $pattern,
            function ($hit) use ($closure, $valueArray) {
                $params = explode(',', $hit[1]);
                $finalsInputs = [];
                $found = true;
                foreach ($params as $param){
                    if(!isset($valueArray[trim($param)])){
                        $found = false;
                    } else {
                        $finalsInputs[] = $valueArray[trim($param)];
                    }
                }
                if ($found) {
                    return $closure(...$finalsInputs);
                }
                return $hit[0];
            },
            $content
        );
        return $replacement;
    }

    private static function retrieveClosurePattern($pure, $closureName)
    {
        $pattern = '/';
        if (!$pure) {
            $pattern .= preg_quote(self::$registeredDelimiters[0]) . "\s*";
        }
        $pattern .= "$closureName\(([a-z0-9,\.\s]+)\)";
        if (!$pure) {
            $pattern .= "\s*" . preg_quote(self::$registeredDelimiters[1]);
        }
        return $pattern . "/i";
    }

    private static function extractAttribute(\DOMElement $hit, $attribute)
    {
        // extract attribute
        $parts = explode(' ', $hit->getAttribute($attribute));
        // clean up
        foreach ($parts as $i => $part) {
            if (empty(trim($part))) {
                unset($parts[$i]);
            }
        }
        $parts = array_values($parts);
        // remove attribute
        $hit->removeAttribute($attribute);
        // while string
        return ['template' => Template::nodeStringify($hit), 'parts' => $parts];
    }

    static private function subContentGeneration(
        \DOMDocument $domDocument,
        \DOMElement $hit,
        array $paramArray,
        array $parts,
        string $template
    ) {
        $newContent = '';
        if (isset($paramArray[$parts[0]]) && !empty($paramArray[$parts[0]])) {
            $subArray = [];
            foreach ($paramArray[$parts[0]] as $key => $value) {
                if (isset($parts[4])) {
                    $subArray[$parts[2]] = $key;
                    $subArray[$parts[4]] = $value;
                } else {
                    $subArray[$parts[2]] = $value;
                }

                $momentary = self::tryClosures($subArray, $template, false);

                $newContent .= Template::embrace($momentary, $subArray);
            }

            Template::clone($domDocument, $hit, $newContent);
        }
        return $newContent;
    }

    /**
     * @param $content
     * @param $array
     *
     * @return string|string[]|null
     */
    static function nFor($content, $array)
    {
        $doc = new \DOMDocument();
        @$doc->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xPath = new \DOMXPath($doc);
        $hits = $xPath->query("//*[@n-for]");
        if ($hits->length < 1) {
            return $content;
        }
        foreach ($hits as $hit) {
            $extracted = self::extractAttribute($hit, 'n-for');
            self::subContentGeneration($doc, $hit, $array, $extracted['parts'], $extracted['template']);
        }
        return $doc->saveHTML();
    }

    private static function evaluateTypedCondition(array $flatArray, $expression)
    {
        $bool = true;
        foreach ($flatArray as $key => $value) {
            if (strpos($expression, $key) !== false) {
                switch (gettype($flatArray[$key])) {
                    case 'boolean':
                        $expression = str_replace($key, $flatArray[$key] ? 'true' : 'false', $expression);
                        break;
                    case 'NULL':
                        $expression = str_replace($key, 'false', $expression);
                        break;
                    case 'string':
                        $expression = str_replace($key, '"' . $flatArray[$key] . '"', $expression);
                        break;
                    case 'object':
                        $expression = self::executeClosure($expression, $key, $flatArray[$key], $flatArray);
                        break;
                    default:
                        $expression = str_replace($key, $flatArray[$key], $expression);
                        break;
                }
                $bool = eval("return $expression;");
            }
        }
        return $bool;
    }

    /**
     * @param $content
     * @param $array
     *
     * @return string
     */
    static function nIf($content, $array)
    {
        $doc = new \DOMDocument();
        @$doc->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xPath = new \DOMXPath($doc);
        $hits = $xPath->query("//*[@n-if]");
        if ($hits->length < 1) {
            return $content;
        }

        foreach ($hits as $hit) {
            $expression = $hit->getAttribute('n-if');
            $array = Template::flattenArray($array);
            $bool = self::evaluateTypedCondition($array, $expression);

            if (!$bool) {
                $hit->parentNode->removeChild($hit);
            } else {
                $hit->removeAttribute('n-if');
            }
        }
        return $doc->saveHTML();
    }
}