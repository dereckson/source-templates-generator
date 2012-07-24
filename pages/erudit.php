<?php

//Page analysis for www.erudit.org
class EruditPage extends Page {
    function analyse () {
        parent::analyse();

       //Fixing citation_doi metatag bug
       //<meta name="citation_doi" content="&#10;                    10.7202/012719ar" />
       $doi = str_replace('&#10;', '', $this->meta_tags['citation_doi']);
       $this->meta_tags['citation_doi'] = trim($doi);
    }
}

?>
