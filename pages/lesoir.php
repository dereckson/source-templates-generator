<?php

//Page analysis for www.lesoir.be
class LeSoirPage extends Page {

    use DownloadWithWget;

    /**
     * Determines if the article belongs to thearchives
     * @return bool
     */
    function isArchive () {
        return strpos($this->url, "//www.lesoir.be/archives") !== false;
    }

    function analyse () {
        parent::analyse();

        $this->site = "[[Le Soir]]";

        if ($this->isArchive()) {
            $this->analyseForArchive();
        } else {
            $this->analyseForMainSite();
        }
    }

    function analyseForMainSite () {
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

    function analyseForArchive () {
        $authors = $this->between('st_signature">', '</p>');
        $this->processAuthors($authors);

        if ($date = trim($this->between('<p class="st_date">', '</p>'))) {
            $this->processDate($date);
        } else {
            $this->extractYYYYMMDDDateFromURL();
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
     * Gets page title for archives sites
     *
     * @return string
     */
    function getTitleForArchive () {
        $title = $this->between('<h3 class="story_title main">', '</h3>');

        if ($title === false) {
            $title = $this->between('<h1>', '</h1>');
        }

        return $title;
    }

    /**
     * Gets page title
     */
    function get_title () {
        if ($this->isArchive()) {
            return $this->getTitleForArchive();
        }

        if (!$title = $this->meta_tags['og:title']) {
            $title = parent::get_title();
        }

        return $title;
    }
}
