<?php

define('LONG_DATE_FORMAT', '%e %B %Y');
define('USER_AGENT', 'WikimediaTools/SourceTemplatesGenerator/0.1');
define('USER_AGENT_FALLBACK', 'Mozilla/5.0');
define('USER_AGENT_FALLBACK_FULL', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

require_once('helpers/Encoding.php');

class Page {
    /*
     * @var string The page URL
     */
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
     * @var Array The page coauthors
     */
    public $coauthors = [];

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
    public $skipYMD = false;

    /**
     * @var bool Indicates if we have to skip month/date (but maybe keep year) template parameters
     */
    public $skipMD = false;


    /**
     * @var bool Indicates if we have to skip author template parameter
     */
    public $skipAuthor;

    /**
     * @var mixed If not null, contains an array for anotheser service to use
     */
    public $switchTo = null;

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
        $this->get_data();
        if ($this->data) {
            $this->analyse();
        }
    }

    function get_data () {
        ini_set('user_agent', USER_AGENT);
        $data = file_get_contents($this->url);
        if (!$data) {
            ini_set('user_agent', USER_AGENT_FALLBACK);
            if (!$data = @file_get_contents($this->url)) {
                $this->error = "Can't read URL";
                return;
            }
        }
        $this->data = $data;
        $this->encodeData();
    }

    function encodeData () {
        $encoding = mb_detect_encoding($this->data, "ISO-8859-15, ISO-8859-1, UTF-8, ASCII, auto");
        if ($encoding && $encoding != 'UTF-8') {
            $this->data = Encoding::toUTF8($this->data);
        }
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
        //Meta tags (including <meta property="" value=""> and <meta itemprop="" value="" syntax)
        $this->meta_tags = $this->get_meta_tags();
        $t = $this->meta_tags;

        //Title
        $this->title = $this->get_title();

        //Date
        if ($date = $this->getMetaTag($t, 'date', 'pubdate', 'content_create_date')) {
            $date = date_parse($date);
            $this->yyyy = $date['year'];
            $this->mm   = $date['month'];
            $this->dd   = $date['day'];
        }

        //Site name
        $this->site = $this->getMetaTag($t, 'og:site_name');

        //Author
        $this->author = $this->getMetaTag($t, 'author');
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
        preg_match_all('/<[\s]*meta[\s]+.*?\b(name|property|itemprop)\b="?' . '([^>"]*)"?[\s]*' . 'content="?([^>"]*)"?[\s]*[\/]?[\s]*>/si', $this->data, $match);
        if (isset($match) && is_array($match) && count($match) == 4) {
            $originals = $match[0];
            $names = $match[2];
            $values = $match[3];

            if (count($originals) == count($names) && count($names) == count($values)) {
                $metaTags = array();

                for ($i = 0, $limiti = count($names) ; $i < $limiti ; $i++) {
                    $key = $names[$i];
                    $value = $values[$i];

                    //Sets an unique scalar value, or if several identical tag names are offered, an array of values.
                    //Some publishers offer several times the same tag to list several values (see T241).
                    if (array_key_exists($key, $metaTags)) {
                        $currentValue = $metaTags[$key];
                        if ($currentValue == $value) {
                            continue;
                        }
                        if (is_array($currentValue)) {
                            $metaTags[$key][] = $value;
                        } else {
                            //Scalar -> array
                            $metaTags[$key] = [ $currentValue, $value ];
                        }
                    } else {
                        $metaTags[$key] = $value;
                    }
                }
            }
        }

        array_walk($metaTags, [ self, clean_tag ]);

        return $metaTags;
    }

    /**
     * Cleans a tag value (callback for array_walk)
     *
     * @param mixed &$value array item's value
     * @param string $key array item's key
     */
    static function clean_tag (&$item, $key) {
        if (is_array($item)) {
            $item = join("; ", $item);
        }
        return trim($item);
    }

    /**
     * Gets title
     *
     * @return string The page title
     */
    function get_title () {
        $title = $this->getMetaTag($this->meta_tags, 'title', 'og:title', 'DC.title', 'Title');
        return $title ?: ((preg_match("#<title>(.+)<\/title>#iU", $this->data, $title)) ? trim($title[1]) : '');
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
            $tag_lowercase = strtolower($tag);
            foreach ($metatags as $key => $value) {
                if ($tag_lowercase == strtolower($key)) return $value;
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
     * @param string $after  The string at the right of the text to be grabbed [facultative]
     *
     * @return string The text found between $before and $after
     */
    static function grab ($text, $before, $after = null) {
        $pos1 = strpos($text, $before);
        if ($pos1 === false) {
            return false;
        } else {
            $pos1 += strlen($before);
        }

        if ($after === null) {
            return substr($text, $pos1);
        }

        $pos2 = strpos($text, $after, $pos1 + 1);
        if ($pos2 === false) {
            return false;
        }

        return substr($text, $pos1, $pos2 - $pos1);
    }

    /**
     * Downloads, through CURL library, accepting cookies.
     *
     * @param $url The URL to fetch
     */
    static function curl_download ($url, $agent = '') {
        $ch = curl_init();
        $timeout = 5;
        $cookie_file = tmpfile();
        $cookie_file = tempnam(sys_get_temp_dir(), "cookie-sourcesgen-");
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        if ($agent != '') {
            curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        }
        $data = curl_exec($ch);
        curl_close($ch);
        unlink($cookie_file);
        return $data;
   }
}
