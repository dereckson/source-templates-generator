<?php

require_once('helpers/namecase.php');

/**
 * Represents a page from the http://www.lavenir.net/ site.
 */
class LAvenirPage extends Page {
    /**
     * Analyses the page and extracts metadata
     */
    function analyse () {
        parent::analyse();

        $author = self::between('<span itemprop="author">', '</span>');
        $this->author = name_case($author);

        $this->extractDate();

        $this->site = "[[L'Avenir (Belgique)|L'Avenir]]";
    }

    function extractDate () {
        $pattern = "/dmf([12][0-9]{3})([0-9]{2})([0-9]{2})/";
        if (preg_match($pattern, $this->url, $matches)) {
            $this->yyyy = $matches[1];
            $this->mm = $matches[2];
            $this->dd = $matches[3];
        }
    }
}
