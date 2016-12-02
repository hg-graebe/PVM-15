<?php

//Import Stylesheet of parent theme
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}

//Register sidebar for filter-widget
register_sidebar ( array (
    		'name' => 'All Packages Sidebar',
    		'id'   => 'all_pkg_sidebar',
    		'description'   => 'This sidebar is used to display the filter widget for package selection.',
    		'before_widget' => '<div id="%1$s">',
    		'after_widget'  => '</div>',
    		'before_title'  => '<h3>',
    		'after_title'   => '</h3>'
) );

//Register sidebar for package_meta-widget
register_sidebar ( array (
    		'name' => 'Single Package Sidebar',
    		'id'   => 'single_pkg_sidebar',
    		'description'   => 'This sidebar is used to display the meta on the single package page.',
    		'before_widget' => '<div id="%1$s">',
    		'after_widget'  => '</div>',
    		'before_title'  => '<h3>',
    		'after_title'   => '</h3>'
) );

//URL-rewrite fix
function prefix_url_rewrite_templates() {
 
    if ( get_query_var( 'filters' ) ) {
        add_filter( 'template_include', function() {
            return get_stylesheet_directory() . '/pvm_view_all_packages_template.php';
        });
    }

}
 
add_action( 'template_redirect', 'prefix_url_rewrite_templates' );

//Register footer menu
register_nav_menu( 'footer_menu', 'Footer Navigation Menu' );

?>