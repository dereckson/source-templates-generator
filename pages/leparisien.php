<?php

/**
 * Represents a page from the http://www.leparisien.fr/ site.
 */
class LeParisienPage extends Page {
    /**
     * Analyses the page and extracts metadata
     */
    function analyse () {
        parent::analyse();

        // Hardcoded known info
        $this->site = "[[Le Parisien]]";

        // From metadata
        $this->dateFromDateParse($this->meta_tags['article:published_time']);

        $author = $this->meta_tags['creator'];
        if ($author === "Le Parisien") {
            $this->skipAuthor = true;
        } else {
            $this->author = $author;
        }
    }
}
