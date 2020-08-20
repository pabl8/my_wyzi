<?php defined('ABSPATH') OR die('restricted access');

if ( empty( $message ) ) {
    return null;
}

$sender_name = get_the_author_meta( 'user_login', $message_sender_id );
$sender_email = get_the_author_meta( 'user_email', $message_sender_id );
$bus_chat_avatar = '';
$owner_bus = get_post( get_post_meta( $message->ID, 'business_id_sent_from', true ) );
if ( is_wp_error( $owner_bus ) )
    $owner_bus = '';
$my_avatar = '';
$their_avatar = '';
if (  '' != $owner_bus && $owner_bus->post_author == $current_user_id ) {
    $my_avatar = get_the_post_thumbnail( $owner_bus->ID, 'thumbnail' );
} else {
    $my_avatar = Messages::the_avatar( $message_sender_id );
}
$my_avatar = str_replace( '"', "'", $my_avatar );
echo '<script> var myAvatar = "'.$my_avatar.'";</script>';
?>

<div class="X__wrapper">

    <div class="X__sidebar">
        <?php require_once $base_directory . '/views/menu.php'; ?>
    </div>

    <div class="X__content">

        <div class="X__detail">
            <div class="XD__header">
                <h2 class="XDH__title"><?php echo esc_html( $message->post_title ); ?></h2>
                <div class="XDB__sender">
                    <figure class="XDB__sender-avatar">
                        <?php echo get_avatar( $message_sender_id ); ?>
                    </figure>
                    <div class="XDB__sender-info">
                        <b><?php echo esc_html( $sender_name ); ?></b> <span><?php echo get_the_time( get_option( 'date_format' ), $message->ID ); ?></span>
                    </div>
                </div>
                <h2 class="XDH__title sent_from_bus"><?php esc_html_e('Sent From: ', 'wyzi-business-finder'); echo get_the_title(get_post_meta($message->ID,'business_id_sent_from',true));?></h2>
                <div style="clear: both;"></div>
            </div>
            <?php
            $chat_purchased_comment = wyz_get_chat_purchased_comment( $message->ID );
            if ( $chat_purchased_comment ) {
                 if ( get_post_meta( $message->ID, 'chat_purchase_completed', true ) == 'yes' ) { ?>
                    <div id="XD_service_info" class="service-success">
                        <span><?php echo sprintf( esc_html__( 'Service "%s" Completed',  'wyzi-business-finder' ), get_comment_meta( $chat_purchased_comment->comment_ID, 'service_name', true ) );?></span>
                    </div>
                <?php
                } elseif($current_user_id == $message->post_author) {
                    $service_name = get_comment_meta( $chat_purchased_comment->comment_ID, 'service_name', true );
                    $service_deration = intval( get_comment_meta( $chat_purchased_comment->comment_ID, 'service_duration', true ) ) * 86400;
                    $service_start = get_comment_meta( $chat_purchased_comment->comment_ID, 'service_start_time', true );
                    $service_start_at = get_comment_meta( $chat_purchased_comment->comment_ID, 'service_start_at', true );
                    $time_remaining = $service_deration + $service_start - current_time( 'timestamp' );
                    ?>
                    <div id="XD_service_info">
                        <span id="chat-ser-name"><?php echo $service_name;?></span>
                        <?php if ( '' != $service_start_at ) {
                            $d = DateTime::createFromFormat( 'd/m/Y G:i', $service_start_at );
                            if ( $d && $d->getTimeStamp() > current_time( 'timestamp' )) {
                                echo '<p class="duration"><span>' . sprintf( esc_html__( 'Starts at: %s (%s)', 'wyzi-business-finder' ), $service_start_at, WyzHelpers::get_wp_timezone() ) . '</span></p>';
                            } else
                                $service_start_at = '';
                        } 
                        if ( $service_start_at == '' )
                            echo '<span id="chat-ser-timer">' . $time_remaining . '</span>';?>
                    </div>
                    <form id="serv-approval" method="POST">
                        <input type="hidden" name="serv-approval" value="<?php echo WyzHelpers::encrypt( $chat_purchased_comment->comment_ID . ':::' . hash('sha256', 'serv_approv_' . $message->post_author . '_' . $chat_purchased_comment->comment_ID ) );?>"/>
                        <input type="submit" value="Validate" onclick="return confirm('<?php esc_html_e( 'Are you sure You want to validate? This means that the service agreed on between you and The vendor has been completed, and you are satisfied with the results.', 'wyzi-business-finder' );?>');">
                    </form>
                    <?php
                }
            }
            ?>

            <div class="XD__body" id="XD__body">
                
                <?php $target = $current_user_id == $message->post_author ? 'out' : 'in';?>
                <div class="XDB__message XDB__receiver-message <?php echo $target;?>">
                    <?php echo Messages::the_avatar( $message->post_author );?>
                    <div class="private-message <?php echo $target;?>">
                        <p><?php echo wpautop( esc_html( $message->post_content ) );?></p>
                        <?php echo private_message_attachments_list( $message->ID ); ?>
                        <span class="time"><?php echo explode(' ', $message->post_date)[0];?></span>
                        <?php if('out'==$target){ ?>
                        <i class="fa fa-check<?php echo (true == get_post_meta($message->ID,'message_read',true) ? '-circle' : '');?>"></i>
                        <?php }?>
                    </div>
                    <div class="clear"></div>
                </div>

                <?php $current_user_id = get_current_user_id();?>

                <?php if ( $comments ) : ?>

                    <?php foreach ( $comments as $comment ) :
                        if ( 'yes' == get_comment_meta( $comment->comment_ID, 'is_serv_paid_comment', true ) ) {
                            wyz_show_serv_paid_comment( $comment );
                            continue;
                        }
                        else if ( 'yes' == get_comment_meta( $comment->comment_ID, 'is_serv_start_comment', true ) ) {
                            wyz_show_serv_start_comment( $comment );
                            continue;
                        }
                        else if ( 'yes' == get_comment_meta( $comment->comment_ID, 'is_serv_approval_comment', true ) ) {
                            wyz_show_serv_approvel_comment( $comment );
                            continue;
                        }
                        $target = $current_user_id == $comment->user_id ? 'out' : 'in';
                        $is_service_message = ( get_comment_meta( $comment->comment_ID, 'is_service_message', true ) == 'yes' );
                    ?>
                        <div class="XDB__receiver-message <?php echo $target;?>">
                            <?php wyz_chat_avatar( $comment, $message, $bus_chat_avatar );?>
                            <?php if ( $is_service_message )
                                wyz_show_service_message($comment, $current_user_id, $message_receiver_id, $target, $message);
                            else 
                                wyz_show_chat_message($comment, $target);
                            ?>
                            <div class="clear"></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>

            <div class="XD__reply-box">
                <div class="XDRB__avatar">
                    <?php echo get_avatar( $message_receiver_id ); ?>
                </div>

                <div class="XDRB__reply" id="private_message_form">
                   <form class="XDRB__reply-form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" id="pm-reply" enctype="multipart/form-data">

                        <div class="xmb-form-field xmb-editor">
                            <textarea name="message" rows="8"></textarea>
                        </div>

                        <div class="xmb-form-field xmb-editor">
                            <input type="file" name="attachment" />
                        </div>
                        
                        <input type="hidden" name="message_id" value="<?php echo esc_attr( $message_id ); ?>" />
                        <input type="hidden" name="secret" value="<?php echo wp_create_nonce( "private-message-{$message_id}" ); ?>" />

                        <input type="hidden" name="action" value="oz_reply_message" />

                        <div class="XMB__footer">
                            <?php Messages::the_submit_buttom();?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
