<?php

//Page analysis for www.tandfonline.com
class TaylorAndFrancisPage extends Page {
    /**
     * Initializes a new TaylorAndFrancisPage instance. If an error occured, you can read it in $this->error.
     *
     * @param string $url the page URL
     */
    function __construct ($url) {
        $this->url = $url;
        $this->data = self::curl_download($url);
        $this->analyse();
    }

    function analyse () {
        parent::analyse();
        $this->publisher = 'Taylor & Francis';

        //DOI
        $this->doi = self::between('meta name="dc.Identifier" scheme="doi" content="', '"');

        //Gets the right dc.Identifier (coden scheme)
        //Expected format: <Issue name>, Vol. <Issue volume>, No. <Issue number>, <Issue date>, pp. <article pages>
        //e.g. Annals of Science, Vol. 68, No. 3, July 2011, pp. 325–350
        $identifier = self::between('meta name="dc.Identifier" scheme="coden" content="', '"');
        $identifier_data = explode(', ', $identifier);

        $pos = strpos($identifier, ", Vol. ");
        $this->journal = substr($identifier, 0, $pos);

        $this->volume = self::grab($identifier, "Vol. ", ",");
        $this->issue  = self::grab($identifier, "No. ", ",");

        $date = explode(' ', $identifier_data[3]);
        $this->yyyy = array_pop($date);

        $pos = strpos($identifier, "pp. ");
        $this->pages = substr($identifier, $pos + 4);

        //Author
        //TODO: handle several authors
        $author = trim(self::getMetaTag($this->meta_tags, 'dc.Creator'));
        $names = explode('   ', $author);
        if (count($names) == 2) {
            $this->author = "$names[1], $names[0]";
        } else {
            $this->author = $author;
        }
    }

    function is_article () {
        return true;
    }
}
