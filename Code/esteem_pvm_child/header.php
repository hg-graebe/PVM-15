<?php
/**
 * Theme Header Section for our theme.
 *
 * @package ThemeGrill
 * @subpackage Esteem
 * @since Esteem 1.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<style>
		.profile_img_top_container {
			position: relative;
			width: 100px;
			height: 100px;
			overflow: hidden;
			border-radius: 50%;
			-webkit-border-radius: 50%;
			-moz-border-radius: 50%;
			border: 3px solid black;
		}

		.profile_img_top_container img {
			position: absolute;
			left: 50%;
			top: 50%;
			height: 100%;
			width: auto;
			-webkit-transform: translate(-50%,-50%);
			-ms-transform: translate(-50%,-50%);
			transform: translate(-50%,-50%);
		}

		.profile_img_top_container img.profile_img_top {
			width: 100%;
			height: auto;
		}
	</style>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php
/**
 * This hook is important for wordpress plugins and other many things
 */
wp_head();

$pvm_is_project = false;
$meproject_templates = array( 'pvm_view_all_projects_template.php', 'pvm_view_single_project_template.php' );
if ( in_array( basename( get_page_template() ), $meproject_templates ) ) {
	$pvm_is_project = true;
}
?>
</head>

<body <?php body_class(); ?>>
	<div id="page" class="hfeed site<?php if ( $pvm_is_project ) { echo ' pvm_project'; } ?>">
		<header id="masthead" class="site-header" role="banner">
			<div class="inner-wrap">
				<div class="hgroup-wrap clearfix">
					<div class="site-branding">
						<?php if( ( get_theme_mod( 'esteem_show_header_logo_text', 'text_only' ) == 'both' || get_theme_mod( 'esteem_show_header_logo_text', 'text_only' ) == 'logo_only' ) && get_theme_mod( 'esteem_header_logo_image', '' ) != '' ) {
						?>
							<div class="header-logo-image">
								<?php if( basename( get_page_template() ) != 'pvm_view_all_packages_template.php' ) {
											$logo = 'logo_inactive.png';
										} else {
											$logo = 'logo_active.png';
										} 
								?>
								<a rel="home" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>">
	<img class="logo_img" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" src="<?php echo get_stylesheet_directory_uri(); ?>/img/<?php echo $logo; ?>">
	</a>
							</div><!-- .header-logo-image -->
						<?php }

                  $screen_reader = '';
						if( get_theme_mod( 'esteem_show_header_logo_text', 'text_only' ) == 'logo_only' || get_theme_mod( 'esteem_show_header_logo_text', 'text_only' ) == 'none' ) {
                        $screen_reader = 'screen-reader-text';
                  }
						?>
							<div class="header-text <?php echo $screen_reader; ?>">
                        <?php if ( is_front_page() || is_home() ) : ?>
   								<h1 id="site-title">
   									<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
   										<?php bloginfo( 'name' ); ?>
   									</a>
   								</h1>
                        <?php else : ?>
                           <h3 id="site-title">
                              <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
                                 <?php bloginfo( 'name' ); ?>
                              </a>
                           </h3>
                        <?php endif; ?>
                        <?php
                        $description = get_bloginfo( 'description', 'display' );
                        if ( $description || is_customize_preview() ) : ?>
   								<p class="site-description"><?php echo $description; ?></p>
                        <?php endif; ?>
							</div><!-- .header-text -->
					</div><!-- .site-branding -->
					<div class="hgroup-wrap-right">
						<nav id="site-navigation" class="main-navigation" role="navigation">
							<h3 class="menu-toggle"></h3>
							<div class="nav-menu clearfix">
								<?php
									if ( has_nav_menu( 'primary' ) ) {
										if ( $pvm_is_project ) {
											wp_nav_menu( array( 'menu' => 'ProjectMenu', 'container' => false ) );
										} else {
											wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false ) );
										}
									} else {
										wp_page_menu();
									}
								?>
							</div><!-- .nav-menu -->
						</nav><!-- #site-description -->
					</div><!-- .hgroup-wrap-right -->
						<?php 
						if ( ! is_user_logged_in() ) {
							add_thickbox(); 
							?>
							<div id="thickbox_login" style="display:none;">
								<div class="loginform_top_label">
									<p class="loginform_top_label_left">Login</p>
									<p class="loginform_top_label_right"><a href="<?php echo 'http://pvm.uni-leipzig.de/registrierung/'; /*wp_registration_url()*/ ?> ">registrieren</a></p>						
								</div>
								<script type="text/javascript">
									jQuery(document).ready(function(){
										jQuery('#user_login').attr('placeholder', 'Benutzername');
										jQuery('#user_pass').attr('placeholder', 'Passwort');
									});
								</script>
							<?php
								$args = array(
						      	'redirect' => admin_url(), 
						         'form_id' => 'loginform_top',
						      	'label_username' => __( '' ),
						      	'label_password' => __( '' ),
						      	'label_remember' => __( '' ),
						      	'label_log_in' => __( '' ),
						      	'remember' => false
							   );
							   wp_login_form( $args );
							?>
							</div>
							<a href="#TB_inline?width=250&height=255&inlineId=thickbox_login" class="thickbox"><img class="login_top" src="<?php echo get_stylesheet_directory_uri(); ?>/img/login_button.png" /></a>
							<?php
						} else {
							$author = new pvmkit_author( get_current_user_id(), false );
							?>
							<div class="login_top profile_img_top_container"><a href="<?php echo $author->get_profile_url(); ?>"><img class="profile_img_top" src="<?php echo $author->get_profile_image_url(); ?>" /></a></div>
							<?php
						} 
						?>
				</div><!-- .hgroup-wrap -->
			</div><!-- .inner-wrap -->
			<?php esteem_render_header_image(); ?>

			<?php
   			if( get_theme_mod( 'esteem_activate_slider', '0' ) == '1' ) {
				if ( is_front_page() ) {
   					esteem_slider();
				}
   			}

			$esteem_slogan 				= get_theme_mod('esteem_slogan');
			$esteem_sub_slogan		 		= get_theme_mod('esteem_sub_slogan');
			$esteem_button_text 			= get_theme_mod('esteem_button_text');
			$esteem_button_redirect_link 	= get_theme_mod('esteem_button_redirect_link');
   			if ( is_front_page() && !empty( $esteem_slogan) && !empty( $esteem_sub_slogan ) ) { ?>
	   			<section id="promo-box">
	   				<div class="inner-wrap clearfix">
	   					<div class="promo-wrap">
	   						<?php if ( !empty( $esteem_slogan ) ) { ?>
			   					<div class="promo-title">
			   						<?php echo esc_html( $esteem_slogan ); ?>
			   					</div>
			   				<?php  } ?>

			   				<?php if ( !empty( $esteem_sub_slogan ) ) { ?>
			   					<div class="promo-text">
			   						<?php echo esc_html( get_theme_mod('esteem_sub_slogan') ); ?>
			   					</div>
			   				<?php  } ?>
		   				</div><!-- .promo-wrap -->
		   				<?php if ( !empty( $esteem_button_text ) && !empty( $esteem_button_redirect_link ) ) { ?>
	   					<a class="promo-action" title="<?php echo esc_attr( $esteem_button_text ); ?>" href="<?php echo esc_url( $esteem_button_redirect_link ); ?>"><?php echo esc_html( $esteem_button_text ); ?></a>
	   					<?php  } ?>
	   				</div>
	   			</section><!-- #promo-box -->
	   		<?php }
		   	if( !( is_front_page()) ) { ?>
				<section class="page-title-bar clearfix">
					<div class="inner-wrap">
						<?php if( '' != esteem_header_title() ) { ?>
                  <?php if ( is_home() ) : ?>
							<div class="page-title-wrap"><h2><?php echo esteem_header_title(); ?></h2></div>
                  <?php else : ?>
                     <div class="page-title-wrap"><h1><?php echo esteem_header_title(); ?></h1></div>
                  <?php endif; ?>
						<?php } ?>
						<?php if( function_exists( 'esteem_breadcrumb' ) ) { esteem_breadcrumb(); } ?>
					</div>
				</section>
			<?php } ?>
		</header><!-- #masthead -->
		<div id="main" class="site-main inner-wrap">