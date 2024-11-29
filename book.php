<?php

use EasyRdf\Graph;

class Book {
    /**
     * OCLC number
     */
    public $OCLC;

    public function queryWorldCatFromOCLC () {
        $oclc = (string)(int)$this->OCLC;

        $url = 'http://www.worldcat.org/oclc/' . $oclc;
        $rdf = new Graph($url . '.rdf');
        $rdf->load();
        $resources = $rdf->resources();
        $book = $resources[$url];

        //Core info
        $this->Title = (string)$book->getLiteral('schema:name');
        $publisherData = $book->get('schema:publisher');
        if ($publisherData) {
            $this->Publisher = (string)$publisherData->get('schema:name');
        }

        //Publishing date
        $date = (string)$book->getLiteral('schema:datePublished');
        if (strlen($date) == 4) {
            $this->YYYY = $date;
        } else {
            echo '<div class="alert-box">Publishing date: ', $date, " / check the template, the code doesn't know how to parse this format and only made a guess. ",
            '<a href="" class="close">&times;</a></div>';
            $date = date_parse($date);
            $this->YYYY = $date['year'];
            $this->MM = $date['month'];
            $this->DD = $date['day'];
        }

        //Authors
        $this->Authors = [];
        //TODO: look type mapping
        $contributors = $book->allResources('schema:contributor');
        foreach ($contributors as $contributor) {
            $this->Authors[] = [
                (string)$contributor->get('schema:givenName'),
                (string)$contributor->get('schema:familyName')
            ];
        }

        //Kludge for library:placeOfPublication
        //We have generally two links, one for the city, one for the country.
        //Only the city has a schema:name, the country is only a reference.
        $rdf_content = file_get_contents($url . '.rdf');
        if (preg_match_all('@<library:placeOfPublication rdf:resource="(.*)"/>@', $rdf_content, $matches)) {
            foreach ($matches[1] as $place) {
                if ($cityCandidate = (string)$resources[$place]->get('schema:name')) {
                    $this->Place = $cityCandidate;
                    break;
                }
            }
        }
    }
}
