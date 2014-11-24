<?php

//Page analysis for www.jsor.rog
class JSTORPage extends Page {
    /**
     * Initializes a new JSTORPage instance. If an error occured, you can read it in $this->error.
     *
     * @param string $url the page URL
     */
    function __construct ($url) {
        $this->url = $url;
        $this->data = self::curl_download($url);
        $this->analyse();
    }

    function get_title () {
        return self::between('<div class="mainCite jnlOverride"><div class="hd title">', '</div>');
    }

    function analyse () {
        parent::analyse();

        //From HTML code
        $this->author = trim(self::between('<div class="author">', '</div>'));
        $this->journal = trim(self::between('<h2>', "\n"));
        $this->issn = self::between('<div class="issn">ISSN: ', '</div>');
        $this->url = self::between('<div class="stable">Article Stable URL: ', '</div>');

        //Publisher
        $pub = self::between('<div class="pubString">Published by: ', '</div>');
        $this->publisher = $pub ? self::grab($pub, '>', '</a>') : 'JSTOR';

        //Issue information
        $srcInfo = trim(self::between('<!-- Formatting requires these tags be mashed together -->', '</div>'));

        $this->volume = self::grab($srcInfo, "Vol. ", ",");

        $this->issue  = self::grab($srcInfo, "No. ", " ");

        $this->yyyy = self::grab($srcInfo, '(', ')');

        $pos = strpos($srcInfo, "pp. ");
        $this->pages = substr($srcInfo, $pos + 4);
    }

    function is_article () {
        return true;
    }
}
