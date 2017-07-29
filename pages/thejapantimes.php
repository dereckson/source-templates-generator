<?php

/**
 * Represents a page from the http://www.japantimes.co.jp/ site.
 */
class TheJapanTimesPage extends Page {
    /**
     * Analyses the page and extracts metadata
     */
    function analyse () {
        parent::analyse();

        // Hardcoded known info
        $this->site = "[[The Japan Times]]";

        // Gets the date from the URL
        $this->extractYYYYMMDDDateFromURL();
	
        // Gets author
        // e.g. <h5 class="writer" role="author" >by <a href="http://www.japantimes.co.jp/author/int-eric_johnston/" title="Posts by Eric Johnston" class="author url fn" rel="author">Eric Johnston</a></h5>
        $this->author = $this->between('rel="author">', '</a>');

        // Removes pipe and website name from the title
        $this->title = $this->between("<title>", " |");
    }
}
