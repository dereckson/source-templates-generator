<?php

class LeFigaroPage extends Page {
    function analyse () {
        parent::analyse();

        //Hardcoded known info
        $this->site = "Le Figaro";
        $this->skipYMD = true;
        $this->issn = '0182-5852';

        //Gets date
        //e.g. http://www.lefigaro.fr/actualite-france/2013/05/24/01016-20130524ARTFIG00438-frigide-barjot-ne-pense-pas-manifester-dimanche.php
	$pos = strpos($this->url, "/20") + 1;
        $yyyy = substr($this->url, $pos, 4);
        $mm   = substr($this->url, $pos + 5, 2);
        $dd   = substr($this->url, $pos + 8, 2);
        $this->unixtime = mktime(0, 0, 0, $mm, $dd, $yyyy);
        $this->date = strftime(LONG_DATE_FORMAT, $this->unixtime);

	//Gets author
	//e.g. <span itemprop="author" class="auteur txt12_120">Stéphanie Le Bars</span>
	//e.g. <a itemprop="name" href="#auteur" class="fig-anchor fig-picto-journaliste-haut">Stéphane Kovacs</a>
	//TODO: ensure no article has more than one author
	$author = self::between('itemprop="name"', '</');
	$pos = strpos($author, '">') + 2;
	$this->author = substr($author, $pos);
    }
}

?>
