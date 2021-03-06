<?php
	// About template for Caldera URL Builder
	$video = '//youtu.be/tI-CJeGsONY';
	if ( is_ssl() ) {
		$video = 'https:'.$video;
	}else{
		$video = 'http:'.$video;
	}

	$video = wp_oembed_get( $video );
?>
<div id="caldera-url-builder-about">
	<p>Thank you for purchasing Caldera URL Builder: the WordPress Visual Permalink Editor.</p>
	<iframe width="100%" height="450" src="https://www.youtube.com/embed/tI-CJeGsONY?rel=0&amp;controls=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>

	<h2>Get Support</h2>

		<p>If you are a paying customer, and have activated your license, you can get support at <a href="https://calderawp.com/support/" title="Contact Support.">calderawp.com/suppor/</a>.</p>

	<h2><a href="http://calderawp.com" title="CalderaWP">Learn More About CalderaWP</a></h2>
		<p>At CalderaWP, we believe that for the developers, site owners, and the site’s end user a like, delivering an excellent user experience is what is most important. We provide easy to use plugins that make complex WordPress tasks easy.</p>


	<!-- Begin MailChimp Signup Form -->
	<link href="//cdn-images.mailchimp.com/embedcode/classic-081711.css" rel="stylesheet" type="text/css">
	<style type="text/css">
		#mc_embed_signup{background:#fff; clear:left; font:14px Helvetica,Arial,sans-serif; }
	</style>
	<div id="mc_embed_signup">
		<form action="//CalderaWP.us10.list-manage.com/subscribe/post?u=e8aeee202b02c1fe9eab2037c&amp;id=f402a6993d" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
			<div id="mc_embed_signup_scroll">
				<h3>Subscribe to our mailing list</h3>
				<div class="indicates-required"><span class="asterisk">*</span> indicates required</div>
				<div class="mc-field-group">
					<label for="mce-EMAIL">Email Address  <span class="asterisk">*</span>
					</label>
					<input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL">
				</div>
				<div class="mc-field-group">
					<label for="mce-FNAME">First Name </label>
					<input type="text" value="" name="FNAME" class="" id="mce-FNAME">
				</div>
				<div class="mc-field-group">
					<label for="mce-LNAME">Last Name </label>
					<input type="text" value="" name="LNAME" class="" id="mce-LNAME">
				</div>
				<div id="mce-responses" class="clear">
					<div class="response" id="mce-error-response" style="display:none"></div>
					<div class="response" id="mce-success-response" style="display:none"></div>
				</div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
				<div style="position: absolute; left: -5000px;"><input type="text" name="b_e8aeee202b02c1fe9eab2037c_f402a6993d" tabindex="-1" value=""></div>
				<div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
			</div>
		</form>
	</div>

	<!--End mc_embed_signup-->

</div>
