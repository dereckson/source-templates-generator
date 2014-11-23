<?php

/**
 * Represents a page from the http://www.formula1.com/ site.
 */
class Formula1Page extends Page {
    /**
     * Analyses the page and extracts metadata
     */
    function analyse () {
        parent::analyse();

        $this->skipAuthor = true;
        $this->title = $this->getTitle();
        $this->yyyy = $this->getYear();
        $this->skipMD = true;
        $this->site = "Formula 1";
    }

    function getTitle () {
        $title = $this->between('<h2>', '</h2>');
        $title = str_replace('<sup>&reg;</sup>', '', $title);
        $title = preg_replace('@\s+@', ' ', $title);
        return trim($title);
    }

    function getYear () {
        $candidateSources = [
            $this->url,
            $this->title
        ];
        foreach ($candidateSources as $candidateSource) {
            if (preg_match("@\b(20[0-9][0-9])\b@", $candidateSource, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
}
