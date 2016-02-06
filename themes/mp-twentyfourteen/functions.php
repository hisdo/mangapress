<?php
/**
 * @package Manga_Press_Templates
 * @subpackage Functions
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */

add_action('wp_enqueue_scripts', 'mangapress_theme_load_twentyfourteen_css');
/**
 * Load the stylesheet from the TwentyEleven Theme
 * 
 * @return void
 */
function mangapress_theme_load_twentyfourteen_css()
{
    $src = get_template_directory_uri() . '/style.css';
    wp_register_style('twentyfourteen', $src, null, MP_VERSION);
    
    wp_enqueue_style('twentyfourteen');
}