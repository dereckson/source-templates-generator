<!-- Content -->
<div id="content">
    <h1 class="icoTitle"><img src="/_pict/ico/forms.png" alt="Tools - form generation"/>{{Lien web}}</h1>
    <form method="post">
        <label for="URL">URL: </label><input type="text" size="80" name="URL" id="URL" value="<?= $_REQUEST['URL'] ?>" />
    </form>
<?php
if (array_key_exists('URL', $_REQUEST)) {
    include('page.php');

    $url = $_REQUEST['URL'];
    setlocale(LC_TIME, 'fr_FR.UTF-8');
    $page = Page::load($url);
    if ($page->is_article()) {
        echo "<h3>Note</h3><p>Cette URL pointe vers un article de revue, aussi le modèle {{Article}} est indiqué.</p>";
    }

    //Template
    echo "    <h3>Template</h3>    \n    <textarea id=\"template\" rows=20 cols=80>\n";
    require('templates/template.php');
    if ($page->is_article()) {
        require('templates/wikipedia-fr/Article.php');
        $template = ArticleTemplate::loadFromPage($page);
    } else {
        require('templates/wikipedia-fr/Lien_web.php');
        $template = LienWebTemplate::loadFromPage($page);
    }
    echo $template, '</textarea>';

    //Meta tags
    echo "\n\n    <h3>Meta tags</h3>\n    <table cellpadding=8>\n        <tr><th>Tag</th><th>Value</th></tr>";
    foreach ($page->meta_tags as $key => $value) {
        echo "        <tr><td>$key</td><td>$value</td></tr>";
    }
    echo "\n    </table>";
}
?>
</div>

<!-- left menu -->
<div id="leftMenu">
    <ul class="navMenu">
	<li><a href="http://fr.wikipedia.org/wiki/Modèle:Lien web">{{Lien web}}</a></li>
	<li><a href="http://fr.wikipedia.org/wiki/Modèle:Article">{{Article}}</a></li>
	<li><a href="http://www.prismstandard.org/specifications/">PRISM</a></li>
	<li><a href="http://dublincore.org/">Dublin Core</a></li>
	<li><a href="http://scholar.google.com/intl/en/scholar/inclusion.html">Google 
Scholar</a></li>
    </ul>
</div>
