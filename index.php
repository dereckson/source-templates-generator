<?php
//Get default form settings
$format = 0;
if (array_key_exists('format', $_REQUEST)) {
    $format = $_REQUEST['format'];
} elseif (array_key_exists('format', $_COOKIE)) {
    $format = $_COOKIE['format'];
}
?>

<!-- Content -->
<div id="content">
    <h1 class="icoTitle"><img src="/_pict/ico/forms.png" alt="Tools - form generation"/>{{Lien web}}</h1>
    <form method="post">
        <label for="URL">URL: </label><input type="text" size="80" name="URL" id="URL" value="<?= array_key_exists('URL', $_REQUEST) ? $_REQUEST['URL'] : '' ?>" />
	<input type="submit" value="OK"><br />
	Prints the template
	<input type="radio" name="format" id="format_multiline" value="0" <?= $format ? '' : 'checked ' ?>/><label for="format_multiline">in multi-lines mode</label>
	<input type="radio" name="format" id="format_oneline_spaced" value="1" <?= ($format == 1) ? 'checked ' : '' ?>/><label for="format_oneline_spaced"">in one line (with spaces)</label>
        <br />
        <input type="radio" name="format" id="format_oneline_nospace" value="2" <?= ($format == 2) ? 'checked ' : '' ?>/><label for="format_oneline_nospace"">in one line (without space)</label>
	<input type="radio" name="format" id="format_oneline_spacebeforepipe" value="3" <?= ($format == 3) ? 'checked ' : '' ?>/><label for="format_oneline_spacebeforepipe"">in one line (without space, except before |)</label>
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
    if ($page->is_article()) {
        echo "<h3>Note</h3><p>Cette URL pointe vers un article de revue, aussi le modèle {{Article}} est indiqué.</p>";
    }

    //Gets template
    require('templates/template.php');
    if ($page->is_article()) {
        require('templates/wikipedia-fr/Article.php');
        $template = ArticleTemplate::loadFromPage($page);
    } else {
        require('templates/wikipedia-fr/Lien_web.php');
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

    //Remembers 30 days the format setting
    if (array_key_exists('format', $_REQUEST)) {
        setcookie('format', $_REQUEST['format'], time() + 2592000);
    }

    //Prints template
    echo "    <h3>Template</h3>    \n    <textarea id=\"template\" rows=20 cols=80>\n$template</textarea>";

    //Meta tags
    echo "\n\n    <h3>Meta tags</h3>\n    <table cellpadding=8>\n        <tr><th>Tag</th><th>Value</th></tr>";
    foreach ($page->meta_tags as $key => $value) {
        echo "        <tr><td>$key</td><td>$value</td></tr>";
    }
    echo "\n    </table>";
}
?>
    <h3>How to improve this tool?</h3>
    <p>A little PHP knowledge will allow you to customize and improve this tool. I will be happy to accept patches in this goal.</p>
    <p>If you wish to adapt this tool to be used on another website (a Wikipedia project in another language or outside Wikipedia), please see the template.php file and samples in the templates/ folder.</p>
    <p>If you wish to add websites analysis, please add the URL in index.dat, then create a class which extends Page ; see page.php and the pages/ folder.</p>
    <p><strong>Source code:</strong> [ <a href="http://hg.dereckson.be/source-templates-generator">git repository</a> | <a href="https://bitbucket.org/dereckson/source-templates-generator/get/master.zip">download current snapshot</a> ]</p>
</div>

<!-- left menu -->
<div id="leftMenu">
    <ul class="navMenu">
	<li><a href="http://fr.wikipedia.org/wiki/Modèle:Lien web">{{Lien web}}</a></li>
	<li><a href="http://fr.wikipedia.org/wiki/Modèle:Article">{{Article}}</a></li>
	<li><a href="http://www.prismstandard.org/specifications/">PRISM</a></li>
	<li><a href="http://dublincore.org/">Dublin Core</a></li>
	<li><a href="http://scholar.google.com/intl/en/scholar/inclusion.html">Google Scholar</a></li>
	<li><a href="http://ogp.me/">Open Graph</a></li>
    </ul>
</div>
