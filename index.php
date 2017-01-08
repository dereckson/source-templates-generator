<?php

require 'vendor/autoload.php';
require 'autoload.php';

//Get default form settings
$format = 0;
if (array_key_exists('format', $_REQUEST)) {
    $format = $_REQUEST['format'];
    setcookie('format', $_REQUEST['format'], time() + 2592000);
} elseif (array_key_exists('format', $_COOKIE)) {
    $format = $_COOKIE['format'];
}
?>
<!-- Content -->
    <h2>Get source template for this URL</h2>
    <form method="post" class="custom">
        <label for="URL">URL: </label>
        <div class="row collapse">
          <div class="ten mobile-three columns">
            <input type="text" name="URL" id="URL" value="<?= array_key_exists('URL', $_REQUEST) ? $_REQUEST['URL'] : '' ?>" />
          </div>
          <div class="two mobile-one columns">
             <input type="submit" class="button expand postfix" value="Generate template" />
          </div>
        </div>
        <div class="row collapse">
          <div class="six columns">
            <label>Prints the template:</label>
            <label for="format_multiline"><input type="radio" name="format" id="format_multiline" value="0" <?= $format ? '' : 'checked ' ?>/> in multi-lines mode</label>
            <label for="format_oneline_spaced"><input type="radio" name="format" id="format_oneline_spaced" value="1" <?= ($format == 1) ? 'checked ' : '' ?>/> in one line (with spaces)</label>
            <label for="format_oneline_nospace"><input type="radio" name="format" id="format_oneline_nospace" value="2" <?= ($format == 2) ? 'checked ' : '' ?>/> in one line (without space)</label>
            <label for="format_oneline_spacebeforepipe"><input type="radio" name="format" id="format_oneline_spacebeforepipe" value="3" <?= ($format == 3) ? 'checked ' : '' ?>/> in one line (without space, except before |)</label>
          </div>
          <div class="six columns">
            <label>Project:</label>
            <select>
              <option value="fr.wikipedia">French Wikipedia</option>
            </select>
            <label for="force_article"><input type="checkbox" name="force_article" id="force_article" /> Force {{Article}} template</label>
          </div>
        </div>
    </form>
<?php
if (array_key_exists('URL', $_REQUEST)) {
    include('page.php');

    //Does the specified URL valid and exist?
    $url = $_REQUEST['URL'];
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        message_die(GENERAL_ERROR, "$url isn't a valid URL.", 'URL issue');
    }

    //Gets page information
    setlocale(LC_TIME, 'fr_FR.UTF-8');
    $page = Page::load($url);
    if ($page->error) {
        message_die(GENERAL_ERROR, "Can't open $url", 'URL issue');
    }
    $force_article = array_key_exists('force_article', $_REQUEST) && $_REQUEST['force_article'];
    if (!$force_article && $page->is_article()) {
        echo "<h3>Note</h3><p>Cette URL pointe vers un article de revue, aussi le modèle <a href=\"https://fr.wikipedia.org/wiki/Template:Article\">{{Article}}</a> est indiqué.</p>";
    }

    if ($page->switchTo != null) {
        $documentObject = new $page->switchTo['document']['class'];
        foreach ($page->switchTo['document']['params'] as $key => $value) {
            $documentObject->$key = $value;
        }
        call_user_func([$documentObject, $page->switchTo['document']['method']]);
    }

    //Gets template
    if ($page->switchTo != null) {
        switch ($page->switchTo['template']) {
            case 'book':
                $template = OuvrageTemplate::loadFromBook($documentObject);
                break;

            default:
                $template = "DEBUG: please add a template logic for this switch object:\n\n" . print_r($page->switchTo, true);
        }
    } elseif ($force_article || $page->is_article()) {
        $template = ArticleTemplate::loadFromPage($page);
    } else {
        $template = LienWebTemplate::loadFromPage($page);
    }

    //Reformats template if needed
    switch ($_REQUEST['format']) {
        case 1:
            $template = str_replace("\n", '', $template);
            break;

       case 2:
            $template = str_replace("\n | ", '|', $template);
            $template = str_replace(" = ", '=', $template);
            break;

       case 3:
           $template = str_replace("\n | ", ' |', $template);
           $template = str_replace(" = ", '=', $template);
           break;
    }

    //Prints template
    echo "    <h3>Template</h3>    \n    <textarea id=\"template\" rows=16 cols=80>\n$template</textarea>";

    //Meta tags
    if (count($page->meta_tags)) {
        echo "\n\n    <h3>Meta tags</h3>\n    <table class=\"twelve\" cellpadding=\"8\">\n      <thead>\n        <tr><th>Tag</th><th>Value</th></tr>\n      </thead>\n      <tbody>";
        foreach ($page->meta_tags as $key => $value) {
            echo "\n        <tr><td>$key</td><td>$value</td></tr>";
        }
        echo "\n      </tbody>\n    </table>";
    }
}
?>

  <h2>Documentation</h2>
  <div class="row">
    <div class="three columns">
      <h3>References</h3>
      <ul class="menu">
        <li><a href="http://fr.wikipedia.org/wiki/Modèle:Lien web">{{Lien web}}</a></li>
        <li><a href="http://fr.wikipedia.org/wiki/Modèle:Article">{{Article}}</a></li>
        <li><a href="http://www.prismstandard.org/specifications/">PRISM</a></li>
        <li><a href="http://dublincore.org/">Dublin Core</a></li>
        <li><a href="http://scholar.google.com/intl/en/scholar/inclusion.html">Google Scholar</a></li>
        <li><a href="http://ogp.me/">Open Graph</a></li>
      </ul>
    </div>
    <div class="nine columns">
      <h3>How to improve this tool?</h3>
      <p>A little PHP knowledge will allow you to customize and improve this tool. I will be happy to accept patches in this goal.</p>
      <p>If you wish to adapt this tool to be used on another website (a Wikipedia project in another language or outside Wikipedia), please see the template.php file and samples in the templates/ folder.</p>
      <p>If you wish to add websites analysis, please add the URL in index.dat, then create a class which extends Page ; see page.php and the pages/ folder.</p>
      <p><strong>Source code:</strong> [ <a href="https://devcentral.nasqueron.org/source/STG/">Git repository</a> | <a href="https://github.com/dereckson/source-templates-generator/archive/master.zip">download current snapshot</a> ]</p>
    </div>
  </div>

<script src="/javascripts/jquery.foundation.forms.js"></script>
