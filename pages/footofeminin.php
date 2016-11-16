<?php

/**
 * Represents a page from the http://www.footofeminin.fr/ site.
 */
class FootOFemininPage extends Page {
    /**
     * Analyses the page and extracts metadata
     */
    function analyse () {
        parent::analyse();

	// Hardcoded known info
        $this->site = "Footoféminin";	

        $this->parseDate();
    }

    /**
     * @return string The date published in the article
     */
    function extractDate() {
        // Date is stored in HTML code as:
        // <div id="date" class="date">
        //     <div class="access">Mardi 15 Nomvebre 2016</div>
        // </div>
        $dateId = self::between('<div id="date" class="date">', '</div>');
        return self::grab($dateId, '<div class="access">');
    }

    /**
     * Parses a date and fill dd, mm, yyyy properties.
     */
    function parseDate() {
        $date = explode(' ', $this->extractDate());

        $this->dd = $date[1];
        $this->mm = strtolower($date[2]);
        $this->yyyy = $date[3];
    }
}
