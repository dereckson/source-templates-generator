<?php

class LiberationPage extends Page {
    function analyse () {
        parent::analyse();

        //Hardcoded known info
        $this->site = "[[Libération (journal)|Libération]]";

        //Gets date
        // e.g. http://www.liberation.fr/france/2016/06/02/affiches...
        $pos = strpos($this->url, "/20");
        $this->yyyy = substr($this->url, $pos + 1, 4);
        $this->mm   = substr($this->url, $pos + 6, 2);
        $this->dd   = substr($this->url, $pos + 9, 2);
    }
}
