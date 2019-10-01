<?php

$district_contact = get_option('council_district_contact');
$legislative_contact = get_option('council_legislative_contact');
$email = get_option('council_district_email');
$contact_form = get_option('council_district_contact_form');
$subscribe_form = get_option('council_district_subscribe_form');
$siteurl = get_site_url();
$site_id = get_current_blog_id();

echo '<div class="callout">';

if ( $district_contact ) { ?><div aria-label="District office contact information"><h2 class="widget-title">District Office</h2><p class="text-small"><?php echo nl2br( $district_contact ); ?></p></div><?php }

if ( $legislative_contact ) { ?><div aria-label="Legislative office contact information"><h2 class="widget-title">Legislative Office</h2><p class="text-small"><?php echo nl2br( $legislative_contact ); ?></p></div><?php }

if ( $email ) { ?><a aria-label="Send an email to Council Member <?php echo get_option('council_member_name'); ?>" href="mailto:<?php echo $email; ?>" class="button secondary expanded dashicons-before dashicons-email-alt">&nbsp;Send&nbsp;Email<br class="show-for-large"><small class="show-for-xlarge" style="font-size:0.5em;"><?php echo $email; ?></small></a><?php }

if ( $contact_form ) {
    ?>
    <div class="reveal" id="contact_form" data-reveal>
      <h2 class="header-small">Send a message to <?php echo get_option('council_member_name'); ?></h2>
      <?php echo do_shortcode($contact_form); ?>
      <button class="close-button" data-close aria-label="Close modal" type="button"><span aria-hidden="true">&times;</span></button>
    </div>
    <a<?php if ($is_main) {?> href="<?php echo $siteurl; ?>?contact=message"<?php } else { ?> data-open="contact_form"<?php } ?> class="button secondary expanded dashicons-before dashicons-admin-comments">&nbsp;Get&nbsp;Assistance</a>
    <?php
}

if ( $subscribe_form ) {
    ?>
    <div class="reveal" id="subscribe_form" data-reveal>
      <h2 class="header-small">Subscribe to updates from <?php echo get_option('council_member_name'); ?></h2>
      <?php echo do_shortcode($subscribe_form); ?>
      <button class="close-button" data-close aria-label="Close modal" type="button"><span aria-hidden="true">&times;</span></button>
    </div>
    <a<?php if ($is_main) {?> href="<?php echo $siteurl; ?>?contact=subscribe"<?php } else { ?> data-open="subscribe_form"<?php } ?> class="button secondary expanded dashicons-before dashicons-email-alt">&nbsp;Subscribe</a>
    <?php
}

echo '</div>';
