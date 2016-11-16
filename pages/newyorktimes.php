<?php

//Page analysis for www.nytimes.com
class NewYorkTimesPage extends Page {

    use DownloadWithWget;

    function analyse () {
        parent::analyse();

        //Hardcoded known info
        $this->site = "[[The New York Times]]";

        //Gets date from pdate metatag
        $this->yyyy = substr($this->meta_tags['pdate'],  0, 4);
        $this->mm   = substr($this->meta_tags['pdate'],  4, 2);
        $this->dd   = ltrim(substr($this->meta_tags['pdate'],  6, 2), '0');
        $this->unixtime = mktime(0, 0, 0, $mm, $dd, $yyyy);

        //Gets author
        //TODO: Handle the several authors case
        require('helpers/namecase.php');
        $author = substr($this->meta_tags['byl'], 3);
        $this->author = name_case($author);
    }
}
