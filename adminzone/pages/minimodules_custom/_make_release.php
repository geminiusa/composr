<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2015

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

/*EXTRA FUNCTIONS: shell_exec*/

/* To be called by make_release.php - not directly linked from menus */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

set_mass_import_mode(true);

restrictify();
$groups = $GLOBALS['FORUM_DRIVER']->get_usergroup_list(false, true);
require_code('composr_homesite');

// Version info / plan

$version_dotted = get_param_string('version');
require_code('version2');
$version_pretty = get_version_pretty__from_dotted(get_version_dotted__from_anything($version_dotted));

$is_substantial = (substr($version_dotted, -2) == '.0') || (strpos($version_dotted, 'beta1') !== false) || (strpos($version_dotted, 'RC1') !== false);

$is_old_tree = get_param_integer('is_old_tree') == 1;

$is_bleeding_edge = get_param_integer('is_bleeding_edge') == 1;
if (!$is_bleeding_edge) {
    $bleeding1 = '';
    $bleeding2 = '';
} else {
    $bleeding1 = ' (bleeding-edge)';
    $bleeding2 = 'bleeding-edge, ';
}

$changes = post_param_string('changes', '', true);

$descrip = get_param_string('descrip', '', true);

$needed = get_param_string('needed', '', true);
$justification = get_param_string('justification', '', true);

$urls = array();

// Bugs list

if (!$is_bleeding_edge) {
    require_code('catalogues');
    require_code('catalogues2');

    $bug_category_id = get_bug_category_id($version_dotted);
    $urls['Bugs'] = static_evaluate_tempcode(build_url(array('page' => 'catalogues', 'type' => 'category', 'id' => $bug_category_id), get_module_zone('catalogues'), null, false, false, true));
} else {
    $bug_category_id = null;
}

// Add downloads (assume uploaded already)

require_code('downloads2');
$releases_category_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', array('parent_id' => db_get_first_id(), $GLOBALS['SITE_DB']->translate_field_ref('category') => 'Releases'));
if (is_null($releases_category_id)) {
    $releases_category_id = add_download_category('Releases', db_get_first_id(), '', '');
    foreach (array_keys($groups) as $group_id) {
        $GLOBALS['SITE_DB']->query_insert('group_category_access', array('module_the_name' => 'downloads', 'category_name' => strval($releases_category_id), 'group_id' => $group_id));
    }
}

$release_category_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', array('parent_id' => $releases_category_id, $GLOBALS['SITE_DB']->translate_field_ref('category') => 'Version ' . strval(intval($version_dotted))));
if (is_null($release_category_id)) {
    $release_category_id = add_download_category('Version ' . strval(intval($version_dotted)), $releases_category_id, '', '');
    foreach (array_keys($groups) as $group_id) {
        $GLOBALS['SITE_DB']->query_insert('group_category_access', array('module_the_name' => 'downloads', 'category_name' => strval($release_category_id), 'group_id' => $group_id));
    }
}

$installatron_category_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', array('parent_id' => $releases_category_id, $GLOBALS['SITE_DB']->translate_field_ref('category') => 'Installatron integration'));
if (is_null($installatron_category_id)) {
    $installatron_category_id = add_download_category('Installatron integration', $releases_category_id, '', '');
    foreach (array_keys($groups) as $group_id) {
        $GLOBALS['SITE_DB']->query_insert('group_category_access', array('module_the_name' => 'downloads', 'category_name' => strval($installatron_category_id), 'group_id' => $group_id));
    }
}

$microsoft_category_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', array('parent_id' => $releases_category_id, $GLOBALS['SITE_DB']->translate_field_ref('category') => 'Microsoft integration'));
if (is_null($microsoft_category_id)) {
    $microsoft_category_id = add_download_category('Microsoft integration', $releases_category_id, '', '');
    foreach (array_keys($groups) as $group_id) {
        $GLOBALS['SITE_DB']->query_insert('group_category_access', array('module_the_name' => 'downloads', 'category_name' => strval($microsoft_category_id), 'group_id' => $group_id));
    }
}

