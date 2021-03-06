<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package nicetheme
 */

?>

	<footer id="colophon" class="site-footer">

        <nav id="secondary-navigation" class="secondary-navigation">
            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Secondary Menu', 'nicetheme' ); ?></button>
            <?php
            wp_nav_menu(
                array(
                    'theme_location' => 'menu-2',
                    'menu_id'        => 'secondary-menu',
                )
            );
            ?>
        </nav><!-- #secondary-navigation -->

		<div class="site-info">
			<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'nicetheme' ) ); ?>">
				<?php
				/* translators: %s: CMS name, i.e. WordPress. */
				printf( esc_html__( 'Proudly powered by %s', 'nicetheme' ), 'WordPress' );
				?>
			</a>
			<span class="sep"> | </span>
				<?php
				/* translators: 1: Theme name, 2: Theme author. */
				printf( esc_html__( 'Theme: %1$s by %2$s.', 'nicetheme' ), 'nicetheme', '<a href="https://github.com/cp3402-students/cp3402-2021-site-a2-sp23-cp3402-2021-team01">Team 1</a>' );
				?>
            <span class="contact-details">
            <span class="sep"> | </span>
            <a href="https://www.facebook.com/laughtonleadlight">Facebook</a>
            <span class="sep"> | </span>
            <a href="https://www.instagram.com/laughtonleadlight">Instagram</a>
            <span class="sep"> | </span>
            <a href="https://www.pinterest.com.au/laughtonleadlight/_created/">pinterest</a>
            </span>
        </div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
