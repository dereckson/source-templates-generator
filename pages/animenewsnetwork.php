<?php

/**
 * Represents a page from the http://www.animenewsnetwork.com/ site.
 */
class AnimeNewsNetworkPage extends Page {
    /**
     * Analyses the page and extracts metadata
     */
    function analyse () {
        parent::analyse();

        $this->skipAuthor = true;
        $this->extractYYYYMMDDDateFromURL();

        $this->site = "[[Anime News Network]]";
    }
}
