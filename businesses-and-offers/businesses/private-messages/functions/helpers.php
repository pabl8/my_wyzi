<?php defined('ABSPATH') or die('restricted access');

function private_message_attachments_list( $message_id, $is_comment = false ) {
    if ( ! intval( $message_id ) ) {
        return null;
    }

    if ( $is_comment )
        $attachment_id = get_comment_meta( $message_id, 'attachment_id', true);
    else
        $attachment_id = get_post_meta( $message_id, 'attachment_id', true);

    $attachment_hash = get_post_meta( $attachment_id, 'attachment_hash', true );

    if ( ! $attachment_hash ) return;

    $attachment_link = get_the_guid( $attachment_id );

    $url_parts = explode('/', $attachment_link );

    end( $url_parts );

    $attachment_name = current( $url_parts );

    printf(
        '<div class="attacment"><a href="%s"><i class="fa fa-paperclip"></i>%s</a></div>',
        esc_url( site_url( "?download_attachment=$attachment_hash" ) ),
        esc_html( $attachment_name )
    );

}

if ( ! function_exists( 'private_message_user_display_name' ) ) :

function private_message_user_display_name( $userId )
{
    $displayName = get_the_author_meta( 'display_name', $userId );

    if ( $displayName ) {
        return $displayName;
    }

    return private_message_get_user_display_name( $userId );
}

endif;

if ( ! function_exists( 'private_message_get_user_display_name' ) )
{
    function private_message_get_user_display_name( $user )
    {
        if ( ! is_object( $user ) && intval( $user ) ) {
            $user = get_userdata( $user );
        }

        if ( ! $user ) {
            return null;
        }

        $user_id = $user->ID;

        $display_name = get_the_author_meta( 'display_name', $user_id );

        if ( ! $display_name ) {
            $display_name = implode(' ', array(
                    get_user_meta( $user_id, 'first_name', true ),
                    get_user_meta( $user_id, 'last_name', true )
                ) );
        }

        return esc_html( $display_name );
    }
}

function private_message_button( $atts )
{
    $atts = shortcode_atts(

                array(
                    'receiver_id' => 'post_author',
                    'button_text' => esc_html__('Send Message', 'wyzi-business-finder')
                ),

                $atts,
                'private_message_button'
            );

    // send message to post author - receiver_id = post_author
    if ( 'post_author' == $atts['receiver_id'] ) {
        $atts['receiver_id'] = get_the_author_meta( 'ID' );
    }

    if ( ! intval( $atts['receiver_id'] ) ) {
        return null;
    }

    return sprintf(
            '<a href="#send_message" class="btn-primary" data-action="send_private_message" data-receiver-id="%d">%s</a>',
            $atts['receiver_id'],
            esc_html( $atts['button_text'] )
        );
}

add_shortcode( 'private_message_button', 'private_message_button' );