$all_downloads_to_add = array(
    array(
        'name' => "Composr Version {$version_pretty}{$bleeding1}",
        'description' => "This is version {$version_pretty}." . (is_null($bug_category_id) ? "" : "\n\nAny [url=\"critical bug fixes\" title=\"{!LINK_NEW_WINDOW}\"]http://compo.sr/site/catalogues/category/" . strval($bug_category_id) . ".htm[/url] for this version are organised on the Composr website."),
        'filename' => 'composr_quick_installer-' . $version_dotted . '.zip',
        'comments' => ($is_bleeding_edge || $is_old_tree) ? '' : 'This is the latest version.',
        'category_id' => $release_category_id,
        'internal_name' => 'Quick installer',
    ),

    array(
        'name' => "Composr Version {$version_pretty} ({$bleeding2}manual)",
        'description' => "Manual installer (as opposed to the regular quick installer). Please note this isn't documentation.",
        'filename' => 'composr_manualextraction_installer-' . $version_dotted . '.zip',
        'comments' => '',
        'category_id' => $release_category_id,
        'internal_name' => 'Manual installer',
    ),

    array(
        'name' => "Composr {$version_pretty}",
        'description' => "This archive is designed for webhosting control panels that integrate Composr. It contains an SQL dump for a fresh install, and a config-file-template. It is kept up-to-date with the most significant releases of Composr.",
        'filename' => 'composr-' . $version_dotted . '.tar.gz',
        'comments' => '',
        'category_id' => $installatron_category_id,
        'internal_name' => 'Installatron installer',
    ),

    array(
        'name' => "Composr {$version_pretty}",
        'description' => "This is a Microsoft Web Platform Installer package of Composr. We will update this routinely when we release new versions, and update Microsoft with the the details.\n\nIt can be manually installed into IIS running the Web Deploy Tool, but it should soon be featured in the Web App Gallery directly. Therefore accessing this archive directly is probably of no direct use to you. If you do want to install on IIS manually, the regular Composr installers can do it fine.",
        'filename' => 'composr-' . $version_dotted . '-webpi.zip',
        'comments' => '',
        'category_id' => $microsoft_category_id,
        'internal_name' => 'Microsoft installer',
    ),
);

foreach ($all_downloads_to_add as $i => $d) {
    $full_local_path = get_custom_file_base() . '/uploads/downloads/' . $d['filename'];
    $d['full_local_path'] = $full_local_path;
    if (!file_exists($full_local_path)) {
        echo '<p>Could not find file <kbd>uploads/downloads/' . escape_html($d['filename']) . '</kbd></p>';
        continue;
    }
    $all_downloads_to_add[$i] = $d;
}

foreach ($all_downloads_to_add as $i => $d) {
    if (!isset($d['full_local_path'])) {
        continue; // Could not find file above
    }

    $full_local_path = $d['full_local_path'];
    $file_size = filesize($full_local_path);
    $original_filename = $d['filename'];
    $name = $d['name'];
    $url = 'uploads/downloads/' . rawurlencode($d['filename']);
    $description = $d['description'];
    $comments = $d['comments'];
    $category_id = $d['category_id'];

    $download_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_downloads', 'id', array('category_id' => $category_id, $GLOBALS['SITE_DB']->translate_field_ref('name') => $name));
    if (is_null($download_id)) {
        $download_id = add_download($category_id, $name, $url, $description, 'ocProducts', $comments, null, 1, 0, 0, 0, '', $original_filename, $file_size, 0, 0);
    } else {
        edit_download($download_id, $category_id, $name, $url, $description, 'ocProducts', $comments, null, 0, 1, 0, 0, 0, '', $original_filename, $file_size, 0, 0, null, '', '');
    }

    $d['download_id'] = $download_id;
    $all_downloads_to_add[$i] = $d;

    $urls[$d['internal_name']] = static_evaluate_tempcode(build_url(array('page' => 'downloads', 'type' => 'entry', 'id' => $download_id), get_module_zone('downloads'), null, false, false, true));
    $urls[$d['internal_name'] . ' (direct download)'] = find_script('dload') . '?id=' . strval($download_id);
}

// Edit past download

