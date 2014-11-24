<?php

//Page analysis for www.rue89.com
class Rue89Page extends Page {
    function analyse () {
        parent::analyse();

        //Hardcoded known info
        $this->site = "Rue 89";
        $this->skipYMD = true;
        $this->issn = '1958-5837';

        //Gets date
        // http://www.rue89.com/2011/02/26/
        $yyyy = substr($this->url, 21, 4);
        $mm   = substr($this->url, 26, 2);
        $dd   = substr($this->url, 29, 2);
        $this->unixtime = mktime(0, 0, 0, $mm, $dd, $yyyy);
        $this->date = strftime(LONG_DATE_FORMAT, $this->unixtime);

	//Gets author
	//TODO: ensure no article has more than one author
        $pos1 = strpos($this->data, '<div class="authors">');
        $pos1 = strpos($this->data, 'class="author">', $pos1) + 15;
        $pos2 = strpos($this->data, '/a>', $pos1) - 1;
        $this->author = substr($this->data, $pos1, $pos2 - $pos1);
    }

    function get_title () {
	//Article title is the meta tag name, and not the page title
        return $this->meta_tags['name'];
    }
}
