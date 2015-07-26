<?php

class LeMondePage extends Page {
    function analyse () {
        parent::analyse();

        //Hardcoded known info
        $this->site = "[[Le Monde]]";
        $this->issn = '1950-6244';

        //Gets date
        // e.g. http://www.lemonde.fr/ameriques/article/2013/05/25/le-bresil-annule-la-dette-de-douze-pays-africains_3417518_3222.html
        $pos = strpos($this->url, "/article/");
        $this->yyyy = substr($this->url, $pos + 9, 4);
        $this->mm   = substr($this->url, $pos + 14, 2);
        $this->dd   = substr($this->url, $pos + 17, 2);

        $this->author = $this->getAuthor();
    }

    /**
     * Gets the author of the article
     *
     * @return string the article's author
     */
    public function getAuthor () {
        //Gets author field from HTML code
        //e.g. <span itemprop="author" class="auteur txt12_120">Stéphanie Le Bars</span>
        //TODO: ensure no article has more than one author
        $author = self::between('itemprop="author"', '</');
        $pos = strpos($author, '">') + 2;
        $author = trim(substr($author, $pos));

        if ($author[0] == '<') {
            $pos = strpos($author, '>') + 1;
            return substr($author, $pos);
        }

        return $author;
    }
}
