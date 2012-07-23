<?php
setlocale(LC_TIME, 'fr_FR.UTF-8');

class LienWebTemplate extends Template {
	public $author;
	public $url;
	public $title;
	public $dd;
	public $mm;
	public $yyyy;
	public $site;
	public $publishdate;
	public $accessdate;

	/**
	 * @var bool Indicates if we've to remove jour/mois/année parameters
	 */
	public $skipYMD = false;

	function __construct () {
		$this->name = "Lien web";
		$this->accessdate = trim(strftime(LONG_DATE_FORMAT));
	}

	static function loadFromPage ($page) {
		$template = new LienWebTemplate();

		$template->author = $page->author;
		$template->skipAuthor = $page->skipAuthor;
		$template->url = $page->url;
		$template->title = $page->title;
		$template->dd = $page->dd;
		$template->mm = $page->mm;
		$template->yyyy = $page->yyyy;
		$template->site = $page->site;
		$template->publishdate = $page->date;
		$template->skipYMD = $page->skipYMD;

		return $template;
	}

	function __toString () {
		if (!$this->skipAuthor) {
			$this->params['auteur'] = $this->author;
		}
		$this->params['titre'] = $this->title;
		if (!$this->skipYMD) {
			$this->params['jour'] = $this->mm;
			$this->params['mois'] = $this->dd;
			$this->params['année'] = $this->yyyy;
		}
		$this->params['url'] = $this->url;
		$this->params['site'] = $this->site;
		$this->params['en ligne le'] = $this->publishdate;
		$this->params['consulté le'] = $this->accessdate;

		return parent::__toString();
	}
}
?>