function wyz_show_chat_message($comment, $target){
    ?>
    <div class="private-message <?php echo $target;?>">
        <p><?php echo wpautop( $comment->comment_content );?></p>
        <?php echo private_message_attachments_list( $comment->comment_ID, true ); ?>
        <span class="time"><?php echo  explode(' ', $comment->comment_date )[0];?></span>
        <?php if('out'==$target){ ?>
        <i class="fa fa-check<?php echo (true == get_comment_meta($comment->comment_ID,'message_read',true) ? '-circle' : '');?>"></i>
        <?php }?>
    </div>
    <?php
}

function wyz_show_service_message($comment, $current_user_id, $message_receiver_id, $target, $message) {
    global  $woocommerce;
    ?>
    <div class="private-message service-message <?php echo $target;?>">
        <div class="service-header"><?php echo apply_filters( 'wyz_chat_service_title', 
            esc_html__( 'Service Offer', 'wyzi-business-finder' ), $comment, $target );?>
            <span class="time"><?php echo explode(' ', $comment->comment_date )[0];?></span>
        </div>
        <div class="service-content">
            <div class="service-icon"><i class="fa fa-weixin"></i></div>
            <div class="service-info">
                <p class="title"><?php echo get_comment_meta( $comment->comment_ID, 'service_name', true );?></p>
                <p class="price">
                    <span><?php esc_html_e( 'Price:','wyzi-business-finder');?></span>
                    <?php echo get_comment_meta( $comment->comment_ID, 'service_price', true );
                    echo '<span class="curr">' . (function_exists('get_woocommerce_currency_symbol')? get_woocommerce_currency_symbol():'') . '</span>';?>
                </p>
                <p class="duration">
                    <span><?php esc_html_e( 'Duration:','wyzi-business-finder');?></span>
                    <?php echo sprintf( esc_html__( '%d days', 'wyzi-business-finder' ), get_comment_meta( $comment->comment_ID, 'service_duration', true ) );?>
                </p>
                <?php $service_start_at = get_comment_meta( $comment->comment_ID, 'service_start_at', true );
                    if ( $service_start_at != '' ) {
                        $d = DateTime::createFromFormat('d/m/Y G:i', $service_start_at);
                        //echo $d->getTimeStamp();
                        if( $d  ) {
                            echo '<p class="duration"><span>' . sprintf( esc_html__( 'Starts at: %s (%s)', 'wyzi-business-finder' ), $service_start_at, WyzHelpers::get_wp_timezone() ) . '</span></p>';
                        } else
                            echo '<p class="duration"><span>' . esc_html__( 'Starts upon payment', 'wyzi-business-finder' ) . '</span></p>';
                    } else
                        echo '<p class="duration"><span>' . esc_html__( 'Starts upon payment', 'wyzi-business-finder' ) . '</span></p>';
                        ?>
                <?php wyz_service_buy_btn( $comment, $current_user_id, $message_receiver_id, $message ); ?>
            </div>
        </div>
    </div>
    <?php
}

