<?php

define('LONG_DATE_FORMAT', '%e %B %Y');

class Page {
    public $url;

    /**
     * @var array Meta tags
     */
    public $meta_tags;

    /**
     * @var string The page content
     */
    public $data;

    public $title;

    function __construct ($url) {
        $this->url = $url;
        $this->data = file_get_contents($url);
        $this->analyse();
    }

    static function load ($url) {
	$pages = file('pages/index.dat', true);
        foreach ($pages as $line) {
            $page = explode("\t", $line);
            if (substr($url, 0, strlen($page[0])) == $page[0]) {
                $file  = strtolower(trim($page[1])) . '.php';
                $class = trim($page[1]) . 'Page';

                require("pages/$file");
                return new $class($url);
            }
        }
        return new Page($url);
    }

    function analyse () {
        $this->meta_tags = $this->get_meta_tags();
        $this->title = $this->get_title();
    }

    function get_meta_tags () {
        return get_meta_tags($this->url);
    }

    function get_all_meta_tags () {
         //Thank you to Michael Knapp and Mariano
         //See http://php.net/manual/en/function.get-meta-tags.php comments
        preg_match_all('/<[\s]*meta[\s]*\b(name|property|itemprop)\b="?' . '([^>"]*)"?[\s]*' . 'content="?([^>"]*)"?[\s]*[\/]?[\s]*>/si', $this->data, $match);

        if (isset($match) && is_array($match) && count($match) == 4)
        {
            $originals = $match[0];
            $names = $match[2];
            $values = $match[3];
           
            if (count($originals) == count($names) && count($names) == 
count($values))
            {
                $metaTags = array();
               
                for ($i=0, $limiti=count($names); $i < $limiti; $i++)
                {
                    $metaTags[$names[$i]] = $values[$i];
                }
            }
        }

        return $metaTags;
    }

    /**
     * Gets title
     *
     * @return string The page title
     */
    function get_title () {
        return (preg_match("#<title>(.+)<\/title>#iU", $this->data, $title)) ? trim($title[1]) : '';
    }

    function is_article () {
        if (array_key_exists('dc_type', $this->meta_tags) && $this->meta_tags['dc_type'] == 'journalArticle') {
            return true;
        }
        return false;
    }
}
