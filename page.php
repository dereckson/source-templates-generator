<?php

define('LONG_DATE_FORMAT', '%e %B %Y');
define('USER_AGENT', 'WikimediaTools/SourceTemplatesGenerator/0.1');
define('USER_AGENT_FALLBACK', 'Mozilla/5.0');

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

    /**
     * @var string The page title
     */
    public $title;

    /**
     * @var string The page author
     */
    public $author;

    /**
     * @var string The site ISSN
     */
    public $issn;

    //If we use the parameters yyyy mm dd, we describe CONTENT date:

    /**
     * @var int The page content's year
     */

    public $yyyy;
    /**
     * @var int The page content's month
     */
    public $mm;

    /**
     * @var int The page content's day
     */
    public $dd;

    //If not, we describe ONLINE RESOURCE PUBLISH date:

    /**
     * @var string The page publication date in relevant locale
     */
    public $date;

    /**
     * @var int The page publication unixtime
     */
    public $unixtime;

    /**
     * @var bool Indicates if we have to skip year/month/date template parameters
     */
    public $skipYMD;


    /**
     * @var bool Indicates if we have to skip author template parameter
     */
    public $skipAuthor;


    /**
     * @var string The last error occured while opening and parsing the page
     */
    public $error;

    /**
     * Initializes a new Page instance. If an error occured, you can read it in $this->error.
     *
     * @param string $url the page URL
     */
    function __construct ($url) {
        $this->url = $url;
	ini_set('user_agent', USER_AGENT);
        $this->data = @file_get_contents($url);
        if (!$this->data) {
            ini_set('user_agent', USER_AGENT_FALLBACK);
            if (!$this->data = @file_get_contents($url)) {
                $this->error = "Can't read URL";
                return;
            }
        }
        $this->analyse();
    }

    /**
     * Return a new Page instance, or if such class exists, an instance class specialized for your site.
     *
     * @param $url the page URL
     */
    static function load ($url) {
        //Classes list are stored in pages/index.dat file
        //Each line contains the URL beginning, a tabulation, and the page analyser name
        //  * class is this name, appended by 'Page'
        //  * source file is the lowercase version of this name, appended by '.php'
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

    /**
     * Analyses metatags to process content
     */
    function analyse () {
        $this->meta_tags = $this->get_meta_tags();
        $this->title = $this->get_title();

        if (array_key_exists('date', $this->meta_tags)) {
            $date = date_parse($this->meta_tags['date']);
            $this->yyyy = $date['year'];
            $this->mm   = $date['month'];
            $this->dd   = $date['day'];
        }
    }

    /**
     * Gets page metatags
     *
     * @return array an array where the keys are the metatags' names and the values the metatags' values
     */
    function get_meta_tags () {
        return $this::get_all_meta_tags($this->url);
    }

    /**
     * Gets all metatags, including those using meta property= and meta itemprop= syntax
     *
     * @return array an array where the keys are the metatags' names and the values the metatags' values
     */
    function get_all_meta_tags () {
        //Thank you to Michael Knapp and Mariano
        //See http://php.net/manual/en/function.get-meta-tags.php comments
        preg_match_all('/<[\s]*meta[\s]*\b(name|property|itemprop)\b="?' . '([^>"]*)"?[\s]*' . 'content="?([^>"]*)"?[\s]*[\/]?[\s]*>/si', $this->data, $match);

        if (isset($match) && is_array($match) && count($match) == 4) {
            $originals = $match[0];
            $names = $match[2];
            $values = $match[3];

            if (count($originals) == count($names) && count($names) == count($values)) {
                $metaTags = array();

                for ($i=0, $limiti = count($names) ; $i < $limiti ; $i++) {
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
        if (array_key_exists('title', $this->meta_tags)) return $this->meta_tags['title'];
        return (preg_match("#<title>(.+)<\/title>#iU", $this->data, $title)) ? trim($title[1]) : '';
    }

    /**
     * Determines if the current page is an article published in a journal.
     *
     * @return bool true if the current page is an article ; otherwise, false
     */
    function is_article () {
        return
        (array_key_exists('dc_type', $this->meta_tags) && $this->meta_tags['dc_type'] == 'journalArticle')
        ||
        (array_key_exists('dcsext_pn-cat', $this->meta_tags) && $this->meta_tags['dcsext_pn-cat'] == 'Article')
        ||
        array_key_exists('citation_journal_title', $this->meta_tags)
        ||
        array_key_exists('prism_publicationname', $this->meta_tags);
    }

    /**
     * Gets relevant metatag
     *
     * @param array the metatags
     * @param string... the list of acceptable metatags
     *
     * @return string the first metatag value found
     */
    static function getMetaTag () {
        $tags = func_get_args();
        $metatags = array_shift($tags);

        foreach ($tags as $tag) {
            if (array_key_exists($tag, $metatags)) {
                return $metatags[$tag];
            }
        }

        return '';
    }

    /**
     * Finds a portion of text included between $before and $after strings on the current page
     *
     * @param string $before The string at the left  of the text to be grabbed
     * @param string $after  The string at the right of the text to be grabbed
     *
     * @return string The text found between $before and $after
     */
    function between ($before, $after) {
        return self::grab($this->data, $before, $after);
    }

    /**
     * Finds a portion of text included between $before and $after strings
     *
     * @param string $text   The text where to find the substring
     * @param string $before The string at the left  of the text to be grabbed
     * @param string $after  The string at the right of the text to be grabbed
     *
     * @return string The text found between $before and $after
     */
    static function grab ($text, $before, $after) {
        $pos1 = strpos($text, $before);
        if ($pos1 === false) { return false; } else { $pos1 += strlen($before); }

        $pos2 = strpos($text, $after, $pos1 + 1);
        if ($pos2 === false) { return false; }

        return substr($text, $pos1, $pos2 - $pos1);
    }
}
