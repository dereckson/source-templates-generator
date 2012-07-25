<?php
setlocale(LC_TIME, 'fr_FR.UTF-8');

class ArticleTemplate extends Template {
	public $lang;
	public $title;
	public $periodique;
	public $year;
	public $accessdate;

	function __construct () {
		$this->name = "Article";
		$this->accessdate = trim(strftime(LONG_DATE_FORMAT));
	}

	static function loadFromPage ($page) {
		$template = new self();
		$t = $page->meta_tags;

		//Language
		$template->lang = page::getMetaTag($t, 'dc_language', 'citation_language');

		//Authors
		if ($author = $page->author ?: page::getMetaTag($t, 'author', 'dc_creator', 'citation_authors', 'dc_contributor', 'citation_author')) {
			//TODO: handle Alpha Beta syntax instead Beta, Alpha
			$template->authors[] = explode(', ', $author, 2);
		}

		//Title
		if (!$template->title = page::getMetaTag($t, 'dc_title', 'citation_title')) {
			$template->title = $page->title;
		}

		//Journal, publisher
		$template->journal =  page::getMetaTag($t, 'prism_publicationname', 'citation_journal_title', 'og:site_name');
		$template->journalLink = $t['dc_source'];
		$template->publisher = page::getMetaTag($t, 'dc_publisher', 'citation_publisher');

		//Issue name, number and volume
		$template->issue  = page::getMetaTag($t, 'prism_number', 'citation_issue');
		$template->volume = page::getMetaTag($t, 'citation_volume');
		if (
			(!$template->issueName = $t['prism_issuename'])
			&&
			array_key_exists('dc_relation_ispartof', $t)
		) {
			$template->issueName = $t['dc_relation_ispartof']
			                     . " <!-- !!! paramètre à nettoyer !!! -->";
		}

		//Date
		if ($page->unixtime) {
			$template->yyyy = date('Y', $page->unixtime);
			$template->mm   = date('m', $page->unixtime);
			$template->dd   = date('j', $page->unixtime);
		} elseif ($date = page::getMetaTag($t, 'prism_publicationdate', 'dc_date', 'citation_date')) {
			$template->yyyy = substr($date, 0, 4);
			$template->mm   = substr($date, 5, 2);
			$template->dd   = substr($date, 8, 2);
		} else {
			$template->yyyy = page::getMetaTag($t, 'citation_year', 'citation_publication_date');
		}

		//Pages
		$template->pageStart = page::getMetaTag($t, 'prism_startingpage', 'citation_firstpage', 'citation_first_page');
		$template->pageEnd   = page::getMetaTag($t, 'prism_endingpage',   'citation_lastpage',  'citation_last_page');

		//ISBN, ISSN, URLs
		$template->issn = $page->issn ?: page::getMetaTag($t, 'prism_issn', 'citation_issn');
		$template->isbn = page::getMetaTag($t, 'citation_isbn');
		$template->doi  = page::getMetaTag($t, 'citation_doi');

		$template->summary = page::getMetaTag($t, 'citation_abstract_html_url');
		$template->url = self::getTextURL($page->url, $t);

		return $template;
	}

	function __toString () {
		//Langue
		$this->params['langue'] = $this->lang;

		//Auteur
		if (count($this->authors)) {
			$k = 1;
			foreach ($this->authors as $author) {
				$this->params["prénom$k"] = $author[1];
				$this->params["nom$k"] = $author[0];
				$this->params["lien auteur$k"] = '';
				$k++;
			}
		}

		//Titre, périodique, éditeur, volume, etc.
		$this->params['titre'] = $this->title;
		$this->params['périodique'] = $this->journal;
		//TODO: vérifier si l'aticle existe sur fr.wikip et contient l'infobox Presse ou est rattaché à une catégorie fille de [[Catégorie:Revue scientifique]]
		//$this->params['lien périodique'] = $this->journal;
		$this->params['éditeur'] = $this->publisher;
		if ($this->volume) $this->params['volume'] = $this->volume;
		$this->params['numéro'] = $this->issue;
		if ($this->issueName) $this->params['titre numéro'] = $this->issueName;

		//Date
		if ($this->mm && $this->dd) {
			$date = mktime(12, 0, 0, $this->mm, $this->dd, $this->yyyy);
			$this->params['jour'] = trim(strftime('%e', $date));
			$this->params['mois'] = strftime('%B', $date);
		}
		$this->params['année'] = $this->yyyy;

		//Pages, ISSN, ISBN, DOI, URL, consulté le
		$this->params['pages'] = $this->pageEnd ? ($this->pageStart . '-' . $this->pageEnd) : $this->pageStart;
		if ($this->issn) $this->params['ISSN'] = $this->issn;
		if ($this->isbn) $this->params['ISBN'] = $this->isbn;
		if ($this->doi) $this->params['doi'] = $this->doi;
		$this->params['url texte'] = $this->url;
 		if (self::isSummaryPertinent($this->url, $this->summary)) {
			$this->params['résumé'] = $this->summary;
		}
		$this->params['consulté le'] = trim(strftime(LONG_DATE_FORMAT));

		return parent::__toString();
	}

	/**
	 * Gets article full text URL
         *
         * @param string $url the article current URL
	 *
	 * @return string the article fulltext URL
	 */
	static function getTextURL ($url, $metatags) {
		if (strpos($url, '.revues.org/') > 0) {
			//revues.org PDF generation is broken
			return $url;
		}

		if ($text_url = page::getMetaTag($metatags, 'citation_pdf_url', 'citation_fulltext_html_url')) {
			return $text_url;
		}

		return $url;
	}

        /**
         * Determines if a summary is pertinent to include in parameters
	 *
	 * @param string $url_article Article URL
	 * @param string $url_summary Summary URL
	 *
	 * @return bool true if the summary URL should be included in templat ; otherwise, false
         */
	static function isSummaryPertinent ($url_article, $url_summary) {
		//Empty summary or identical to URL are rejected
		if ($url_summary == '' || $url_summary == $url_article) return false;

		//This site is indexed through /resume.php but gives /article.php as summary URL in metadata
		if (substr($url_article, 0, 32) == "http://www.cairn.info/resume.php") return false;

		return true;
	}
}
?>
