<?php
setlocale(LC_TIME, 'fr_FR.UTF-8');

class LienWebTemplate extends Template {
	public $author;
	public $coauthors;
	public $url;
	public $title;
	public $dd;
	public $mm;
	public $yyyy;
	public $site;
    public $pageDate = null;
	public $accessdate;

	/**
	 * @var bool Indicates if we've to remove jour/mois/année parameters
	 */
	public $skipYMD = false;

	/**
	 * @var bool Indicates if we've to remove jour/mois parameters but maybe keep année
	 */
	public $skipMD = false;

	/**
	 * @var bool Indicates if we've to remove auteur and coauteurs parameters
	 */
	public $skipAuthor = false;

	function __construct () {
		$this->name = "Lien web";
		$this->accessdate = trim(strftime(LONG_DATE_FORMAT));
	}

	static function loadFromPage ($page) {
		$template = new LienWebTemplate();

		$template->author = $page->author;
		$template->skipAuthor = $page->skipAuthor;
		$template->coauthors = $page->coauthors;
		$template->url = $page->url;
		$template->title = $page->title;
		$template->dd = $page->dd;
		$template->mm = $page->mm;
		$template->yyyy = $page->yyyy;
		$template->site = $page->site;
        $template->pageDate = $page->date;
		$template->skipYMD = $page->skipYMD;
		$template->skipMD = $page->skipMD;

		return $template;
	}

    function computeDate () {
        if ($this->pageDate !== "" && $this->pageDate !== null) {
            echo '<div data-alert class="alert-box info radius">';
            echo "<p>The Page metadata contains the following date information:<br />$this->pageDate</p><p>{{Lien web}} should now use jour, mois, année instead of a date parameter to provide richer machine data.</p>";
            echo ' <a href="#" class="close">&times;</a></div>';
        }
    }

	function __toString () {
		if (!$this->skipAuthor) {
			$this->params['auteur'] = $this->author;

			if ($this->coauthors) {
				$this->params['coauteurs'] = implode(', ', $this->coauthors);
			}
		}
		$this->params['titre'] = $this->title;
        $this->computeDate();
		if (!$this->skipYMD && !$this->skipMD) {
			$this->params['jour'] = $this->mm;
			$this->params['mois'] = $this->dd;
        }
		if (!$this->skipYMD) {
			$this->params['année'] = $this->yyyy;
		}
		$this->params['url'] = $this->url;
		$this->params['site'] = $this->site;
		$this->params['consulté le'] = $this->accessdate;

		return parent::__toString();
	}
}
