<?php

//Page analysis for www.chroniquesautomatiques.com
class LesChroniquesAutomatiquesPage extends Page {
    function analyse () {
        parent::analyse();

        //Hardcoded known info
        $this->site = "Les Chroniques Automatiques";
        $this->author = "Dat’";

        //Gets date
        $this->dateFromDateParse(trim(self::between('This entry was posted	on', 'and is filed under')));
    }

    function get_title () {
        $title = parent::get_title();
        $pos = strpos($title, ' &raquo;');
        return substr($title, 0, $pos);
    }
}
