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

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$sites_url = build_url(array('page' => 'sites'), 'site');
if (is_object($sites_url)) {
    $sites_url = $sites_url->evaluate();
}

$importing_tutorial_url = build_url(array('page' => 'tut_importer'), 'docs');
if (is_object($importing_tutorial_url)) {
    $importing_tutorial_url = $importing_tutorial_url->evaluate();
}

$featuretree = array(
    // Ways to help (using same code, bit of a hack)
    'help' => array(
        'evangelism' => array(
            'Evangelism',
            array(
                array('Twitter', 'Follow [url="https://twitter.com/composr_cms"]Composr[/url] on Twitter, and tweet about #composr.'),
                array('Become a fan of Composr [url="http://www.facebook.com/pages/Composr/80430912569"]on Facebook[/url].'),
                array('Support us on YouTube', 'Rate and comment on [url="http://www.youtube.com/results?search_query=composr"]our video tutorials[/url] on YouTube.'),
                array('Vote for Composr', 'Vote/rate and review Composr wherever you see it. [url=http://www.hotscripts.com/listing/composr/]HotScripts[/url] and [url=http://php.opensourcecms.com/scripts/details.php?scriptid=210&name=Composr]OpenSourceCMS[/url] are two places to start.'),
                array('Post about Composr', 'If you see CMSs compared on other websites and Composr isn\'t mentioned, [page="forum:topicview:browse:10312"]let us know about it[/page]!'),
                array('Tell a friend', 'Recommend Composr if a friend or your company is looking to make a website.'),
                array('Mention ocProducts', 'Mention the staff as web developers to help them bring in an income.'),
                array('Show our ad', 'You can advertise Composr via the [url="banner ad"]{$BRAND_BASE_URL}/uploads/website_specific/compo.sr/ad-banner.swf.zip[/url] we have created.'),
                array('Self-initiatives', 'Find any opportunity to share Composr with someone. Write your own article and publish it. Talk about Composr at a conference. Be creative!'),
            ),
        ),

        'skill_based' => array(
            'Skill-based',
            array(
                array('Make addons', 'If you know PHP, or are learning, [page="forum:topicview:browse:11688"]make and release some addons[/page] for the community. It takes a lot of knowledge, but anybody can learn and it\'s fun, fulfilling and makes you employable.'),
                array('Theme', 'If you know [abbr="eXtensible HyperText Markup Language"]XHTML[/abbr]/[abbr="Cascading Style Sheets"]CSS[/abbr], or are learning, [page="docs:tut_releasing_themes"]make and release some themes[/page] for the community. With CSS you can start small and still achieve cool things.'),
                array('Translate', 'If you know another language, [url="collaborate with others on Transifex"]https://www.transifex.com/organization/ocproducts/dashboard[/url] to make a new language pack.'),
                //array('Use Composr for your own clients','Are you a professional website developer? Try to start using Composr for your projects &ndash; it provides you [page="site:features"]lots of advantages[/page] to other software, it\'s free, and we want the community and install-base to grow!'),         Removed to save space
                array('Google Summer of Code', 'If you\'re a student and want to work on Composr for the [url="http://code.google.com/soc/"]Google Summer of Code[/url], please [page="site:tickets:ticket:ticket_template=general_feedback:cost=free"]contact us[/page] and we will work to try and make it happen.'),
            ),
        ),

        'our_site' => array(
            'On compo.sr',
            array(
                array('Reach out to other users', 'Particularly new members, try [page="forum:forumview:browse:198"]to welcome them[/page] and help make sure they don\'t get lost. Also [page="forum:topicview:browse:11683"][/page] so people near you can get in contact.'),
                array('Help others on the forum', 'Where you can, answer other user\'s questions.'),
                array('Give gift points', 'If you see other members doing good things, give them some gift points.'),
            ),
        ),

        'usability' => array(
            'Usability and stability',
            array(
                array('Reporting bugs', 'Big or tiny &ndash; we will be happy if you even report typos we make as bugs.'),
                array('Reporting usability issues', 'We will be happy if you have any concrete suggestions for making reasonably common tasks even a little bit easier.'),
                array('Write community tutorials', 'Post them on [page="site:wiki"]Wiki+[/page] and the staff may highlight them.'),
                array('Developing unit tests', 'If you know some PHP you can help us test Composr en-masse. Write unit tests (the latest version of this framework is in our public git repository).'),
            ),
        ),

        'money' => array(
            'Money',
            array(
                array('Sponsor a feature', 'Do you want something new implemented in Composr? Sponsor little projects listed on the [page="site:tracker"]tracker[/page] (payment is made via buying support credits).'),
            ),
        ),

        'other' => array(
            'Other',
            array(
                array('Supply test data for importers', 'Send an SQL-dump to help us create a Composr importer. There\'s no promise of anything, but it helps us a lot to have test data on hand should we decide to make an importer.'),
                array('Core developers wanted', 'If you ask and demonstrate your skillz and commitment then you can get commit privileges and be fully credited as one of the Composr developers. We just need a standard dual-copyright agreement signing.'),
                array('Other', 'Do you have some other expertise? Do you have the ability to help the staff make business connections? There are many other ways to support our mission &ndash; be imaginative!'),
            ),
        ),
    ),

    // Real features
    'browse' => array(
        'installation' => array(
            'Installation <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Installation" href="{$PAGE_LINK*,docs:tut_install}"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>',
            array(
                array('Quick installer', 'Our self-extractor allows faster uploads and will automatically set permissions'),
                array('Wizard-based installation'),
                array('Auto-scans for compatibility problems'),
                array('Get your site up and running in just a few minutes'),
                null, // divider
                array('Keep your site closed to regular visitors until you\'re happy to open it'),
                array('Configures server', 'Automatically generates a <kbd>.htaccess</kbd> file for you'),
                array('Auto-detection of forum settings for easy integration'),
            ),
        ),
        'banners' => array(
            'Banners <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Banners" href="http://shareddemo.composr.info/cms/index.php?page=cms_banners&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>',
            array(
                array('Multiple campaigns', 'Each one can specify it\'s own width-by-height (e.g. skyscraper)'),
                array('Smart banners', 'Integrate text-banners into your content via keyword detection'),
                array('Wide support', 'Accepts image banners, flash banners, external banner rotations, and text banners'),
                null, // divider
                array('Determine which banners display most often'),
                array('Run a cross-site banner network'),
                array('Hit-balancing support', 'A site on a banner network gets as many inbound hits as it provides outbound clicks'),
                array('Show different banners to different usergroups'),
                array('Track banner performance'),
                array('Use the banner system to display whole sets of sponsor logos'),
            ),
        ),
        'search' => array(
            'Search engine <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Search engine" href="http://shareddemo.composr.info/site/index.php?page=search&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>',
            array(
                array('Choose what is searchable'),
                array('Boolean and full-text modes'),
                array('Keyword highlighting in results'),
                array('Search boxes to integrate into your website'),
                null, // divider
                array('Logging/stats'),
                array('OpenSearch support', 'Allow users to search from inside their web browser'),
                array('Results sorting, and filtering by author and date'),
                array('Search within downloads', 'Including support for looking inside archives'),
            ),
        ),
        'newsletters' => array(
            'Newsletters <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Newsletters" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_newsletter&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>',
            array(
                array('Automatically create newsletters showing off your latest content'),
                array('Double opt-in', 'Prevent false sign-ups by asking subscribers to confirm their subscriptions'),
                array('Users can set their own interest level', 'From &ldquo;all updates&rdquo; to &ldquo;important updates only&rdquo;'),
                array('Host multiple newsletters'),
                array('Flexible mailings', 'Send out mailings to all members, to different usergroups, or to subscribers of specific newsletters'),
                array('Welcome e-mails', 'Send multiple welcome e-mails to new users automatically, on a configurable schedule'),
                array('Bounce cleanup', 'Automatically clean out bounces from your e-mail list'),
            ),
        ),
        'featured' => array(
            'Featured content <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Featured content" href="http://shareddemo.composr.info/site/index.php?page=featured_content"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>',
            array(
                array('Random quotes', 'Put random quotes (e.g. testimonials) into your design'),
                array('Image of the day', 'To have your website feeling fresh'),
                array('Showcase popular content', 'Automatically feature links to your most popular downloads and galleries'),
                null, // divider
                array('Recent content', 'Automatically feature links to your most recent downloads, galleries, and catalogue entries'),
                array('Show website statistics to your visitors'),
                array('Random content', 'Feature random content from your website, specified via a sophisticated filtering language'),
                array('Tags', 'Set tags for content and display tag clouds'),
            ),
        ),
        'ecommerce' => array(
            'eCommerce and subscriptions <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of eCommerce" href="http://shareddemo.composr.info/site/index.php?page=catalogues&amp;type=category&amp;id=home"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>',
            array(
                array('Paid membership', 'Sell access to sections of your website, or offer members privileges'),
                array('Shopping cart for running an online store'),
                array('Extendable framework', 'Programmers can easily add new product types to sell, or payment gateways'),
                null, // divider
                array('Multiple payment gateways', 'Accepts payments via PayPal, WorldPay, SecPay, and manual transactions (cash/cheque)'),
                array('Invoicing support', 'Including status tracking and online payment tracking'),
                array('Basic accounting support', 'Input your incoming and outgoing transactions to get basic ledger, profit-and-loss, and cashflow charting'),
                array('<abbr title="Secure Socket Layer">SSL</abbr>/<abbr title="Transport Layer Security">TLS</abbr>/HTTPS certificate support', 'Make key pages of your choice run over SSL (e.g. the join and payment pages)'),
                array('Currency conversions', 'Perform automatic currency conversions within your website pages'),
            ),
        ),
        'support' => array(
            'Support/user-to-staff messaging <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of ticket system" href="http://shareddemo.composr.info/site/index.php?page=tickets&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>',
            array(
                array('Support ticket system', 'Users can view and reply in private tickets to staff'),
                array('Recommend-to-a-friend', 'Visitors can recommend your website to other visitors'),
                array('Staff can manage feedback', 'Includes the ability for staff members to &ldquo;take ownership&rdquo; of raised issues, and for all staff to discuss them'),
                null, // divider
                array('Support ticket types', 'Set up different kinds of support ticket, with different access levels'),
                array('Allow users to e-mail in their tickets and replies'),
                array('Receive SMS alerts for important tickets'),
                array('Anonymous feedback', 'Any user can submit anonymous feedback'),
                array('Users may review your content (optional)'),
            ),
        ),
    ),
    'web20' => array(
        'polls' => array(
            'Polls <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Polls" href="http://shareddemo.composr.info/site/index.php?page=polls&amp;type=view&amp;id=what_type_of_books_do"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>',
            array(
                array('Integrate polls into your website'),
                array('Virtually cheat-proof'),
                array('Community involvement', 'Users can submit polls, and comment and rate them'),
                null, // divider
                array('Have unlimited polls'),
            ),
        ),
        'points' => array(
            'Points system',
            array(
                array('So many ways to earn points', 'From submitting different content to how active they are, you control the economy'),
                array('Point store', 'Members can buy advertising space, temporary privileges, gamble, and more! <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Point Store" href="http://shareddemo.composr.info/site/index.php?page=pointstore&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Gift system', 'Allows members to reward each other with gift points <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Points" href="http://shareddemo.composr.info/site/index.php?page=points"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Leader board', 'Create some community competition, by showing a week-by-week who has the most points <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Leaderboard" href="http://shareddemo.composr.info/site/index.php?page=leader_board&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                null, // divider
                array('Auditing'),
                array('Profiles', 'Browse through member points profiles, and see what gifts members have been given'),
            ),
            'A virtual economy for your members',
        ),
        'community' => array(
            'Community features',
            array(
                array('User submission', 'Allow users to submit to any area of your site. Staff approval is supported <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of CMS" href="http://shareddemo.composr.info/cms/index.php?page=cms&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Public awards', 'Give public awards to your choice of &ldquo;best content&rdquo; <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Awards" href="http://shareddemo.composr.info/site/index.php?page=awards&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Per-usergroup privileges', 'Give special members access to extra features, like file storage <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Permissions" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_permissions&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Bookmarks', 'Users can bookmark their favourite pages to their account <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Bookmarks" href="http://shareddemo.composr.info/site/index.php?page=bookmarks&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
            ),
        ),
        'chat' => array(
            'Chatrooms and instant messaging <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Chatrooms" href="http://shareddemo.composr.info/site/index.php?page=chat&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>',
            array(
                array('Unlimited chatrooms', 'Each with your choice of access restrictions'),
                array('Moderation', 'Moderate messages and ban troublesome users'),
                array('Integrate shout-boxes into your website'),
                array('Instant messaging', 'Members may have IM conversations with each other, or in groups'),
                array('Site-wide IM', 'Give your members the ability to pick up conversations anywhere on your site'),
                null, // divider
                array('Sound effects', 'Members may configure their own'),
                array('Programmers can write their own chat bots'),
                array('Download chat rooms logs'),
                array('Blocking', 'Choose to appear offline to certain members'),
            ),
        ),
        'msn' => array(
            'Multi-site networks',
            array(
                array('Shared membership', 'Share members between multiple Composr websites'),
                null, // divider
                array('Syndicated site list', 'Allows each member site to see an up-to-date list of sites in the network'),
                array('Staff filter', 'Choose which members are staff on which sites'),
            ),
        ),
    ),
    'content' => array(
        'catalogues' => array(
            'Catalogues <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Catalogues" href="http://shareddemo.composr.info/site/index.php?page=catalogues&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>',
            array(
                array('Unlimited data control', 'Set up multiple catalogues, each with it\'s own set of fields. There are 44 kinds of field, such as short text fields, description fields, and date fields'),
                array('Different display modes', 'Display the contents of categories using tables, boxes, or lists'),
                null, // divider
                array('Powerful structure', 'Each catalogue contains categories which contain entries. Catalogues can have a tree structure of categories and/or work from an index'),
                array('Configurable searching', 'Choose which fields are shown on categories, and which can be used to perform searches (template searches)'),
                array('Entirely customisable', 'Full support for customising catalogue categories and entries, exactly as you want them- field by field'),
                array('Classified ads', 'Entries can automatically expire and get archived. See view reports'),
                array('Community interaction', 'Allow users to comment upon and rate entries'),
                array('Import data from CSV files'),
                array('Periodic content reviews', 'Helping you ensure ongoing accuracy of your content'),
            ),
            'Think &ldquo;databases on my website&rdquo;',
        ),
        'wiki' => array(
            'Wiki+ <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Wiki+" href="http://shareddemo.composr.info/site/index.php?page=wiki&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>',
            array(
                array('Create an encyclopaedic database for your website'),
                array('Create a tree-structure, or use traditional cross-linking'),
                array('Track changes'),
                array('Display a tree structure of your whole Wiki+ (normal wiki\'s can\'t do that!)'),
                null, // divider
                array('Allow users to jump in at random pages'),
                array('Make your pages either wiki-style or topic-style'),
            ),
            'Think &ldquo;structured wikis&rdquo;',
        ),
        'calendar' => array(
            'Calendar <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Calendar" href="http://shareddemo.composr.info/site/index.php?page=calendar"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>',
            array(
                array('Behaves like you\'d expect', 'Day/week/month/year views'),
                array('Advanced &ldquo;recurring event&rdquo; settings'),
                array('Event reminders'),
                array('Detect conflicting events'),
                null, // divider
                array('Microformats support'),
                array('Integrate a calendar month view, or an upcoming events view, onto your design'),
                array('Different event types'),
                array('Sophisticated permissions'),
                array('Priority flagging'),
                array('Programmers can even use the calendar to schedule website cronjobs'),
                array('<abbr title="Really Simple Syndication">RSS</abbr> and Atom support', 'Export support, but also support for overlaying news feeds onto the calendar'),
            ),
        ),
        'news' => array(
            'News and blogging <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of News" href="http://shareddemo.composr.info/site/index.php?page=news&amp;type=select"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>',
            array(
                array('Member blogs', 'Allow members to have their own blogs <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of News" href="http://shareddemo.composr.info/cms/index.php?page=cms_blogs"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('<abbr title="Really Simple Syndication">RSS</abbr> and Atom support', 'Export and import feeds'),
                array('Trackback support', 'Send and receive trackbacks'),
                array('Scheduled publishing'),
                null, // divider
                array('Ping support and <abbr title="Really Simple Syndication">RSS</abbr> Cloud support'),
                array('Multiple news categories, and filtering'),
                array('Chicklet support'),
                array('Multiple ways to integrate news into your website'),
                array('Import from RSS feeds <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of News" href="http://shareddemo.composr.info/cms/index.php?page=cms_news"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Easily syndicate to Facebook and Twitter'),
            ),
        ),
        'quizzes' => array(
            'Quizzes <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Galleries" href="http://shareddemo.composr.info/site/index.php?page=quiz"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>',
            array(
                array('Run a competition', 'Give members a chance to win'),
                array('Surveys', 'Gather data and find trends'),
                array('Tests', 'Allow members to take tests'),
                array('Cheat prevention', 'Settings to prevent cheating'),
            ),
        ),
        'galleries' => array(
            'Galleries <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Galleries" href="http://shareddemo.composr.info/site/index.php?page=galleries&amp;type=browse&amp;id=root&amp;root=root"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>',
            array(
                array('Supports images, videos, and audio'),
                array('Personal galleries', 'Allow your members to create their own galleries'),
                //array('Transcoding', 'Upload any video format, have the server convert it'),  Optimistic, transcoding can be a PITA
                array('Support for linking to YouTube videos', 'Save on bandwidth'),
                null, // divider
                array('Auto-detection of video length and resolution (most file formats)'),
                array('Full tree-structure support'),
                array('Two different display modes'),
                array('e-cards'),
                array('Slide-shows'),
                array('Easily syndicate to Facebook'),
                array('Automatic thumbnail generation'),
                array('Import and export easily', 'With <kbd>.zip</kbd> and metadata support'),
                array('Optional watermarking', 'To guard against thieving swines ;)'),
            ),
        ),
        'downloads' => array(
            'Downloads database <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Downloads" href="http://shareddemo.composr.info/site/index.php?page=downloads&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>',
            array(
                array('Great organisation', 'Uses a tree structure for unlimited categorisation'),
                array('&lsquo;Sell&rsquo; downloads using website points'),
                array('Anti-leech protection'),
                array('Community-centered', 'Allow users to comment upon and rate downloads'),
                null, // divider
                array('Many ways to add new files', 'Upload files. Link-to existing files. Copy existing files using a live URL. Batch import links from existing file stores'),
                array('Author support', 'Assign your downloads to authors, so users can find other downloads by the same author'),
                array('Licences', 'Make users agree to a licence before downloading'),
                array('Images', 'Show images along with your downloads (e.g. screen-shots)'),
                array('Basic file versioning support'),
            ),
        ),
        'pages' => array(
            'Page support <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Page support" href="http://shareddemo.composr.info/site/index.php?page=comcode_page"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>',
            array(
                array('Add unlimited pages'),
                array('<abbr title="What You See Is What You Get">WYSIWYG</abbr> editor'),
                array('Convenient edit links', 'Staff see &ldquo;edit this&rdquo; links at the bottom of every page'),
                array('PHP support', 'Upload your PHP scripts and run them inside Composr (may require adjustments to the script code)'),
            ),
        ),
    ),
    'architecture' => array(
        'debranding' => array(
            'Debranding <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Debranding" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_debrand&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>',
            array(),
            'Use Composr for clients and pretend <strong>you</strong> made it',
        ),
        'permissions' => array(
            'Permissions <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Permissions" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_permissions&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>',
            array(
                array('Detailed privilege control', 'Over 150 permissions'),
                array('Control access to all your resources'),
                array('User-friendly permissions editor'),
                null, // divider
                array('Create addition access controls based on URL'),
                array('Customise your permission error messages'),
            ),
        ),
        'nav' => array(
            'Structure and navigation',
            array(
                array('Visually browse your site structure', 'Intuitive sitemap editor <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Sitemap Editor" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_sitemap&amp;type=sitemap"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Menu editor', 'Our user friendly editor can work with 7 different kinds of menu design (drop-downs, tree menus, pop-ups, etc) <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Menus" href="http://shareddemo.composr.info/index.php?page=menus"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Zones (sub-sites)', 'Organise your pages into separate zones. Zones can have different menus, themes, permissions, and content <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Zones" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_zones&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                null, // divider
                array('Full structural control', 'Edit, move, and delete existing pages'),
                array('Redirects', 'Set up redirects if you move pages, or if you want pages to appear in more than one zone <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Redirects" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_redirects&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
            ),
        ),
        'extendable' => array(
            'Extendable and programmable',
            array(
                array('Versatile', 'You can strip down to a core system, or build up with 3rd-party addons <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Addons" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_addons&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Full <abbr title="Application Programming Interface">API</abbr> documentation <a target="_blank" class="link_exempt no_print" title="(Opens in new window) API documentation" href="{$BRAND_BASE_URL}/docs/api/"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('High coding standards', 'No PHP notices. Type-strict codebase. We use <abbr title="Model View Controller">MVC</abbr>'),
                array('Free online developers guide book <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Developers Documentation" href="{$PAGE_LINK*,docs:cookbook}"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                null, // divider
                array('Custom field filters', 'For example, restrict news posts to a minimum length'),
                array('Stack dumps for easy debugging'),
                array('Synchronise data between staging and live sites using XML <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of XML transfer tool" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_xml_storage&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
            ),
        ),
        'integration' => array(
            'Integration and conversion',
            array(
                array('Convert from other software', 'See our <a href="' . escape_html($importing_tutorial_url) . '">importing tutorial</a> for a list of importers'),
                array('Use an existing member system', 'See our <a href="' . escape_html($sites_url) . '">download page</a> for a list of forum drivers'),
                array('Convert an <abbr title="HyperText Markup Language">HTML</abbr> site into Composr pages'),
                array('LDAP support for corporate networks (<abbr title="The Composr forum">Conversr</abbr>) <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Help of LDAP usage" href="{$PAGE_LINK*,docs:tut_ldap}"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                null, // divider
                array('HTTP authentication', 'Tie into an existing HTTP authentication-based login system (<abbr title="The Composr forum">Conversr</abbr>)'),
                array('Proxying system', 'Programmers can integrate any existing scripts using our sophisticated proxying system (which includes full cookie support)'),
                array('Minimodules and miniblocks', 'Programmers can port existing PHP code into Composr itself <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Framework documentation" href="{$PAGE_LINK*,docs:tut_framework}"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Export your Comcode as <abbr title="eXtensible HyperText Markup Language">XML</abbr>', 'Other systems may integrate your rich content, using (for example) <abbr title="XSL transformations">XSLT</abbr>'),
            ),
        ),
    ),
    'design' => array(
        'adminzone' => array(
            'Administration Zone',
            array(
                array('Status overview', 'Upgrade and task notification from the Admin Zone front page <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Admin Zone" href="http://shareddemo.composr.info/adminzone/index.php?page=start&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Backups', 'Create and schedule full and incremental backups, local or remote <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Backups" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_backup&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Analytics', 'Website statistics rendered as charts <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Statistics" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_stats&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Conflict detection', 'Detect when two staff are trying to change the same thing at the same time'),
                array('Examine audit trails', 'See exactly who has done what and when <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Audit Trails" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_actionlog&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Commandr', 'Optional use of a powerful command-line environment (for Unix geeks). Use unix-like tools to explore and manage your database as it if was a filesystem, and perform general maintenance tasks'),
                array('Aggregate content types', 'Design complex content relationships, cloning out large structures in a single operation.'),
                null, // divider
                array('Configurable access', 'Restrict to no/partial/full access based on usergroup'),
                array('Configure a staff roster, for an automatically created staff page <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Staff" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_staff&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Detect broken URLs <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Cleanup Tools" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_cleanup&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Content versioning <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Page Versioning" href="http://shareddemo.composr.info/cms/index.php?page=cms_comcode_pages&amp;type=_edit&amp;page_link=:start"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
            ),
        ),
        'tools' => array(
            'Themeing tools',
            array(
                array('Theme Wizard: pick a colour, let Composr do the work', 'Recolour all your <abbr title="Cascading Style Sheets">CSS</abbr> and images in just a few clicks (Composr picks the perfect complementary palette and automatically makes 100\'s of CSS and image changes) <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Theme Wizard" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_themewizard&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Built-in template and <abbr title="Cascading Style Sheets">CSS</abbr> editing tools <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Theme Tools" href="http://shareddemo.composr.info/adminzone/index.php?page=admin&amp;type=style"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Quick-start logo wizard <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Logo Wizard" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_themewizard&amp;type=make_logo"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Interactive CSS editor', 'Quickly identify what to change and preview'),
            ),
        ),
        'barriers' => array(
            'Design without barriers',
            array(
                array('Full control of your vision', 'Control hundreds of settings. Strip Composr down. Reshape features as needed'),
                array('Full templating support', 'Reskin features so things look however you want them to'),
                array('No navigation assumptions', 'Replace default page and structures as required'),
                null, // divider
                array('No layout assumptions', 'Shift content between templates, totally breaking down any default layout assumptions'),
                array('Embed content entries of any type on your pages'),
            ),
        ),
        'tempcode' => array(
            'Template programming language (Tempcode) <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Tempcode" href="{$PAGE_LINK*,docs:tut_tempcode}"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>',
            array(
                array('Perform computations', 'Run loops, manipulate logic, numbers, and text'),
                array('Handy effects', 'Easily create design effects like &ldquo;Zebra striping&rdquo; and tooltips &ndash; and much more'),
                array('Branching and filtering', 'Tailor output according to permissions and usergroups, as well as user options such as language selection'),
                null, // divider
                array('Include other templates, blocks, or pages, within a template'),
                array('Create and use standard boxes', 'Avoid having to copy and paste complex segments of <abbr title="eXtensible HyperText Markup Language">XHTML</abbr>'),
                array('Easy web browser sniffing', 'Present different markup to different web browsers, detect whether JavaScript is enabled, detect bots, and detect PDAs/Smartphones'),
                array('Randomisation features'),
                array('Pull up member details with ease', 'For example, show the current users avatar or point count'),
                array('Easily pull different banner rotations into your templates'),
            ),
        ),
        'rad' => array(
            '<abbr title="Rapid Application Development">RAD</abbr> and testing tools',
            array(
                array('Switch users', 'Masquerade as any user using your admin logging <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of SU" href="http://shareddemo.composr.info/index.php?keep_su=test"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Change theme images inline with just a few clicks'),
                array('Easily find and edit the templates used to construct any screen <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Template Tree" href="http://shareddemo.composr.info/index.php?special_page_type=tree"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Error monitoring', 'Get informed by e-mail if errors ever happen on your site'),
                null, // divider
                array('Make changes to content titles inline'),
                array('Easy text changes', 'Easily change the language strings used to build up any screen'),
                array('Easily diagnose permission configuration problems', 'Log permission checks, or interactively display them in Firefox'),
                array('Developers tool to add and manage test sets'),
            ),
        ),
        'richmedia' => array(
            'Rich media <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Comcode" href="http://shareddemo.composr.info/index.php?page=rich"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>',
            array(
                array('Comcode', 'Powerful but simple content-enrichment language'),
                array('Media embedding', 'Easily integrate/attach all common video and image formats, as well as embeds for common sites such as Youtube and Google Maps (just by pasting in the URL)'),
                array('Easily create cool effects', 'Create scrolling, rolling, randomisation, and hiding effects. Put content in boxes, split content across subpages. Create <abbr title="eXtensible HyperText Markup Language">XHTML</abbr> overlays. Place tooltips'),
                array('Customise your content for different usergroups'),
                array('Create count-downs and hit counters'),
                array('Automatic table of contents creation for your documents'),
                array('Custom Comcode tags', 'Set up your own tags, to make it easy to maintain a sophisticated and consistent design as your site grows <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Custom Comcode" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_custom_comcode&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Include pages within other pages'),
            ),
        ),
    ),
    'standards' => array(
        'security' => array(
            'Security',
            array(
                array('Configurable swear filtering <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Word Filter" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_wordfilter&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('IP address tools', 'Audit, check, and ban them <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Lookup Tools" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_lookup"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('<abbr title="Completely Automated Public Turing test to tell Computers and Humans Apart">CAPTCHA</abbr>, to stop spammers'),
                array('Automatic detection and banning of hackers <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of IP Banning" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_ip_ban&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Architectural approaches to combat all major exploit techniques'),
                array('Defence-in-depth', 'Multiple layers of built-in security'),
                array('<abbr title="Cross-Site scripting">XSS</abbr> protection', 'Developed using unique technology to auto-detect XSS security holes before the software gets even released'),
                null, // divider
                array('Track failed logins <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Security" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_security&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Integrate with known-spammer blacklists'),
                array('Published e-mail addresses will be protected from spammers'),
                array('Protection from spammers trying to use your website for their own <abbr title="Search Engine Optimisation">SEO</abbr>'),
                array('<abbr title="HyperText Markup Language">HTML</abbr> filtering'),
                array('Protection against <abbr title="Cross-Site Request-Forgery">CSRF</abbr> attacks', 'You can temporarily &lsquo;Concede&rsquo; your admin access for added protection'),
                array('Root-kit detection kit for developers (developers developers!)'),
            ),
        ),
        'easeofuse' => array(
            'Ease of use <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Ease of use" href="{$BRAND_BASE_URL}/docs/"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>',
            array(
                array('Professionally designed user interfaces'),
                array('<abbr title="Asynchronous JavaScript And XML">AJAX</abbr> techniques', 'Streamlined website interaction'),
                array('<abbr title="What You See Is What You Get">WYSIWYG</abbr> editing'),
                array('Tutorials', 'Over 200 written tutorials, and a growing collection of video tutorials'),
                array('Displays great on mobiles', 'Mobile browsers can be automatically detected, or the user can select the mobile version from the footer. All public website features work great on <abbr title="Quarter VGA, a mobile display size standard">QVGA</abbr> or higher.'),
                array('A consistent and fully integrated feature-set', 'Breadcrumb navigation, previews, and many other features we didn\'t have space to mention here &ndash; are all present right across Composr'),
            ),
        ),
        'performance' => array(
            'Performance',
            array(
                array('Highly optimised code'),
                array('Support for <acronym title="Content Delivery Networks">CDN</acronym>s'),
                null, // divider
                array('Multiple levels of caching'),
                array('Sophisticated template compiler'),
            ),
        ),
        'webstandards' => array(
            'Web standards <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Web standards" href="{$PAGE_LINK*,site:vision}"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>',
            array(
                array('Support for all major web browsers'),
                array('True and correct <abbr title="eXtensible HyperText Markup Language">XHTML5</abbr> markup'),
                array('<abbr title="Web Content Accessibility Guidelines">WCAG</abbr>, <abbr title="Authoring Tool Accessibility Guidelines">ATAG</abbr>', 'Meeting of accessibility guidelines in full'),
                array('Tableless <abbr title="Cascading Style Sheets">CSS</abbr> markup, with no hacks'),
                null, // divider
                array('Inbuilt tools for checking webstandards conformance of <abbr title="eXtensible HyperText Markup Language">XHTML</abbr>, <abbr title="Cascading Style Sheets">CSS</abbr>, and JavaScript'),
                array('Extra markup semantics, including Dublin Core support and microformats'),
                array('Standards-based (modern <abbr title="Document Object Model">DOM</abbr> and <abbr title="Asynchronous JavaScript And XML">AJAX</abbr>, no DOM-0 or innerHTML) JavaScript'),
                array('Automatic cleanup of bad <abbr title="eXtensible HyperText Markup Language">XHTML</abbr>', 'XHTML outside your control (e.g. from <abbr title="Really Simple Syndication">RSS</abbr>) will be cleaned up for you'),
            ),
        ),
        'itln' => array(
            'Localisation support <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Localisation" href="https://www.transifex.com/organization/ocproducts/dashboard"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>',
            array(
                array('Translate Composr into your own language'),
                array('Translate content into multiple languages'),
                null, // divider
                array('Custom time and date formatting'),
                array('Language packs', 'Download new language packs as users post them'),
                array('Timezone support', 'Members may choose their own timezones'),
                array('Host multiple languages on your website at the same time'),
                array('Support for different character sets, and Unicode'),
                array('Serve different theme images for different languages'),
                array('Support for right-to-left languages'),
            ),
        ),
        'seo' => array(
            '<acronym title="Search Engine Optimisation">SEO</acronym> <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of SEO" href="http://shareddemo.composr.info/index.php?page=sitemap"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>',
            array(
                array('Support for short URLs'),
                array('Automatic site-map generation'),
                array('META data', 'Meta descriptions and keywords for all content. Auto-summarisation.'),
                array('XML Sitemaps'),
                null, // divider
                array('Keyword density analysis for your content'),
                array('Correct use of HTTP status codes'),
                array('Correct use of page titles'),
                array('<abbr title="Search Engine Optimisation">SEO</abbr> via semantic and accessible markup (e.g. &lsquo;alt tags&rdquo;)'),
            ),
        ),
    ),
    'forums' => array(
        'cnsmembers' => array(
            'Membership',
            array(
                array('Multiple usergroups', 'Members can be in an unlimited number of different usergroups. They can also &lsquo;apply&rsquo; to join new ones <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Usergroups" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_cns_groups&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Social networking', 'Create and browse friendships'),
                array('Custom profile fields', 'Allow your members to add extra information which is relevant to your website (or to their subcommunity) <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Custom Profile Fields" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_cns_customprofilefields&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Promotion system', 'Members can &lsquo;advance the ranks&rsquo; by earning points'),
                array('Punishment system', 'Warnings, probation, and silencing of members from forums/topics <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Punishments" href="http://shareddemo.composr.info/site/index.php?page=warnings&amp;type=add&amp;id=3&amp;post_id=11"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Personal topics between 2 or more members', 'Better than the basic personal messages most forum software provides <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Personal Topics" href="http://shareddemo.composr.info/forum/index.php?page=forumview&amp;type=pt&amp;id=2"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                null, // divider
                array('Invitation-only websites', 'Existing members can invite others <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Recommendation &ndash; the demo does not have invites turned on though" href="http://shareddemo.composr.info/index.php?recommend"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Allow members to create and manage a club <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Clubs" href="http://shareddemo.composr.info/cms/index.php?page=cms_cns_groups&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Over 40 bundled avatars', 'Member\'s may also upload or link to their own <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Avatars" href="http://shareddemo.composr.info/site/index.php?page=members&type=view#tab__edit__avatar"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Member signatures, photos, and personal titles <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Personal Zone" href="http://shareddemo.composr.info/site/index.php?page=members&type=view#tab__edit"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Profiles', 'Browse through and search for members, and view member profiles <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Member Directory" href="http://shareddemo.composr.info/site/index.php?page=members&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Member e-mailing', 'Allow your members to e-mail other members through your website <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Member Contact Form" href="http://shareddemo.composr.info/site/index.php?page=contact_member&amp;type=browse&amp;id=3"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Users online', 'See which members are currently online, unless they logged in as invisible <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Online Members" href="http://shareddemo.composr.info/site/index.php?page=users_online"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('IP address lock-down', 'Extra (but optional) security for staff groups'),
                array('Welcome e-mails', 'Set up multiple &ldquo;welcome e-mails&rdquo; to be sent out to new members on a schedule <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Welcome E-mails" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_cns_welcome_emails"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Account pruning', 'Find and delete unused accounts, merge duplicate accounts <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Account Pruning" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_cns_members&amp;type=delurk"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Encrypted custom profile fields'),
                array('Members may set privacy settings for individual fields <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Privacy Settings" href="http://shareddemo.composr.info/site/index.php?page=members&type=view#tab__edit__privacy"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('CSV files', 'Import and export members using CSV files, including support for automatic creation of custom profile fields and usergroups &ndash; great for migrating data <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Member CSV Import" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_cns_members&amp;type=import_csv"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
            ),
            'Conversr-only',
        ),
        'cnsforum' => array(
            'Forums',
            array(
                array('The usual stuff', 'Categories, forums, topics, posts, polls <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Forum" href="http://shareddemo.composr.info/forum/index.php?page=topicview&amp;id=general_chat%2Fhere_is_a_topic_with_a"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Forum and Topic tracking', 'Get alerted by e-mail when new posts are made'),
                array('Password-protected forums <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Protected Forum" href="http://shareddemo.composr.info/forum/index.php?page=forumview&amp;id=feedback"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Full moderator control', 'Determine who may moderate what forums'),
                array('Report posts', 'When users report a post, a topic is created for the staff to discuss it <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Post Reporting" href="http://shareddemo.composr.info/forum/index.php?page=forumview&amp;id=reported_posts_forum"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Inline personal posts', 'Whisper to members within a public topic <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Whispering" href="http://shareddemo.composr.info/forum/index.php?page=topicview&amp;type=findpost&amp;id=6#post_6"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Over 50 bundled emoticons', 'Also, support for batch importing new ones <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Emoticons" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_cns_emoticons"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Multi-moderation', 'Record and perform complex routine tasks <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Multi Moderation" href="http://shareddemo.composr.info/adminzone/index.php?page=admin_cns_multi_moderations"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                null, // divider
                array('Announcements <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Announcement" href="http://shareddemo.composr.info/forum/index.php?page=topicview&amp;id=this_topic_acts_as_an"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Quick reply'),
                array('Post/topic moderation and validation'),
                array('Unlimited sub-forum depth'),
                array('Mass-moderation', 'Perform actions on many posts and topics at once'),
                array('Post templates', 'Use your forum as a database for record gathering <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Post Templates" href="http://shareddemo.composr.info/adminzone/index.php?admin_cns_post_templates"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Post preview', 'Read a topics first post directly from the forum-view'),
                array('Records post edit/delete history <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Post History" href="http://shareddemo.composr.info/adminzone/index.php?admin_cns_history"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Highlight posts as &lsquo;important&rsquo; <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Highlighted Posts" href="http://shareddemo.composr.info/forum/index.php?page=topicview&amp;type=findpost&amp;id=13#post_13"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>', 'Your posts will be <a href="http://www.youtube.com/watch?v=lul-Y8vSr0I" target="_blank" title="(Opens in new window)">high as a kite by then</a>'),
            ),
            'Conversr-only',
        ),
        'tracking' => array(
            'Stay on top of things',
            array(
                array('Find posts made since you last visited <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of New Posts" href="http://shareddemo.composr.info/forum/index.php?page=vforums&amp;type=browse"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Remembers your unread posts', 'Even if you frequently change computers <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Unread Posts" href="http://shareddemo.composr.info/forum/index.php?page=vforums&amp;type=unread"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('<abbr title="Really Simple Syndication">RSS</abbr> and Atom support <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of RSS Feeds" href="http://shareddemo.composr.info/backend.php"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Recent activity', 'See what topics you recently read or posted in <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of Recent Posts" href="http://shareddemo.composr.info/forum/index.php?page=vforums&amp;type=recently_read"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
            ),
            'Conversr-only',
        ),
        'forumdrivers' => array(
            'Forum integration',
            array(
                array('Support for popular products', 'See our <a href="' . escape_html($sites_url) . '">download page</a> for a list of supported forums'),
                array('Share login credentials', 'Login with the same usernames/passwords'),
                array('Share usergroups', 'Control website access based on someone\'s usergroup'),
                array('Emoticon support', 'The emoticons on your forum will also be used on your website. Your members will be happy little <a href="http://www.youtube.com/watch?v=XC73PHdQX04" target="_blank" title="(Opens in new window)">hobbits</a>'),
            ),
            'If integrating a third-party product',
        ),
        'forumcontentsharing' => array(
            'Content sharing',
            array(
                array('Show forum topics on your website', 'Great if you have a &lsquo;news&rsquo; forum <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of displayed forum topics" href="http://shareddemo.composr.info/site/index.php?page=featured_content"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
                array('Comment integration', 'New topics appear in the &lsquo;comments&rsquo; forum as you add content to your website. Members can watch these topics so they never miss an addition to your website <a target="_blank" class="link_exempt no_print" title="(Opens in new window) Example of comment topics" href="http://shareddemo.composr.info/forum/index.php?page=forumview&amp;id=website_comment_topics"><img class="inline_image_3" alt="" src="{$IMG*,help_small}" /></a>'),
            ),
        ),
    ),
);

$collapsed_tree = array();
foreach ($featuretree as $t) {
    $collapsed_tree += $t;
}

$raw = (isset($map['raw'])) && ($map['raw'] == '1');

// Columns
if (!$raw) {
    echo '<div class="feature_columns float_surrounder_hidden">' . "\n";
}
foreach (($map['param'] == '') ? array() : explode(',', $map['param']) as $i => $column) {
    if (!$raw) {
        echo '<div class="column column' . strval($i) . '">';
    }

    // Subsections in column
    foreach (explode('|', $column) as $subsection_code) {
        if (!isset($collapsed_tree[$subsection_code])) {
            fatal_exit('Missing: ' . $subsection_code);
        }
        $subsection = $collapsed_tree[$subsection_code];

        if (!$raw) {
            echo '<div class="subsection">' . "\n\n";
        }

        // Icon and title
        echo '<div class="icon_and_title">' . "\n\n";
        $subsection_title = $subsection[0];
        require_code('tempcode_compiler');
        $subsection_title = comcode_to_tempcode('[semihtml]' . $subsection_title . '[/semihtml]', null, true);
        $subsection_title = $subsection_title->evaluate();
        $subsection_items = $subsection[1];
        $s_img = find_theme_image('composr_homesite/features/' . preg_replace('#[^\w]#', '', $subsection_code), true);
        if ($s_img != '') {
            echo '<img alt="" src="' . $s_img . '" />' . "\n\n";
        }
        if (!$raw) {
            echo '<h3>' . $subsection_title . '</h3>' . "\n\n";
            echo '</div>' . "\n\n";
        }

        // Subsection caption, if there is one
        if (array_key_exists(2, $subsection)) {
            $subsection_caption = $subsection[2];
        } else {
            $subsection_caption = '';
        }
        if ((!is_null($subsection_caption)) && ($subsection_caption != '')) {
            echo '<p class="subsection_caption">' . $subsection_caption . '.</p>';
        }

        // List
        if (count($subsection_items) != 0) {
            echo '<div><ul class="main">';
            $see_more = false;
            foreach ($subsection_items as $item) {
                if (is_null($item)) { // Divider
                    if (has_js()) {
                        echo '</ul></div>' . "\n\n";
                        $see_more = true;
                        echo '<div class="moree"><ul class="more">';
                    }
                } else {
                    $item[0] = comcode_to_tempcode('[semihtml]' . $item[0] . '[/semihtml]', null, true);
                    $item[0] = $item[0]->evaluate();
                    if (array_key_exists(1, $item)) {
                        $item[1] = comcode_to_tempcode('[semihtml]' . $item[1] . '[/semihtml]', null, true);
                        $item[1] = $item[1]->evaluate();
                    }

                    echo '<li>';
                    echo '<span class="itemtitle">' . $item[0] . '</span>';
                    if (array_key_exists(1, $item)) {
                        if ((substr($item[1], -1) != '!') && (substr($item[1], -1) != '?') && (substr($item[1], -1) != '.')) {
                            $item[1] .= '.';
                        }
                        echo '<span class="itemdescription">' . $item[1] . '</span>';
                    }
                    echo '</li>';
                }
            }
            echo '</ul></div>';
            if ($see_more) {
                echo '<p class="button"><a class="seemore" href="#" onclick="toggle_seemore(this); return false;">See more</a></p>'/*."\n\n"*/;
            }
        }

        echo '</div>' . "\n\n";
    }

    if (!$raw) {
        echo '</div>' . "\n\n";
    }
}
if (!$raw) {
    echo '</div>' . "\n\n";
}
