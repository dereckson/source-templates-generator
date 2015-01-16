<?php
class template {
    /**
     * @var Array the template parameters
     */
    var $params;

    /**
     * @var string the template name
     */
    var $name;

    /**
     * Gets the wikicode string representation of the template
     *
     * @return string the template wikicode
     */
    function __toString () {
        $template  = '{{' . $this->name . "\n";
        foreach ($this->params as $key => $value) {
            $template .= " | $key = $value\n";
        }
        $template .= '}}';

        return $template;
    }
}