function private_message_inbox( $atts )
{
    if ( ! is_user_logged_in() ) {
        die(
            esc_html__('You must login to access this page.', 'wyzi-business-finder')
        );
    }

    $post_id = get_the_ID();
    $current_user_id = get_current_user_id();

    $args = array(
        'menu'  => array(
                'inbox'     => array(
                                    'label' => esc_html__('Inbox', 'wyzi-business-finder'),
                                    'icon'  => 'fa fa-envelope'
                                ),

                'sent_items'    => array(
                                    'label' => esc_html__('Sent', 'wyzi-business-finder'),
                                    'icon'  => 'fa fa-paper-plane'
                                ),

                'trash'         => array(
                                    'label' => esc_html__('Trash', 'wyzi-business-finder'),
                                    'icon'  => 'fa fa-trash'
                                ),

                // 'blocked'    => array(
                //                  'label' => esc_html__('Blocked Users', 'wyzi-business-finder'),
                //                  'icon'  => 'fa fa-user-times'
                //              )
            ),

        'post_id'   => $post_id,
        'post_link' => get_the_permalink( $post_id ),
    );

    $base_directory = dirname( plugin_dir_path( __FILE__ ) );

    $allowed_actions = array(
        'inbox', 'sent_items', 'read', 'trash', 'blocked'
    );

    $args['current_action'] = 'inbox';

    if ( isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], $allowed_actions ) ) {
        $args['current_action'] = $_REQUEST['action'];
    }

    if ( isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], $allowed_actions ) ) : ?>
        <script type="text/javascript">
            jQuery(document).ready(function(){

                // Tabs
                setTimeout(function(){ jQuery('.business-profile-tab-list ul li.inbox .profile-tab').trigger('click'); }, 100);
            });

        </script>
    <?php
    endif;

    switch( $args['current_action'] ) {

        case "read" :

            if ( ! isset( $_REQUEST['id'] ) || ! intval( $_REQUEST['id'] ) ) {
                return esc_html__('Invalid request, please make sure you are accessing a valid link', 'wyzi-business-finder');
            }

            $not_allowed_message = esc_html__( 'You do not have permission to access this message.', 'wyzi-business-finder' );

            $message_id = $_REQUEST['id'];

            $message = get_post( $message_id );

            if ( ! $message || 'private-message' != $message->post_type || 'publish' != $message->post_status ) {
                return $not_allowed_message;
            }

            // make sure the reader is either sender or receiver
            $message_sender_id = get_post_meta( $message->ID, 'message_sender_id', true );
            $message_receiver_id = get_post_meta( $message->ID, 'message_receiver_id', true );

            if ( $current_user_id != $message_sender_id && $current_user_id != $message_receiver_id ) {
                return $not_allowed_message;
            }
    
            $user_role = private_message_the_user_role( $message_id );

            // Do not display this message if removed for this user role
            $meta_key = "_message_deleted_for_{$user_role}";

            if ( get_post_meta( $message_id, $meta_key, true ) ) {
                return $not_allowed_message;
            }
            
           // $post_parent = get_post( $message_id );//private_message_get_parent_id( $message_id );
            
            if ( $user_role == 'sender' ) {
                update_post_meta( $message, 'sender_read_status', true );
            } else {
                update_post_meta( $message, 'read_status', true );   
            }
            
            add_action('wp_footer','wyz_display_service_chat_form', $message_id);
            $args = array(
                'orderby' => 'date',
                'order' => 'ASC',
                'post_id' => $message_id,
            );

            // The Query
            $comments_query = new WP_Comment_Query;
            $comments = $comments_query->query( $args );

            //wp_localize_script( 'private-message', 'messagesRead',  );

            /*$replies = new WP_Query( array(
                'post_type'     => 'private-message',
                'post_parent'   => $message_id,
                'order'         => 'ASC',
                'posts_per_page' => -1
            ) );*/

            require_once $base_directory . '/views/read.php';

            return null;

        break;

        case "sent_items" :

           $query_args = array(
                'post_type'     => 'private-message',
                'post_parent'   => 0,
                'posts_per_page' => -1,
                'meta_key' => 'last_reply_date',
            	'orderby' => 'meta_value',
            	'order' => 'DESC'
            );
            
            $query_args['meta_query'] = array(
                'relation'  => 'OR',
                'receiver_query' => array(
                    'relation'    => 'AND',  
                    array(
                        'key'      => 'message_receiver_id',
                        'value'    => $current_user_id,    
                    ),
                     array(
                         'key'       => 'receiver_status',
                         'value'     => 'sent_item'
                     )
                )
            );
        
            $query_args['meta_query']['sender_query'] = array(
                    'relation'  => 'AND',
                    array(
                        'key'   => 'message_sender_id',
                        'value' => $current_user_id                
                    ),
                    array(
                        'key'       => 'sender_status',
                        'value'     => 'sent_item'
                    ),
                );
            

            $args['posts'] = new WP_Query( $query_args );

            require_once dirname( plugin_dir_path( __FILE__ ) ) . '/views/sent.php';

            // rest post data
            wp_reset_postdata();

            return null;
        break;

        case "blocked" :

            $users = array();
            $blocked_users = get_user_meta( $current_user_id, '_pm_blocked_users', true );

            if ( $blocked_users && is_array( $blocked_users ) ) {
                $users = new WP_USER_QUERY( array(
                    'include'   => $blocked_users
                ) );
            }

            require_once $base_directory . '/views/blocked.php';

            return null;
        break;

        case "trash" :
        default :

            $query_args = array(
                'post_type'     => 'private-message',
                'post_parent'   => 0,
                'posts_per_page' => -1,
                'meta_key' => 'last_reply_date',
            	'orderby' => 'meta_value',
            	'order' => 'DESC'
            );
            
            $message_status = 'inbox';

            if ( 'trash' == $args['current_action'] ) {
                $message_status = 'trash';
            }
            
            $query_args['meta_query'] = array(
                    'relation'  => 'OR',
                    
                    'receiver_query' => array(
                        'relation'    => 'AND',  
                        
                        array(
                            'key'      => 'message_receiver_id',
                            'value'    => $current_user_id,    
                        ),
                        
                         array(
                             'key'       => 'receiver_status',
                             'value'     => $message_status
                         )
                    )
                );
            
                $query_args['meta_query']['sender_query'] = array(
                        'relation'  => 'AND',
                        
                        array(
                            'key'   => 'message_sender_id',
                            'value' => $current_user_id                
                        ),
                        
                        array(
                            'key'       => 'have_replies',
                            'value'     => 1,
                            'type'      => 'NUMERIC'
                        ),
                        
                        array(
                            'key'       => 'sender_status',
                          //  'value'     => ( 'inbox' == $message_status ) ? 'sent_item' : $message_status
                            'value'     => $message_status
                        ),
                    );
            
            if ( isset( $_REQUEST['from'] ) && intval( $_REQUEST['from'] ) ) {
                $query_args['meta_query']['receiver_query'][] = array(
                        'key'   => 'message_sender_id',
                        'value' => $_REQUEST['from'],
                        'type'  => 'NUMERIC'
                    );
                    
                if ( isset( $query_args['meta_query']['sender_query'] ) ) {
                    $query_args['meta_query']['sender_query'][] = array(
                        'key'   => 'message_receiver_id',
                        'value' => $_REQUEST['from'],
                        'type'  => 'NUMERIC'
                    );
                }
            }
            
            $args['posts'] = new WP_Query( $query_args );

        break;
    }

    require_once dirname( plugin_dir_path( __FILE__ ) ) . '/views/inbox.php';

    // rest post data
    wp_reset_postdata();
}

