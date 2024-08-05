<?php
/**
 * Template Name: Contact Page
 */
wp_enqueue_style('bootstrap');
wp_enqueue_script('bootstrap');
get_header();
?>
<div class="contact-page-wrapper w-100">
<?php
the_content();
?>
</div>
<?php get_footer();
?>