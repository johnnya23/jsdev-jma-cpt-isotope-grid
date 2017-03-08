<?php

echo '<div class="specialty-inner">' ;
echo '<h3><a href="' . get_permalink() . '" class="featured-image tb-thumb-link post" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>' ;
/*if ( has_post_thumbnail() ){
    echo '<a href="' . get_permalink() . '" class="featured-image tb-thumb-link post" title="' . get_the_title() . '">';
    echo the_post_thumbnail( $thumb_size );
    echo '</a>';
}*/

    echo '</div>';