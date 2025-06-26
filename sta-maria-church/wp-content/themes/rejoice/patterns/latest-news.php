<?php
/**
 * Title: Latest News
 * Slug: rejoice/latest-news
 * Categories: rejoice, latest-news
 */
?>

<!-- wp:group {"style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"60px","bottom":"60px","left":"20px","right":"20px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:60px;padding-right:20px;padding-bottom:60px;padding-left:20px"><!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide"><!-- wp:group {"className":"section_head","layout":{"type":"constrained"}} -->
<div class="wp-block-group section_head"><!-- wp:heading {"textAlign":"center","level":4,"style":{"typography":{"fontStyle":"normal","fontWeight":"400","fontSize":"20px"},"elements":{"link":{"color":{"text":"var:preset|color|primary"}}}},"textColor":"primary","fontFamily":"worksans"} -->
<h4 class="wp-block-heading has-text-align-center has-primary-color has-text-color has-link-color has-worksans-font-family" style="font-size:20px;font-style:normal;font-weight:400"><?php esc_html_e('From The Blog','rejoice'); ?></h4>
<!-- /wp:heading -->

<!-- wp:heading {"textAlign":"center","style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"fontSize":"section-title"} -->
<h2 class="wp-block-heading has-text-align-center has-section-title-font-size" style="margin-top:var(--wp--preset--spacing--20)"><?php esc_html_e('Latest News Blog Articles & Tips','rejoice'); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:query {"queryId":35,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"exclude","inherit":false},"metadata":{"categories":["posts"],"patternName":"core/query-standard-posts","name":"Standard"}} -->
<div class="wp-block-query"><!-- wp:post-template {"layout":{"type":"grid","columnCount":3,"minimumColumnWidth":null}} -->
<!-- wp:group {"className":"shadow","style":{"border":{"radius":"32px"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group shadow" style="border-radius:32px"><!-- wp:group {"className":"pos-relative","layout":{"type":"constrained"}} -->
<div class="wp-block-group pos-relative"><!-- wp:post-featured-image {"isLink":true,"align":"wide","style":{"border":{"radius":{"topLeft":"12px","topRight":"12px"}}}} /-->

<!-- wp:group {"className":"ln-post-date","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group ln-post-date" style="margin-top:0;margin-bottom:0"><!-- wp:group {"style":{"spacing":{"padding":{"top":"5px","bottom":"5px","left":"15px","right":"15px"}},"elements":{"link":{"color":{"text":"var:preset|color|white"}}},"border":{"radius":"8px"},"typography":{"letterSpacing":"0.5px"}},"backgroundColor":"primary","textColor":"white","fontSize":"small","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-white-color has-primary-background-color has-text-color has-background has-link-color has-small-font-size" style="border-radius:8px;padding-top:5px;padding-right:15px;padding-bottom:5px;padding-left:15px;letter-spacing:0.5px"><!-- wp:post-date {"format":"j M"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"30px","bottom":"30px","left":"30px","right":"30px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:30px;padding-right:30px;padding-bottom:30px;padding-left:30px"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group"><!-- wp:post-terms {"term":"category"} /-->

<!-- wp:post-author {"showAvatar":false,"isLink":true} /--></div>
<!-- /wp:group -->

<!-- wp:post-title {"isLink":true,"style":{"typography":{"fontSize":"24px","letterSpacing":"1px","lineHeight":"1.4"},"spacing":{"margin":{"top":"var:preset|spacing|30"}}}} /-->

<!-- wp:post-excerpt {"excerptLength":16,"style":{"typography":{"lineHeight":"1.8"}}} /-->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"style":{"border":{"radius":"50px"},"spacing":{"padding":{"left":"18px","right":"18px","top":"8px","bottom":"8px"}},"typography":{"fontStyle":"normal","fontWeight":"500","textTransform":"uppercase","letterSpacing":"1px"}},"fontSize":"small"} -->
<div class="wp-block-button has-custom-font-size has-small-font-size" style="font-style:normal;font-weight:500;letter-spacing:1px;text-transform:uppercase"><a class="wp-block-button__link wp-element-button" style="border-radius:50px;padding-top:8px;padding-right:18px;padding-bottom:8px;padding-left:18px"><?php esc_html_e('Read More','rejoice'); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->