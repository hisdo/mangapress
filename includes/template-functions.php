<?php
/**
 * Manga+Press Template functions
 *
 * @todo Update docblocks
 *
 * @package Manga_Press
 * @subpackage Manga_Press_Template_Functions
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */

/**
 * is_comic()
 *
 * Used to detect if post contains a comic.
 * @since 0.1
 *
 * @global object $wpdb
 * @global array $mp_options
 * @global object $post
 * @return bool Returns true if post contains a comic, false if not.
 */
if (!function_exists('is_comic')) {
    function is_comic($post = null)
    {
        if (is_integer($post)) {
            $post = get_post($post);
        }

        if (is_null($post)) {
            global $post;
        }

        $post_type = get_post_type($post);

        return ($post_type == 'mangapress_comic');
    }
}


/**
 * @since 1.0 RC1
 *
 * @global WP_Query $wp_query
 * @return bool
 */
if (!function_exists('is_comic_page')) {
    function is_comic_page()
    {
        global $wp_query;

        $mp_options = MangaPress_Bootstrap::get_instance()->get_options();

        $query      = $wp_query->get_queried_object();

        return ($wp_query->is_page && ($query->post_name == $mp_options['basic']['latestcomic_page']));

    }
}


/**
 *
 * @since 1.0 RC1
 *
 * @global WP_Query $wp_query
 * @return bool
 */
if (!function_exists('is_comic_archive_page')) {
    function is_comic_archive_page()
    {
        global $wp_query;

        $mp_options = MangaPress_Bootstrap::get_instance()->get_options();

        $query      = $wp_query->get_queried_object();

        $is_comic_archive_page
            = ($wp_query->is_page && ($query->post_name
                                        == $mp_options['basic']['comicarchive_page']));

        return $is_comic_archive_page;

    }
}


/**
 * Retrieve the previous post in The Loop. We have our reasons
 *
 * @global WP_Query $wp_query
 * @return WP_Post|false
 */
function mangapress_get_previous_post_in_loop()
{
    global $wp_query;

    if ($wp_query->current_post == -1 || $wp_query->current_post == 0) {
        return false;
    }

    return $wp_query->posts[$wp_query->current_post - 1];
}


/**
 * Get the next post in the loop. Might come in handy.
 *
 * @global WP_Query $wp_query
 * @return WP_Post|false
 */
function mangapress_get_next_post_in_loop()
{
    global $wp_query;

    if ($wp_query->current_post == ($wp_query->found_posts - 1)) {
        return false;
    }

    return $wp_query->posts[$wp_query->current_post + 1];
}


/**
 * Get comic term ID.
 * 
 * @param WP_Post|int $post WordPress post object or post ID
 * @return false|int
 */
function mangapress_get_comic_term_ID($post = 0)
{
    if ($post === false) {
        return false;
    }

    $post = get_post($post);
    if (!isset($post->term_ID)) {
        return false;
    }

    return $post->term_ID;
}



/**
 * Get comic slug
 * 
 * @param WP_Post|int $post WordPress post object or post ID
 * @return false|string
 */
function mangapress_get_comic_term_title($post = 0)
{
    $post = get_post($post);
    if (!isset($post->term_name)) {
        return false;
    }

    return $post->term_name;
}


/**
 * mangapress_comic_navigation()
 *
 * Displays navigation for post specified by $post_id.
 *
 * @since 0.1b
 *
 * @global object $wpdb
 * @param WP_Query $query Query for post object or page.
 * @param array $args Arguments for navigation output
 * @param bool $echo Specifies whether to echo comic navigation or return it as a string
 * @return string Returns navigation string if $echo is set to false.
 */
