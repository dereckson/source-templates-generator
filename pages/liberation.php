<?php

class LiberationPage extends Page {
    function analyse () {
        parent::analyse();

        // Hardcoded known info
        $this->site = "[[Libération (journal)|Libération]]";
        $this->paywall = true;

        // Gets date
        // e.g. http://www.liberation.fr/france/2016/06/02/affiches...
        $this->extractYYYYMMDDDateFromURL();

        // Extracts author
        $author = self::between('<span class="author">', '</span>');
        $this->author = self::grab($author, '<span>'); // cleans URL
        if ($this->author === false) {
            $this->author = self::grab($author, '">', '</a>'); // link
        }
        $this->author = trim($this->author);
    }
}
