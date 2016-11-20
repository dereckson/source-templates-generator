<?php

/**
 * Represents a page from the http://www.animeland.fr/ site.
 */
class AnimeLandPage extends Page {
    /**
     * Analyses the page and extracts metadata
     */
    function analyse () {
        parent::analyse();

        $this->author = self::between('rel="author">', '</a>');

        $this->extractYYYYMMDDDateFromURL();

        $this->site = "[[AnimeLand]]";
    }
}
