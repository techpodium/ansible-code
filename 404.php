<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 */

get_header(); ?>

	<div class="l-content">
		<?php if ( ! Utilities::is_mobile() ) { ?>
		<section class="l-top-content">
			<div class="adunit">
				<?php
					get_template_part( 'ads/leaderboard', 'top' );
				?>
			</div>
		</section>
		<?php } ?>
		<div class="l-constrained site-container contentbody">
			<div class="l-main no-sidebar" id="main">

				<section class="error-404 not-found">

					<div class="page-content">

						<figure style="text-align: center;" id="monkey">
							<img src="<?php echo esc_url( PN_URL ) . '/images/monkey.jpg'; ?>" alt="Bananas">
							<div id="bananas">Oh bananas! I can't find the page you're looking for!</div>
						</figure>
						<p id="luck">
							You might have better luck searching our site, or starting at our home page
						</p>
						<form class="search" name="search" method="get" action="/?" id="search">
							<input type="text" name="s" placeholder="Search <?php echo ('fp' == SITE_ID ) ? 'FinancialPost.com ...' : 'NationalPost.com ...' ?>">
							<button type="submit" value="" id="search-btn" class="search-btn">
								<span class="search-icon"></span>
							</button>
						</form>
						<div id="links">
							<a href="/contact" >Report a Broken Link</a><span class="bullet-404"> • </span><a href="<?php echo esc_url( home_url() ) ?>">Go to the Home Page</a>
						</div>

						<div id="posts-in-category" class="content">
							<?php
								pn_other_posts_in_list( null, Utilities::is_mobile() ? 4 : 6, '404', 'zone', 'hp-top-stories', 'Top Stories' );
							?>
						</div>
					</div><!-- .page-content -->
				</section><!-- .error-404 -->
			</div>
		</div>
		<!-- / container -->
	</div>
	<!-- / main -->

<?php get_footer();
