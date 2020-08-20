<?php defined('ABSPATH') OR die('restricted access');

    $user_filter_id = 'to';
    $user_meta_key = 'message_receiver_id';
?>

<div class="X__wrapper">
    <div class="X__sidebar">
        <?php require_once $base_directory . '/views/menu.php'; ?>
    </div>

    <div class="X__content" id="pm-actions">

        <div class="X__controls">
            <ul>
                <li>
                    <a class="XM__control" href="#" data-action="delete">
                        <i class="fa fa-trash"></i><?php esc_html_e('Move to Trash', 'wyzi-business-finder'); ?>
                    </a>
                </li>
            </ul>
        </div>

        <?php if ( isset( $args['posts'] ) && $args['posts']->have_posts() ) : ?>
            <div class="XB__content">
                <table class="XB__table">
                    <thead>
                        <tr>
                            <th class="xm-select"><input type="checkbox" data-action="selectall"></th>
                            <th class="xm-subject"><?php esc_html_e('Subject', 'wyzi-business-finder'); ?></th>
                            <th class="xm-sender"><?php esc_html_e('To', 'wyzi-business-finder'); ?></th>
                            <th class="xm-date"><?php esc_html_e('Date', 'wyzi-business-finder'); ?></th>
                        </tr>
                    </thead>

                    <tbody id="pm-list">

                        <?php while( $args['posts']->have_posts() ) : $args['posts']->the_post(); ?>

                            <?php

                            $post_id = get_the_ID();

                            $already_read = true;

                            if ( 'inbox' == $args['current_action'] ) {
                                $already_read = get_post_meta( $post_id, 'read_status', true );
                            }

                            
                            $author_id = get_the_author_meta('ID'); 
                            $sender_id = get_post_meta( $post_id, 'message_sender_id', true );
                            $receiver_id = get_post_meta( $post_id, 'message_receiver_id', true );
                            
                            $message_user_id = ( $receiver_id == $current_user_id ) ? $sender_id : $receiver_id;
                            ?>

                            <tr class="xm-row">
                                <td><input type="checkbox" name="messages[]" value="<?php the_ID(); ?>" /></td>
                                
                                <td>
                                    <div class="xm-sender-title">
                                        <?php
                                        $title_link = '?action=read&id='.get_the_ID().'';

                                        if ( isset( $_REQUEST['mode'] ) && ! empty( $_REQUEST['mode'] ) ) {
                                            $title_link = '?mode=profile&action=read&id='.get_the_ID().'';
                                        } ?>
                                        <a href="<?php echo esc_url( $args['post_link'] ); ?><?php echo $title_link; ?>">
                                            <?php if ( $already_read ) : ?>
                                                <?php echo esc_html( get_the_title() ); ?>
                                            <?php else : ?>
                                                <strong><?php echo esc_html( get_the_title() ); ?></strong>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                </td>
                                
                                <td>
                                    <?php
                                    $user_link = '?action='.esc_attr( $args["current_action"] ).'&'.$user_filter_id.'='.$message_user_id.'';

                                    if ( isset( $_REQUEST['mode'] ) && ! empty( $_REQUEST['mode'] ) ) {
                                        $user_link = '?mode=profile&action='.esc_attr( $args["current_action"] ).'&'.$user_filter_id.'='.$message_user_id.'';
                                    }
                                    ?>

                                    <a href="<?php echo esc_url( $args['post_link'] ); ?><?php echo $user_link; ?>">

                                        <?php $user_info = get_userdata($message_user_id);
                                        
                                        echo $user_info->user_login; ?>
                                    </a>
                                </td>

                                <td>
                                    <div class="xm-date-title">
                                        <?php the_time( get_option( 'date_format' ) ); ?>
                                    </div>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="X__placeholder">
                <?php esc_html_e( 'There is no message available', 'wyzi-business-finder' ); ?>
            </div>
        <?php endif; ?>
    </div>
</div>