<?php

/**
 * Represents a page from the http://www.lesechos.fr/ site.
 */
class LesEchosPage extends Page {
    /**
     * Analyses the page and extracts metadata
     */
    function analyse () {
        parent::analyse();

        // From content
        $this->author = self::between('<span itemprop="name">', '</span>');

        // From metatags
        $this->dateFromDateParse($this->meta_tags['article:published_time']);

        // Static
        $this->site = "[[Les Échos]]";
    }
}