if ((!$is_bleeding_edge) && (!$is_old_tree) && (isset($all_downloads_to_add[0]['download_id']))) {
    $last_version_str = $GLOBALS['SITE_DB']->query_select_value_if_there('download_downloads', 'comments', array($GLOBALS['SITE_DB']->translate_field_ref('comments') => 'This is the latest version.'), ' AND d.id<>' . strval($all_downloads_to_add[0]['download_id']));
    if (!is_null($last_version_str)) {
        $last_version_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_downloads', 'id', array($GLOBALS['SITE_DB']->translate_field_ref('comments') => 'This is the latest version.'), ' AND d.id<>' . strval($all_downloads_to_add[0]['download_id']));
        if ($last_version_id != $all_downloads_to_add[0]['download_id']) {
            $description = "A new version, {$version_pretty} is available. Upgrading to {$version_pretty} is considered {$needed} by ocProducts{$justification}. There may have been other upgrades since {$version_pretty} - see [url=\"the ocProducts news archive\" target=\"_blank\"]http://compo.sr/site/news.htm[/url].";
            $GLOBALS['SITE_DB']->query_update('downloads', lang_remap_comcode($last_version_str, 'description', $description), array('id' => $last_version_id), '', 1);
        }
    }
}

// Extract latest download
if ((!$is_bleeding_edge) && (!$is_old_tree)) {
    @unlink('data.cms');
    @unlink('install.php');
    $cmd = 'cd ' . get_custom_file_base() . '/uploads/downloads; unzip -o ' . $all_downloads_to_add[0]['filename'];
    shell_exec($cmd);
}

// News

$major_release = '';
$major_release_1 = '';
if ($is_substantial) {
    $major_release = " As this is more than just a patch release it is crucial that you also choose to run a file integrity scan and a database upgrade.";
    $major_release_1 = "Please [b]make sure you take a backup before uploading your new files![/b]";
    $news_title = 'Composr ' . $version_pretty . ' released!';
} else {
    $news_title = 'Composr ' . $version_pretty . ' released';
}

require_code('news');
require_code('news2');

$summary = "{$version_pretty} released. Read the full article for more information, and upgrade information.";

$article = "Version {$version_pretty} has now been released. {$descrip}. Upgrading to this release is {$needed}{$justification}.

To upgrade follow the steps in your website's [tt]http://mybaseurl/upgrader.php[/tt] script. You will need to copy the URL of the attached file (created via the form below) during step 3.
{$major_release_1}

[block=\"{$version_pretty}\"]composr_homesite_make_upgrader[/block]

{$changes}";

$news_category = $GLOBALS['SITE_DB']->query_select_value_if_there('news_categories', 'id', array($GLOBALS['SITE_DB']->translate_field_ref('nc_title') => 'New releases'));
if (is_null($news_category)) {
    $news_category = add_news_category('New releases', 'newscats/general', '');
    foreach (array_keys($groups) as $group_id) {
        $GLOBALS['SITE_DB']->query_insert('group_category_access', array('module_the_name' => 'news', 'category_name' => strval($news_category), 'group_id' => $group_id));
    }
}

$news_id = $GLOBALS['SITE_DB']->query_select_value_if_there('news', 'id', array('news_category' => $news_category, $GLOBALS['SITE_DB']->translate_field_ref('title') => $news_title));
if (is_null($news_id)) {
    $news_id = add_news($news_title, $summary, 'ocProducts', 1, 0, 1, 0, '', $article, $news_category);
} else {
    edit_news($news_id, $news_title, $summary, 'ocProducts', 1, 0, 1, 0, '', $article, $news_category, null, '', '', '');
}
$urls['News: ' . $news_title] = static_evaluate_tempcode(build_url(array('page' => 'news', 'type' => 'view', 'id' => $news_id), get_module_zone('news'), null, false, false, true));

// DONE!

echo '<p>Done version ' . escape_html($version_pretty) . '!</p>';

echo '<ul>';
foreach ($urls as $title => $url) {
    echo '<li><a href="' . escape_html($url) . '">' . escape_html($title) . '</a></li>';
}
echo '</ul>';
