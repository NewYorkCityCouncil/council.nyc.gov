<style>
  .image-header::before {
    background-image: url("<?php the_post_thumbnail_url( 'large' ); ?>");
  }
  /* small retina */
  @media only screen and (-webkit-min-device-pixel-ratio: 2), only screen and (min--moz-device-pixel-ratio: 2), only screen and (-o-min-device-pixel-ratio: 2 / 1), only screen and (min-device-pixel-ratio: 2), only screen and (min-resolution: 192dpi), only screen and (min-resolution: 2dppx) {
    .image-header::before {
      background-image: url("<?php the_post_thumbnail_url( 'large' ); ?>");
    }
  }
  /* medium */
  @media only screen and (min-width: 40.0625em) {
    .image-header::before {
      background-image: url("<?php the_post_thumbnail_url( 'xlarge' ); ?>");
    }
  }
  /* medium retina */
  @media only screen and (min-width: 40.0625em) and (-webkit-min-device-pixel-ratio: 2), only screen and (min-width: 40.0625em) and (min--moz-device-pixel-ratio: 2), only screen and (min-width: 40.0625em) and (-o-min-device-pixel-ratio: 2 / 1), only screen and (min-width: 40.0625em) and (min-device-pixel-ratio: 2), only screen and (min-width: 40.0625em) and (min-resolution: 192dpi), only screen and (min-width: 40.0625em) and (min-resolution: 2dppx) {
    .image-header::before {
      background-image: url("<?php the_post_thumbnail_url( 'xlarge' ); ?>");
    }
  }
  /* large */
  @media only screen and (min-width: 64.0625em) and (-webkit-min-device-pixel-ratio: 2), only screen and (min-width: 64.0625em) and (min--moz-device-pixel-ratio: 2), only screen and (min-width: 64.0625em) and (-o-min-device-pixel-ratio: 2 / 1), only screen and (min-width: 64.0625em) and (min-device-pixel-ratio: 2), only screen and (min-width: 64.0625em) and (min-resolution: 192dpi), only screen and (min-width: 64.0625em) and (min-resolution: 2dppx) {
    .image-header::before {
      background-image: url("<?php the_post_thumbnail_url(); ?>");
    }
  }
</style>