function mangapress_comic_navigation($args = array(), $echo = true)
{
    global $post;

    $mp_options = MangaPress_Bootstrap::get_instance()->get_options();

    $defaults = array(
        'container'      => 'nav',
        'container_attr' => array(
            'id'    => 'comic-navigation',
            'class' => 'comic-nav-hlist-wrapper',
        ),
        'items_wrap'     => '<ul%1$s>%2$s</ul>',
        'items_wrap_attr' => array('class' => 'comic-nav-hlist'),
        'link_wrap'      => 'li',
        'link_before'    => '',
        'link_after'     => '',
    );

    $parsed_args = wp_parse_args($args, $defaults);
    $r = apply_filters('mangapress_comic_navigation_args', $parsed_args);
    $args = (object) $r;

    $group = (bool)$mp_options['basic']['group_comics'];
    $by_parent = (bool)$mp_options['basic']['group_by_parent'];

    $next_post  = mangapress_get_adjacent_comic($group, $by_parent, 'mangapress_series', false, false);
    $prev_post  = mangapress_get_adjacent_comic($group, $by_parent, 'mangapress_series', false, true);
    add_filter('pre_get_posts', '_mangapress_set_post_type_for_boundary');
    $last_post  = mangapress_get_boundary_comic($group, $by_parent, 'mangapress_series', false, false);
    $first_post = mangapress_get_boundary_comic($group, $by_parent, 'mangapress_series', false, true);
    remove_filter('pre_get_posts', '_mangapress_set_post_type_for_boundary');
    $current_page = $post->ID; // use post ID this time.

    $next_page = !isset($next_post->ID) ? $current_page : $next_post->ID;
    $prev_page = !isset($prev_post->ID) ? $current_page : $prev_post->ID;
    $last      = !isset($last_post[0]->ID) ? $current_page : $last_post[0]->ID;
    $first     = !isset($first_post[0]->ID) ? $current_page : $first_post[0]->ID;

    $first_url = get_permalink($first);
    $last_url  = get_permalink($last);
    $next_url  = get_permalink($next_page);
    $prev_url  = get_permalink($prev_page);

    $show_container = false;
    $comic_nav      = "";
    if ( $args->container ) {

        $show_container = true;
        $attr           = "";
        if (!empty($args->container_attr)) {
            $attr_arr = array();
            foreach ($args->container_attr as $name => $value) {
                $attr_arr[] = "{$name}=\"" . esc_attr($value) . "\"";
            }

            $attr = " " . implode(" ", $attr_arr);
        }

        $comic_nav .= "<{$args->container}$attr>";
    }

    $items_wrap_attr = "";
    if (!empty($args->items_wrap_attr)) {
        $items_attr_arr = array();
        foreach ($args->items_wrap_attr as $name => $value) {
            $items_attr_arr[] = "{$name}=\"" . esc_attr($value) . "\"";
        }

        $items_wrap_attr = " " . implode(" ", $items_attr_arr);
    }

    $items = array();

    // Here, we start processing the urls.
    // Let's do first page first.
    $first_html = "<{$args->link_wrap}>" . ( ($first == $current_page)
                ? '<span class="comic-nav-span">' . __('First', 'mangapress') . '</span>'
                : '<a href="' . $first_url . '">' . __('First', 'mangapress') . '</a>' )
             . "</{$args->link_wrap}>";

    $last_html = "<{$args->link_wrap}>" .
                ( ($last == $current_page)
                    ? '<span class="comic-nav-span">' . __('Last', 'mangapress') . '</span>'
                    : '<a href="' . $last_url . '">'. __('Last', 'mangapress') . '</a>')
                . "</{$args->link_wrap}>";

    $next_html = "<{$args->link_wrap}>" . ( ($next_page == $current_page)
                ? '<span class="comic-nav-span">' . __('Next', 'mangapress') . '</span>'
                : '<a href="' . $next_url . '">'. __('Next', 'mangapress') . '</a>' )
            . "</{$args->link_wrap}>";

    $prev_html = "<{$args->link_wrap}>" . ( ($prev_page == $current_page)
                ? '<span class="comic-nav-span">' . __('Prev', 'mangapress') . '</span>'
                : '<a href="' . $prev_url . '">'. __('Prev', 'mangapress') . '</a>' )
            . "</{$args->link_wrap}>";

    $items['first'] = apply_filters('mangapress_comic_navigation_first', $first_html, $args);
    $items['prev']  = apply_filters('mangapress_comic_navigation_prev', $prev_html, $args);
    $items['next']  = apply_filters('mangapress_comic_navigation_next', $next_html, $args);
    $items['last']  = apply_filters('mangapress_comic_navigation_last', $last_html, $args);

    $items_str      = implode(" ", apply_filters( 'mangapress_comic_navigation_items', $items, $args ));

    $comic_nav .= sprintf( $args->items_wrap, $items_wrap_attr, $items_str );

    if ($show_container){
        $comic_nav .= "</{$args->container}>";
    }

    if ($echo){
        echo $comic_nav;
    } else {
        return $comic_nav;
    }

}
