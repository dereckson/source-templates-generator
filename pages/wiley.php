<?php

//Page analysis for onlinelibrary.wiley.com
class WileyPage extends Page {
    function analyse () {
        parent::analyse();

        $authors = explode("; ", $this->meta_tags['citation_author']);
        $this->coauthors = $authors;
    }

    function is_article () {
        return true;
    }
}
