<?php

//Page analysis for www.rue89.com
class Rue89Page extends Page {
    function analyse () {
        parent::analyse();

        //Hardcoded known info
        $this->site = "[[Rue89]]";
        $this->issn = '1958-5837';

        //Gets date
        list($this->yyyy, $this->mm, $this->dd) = $this->extractDateFromURL('/');

        //Gets author
        //TODO: ensure no article has more than one author
        $pos1 = strpos($this->data, '<div class="authors">');
        $pos1 = strpos($this->data, 'class="author">', $pos1) + 15;
        $pos2 = strpos($this->data, '/a>', $pos1) - 1;
        $this->author = substr($this->data, $pos1, $pos2 - $pos1);
    }

    function extractDateFromURL ($separator) {
        $regexp = "@.*([0-9][0-9][0-9][0-9])$separator([0-9][0-9])$separator([0-9][0-9]).*@";
        preg_match($regexp, $this->url, $matches);
        array_shift($matches);
        return $matches;
    }

    function get_title () {
        //Article title is the meta tag name, and not the page title
        return $this->meta_tags['name'];
    }
}
