<?php

/**
 * Represents a page from the http://archives.lesoir.be/ site.
 */
class ArchivesLeSoirPage extends LeSoirPage {

    /**
     * Determines if this is the archive
     * @return bool always true
     */
    function isArchive () {
        return true;
    }

}