function wyz_get_private_message_status_count() {
    $query_args = array(
        'post_type'     => 'private-message',
        'post_parent'   => 0,
        'posts_per_page' => -1,
        'meta_key' => 'last_reply_date',
        'orderby' => 'meta_value',
        'order' => 'DESC',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key'      => 'message_receiver_id',
                'value'    => get_current_user_id(),
            )
        )
    );
    $query = new WP_Query( $query_args );

    $stats = array(
        'inbox'  => 0,
        'sent'   => 0,
        'read'   => 0,
        'not_read' => 0,
        'trash'  => 0
    );

    if ( !$query->have_posts() )
        return $stats;

    foreach ( $query->posts as $id ) {
        $s = get_post_meta( $id, 'receiver_status', true );
        switch ($s) {
            case 'sent_item':
                $stats['sent']++;
                break;
            case 'inbox':
                $stats['inbox']++;
                if ( !get_post_meta( $id, 'read_status', true ) )
                    $stats['not_read']++;
                break;
            case 'trash':
                $stats['trash']++;
                break;
        }
    }
    return $stats;
}

function wyz_parse_chat_service_token( $token ) {
    $key = explode( ':::', WyzHelpers::encrypt( $token, 'd' ) );
    $id = $key[0];
    $comment = get_comment( $id );
    if ( ! $comment ) return false;
    $name = get_comment_meta( $id, 'service_name', true );
    $price = get_comment_meta( $id, 'service_price', true );
    $duration = get_comment_meta( $id, 'service_duration', true );
    $parent = $comment->comment_post_ID;
    
    if ( $key[1] != hash('sha256',"ser_key_$parent-$id-$name-$duration-$price" ) ) return false;
    return array(
        'name' => $name,
        'price' => $price,
        'duration' => $duration,
        'comment' => $comment
    );
}

function wyz_chat_has_purchase( $message_id ) {
    return 'yes' == get_post_meta( $message_id, 'chat_has_purchase', true );
}

function wyz_get_chat_purchased_comment( $message_id ) {
    if ( ! wyz_chat_has_purchase( $message_id ) )
        return false;
    $comment_id = get_post_meta( $message_id, 'chat_purchase_comment_id', true );
    $comment = get_comment( $comment_id );
    if ( ! $comment )
        return false;
    return $comment;
}

