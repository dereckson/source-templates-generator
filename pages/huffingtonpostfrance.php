<?php

/**
 * Represents a page from the http://www.huffingtonpost.fr/ site.
 */
class HuffingtonPostFrancePage extends Page {
    /**
     * Analyses the page and extracts metadata
     */
    function analyse () {
        parent::analyse();

        $this->site = "[[Le Huffington Post]]";

        $this->author = self::between('<a class="author-card__details__name">', '</a>');
        $this->extractYYYYMMDDDateFromURL();
    }
}
