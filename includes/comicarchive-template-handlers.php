<?php
/**
 * mangapress
 * 
 * @package comicarchive-template-handlers
 * @author Jess Green <jgreen at psy-dreamer.com>
 * @version $Id$
 * @license GPL
 */

/**
 * Template handler for Latest Comic end-point
 *
 * @global WP $wp
 * @param string $template
 * @return string
 */
function mangapress_comicarchive_template($template)
{
    global $wp;

    if (!$wp->did_permalink) {
        return $template;
    }

    if (strpos($wp->matched_rule, 'past-comics') !== false) {
        $template = locate_template(array('comics/comic-archive.php', 'comics/past-comics.php'));
        return $template;
    }

    return $template;
}


/**
 * Template handler for Latest Comic page
 *
 * @param string $template Default template if requested template is not found
 * @return string
 */
function mangapress_comicarchive_page_template($template)
{
    if (!mangapress_is_queried_page('comicarchive_page')) {
        return $template;
    }

    $template = locate_template(array('comics/comic-archive.php'));

    // if template can't be found, then look for query defaults...
    if (!$template) {
        add_filter('the_content', 'mangapress_create_comicarchive_page');
        return get_page_template();
    } else {
        return $template;
    }
}


/**
 * Add comic archive output to Comic Archive page content
 * 
 * @access private
 * @param string $content Page content being filtered
 * @return string
 */
function mangapress_create_comicarchive_page($content)
{
    global $post;

    if (!mangapress_is_queried_page('comicarchive_page')) {
        return $content;
    }
    
    $wp_query = mangapress_get_all_comics_for_archive();

    if (!$wp_query){
        return apply_filters(
            'the_comicarchive_content_error',
            '<p class="error">No comics were found.</p>'
        );
    }
    
}

function mangapress_pre_get_posts(WP_Query $query)
{
    if (!did_action('_mangapress_pre_archives_get_posts')) {
        return $query;
    }
    
}
add_filter('pre_get_posts', 'mangapress_pre_get_posts');