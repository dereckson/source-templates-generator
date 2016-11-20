<?php

/**
 * Represents a page from the http://www.crunchyroll.com/ site.
 */
class CrunchyRollPage extends Page {
    /**
     * Analyses the page and extracts metadata
     */
    function analyse () {
        parent::analyse();

        $authorElement = self::between('<div class="byline">', '</div>');
        $this->author = self::grab($authorElement, '">', '</a>');

        $this->extractYYYYMMDDDateFromURL();

        $this->site = "[[Crunchyroll]]";
    }
}
