<?php

//Page analysis for www.lesoir.be
class LeSoirPage extends Page {
    function analyse ($skipSpecificProcessing = false) {
        parent::analyse();

        //Hardcoded known info
        $this->site = "[[Le Soir]]";

        //Allows to skip the analyis for ArchivesLeSoirPage
        if ($skipSpecificProcessing) {
            return;
        }

        //Gets metadata
        $meta = $this->between('<div class="meta">', '</div>');
        $authors = trim(self::grab($meta, '<strong>', '</strong>'));
        $date = self::grab($meta, 'class="prettydate">', ',');

        //Processes metadata
        $this->processAuthors($authors);
        if ($date) {
            $this->processDate($date);
        }
    }

    protected function processDate ($date) {
        $dateFragments = explode(' ', $date);
        if (count($dateFragments) == 4) {
            array_shift($dateFragments); //drops day name
        }
        list($this->dd, $this->mm, $this->yyyy) = $dateFragments;
    }

    protected function processAuthors ($authors) {
        if ($authors == "Rédaction en ligne") {
            $this->skipAuthor = true;
            return;
        }

        require_once('helpers/namecase.php');

        //Some Le Soir articles use firstname name, others name,firstname.
        //When there are several authors, ' ;' is the separator.
        //Authors are in uppercase, so we need to clean case.

        $authors = explode('; ', $authors);
        $start = true;

        foreach ($authors as $author) {
            if (strpos($author, ',') !== false) {
                $name = explode(',', $author, 2);
                $author = $name[1] . ' ' . $name[0];
            }
            $author = name_case($author);
            if ($start) {
                $this->author = name_case($author);
                $start = false;
            } else {
                $this->coauthors[] = name_case($author);
            }
        }
    }

    /**
     * Gets page title
     */
    function get_title () {
        if (!$title = $this->meta_tags['og:title']) {
            $title = parent::get_title();
        }
        return $title;
    }
}
