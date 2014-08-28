<?php
setlocale(LC_TIME, 'fr_FR.UTF-8');

class OuvrageTemplate extends Template {
	public $accessdate;

	function __construct () {
		$this->name = "Ouvrage";
		$this->accessdate = trim(strftime(LONG_DATE_FORMAT));
	}

	static function loadFromBook ($book) {
        $template = new self;

        $i = 1;
        foreach ($book->Authors as $author) {
            $template->params["prénom$i"] = $author[0];
            $template->params["nom$i"] = $author[1];
            $i++;
        }

        $template->params['titre'] = $book->Title;
        $template->params['éditeur'] = $book->Publisher;
        $template->params['lieu'] = $book->Place;

        $template->params['année'] = $book->YYYY;
        if ($book->MM) { $template->params['mois'] = strftime('%B', mktime(0, 0, 0, $book->MM)); }
        if ($book->DD) { $template->params['jour'] = $book->DD; }

        $template->params['oclc'] = (int)$book->OCLC;

		return $template;
	}

	function __toString () {
		$this->params['consulté le'] = $this->accessdate;

		return parent::__toString();
	}
}
