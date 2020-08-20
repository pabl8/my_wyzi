<?php if ( ! empty( $args['menu'] ) ) :?>

    <ul class="X__sidebar-menu">
        <?php foreach( $args['menu'] as $menu_slug => $menu ) :

            $menu_link = '?action='.esc_attr( $menu_slug ).'';

            if ( isset( $_REQUEST['mode'] ) && ! empty( $_REQUEST['mode'] ) ) {
                $menu_link = '?mode=profile&action='.esc_attr( $menu_slug ).'';
            }

        ?>
            <li <?php if ( $args['current_action'] == $menu_slug ) : ?>class="active"<?php endif;?>>
                <a href="<?php echo esc_url( $args['post_link'] ); ?><?php echo $menu_link; ?>">
                    <i class="<?php echo esc_attr( $menu['icon'] ); ?>"></i><span><?php echo esc_html( $menu['label'] ); ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

<?php endif; ?>