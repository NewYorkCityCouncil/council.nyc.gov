<?php ?>
<form role="search" method="get" class="search-form" action="<?php echo home_url( '/' ); ?>">
  <label>
    <span class="screen-reader-text"><?php echo _x( 'Search for:', 'label' ) ?></span>
    <input type="search" class="search-field"
      placeholder="<?php echo esc_attr_x( 'Search this site', 'placeholder' ) ?>"
      value="<?php echo get_search_query() ?>" name="s"
      title="<?php echo esc_attr_x( 'Search for:', 'label' ) ?>"
    />
  </label>
  <input type="submit" class="button" style="width:100%;" value="<?php echo esc_attr_x( 'Search', 'submit button' ) ?>" />
</form>