function wyz_display_service_chat_form( $m_id ) {
    $chat_purchased_comment = wyz_get_chat_purchased_comment( $m_id );

    if ( $chat_purchased_comment ) return;
    if ( ! WyzHelpers::is_user_vendor( wp_get_current_user() ) )return;
    ?>
    <div id="service-chat-slide-form" class="drawer-content-left">
        <button id="service-trigger"><?php esc_html_e( 'Send New Service', 'wyzi-business-finder' );?> <i class="fa fa-plus-square-o" aria-hidden="true"></i></button>
        <aside class="contact100-form validate-form">
            <h2><?php esc_html_e( 'Submit a new Service', 'wyzi-business-finder' );?></h2>
            <div class="wrap-input100 validate-input">
                <input id="service-name" class="input100" type="text" name="service-name" placeholder="<?php esc_html_e( 'Service Name', 'wyzi-business-finder' );?>">
                <span class="focus-input100"></span>
                <label class="label-input100" for="name">
                    <span class="fa fa-briefcase"></span>
                </label>
            </div>


            <div class="wrap-input100 validate-input">
                <input id="service-duration" class="input100" type="number" name="service-duration" placeholder="<?php esc_html_e( 'Service Duration (in days)', 'wyzi-business-finder' )?>">
                <span class="focus-input100"></span>
                <label class="label-input100" for="email">
                    <span class="fa fa-clock-o"></span>
                </label>
            </div>


            <div class="wrap-input100 validate-input">
                <input id="service-price" class="input100" type="number" name="service-price" placeholder="<?php esc_html_e( 'Service Price', 'wyzi-business-finder' );?>">
                <span class="focus-input100"></span>
                <label class="label-input100" for="phone">
                    <span class="fa fa-dollar"></span>
                </label>
            </div>

            <div class="wrap-input100">
                <input id="service-start-date" class="input100" type="text" name="service-start-date" placeholder="<?php esc_html_e( 'Service Start Date', 'wyzi-business-finder' );?>">
                <span class="focus-input100"></span>
                <label class="label-input100" for="phone">
                    <span class="fa fa-calendar"></span>
                </label>
            </div>

            <div class="container-contact100-form-btn">
                <button class="wyz-button" id="service-submit">
                    Send Now
                </button>
            </div>
        </aside>
    </div>
    <?php
}

add_shortcode( 'private_message_user_inbox', 'private_message_inbox' );

if ( ! function_exists( 'exc_get_user_display_name' ) )
{
    function exc_get_user_display_name( $user )
    {
        if ( ! is_object( $user ) && intval( $user ) ) {
            $user = get_userdata( $user );
        }

        if ( ! $user ) {
            return null;
        }

        $user_id = $user->ID;

        $display_name = get_the_author_meta( 'display_name', $user_id );

        if ( ! $display_name ) {
            $display_name = implode(' ', array(
                    get_user_meta( $user_id, 'first_name', true ),
                    get_user_meta( $user_id, 'last_name', true )
                ) );
        }

        return esc_html( $display_name );
    }
}

function private_message_read_status( $message_id )
{
    $post_parent = private_message_get_parent_id( $message_id );
    $user_role = private_message_the_user_role( $message_id );

    return ( $user_role == 'sender' )
            ? get_post_meta( $post_parent, 'sender_read_status', true )
            : get_post_meta( $post_parent, 'read_status', true );
}

function private_message_update_read_status( $message_id, $new_status = true )
{
    $post_parent = private_message_get_parent_id( $message_id );
    $user_role = private_message_the_user_role( $message_id );

    return ( $user_role == 'sender' )
            ? update_post_meta( $post_parent, 'sender_read_status', $new_status )
            : update_post_meta( $post_parent, 'read_status', $new_status );
}

function private_message_the_user_role( $message_id )
{
    $post_parent = private_message_get_parent_id( $message_id );
    $current_user_id = get_current_user_id();
    
    $sender_id = get_post_meta( $post_parent, 'message_sender_id', true );
    $receiver_id = get_post_meta( $post_parent, 'message_receiver_id', true );
    
    return ( $current_user_id == $sender_id ) ? 'sender' : 'receiver';
}

function private_message_get_parent_id( $message_id )
{
    $post_parent = wp_get_post_parent_id( $message_id );
    
    if ( ! $post_parent ) {
        $post_parent = $message_id;
    }
    
    return $post_parent;
}