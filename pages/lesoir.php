<?php

//Page analysis for www.lesoir.be
class LeSoirPage extends Page {
    function analyse () {
        parent::analyse();

        //Hardcoded known info
        $this->site = "Le Soir";
        $this->skipYMD = true;

        //Gets date
        //meta tag 'archi_id' has t-YYYYMMDD-HHMMhh as format (where hh = AM/PM)
	//                   e.g. t-20120722-0211PM
        $yyyy = substr($this->meta_tags['archi_id'],  2, 4);
        $mm   = substr($this->meta_tags['archi_id'],  6, 2);
        $dd   = substr($this->meta_tags['archi_id'],  8, 2);
        $this->unixtime = mktime(0, 0, 0, $mm, $dd, $yyyy);
        $this->date = strftime(LONG_DATE_FORMAT, $this->unixtime);

	//Gets author
	$authors = self::between('st_signature">', '</p>');

	if ($authors == "R&#233;daction en ligne") {
	    $this->skipAuthor = true;
	} else {
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
    }

    function get_title () {
        if (!$title = $this->meta_tags['og:title']) {
            $title = parent::get_title();
	}
        return $title;
    }
}

?>
