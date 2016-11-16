<?php

/**
 * Represents a page from the http://www.latimes.com/ site.
 */
class LosAngelesTimesPage extends Page {
    /**
     * Analyses the page and extracts metadata
     */
    function analyse () {
        parent::analyse();

        $this->site = '[[Los Angeles Times]]';
        $this->antiAdBlocker = true;

        $this->author = self::between('<span itemprop="author">', '</span');
    }
}
