<?php

/**
 * Autoload function to register into the __autoload stack
 */
function sourcetemplatesgenerator_autoloader ($class) {
    switch ($class) {
        case 'Book': require('book.php'); return;
        case 'Page': require('page.php'); return;

        case 'Template': require('templates/template.php'); return;
        case 'ArticleTemplate': require('templates/wikipedia-fr/Article.php'); return;
        case 'LienWebTemplate': require('templates/wikipedia-fr/Lien_web.php'); return;
        case 'OuvrageTemplate': require('templates/wikipedia-fr/Ouvrage.php'); return;

        case 'DownloadWithWget': require('pages/DownloadWithWget.php'); return;
    }

    if (substr($class, -4) === "Page") {
        if (file_exists("pages/$class.php")) {
            require "pages/$class.php";
            return;
        }

        $file = strtolower(substr($class, 0, -4));
        require "pages/$file.php";
        return;
    }
}

spl_autoload_register('sourcetemplatesgenerator_autoloader');
