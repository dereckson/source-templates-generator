<?php

require 'lesoir.php';

/**
 * Represents a page from the http://archives.lesoir.be/ site.
 */
class ArchivesLeSoirPage extends LeSoirPage {
    /**
     * Analyses the page and extracts metadata
     */
    function analyse ($skipSpecificProcessing = false) {
        parent::analyse(true);

        $authors = $this->between('<p class="st_signature">', '</p>');
        $date = trim($this->between('<p class="st_date">', '</p>'));

        $this->processAuthors($authors);
        $this->processDate($date);
    }

    function get_title () {
        return $this->between('<h3 class="story_title main">', '</h3>');
    }
}
