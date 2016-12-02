<?php
/**
 * Displays the searchform of the theme.
 *
 * @package ThemeGrill
 * @subpackage Esteem
 * @since Esteem 1.0
 */
?>
<form action="<?php echo esc_url( home_url( '/' ) ); ?>" id="search-form" class="searchform clearfix" method="get">
	<div class="search-wrap">
		<input type="text" class="input_search" placeholder="Suchen" class="s field" name="s">
		<button type="submit"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/lupe.png" /></button>
	</div>
	<input type="submit" value="<?php esc_attr_e( 'Search', 'esteem' ); ?>" id="search-submit" name="submit" class="submit">
</form><!-- .searchform -->