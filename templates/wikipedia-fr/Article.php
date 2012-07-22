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
		$template->lang = self::getMetaTag($t, 'dc_language', 'citation_language');

		//Authors
		if ($author = self::getMetaTag($t, 'author', 'dc_creator', 'citation_authors')) {
			$template->authors[] = explode(', ', $author, 2);
		}

		//Title
		if (!$template->title = self::getMetaTag($t, 'dc_title', 'citation_title')) {
			$template->title = $page->title;
		}

		//Journal, publisher
		$template->journal =  self::getMetaTag($t, 'prism_publicationname', 'citation_journal_title');
		$template->journalLink = $t['dc_source'];
		$template->publisher = self::getMetaTag($t, 'dc_publisher', 'citation_publisher');

		//Issue name and number
		$template->issue = self::getMetaTag($t, 'prism_number', 'citation_issue');
		if (
			(!$template->issueName = $t['prism_issuename'])
			&&
			array_key_exists('dc_relation_ispartof', $t)
		) {
			$template->issueName = $t['dc_relation_ispartof']
			                     . " <!-- !!! paramètre à nettoyer !!! -->";
		}

		//Date
		$date = self::getMetaTag($t, 'prism_publicationdate', 'dc_date', 'citation_date');
		$template->yyyy = substr($date, 0, 4);
		$template->mm   = substr($date, 5, 2);
		$template->dd   = substr($date, 8, 2);

		//Pages
		$template->pageStart = self::getMetaTag($t, 'prism_startingpage' , 'citation_firstpage');
		$template->pageEnd = self::getMetaTag($t, 'prism_endingpage', 'citation_lastpage');

		//ISBN, ISSN, URL
		$template->issn = self::getMetaTag($t, 'prism_issn', 'citation_issn');
		$template->isbn = self::getMetaTag($t, 'citation_isbn');
		$template->summary = self::getMetaTag($t, 'citation_abstract_html_url');
		$template->url = $page->url;

		return $template;
	}

	function __toString () {
		//Langue
		$this->params['langue'] = $this->lang;

		//Authors
		$k = 1;
		foreach ($this->authors as $author) {
			$this->params["prénom$k"] = $author[1];
			$this->params["nom$k"] = $author[0];
			$this->params["lien auteur$k"] = '';
			$k++;
		}

		//Titre, périodique, éditeur, volume, etc.
		$this->params['titre'] = $this->title;
		$this->params['périodique'] = $this->journal;
		//TODO: vérifier si l'aticle existe sur fr.wikip et contient l'infobox Presse ou est rattaché à une catégorie fille de [[Catégorie:Revue scientifique]]
		$this->params['lien périodique'] = $this->journal;
		$this->params['éditeur'] = $this->publisher;
		$this->params['numéro'] = $this->issue;
		$this->params['titre numéro'] = $this->issueName;

		//Date
		$date = mktime(12, 0, 0, $this->mm, $this->dd, $this->yyyy);
		$this->params['jour'] = trim(strftime('%e', $date));
		$this->params['mois'] = strftime('%B', $date);
		$this->params['année'] = $this->yyyy;

		//Pages, ISSN, ISBN, URL, consulté le
		$this->params['pages'] = $this->pageEnd ? ($this->pageStart . '-' . $this->pageEnd) : $this->pageStart;
		$this->params['ISSN'] = $this->issn;
		$this->params['ISBN'] = $this->isbn;
		$this->params['url texte'] = $this->url;
		if ($this->summary != '' && $this->summary != $this->url) {
			$this->params['résumé'] = $this->summary;
		}
		$this->params['consulté le'] = trim(strftime(LONG_DATE_FORMAT));

		return parent::__toString();
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

}
?>
