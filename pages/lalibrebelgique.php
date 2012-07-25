<?php

//Page analysis for www.lalibre.be
class LaLibreBelgiquePage extends Page {
    function analyse () {
        //La Libre uses ISO-8859-1 and not UTF-8
        $this->data = iconv('iso-8859-1', 'utf-8', $this->data);

	//Calls parent analyzer
        parent::analyse();

        //Hardcoded known info
        $this->site = "La Libre Belgique";
        $this->skipYMD = true;

        //Gets date
        $date = trim(self::between('Mis en ligne le ', '</p>'));
        $yyyy = substr($date, 6, 4);
        $mm   = substr($date, 3, 2);
        $dd   = substr($date, 0, 2);
	$this->unixtime = mktime(12, 0, 0, $mm, $dd, $yyyy);
        $this->date = strftime(LONG_DATE_FORMAT, $this->unixtime);

	//Gets authors
        $authors = trim(self::between('<p id="writer">', '</p>'));
	if (strpos($authors, 'daction ') > 0) {
            //"rédaction en ligne", "Rédaction web","Rédaction en ligne (avec afp)", etc.
            //(they're not coherent about case).
	    $this->skipAuthor = true;
	} else {
            $authors = preg_split('/( et |, )/', $authors);
            $start = true;
            foreach ($authors as $author) {
                //Fixes some authors
                switch ($author) {
                    case 'G. Dt':       $author =  'Guy Duplat'; break;
                    case 'afp':         $author =  'AFP'; break;
                }
                if ($start) {
                    $this->author = $author;
                    $start = false;
                } else {
                    $this->coauthors[] = $author;
                }
            }
	}

        //Gets title
        if (!$this->title = $this->meta_tags['og:title']) {
            $this->title = self::between("<title>Lalibre.be - ", "</title>");
        }
    }
}

?>
