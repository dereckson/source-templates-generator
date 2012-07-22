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
        $this->date = strftime(LONG_DATE_FORMAT, mktime(0, 0, 0, $mm, $dd, $yyyy));

	//Gets author
	//TODO: ensure no article has more than one author
        $pos1 = strpos($this->data, '<p class="info st_signature">') + 29;
        $pos2 = strpos($this->data, '</p>', $pos1);
        $author = substr($this->data, $pos1, $pos2 - $pos1);
	if ($author == "R&#233;daction en ligne") {
		$this->skipAuthor = true;
	} else {
		require_once('helpers/namecase.php');
		$this->author =  name_case($author);
	}
    }

    function get_title () {
        return $this->meta_tags['og:title'];
    }

    function get_meta_tags () {
	//Rue89 doesn't always use <meta name="" value=""> but sometimes property= or itemprop=
        return $this->get_all_meta_tags();
    }
} 

?>
