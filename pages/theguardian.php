<?php

// Page analysis for www.theguardian.com
class TheGuardianPage extends Page {

    function analyse () {
        parent::analyse();

        // Hardcoded known info
        $this->site = "[[The Guardian]]";

        // Gets date from article:published_time metatag
        $this->yyyy = substr($this->meta_tags['article:published_time'],  0, 4);
        $this->mm   = substr($this->meta_tags['article:published_time'],  4, 2);
        $this->dd   = ltrim(substr($this->meta_tags['article:published_time'],  8, 2), '0');
        $this->unixtime = mktime(0, 0, 0, $mm, $dd, $yyyy);

        // Gets author from author metatag
        // TODO Handle multiple authors
        $this->author = $this->meta_tags['author'];
    }

}
