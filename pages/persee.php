<?php

//Page analysis for www.persee.org
class PerseePage extends Page {
    /**
     * Initializes a new JSTORPage instance. If an error occured, you can read it in $this->error.
     *
     * @param string $url the page URL
     */
    function __construct ($url) {
        $this->url = $url;
        $this->data = self::curl_download($url, USER_AGENT_FALLBACK_FULL);
        $this->analyse();
    }

    function analyse () {
        parent::analyse();
        $this->publisher = 'Persée';
    }

    function get_all_meta_tags () {
        $metaTags = parent::get_all_meta_tags();

        //Round 2, as persee.fr uses <meta content="..." name="...">
        preg_match_all('/<[\s]*meta[\s]*\bcontent\b="?' . '([^>"]*)"?[\s]*' . 'name="?([^>"]*)"?[\s]*[\/]?[\s]*>/si', $this->data, $match);
        if (isset($match) && is_array($match) && count($match) == 3) {
            $originals = $match[0];
            $names = $match[2];
            $values = $match[1];

            if (count($originals) == count($names) && count($names) == count($values)) {
                for ($i=0, $limiti = count($names) ; $i < $limiti ; $i++) {
                    $metaTags[$names[$i]] = $values[$i];
                }
            }
        }

        return $metaTags;
    }

    function is_article () {
        return true;
    }
}
