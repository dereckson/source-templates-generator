<?php

//Page analysis for www.chroniquesautomatiques.com
class LesChroniquesAutomatiquesPage extends Page {
    function analyse () {
        parent::analyse();

        //Hardcoded known info
        $this->site = "Les Chroniques Automatiques";
        $this->author = "Dat’";
        $this->skipYMD = true;

        //Gets date
        $old_tz = date_default_timezone_get();
        date_default_timezone_set('Europe/Paris');
	$date = date_parse(trim(self::between('This entry was posted	on', 'and is filed under')));
        $this->unixtime = mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);
        $this->date = strftime(LONG_DATE_FORMAT, $this->unixtime);
	$new_tz = date_default_timezone_set($old_tz);
    }

    function get_title () {
        $title = parent::get_title();
	$pos = strpos($title, ' &raquo;');
        return substr($title, 0, $pos);
    }
}
