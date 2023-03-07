<?php ?>
<form role="search" method="get" class="search-form" action="<?php echo home_url( '/' ); ?>">
  <label style="display: inline-flex">
    <!-- <input type="submit" class="button" value="<#?php echo esc_attr_x( '&#128269;', 'submit button' ) ?>" /> -->
    <button type="submit" style="cursor:pointer; width: 50px;">
      <img src="<?php echo get_template_directory_uri(); ?>/assets/images/magnifying-glass.svg" alt="Search icon" style="width:25px;"/>
    </button>
    <span class="screen-reader-text"><?php echo _x( 'Search for:', 'label' ) ?></span>
    <input type="search" class="search-field"
      placeholder="<?php echo esc_attr_x( 'Search...', 'placeholder' ) ?>"
      value="<?php echo get_search_query() ?>" name="s"
      title="<?php echo esc_attr_x( 'Search for:', 'label' ) ?>"
    />
  </label>
</form>