function wyz_service_buy_btn( $comment, $current_user_id, $message_receiver_id, $message ) {

    if ( 'yes' == get_comment_meta( $comment->comment_ID, 'service_paid', true ) ){
        echo '<div class="service-pay-btn"><span class="paid">' . esc_html__( 'Paid', 'wyzi-business-finder' ) . '</span></div>';
        return;
    }

    if ( Messages::chat_has_service( $message->ID ) )return;
    if ( $current_user_id == $message_receiver_id )return;
    $token = get_comment_meta( $comment->comment_ID, 'service_key', true );
    $ver = explode( '=', $token );

    echo '<div class="service-pay-btn">';
    if ( 2 != count( $ver ) || ! wyz_parse_chat_service_token( $ver[1] ) )
        echo '<span class="invalid">'.esc_html__( 'Invalid Service', 'wyzi-business-finder' ).'</span>';
    else
        echo '<a class="buy-link" href="'. home_url() . $token .'">'.esc_html__( 'Pay Now', 'wyzi-business-finder' ).'</a>';
    
    echo '</div>';
}


function wyz_chat_avatar( $comment, $message, $bus_chat_avatar ) {
    global $message_sender_id;
    if ( $comment->user_id == $message_sender_id ){
        echo Messages::the_avatar( $comment->user_id );
    }
    else {
        if ( '' == $bus_chat_avatar) {
            $bus_chat_avatar = get_the_post_thumbnail( get_post_meta( $message->ID, 'business_id_sent_from', true ), 'thumbnail' );
        }
        echo $bus_chat_avatar;
    }
}


function wyz_show_serv_paid_comment ( $comment ) {
    ?>
    <div class="XDB__receiver-message serv-paid-message">
        <p><?php esc_html_e( 'Service Paid', 'wyzi-business-finder' );?></p>
        <span class="float-right"><?php echo  explode(' ', $comment->comment_date )[0];?></span>
        <div class="clear"></div>
    </div>
    <?php
}

function wyz_show_serv_start_comment ( $comment ) {
    ?>
    <div class="XDB__receiver-message serv-start-message">
        <p><?php esc_html_e( 'Service Started', 'wyzi-business-finder' );?></p>
        <span class="float-right"><?php echo  explode(' ', $comment->comment_date )[0];?></span>
        <div class="clear"></div>
    </div>
    <?php
}

function wyz_show_serv_approvel_comment ( $comment ) {
    ?>
    <div class="XDB__receiver-message serv-start-message">
        <p><?php esc_html_e( 'Service Completed', 'wyzi-business-finder' );?></p>
        <span class="float-right"><?php echo  explode(' ', $comment->comment_date )[0];?></span>
        <div class="clear"></div>
    </div>
    <?php
}
?>