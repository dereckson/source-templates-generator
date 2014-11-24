<?php

//Page analysis for www.nytimes.com
class NewYorkTimesPage extends Page {
    function analyse () {
        parent::analyse();

        //Hardcoded known info
        $this->site = "New York Times";
        $this->skipYMD = true;

        //Gets date from pdate metatag
        $yyyy = substr($this->meta_tags['pdate'],  0, 4);
        $mm   = substr($this->meta_tags['pdate'],  4, 2);
        $dd   = substr($this->meta_tags['pdate'],  6, 2);
        $this->unixtime = mktime(0, 0, 0, $mm, $dd, $yyyy);
        $this->date = strftime(LONG_DATE_FORMAT, $this->unixtime);

        //Gets author
        //TODO: Handle the several authors case
        require('helpers/namecase.php');
        $author = substr($this->meta_tags['byl'], 3);
        $this->author = name_case($author);
    }
}
