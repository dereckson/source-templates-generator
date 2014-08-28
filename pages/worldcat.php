<?php

class WorldCatPage extends Page {
    function analyse () {
        parent::analyse();

        if (substr($this->url, 0, 30) == "http://www.worldcat.org/title/" && preg_match("@/oclc/([0-9]*)@", $this->url, $matches)) {
            $this->switchTo = [
                'document' => [
                    'class' => 'Book',
                    'params' => [
                        'OCLC' => $matches[1]
                    ],
                    'method' => 'queryWorldCatFromOCLC',
                ],
                'template' => 'book',
            ];
        }
    }
}
