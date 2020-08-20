<?php
/**
 * Main initializer of the Private Messaging System
 *
 * @package wyz
 */
class Messages
{
    /**
     * Plugin Directory Path
     *
     * @var string
     */
    private $_plugin_dir;

    public static $SubmitButton;
    public static $Avatar = array();
    public static $Has_Purchase = '';
    /**
     * Plugin URL
     *
     * @var string
     */
    private $_plugin_url;

    private $allowed_mimes = array( 'image/jpg', 'image/jpeg', 'image/png', 'image/tiff', 'image/tiff-fx', 'video/mp4', 'application/pdf', 'application/zip', 'application/x-zip-compressed', 'application/x-gzip', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' );

    public function __construct()
    {
        $this->_plugin_dir = WYZI_PLUGIN_DIR;
        $this->_plugin_url = plugin_dir_url( __FILE__ );

        add_action('wp_ajax_oz_send_message', array( &$this, 'send_message') );
        add_action('wp_ajax_nopriv_oz_send_message', array( &$this, 'send_message') );

        add_action('wp_ajax_oz_reply_message', array( &$this, 'send_message') );
        add_action('wp_ajax_nopriv_oz_reply_message', array( &$this, 'send_message') );

        add_action('wp_ajax_oz_pm_action', array( &$this, 'message_action' ) );
        add_action('wp_ajax_nopriv_oz_pm_action', array( &$this, 'message_action' ) );

        add_action('wp_ajax_update_user_token', array( &$this, 'update_user_token' ) );
        add_action('wp_ajax_nopriv_update_user_token', array( &$this, 'update_user_token' ) );


        add_action('wp_footer', array( &$this, 'footer_markup') );

        add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_files' ), 2 );
        
        add_action( 'woocommerce_order_status_completed', array( &$this, 'service_purchased' ),2 );


        // load helper functions
        require_once $this->_plugin_dir . 'businesses-and-offers/businesses/private-messages/functions/helpers.php';

        // Custom post type
        require_once $this->_plugin_dir . 'businesses-and-offers/businesses/private-messages/core/message-post.php';

        // Metabox
        require_once $this->_plugin_dir . 'businesses-and-offers/businesses/private-messages/core/metabox.php';
    }

    public function send_message()
    { 
        if ( ! is_user_logged_in() ) {
            wp_send_json_error(
                esc_html__(
                    "You must login to send a private message.", "private-message"
                )
            );
        }

        $attachment_file = false;

        if ( ! empty( $_FILES['attachment']['type'] ) ) {
            $attachment_file = $_FILES['attachment'];

            $attachment_type = current( (array) explode( '/', $attachment_file['type'] ) );

            $allowed_mimetypes = $this->allowed_mimetype();

            if ( false === in_array( $attachment_file['type'], $this->allowed_mimes ) )
            {
                wp_send_json_error(
                    sprintf(
                        __( 'You must upload only supported %s files.', 'wyzi-business-finder' ),
                        implode( ', ', array_keys( $allowed_mimetypes ) )
                    )
                );
            }
        }

        $wp_nonce_action = "private-message";

        if ( isset( $_POST['message_id'] ) ) {

            $message_id = $_POST['message_id'];

            // is valid message_id and user have permission to send reply
            $user_role = $this->user_message_role( $message_id );

            $invalid_request_response = esc_html__( "Invalid form data, please refresh the page and try again.", "wyzi-business-finder" );

            if ( ! $user_role || ! $this->is_valid_message_id( $message_id ) || ! $this->is_message_available_for( $user_role, $message_id ) ) {
                wp_send_json_error( $invalid_request_response );
            }
            $wp_nonce_action = "private-message-{$message_id}";

            $_POST['send_message_to'] = ( 'sender' == $user_role )
                            ? get_post_meta( $message_id, 'message_receiver_id', true )
                            : get_post_meta( $message_id, 'message_sender_id', true );

            $message = ( ! empty( $_POST['message'] ) ) ? $_POST['message'] : '';

            $_POST['subject'] = wp_trim_words( $message, 5 );
        }


        if ( empty( $_POST['secret'] ) || empty( $_POST['send_message_to'] || ! intval( $_POST['send_message_to'] ) )
                || ! wp_verify_nonce( $_POST['secret'], $wp_nonce_action ) ) {
            wp_send_json_error( $invalid_request_response );
        }

        // verify receiver information
        $send_message_to = get_user_by( 'id', $_POST['send_message_to'] );

        if ( ! $send_message_to ) {
            wp_send_json_error( $invalid_request_response );
        }

        $current_user_id = get_current_user_id();

        /*if ( $current_user_id == $send_message_to->ID ) {
            wp_send_json_error(
                esc_html__("Huh! you cannot send message to yourself", "private-message")
            );
        }*/

        $is_service_message = false;
        if ( isset( $message_id ) && isset( $_POST['service-message'] ) ) {
            // Check if it's a service message
            $chat_error = ( esc_html__( "Unallowed message", "wyzi-business-finder") );
            if ( ! WyzHelpers::is_user_vendor( new WP_User( get_current_user_id() ) )||
                '' != get_post_meta( $message_id, 'chat_has_service', true ) ){
                wp_send_json_error( $chat_error );
            }
            foreach ( array('name','duration','price') as $fld ) {
                $errors = '';
                if ( ! isset( $_POST[ $fld ] ) || empty( $_POST[ $fld ] ) ) {
                    $errors .= '<p>' . sprintf(
                            esc_html__("The %s field is required.", "wyzi-business-finder"),
                            $fld
                        ) . '</p>';
                }
            }
            if ( ! empty( $errors ) )
                wp_send_json_error($errors);
            $dur = intval( $_POST['duration'] );
            $start_at = $_POST['start-at'];
            $prc = intval( $_POST['price'] );

            if ( '' != $start_at ) {
                $d = DateTime::createFromFormat('d/m/Y G:i', $start_at);
                if( ! $d ) {
                    $errors .= '<p>' . esc_html__("Error with start date.", "wyzi-business-finder") . '</p>';
                }
            }
            if ( 0 >= $dur )
                $errors .= '<p>' . esc_html__("Please enter a valid duration.", "wyzi-business-finder") . '</p>';
            if ( 0 >= $prc )
                $errors .= '<p>' . esc_html__("Please enter a valid price.", "wyzi-business-finder") . '</p>';
            if ( ! empty( $errors ) )
                wp_send_json_error($errors);
            $is_service_message = true;
        }
        if ( ! $is_service_message ) {
            foreach ( array('subject', 'message') as $field_name ) {

                if ( empty( $_POST[ $field_name ] ) ) {
                    wp_send_json_error(
                        sprintf(
                            esc_html__("The %s field is required.", "private-message"),
                            $field_name
                        )
                    );
                }
                $field_name = wp_kses_post( strip_tags( $_POST[ $field_name ] ) );
            }
        }

        $is_reply = false;
        $parent = '';

        if ( isset( $message_id ) ) {
            $time = current_time('mysql');
            $parent = $message_id;
            $data = array(
                'comment_post_ID' => $message_id,
                'comment_content' => $message,
                'user_id' => $current_user_id,
                'comment_date' => $time
            );


            $message_id = wp_insert_comment($data);

            //update_comment_meta( $message_id, 'subject', $subject );
            update_comment_meta( $message_id, 'message_receiver_id', $send_message_to->ID );
            update_comment_meta( $message_id, 'message_sender_id', $current_user_id );
            if ( $is_service_message ) {
                update_comment_meta( $message_id, 'is_service_message', 'yes' );
                update_comment_meta( $message_id, 'service_name', $_POST['name'] );
                update_comment_meta( $message_id, 'service_duration', intval( $_POST['duration'] ) );
                update_comment_meta( $message_id, 'service_start_at', $_POST['start-at'] );
                update_comment_meta( $message_id, 'service_price', intval( $_POST['price'] ) );
                update_comment_meta( $message_id, 'service_key', '?service_message=' . WyzHelpers::encrypt( "$message_id:::" . hash('sha256',"ser_key_$parent-$message_id-" . $_POST['name'] . '-' . intval( $_POST['duration'] ) . '-' . intval( $_POST['price'] ) ) ) );
            }

            $is_reply = true;
        } else {
            $message_post_data = array(
                'post_title'    => $_POST['subject'],
                'post_content'  => $_POST['message'],
                'post_type'     => 'private-message',
                'post_status'   => 'publish',
                'post_author'   => $current_user_id,
                'meta_input'    => array(
                    'message_receiver_id' => $send_message_to->ID,
                    'message_sender_id'   => $current_user_id
                )
            );
            $message_id = wp_insert_post( $message_post_data, true );
        }

        if ( is_wp_error( $message_id ) || ! $message_id ) {
            wp_send_json_error( esc_html__(
                "There is an error while sending the message, please try again.", "private-message"
            ) );
        }

        // add additional fields
        if ( ! $is_reply ) {

            $additional_meta_fields = $this->additional_meta_fields();

            foreach ( $additional_meta_fields as $meta_key => $meta_value ) {
                add_post_meta( $message_id, $meta_key, $meta_value, true );
            }
            
            update_post_meta( $message_id, 'last_reply_date', current_time( 'timestamp' ) );
            update_post_meta( $message_id, 'business_id_sent_from',$_POST['current__id'] );
            

        } else {
            update_post_meta( $parent, 'have_replies', 1 );
        
            $user_role = private_message_the_user_role( $message_id );
            
            if ( 'sender' == $user_role ) {
                update_post_meta( $parent, 'sender_status', 'sent_item' );
                update_post_meta( $parent, 'receiver_status', 'inbox' );
            } else {
                update_post_meta( $parent, 'receiver_status', 'sent_item' );
                update_post_meta( $parent, 'sender_status', 'inbox' );
            }
   
            update_post_meta( $parent, 'last_reply_date', current_time( 'timestamp' ) );
            
            $read_status_key = ( 'sender' == $user_role ) ? 'read_status' : 'sender_read_status';
            
            if ( ! add_post_meta( $parent, $read_status_key, false, true ) ) {
                update_post_meta( $parent, $read_status_key, false );    
            }
        }

        // Upload attachments
        // Upload file handler
        if ( ! empty( $attachment_file ) ) {
            $upload_overrides = array( 'test_form' => false );

            $movefile = wp_handle_upload( $attachment_file, $upload_overrides );

            if ( ! $movefile || isset( $movefile['error'] ) ) {
                wp_send_json_error( $movefile['error'] );
            }

            $filename = $movefile['file'];
            $post_id = $message_id;
            $ext = explode('/',$movefile['type']);
            $ext = end($ext);

            $attachment_data = array(
                'post_mime_type' => $movefile['type'],
                'guid'           => $movefile['url'],
                'post_parent'    => $post_id,
                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
                'post_content'   => ''
            );

            $attachment_id = wp_insert_attachment( $attachment_data, $filename, $post_id );

            if ( is_wp_error( $attachment_id ) )
            {
                @unlink( $movefile['file'] );

                wp_send_json_error(
                    sprintf(
                        esc_html__( 'There was an error while uploading the attachment.', 'wyzi-business-finder' ),
                        $filename
                    )
                );
            }

            $metadata = wp_generate_attachment_metadata( $attachment_id, $filename );

            wp_update_attachment_metadata( $attachment_id, $metadata );

            $attachment_hash = uniqid();

            add_post_meta( $attachment_id, 'attachment_hash', $attachment_hash, true );

            if($is_reply) {
                update_comment_meta( $message_id, 'attachment_id', $attachment_id);
                update_comment_meta( $message_id, 'attachment_ext', $ext);
            }
            else {
                update_post_meta( $message_id, 'attachment_id', $attachment_id );
                update_post_meta( $message_id, 'attachment_ext', $ext);
            }
        }

        if ( 1 /*check if mesage is read in frontend*/){
            // Send email to receiver
            $sender_user_data = get_user_by( 'id',$current_user_id );
            $message_subject = wyz_get_option( 'private-messaging-mail-subject' );
                    if ( empty( $message_subject ) )
                          $message_subject = sprintf(
                                esc_html__("You received a message from %s"),
                                $sender_user_data->user_login
                            );
             $message_subject = str_replace( '%SENDERUSERNAME%', $sender_user_data->user_login, $message_subject );  
             $message_subject = str_replace( '%RECEIVERUSERNAME%', $send_message_to->user_login, $message_subject );         
                        
            $message_to_reciever = wyz_get_option( 'private-messaging-mail' );

            if ( empty( $message_to_reciever ) ) {
                $message_to_reciever = esc_html__( 'Hi %reciever_user_name%, You have recevied a new message from %sender_user_name%', 'wyzi-business-finder' );
            }
            
            if (! $is_reply ) {
                $message_id_for_email = $message_id;
            }
            else {
                $comment = get_comment( $message_id ); 
                $message_id_for_email  = $comment->comment_post_ID;
            }

            $message_to_reciever = str_replace( '%reciever_user_name%', $send_message_to->user_login , $message_to_reciever );
            $message_to_reciever = str_replace( '%sender_user_name%', $sender_user_data->user_login , $message_to_reciever );
            $message_to_reciever = str_replace( '%message_link%', get_site_url(). '/user-account/?action=read&id='. $message_id_for_email , $message_to_reciever );
            $message = ( ! empty( $_POST['message'] ) ) ? $_POST['message'] : '';
            $message_to_reciever = str_replace( '%message_content%', $message , $message_to_reciever );
            $business_url = '';
            $business_from = get_post( $_POST['current__id'] );
            if ( $business_from && 'wyz_business' == $business_from->post_type ) {
                $business_url = get_the_permalink( $_POST['current__id'] );
            }
            $message_to_reciever = str_replace( '%business_url%', $business_url , $message_to_reciever );
            $success_message = esc_html__("Your message has been sent successfully", "private-message");

            if ( ! WyzHelpers::wyz_mail(
                $send_message_to->user_email,
                $message_subject,
                $message_to_reciever
            ) ) {

                $success_message .= ", " . esc_html__("but there was an issue in email delivery.", "private-message");
            }
        }
        //require_once( plugin_dir_path(  __FILE__ ) . 'firebase/InitFirebase.php' );
        wp_send_json_success( $success_message );
    }

    public static function the_avatar( $u_id ) {
        if ( ! isset( self::$Avatar[ $u_id ] ) || empty( self::$Avatar[ $u_id ] ) )
            self::$Avatar[ $u_id ] = get_avatar( $u_id );
        return self::$Avatar[ $u_id ];
    }

    public static function chat_has_service( $chat_id ){
        if(''==self::$Has_Purchase)
            self::$Has_Purchase = ( 'yes' == get_post_meta( $chat_id, 'chat_has_purchase', true ) );
        return self::$Has_Purchase;
    }

    public function message_action()
    {
        if ( ! is_user_logged_in() ) {
            wp_send_json_error(
                esc_html__('You must login to perform this action.', 'wyzi-business-finder')
            );
        }

        $invalid_request_response = esc_html__('Invalid request, please try again.', 'wyzi-business-finder');

        if ( empty( $_POST['action_name'] ) || empty( $_POST['selected_items'] ) || ! is_array( $_POST['selected_items'] ) ) {

            wp_send_json_error( $invalid_request_response );
        }

        // Make sure there was only integer values
        $selected_items = array();

        foreach ( $_POST['selected_items'] as $item ) {

            if ( ! intval( $item ) ) {
                continue;
            }

            $selected_items[] = $item;
        }

        // send error message if the list is empty
        if ( ! $selected_items ) {
            wp_send_json_error( esc_html__(
                'There is nothing to do, please try again.', 'wyzi-business-finder'
            ) );
        }

        $action_name = $_POST['action_name'];

        switch( $action_name ) {

            case "delete" :
                $response = $this->_delete_messages( $selected_items );
            break;

            case "block" :
                $response = $this->_block_user( $selected_items );
            break;

            case "report-spam" :
            case "report-harassment" :

                $response = $this->_report_message( $selected_items, $action_name );
            break;

            case "move-to-inbox" :
                $response = $this->_move_to_inbox( $selected_items );
            break;

            default :
                wp_send_json_error( $invalid_request_response );
            break;
        }

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( $response->get_error_message() );
        }

        wp_send_json_success( $response );
    }

    public function update_user_token() {
        $nonce = filter_input( INPUT_POST, 'nonce' );
        if ( ! wp_verify_nonce( $nonce, 'wyz_ajax_custom_nonce' ) ) {
            wp_send_json_error( esc_html__( 'Security check failed', 'wyzi-business-finder' ) );
        }
        $uid = filter_input( INPUT_POST, 'uid' );
        $token = filter_input( INPUT_POST, 'token' );
        if ( ! is_user_logged_in() || ! $uid || empty( $uid ) || ( get_current_user_id() != ( explode(':::', WyzHelpers::encrypt( $uid, 'd' ) )[1] ) ) ||
             ! $token || empty( $token )  )
            wp_send_json_error( esc_html__( 'Invalid information', 'wyzi-business-finder' ) );
        update_user_meta( get_current_user_id(), 'wyz_fcm_token', $token );
        wp_send_json_success(true);
    }

    public function enqueue_files()
    {
        //wp_enqueue_style( 'font-awesome', $this->_plugin_url . 'views/resources/css/font-awesome.min.css' );
        wp_enqueue_style( 'private-message', $this->_plugin_url . 'views/resources/css/pm.css' );

        wp_enqueue_script( 'private-message', $this->_plugin_url . 'views/resources/js/pm.js', array( 'jquery', 'jquery-ui-autocomplete' ), '', true );

        wp_localize_script( 'private-message', 'private_message', array(
                'ajaxurl'              => admin_url( 'admin-ajax.php' ),
                'unexpectedResponse'   => esc_html__( 'Unexpected Response', 'wyzi-business-finder' ),
                'noMessage'            => esc_html__( 'There is no message available', 'wyzi-business-finder'),
                'fcm'                  => ( WyzHelpers::FCM_is_on() ? 'yes' : 'no' ),
                'isSingleBusiness'     => ( is_singular( 'wyz_business' ) ? 'yes' : 'no' ),
                'serviceHeader'        => esc_html__( 'Service Offer', 'wyzi-business-finder' ),
                'currencySymbol'       => function_exists('get_woocommerce_currency_symbol')? get_woocommerce_currency_symbol():'',
                'price'                => esc_html__( 'Price:', 'wyzi-business-finder' ),
                'duration'             => esc_html__( 'Duration:', 'wyzi-business-finder' ),
                'days'                 => esc_html__( 'days', 'wyzi-business-finder' ),
                'startAt'              => esc_html__( 'Starts at', 'wyzi-business-finder' ),
                'startAtPayment'       => esc_html__( 'Starts upon payment', 'wyzi-business-finder' ),
                'startAtTimezne'       => WyzHelpers::get_wp_timezone(),
            ) );
    }

    public function footer_markup()
    {
        require_once plugin_dir_path( __FILE__ ) . 'views/message_form.php';
    }

    private function allowed_mimetype()
    {
        //@TODO: Move this code to method
        $wp_allowed_mimes = get_allowed_mime_types();

        if ( ! is_array( $this->allowed_mimes ) ) {
            $this->allowed_mimes = array( $this->allowed_mimes );
        }

        $allowed_mimes = array();

        foreach ( $this->allowed_mimes as $mime ) {

            if ( empty( $mime ) ) {
                continue;
            }

            // Fix for Chrome uploaded audio mp3
            if ( $mime == 'audio' ) {
                $allowed_mimes[ 'mp3' ] = 'audio/mp3';
            }

            $is_mime = strstr( $mime, '/' );

            foreach ( $wp_allowed_mimes as $k => $v ) {

                if ( $is_mime ) {

                    if ( $v == $mime ) {

                        $allowed_mimes[ $k ] = $v;
                        break;
                    }

                } elseif ( false !== strstr( $v, $mime . '/' ) ) {
                    $allowed_mimes[ $k ] = $v;
                }
            }
        }

        return $allowed_mimes;
    }

    public function cmp( $a, $b ) { 
        if(  $a['timestamp'] ==  $b['timestamp'] ){ return 0 ; } 
        return ( $a['timestamp'] < $b['timestamp'] ) ? -1 : 1;
    }

    public function service_purchased( $order_id ) { 
        $order = wc_get_order( $order_id );
        foreach ($order->get_items() as $item_id => $item_data) {
            // Get an instance of corresponding the WC_Product object
            $product = $item_data->get_product();
            $product_id = $product->get_id();

            $comment_id = get_post_meta( $product_id, 'service_comment', true );
            if ( '' == $comment_id )
                continue;

            update_post_meta( $product_id, 'chat_has_purchase', 'yes' );

            $comment = get_comment( $comment_id );

            if ( ! $comment ) return false;

            $parent = $comment->comment_post_ID;
            update_comment_meta( $comment_id, 'service_paid', 'yes' );
            $service_start_at = get_comment_meta( $comment_id, 'service_start_time', true );
            $service_duration = intval( get_comment_meta( $comment_id, 'service_duration', true ) );
            $current_time = current_time( 'timestamp' );
            $service_due_date = $current_time + $service_duration*86400;
            $d = DateTime::createFromFormat( 'd/m/Y G:i', $service_start_at );
            $is_pending_service = false;
            if ( $d && $d->getTimeStamp() > $current_time ) {
                update_comment_meta( $comment_id, 'service_start_time', $d->getTimeStamp() );
                update_comment_meta( $comment_id, 'service_due_date', $d->getTimeStamp() + $service_due_date );
                $pending_services = get_option( 'wyz_pending_comment_services', array() );
                $pending_services[] = array( 'id' => $comment_id, 'timestamp' => $d->getTimeStamp() );
                usort( $pending_services, array( $this,'cmp' ) );
                update_option( 'wyz_pending_comment_services', $pending_services );
                $is_pending_service = true;
            } else {
                $running_services = get_option( 'wyz_running_comment_services', array() );
                $running_services[] = array( 'id' => $comment_id, 'timestamp' => $current_time );
                update_option( 'wyz_running_comment_services', $running_services );
                update_comment_meta( $comment_id, 'service_start_time', $current_time );
                update_comment_meta( $comment_id, 'service_due_date', $service_due_date );
            }

            $data = array(
                'comment_post_ID' => $parent,
                'comment_content' => '',
                'user_id' => get_current_user_id(),
                'comment_date' => current_time('mysql')
            );


            $serv_paid_comment = wp_insert_comment($data);
            update_comment_meta( $serv_paid_comment, 'is_serv_paid_comment', 'yes' );

            if ( $is_pending_service && $d->getTimeStamp() < $current_time ) {
                $data['comment_date'] = $d->format( 'Y-m-d H:i:s' );
                $serv_start_comment = wp_insert_comment($data);
                update_comment_meta( $serv_start_comment, 'is_serv_start_comment', 'yes' );
            }

            update_post_meta( $parent, 'chat_has_purchase', 'yes' );
            update_post_meta( $parent, 'chat_purchase_comment_id', $comment_id );
            update_post_meta( $parent, 'chat_purchase_order_id', $order_id );
            update_post_meta( $parent, 'chat_purchase_product_id', $product_id );

            if ( function_exists( 'wyz_get_option' ) ) {

                $message_sender_id = get_post_meta( $parent, 'message_sender_id', true );
                $message_receiver_id = get_post_meta( $parent, 'message_receiver_id', true );

                $subject = wyz_get_option( 'new-chat-service-started-email-subject' );
                if ( empty( $subject ) )
                    $subject = 'Service Started';

                $message = wyz_get_option( 'new-chat-service-started-email' );
                
                $sender_data = get_user_meta( $message_sender_id );
                $receiver_data = get_user_meta( $message_receiver_id );


                $service_name = get_comment_meta( $comment_id, 'service_name', true );
                $service_duration = intval( get_comment_meta( $comment_id, 'service_duration', true ) );
                $service_price = get_comment_meta( $comment_id, 'service_price', true );
                $service_start_at = get_comment_meta( $comment_id, 'service_start_at', true );
                
                $service_info = sprintf( '<p>' . esc_html__( 'Service Name: %s', 'wyzi-business-finder' ) . '</p>' .
                                 '<p>' . esc_html__( 'Service Price: %s %s', 'wyzi-business-finder' ) . '</p>' .
                                 '<p>' . esc_html__( 'Service Scheduled Duration: %s day(s)', 'wyzi-business-finder' ) . '</p>'.
                                 ( $service_start_at != '' ? '<p class="duration"><span>' . esc_html__( 'Starts at: %s (%s)', 'wyzi-business-finder' ) . '</span></p>' : '%s' ),
                                   $service_name, $service_price, get_woocommerce_currency(), $service_duration, $service_start_at, WyzHelpers::get_wp_timezone() );

                $message1 = str_replace( '%FIRST_NAME%', $sender_data->first_name, $message );
                $message1 = str_replace( '%LAST_NAME%', $sender_data->last_name, $message1 );
                $message1 = str_replace( '%SERVICE_INFO%', $service_info, $message1 );

                WyzHelpers::wyz_mail( $sender_data->user_email, $subject, $message1, 'chat-service-complete' );

                $message2 = str_replace( '%FIRST_NAME%', $receiver_data->first_name, $message );
                $message2 = str_replace( '%LAST_NAME%', $receiver_data->last_name, $message2 );
                $message2 = str_replace( '%SERVICE_INFO%', $service_info, $message2 );

                WyzHelpers::wyz_mail( $receiver_data->user_email, $subject, $message2, 'chat-service-started' );
            }
        }
    }


    private function is_valid_message_id( $message_id )
    {
        if ( ! intval( $message_id ) ) {
            return false;
        }

        $message = get_post( $message_id );

        return ( $message && 'private-message' == $message->post_type && 'publish' == $message->post_status );
    }

    private function user_message_role( $message_id )
    {
        if ( ! intval( $message_id ) || ! is_user_logged_in() ) {
            return false;
        }

        $current_user_id = get_current_user_id();

        $sender_id = get_post_meta( $message_id, 'message_sender_id', true );
        $receiver_id = get_post_meta( $message_id, 'message_receiver_id', true );

        if ( ! in_array( get_current_user_id(), array( $sender_id, $receiver_id ) ) ) {
            return false;
        }

        return ( $sender_id == $current_user_id ) ? 'sender' : 'receiver';
    }

    private function is_message_available_for( $user_role, $message_id )
    {
        if ( ! in_array( $user_role, array('sender', 'receiver') ) || ! intval( $message_id ) ) {
            return false;
        }

        $meta_key = "_message_deleted_for_{$user_role}";

        return get_post_meta( $message_id, $meta_key, true ) ? false : true;
    }

    private function _delete_messages( $list )
    {
        $user_id = get_current_user_id();

        // Make sure user have permission to delete this message
        foreach ( $list as $message_id ) {

            if ( ! $this->is_valid_message_id( $message_id ) || ! ( $user_role = $this->user_message_role( $message_id ) ) ) {

                return new \WP_Error(
                    'restricted', esc_html__('You do not have permission to delete one of the selected message.', 'wyzi-business-finder')
                );
            }

            $meta_key = "{$user_role}_status";
            $current_message_status = get_post_meta( $message_id, $meta_key, true );

            $new_message_status = 'trash';

            if ( 'trash' == $current_message_status ) {
                $new_message_status = 'permanent_deleted';
            }

            // delete this message for the current user only
            update_post_meta( $message_id, $meta_key, $new_message_status );
        }

        // send success message
        return sprintf(
                esc_html__('The %s has been deleted successfully.', 'wyzi-business-finder'),
                _n('message', 'messages', count( $list ), 'wyzi-business-finder' )
            );
    }

    private function _move_to_inbox( $list )
    {
        $user_id = get_current_user_id();

        // Make sure user have permission to delete this message
        foreach ( $list as $message_id ) {

            if ( ! $this->is_valid_message_id( $message_id ) || 'receiver' != $this->user_message_role( $message_id )  || 'trash' != get_post_meta( $message_id, 'receiver_status', true ) ) {

                return new \WP_Error(
                    'restricted', esc_html__('You do not have permission to move one of the selected message.', 'wyzi-business-finder')
                );
            }

            // delete this message for the current user only
            update_post_meta( $message_id, 'receiver_status', 'inbox' );
        }

        // send success message
        return sprintf(
                esc_html__('The %s moved to inbox successfully.', 'wyzi-business-finder'),
                _n('message', 'messages', count( $list ), 'wyzi-business-finder' )
            );
    }

    private function _block_user( $list )
    {
        $user_id = get_current_user_id();

        $blocked_users = get_the_author_meta( '_pm_blocked_users', $user_id );

        if ( ! $blocked_users ) {
            $blocked_users = array();
        }

        // Make sure user have permission to delete this message
        foreach ( $list as $message_id ) {

            if ( ! $this->is_valid_message_id( $message_id ) || ! ( $user_role = $this->user_message_role( $message_id ) ) ) {

                return new \WP_Error(
                    'restricted', esc_html__('You do not have permission to block one of the selected message.', 'wyzi-business-finder')
                );
            }

            $message = get_post( $message_id );

            if ( ! in_array( $message->post_author, $blocked_users ) ) {
                $blocked_users[] = $message->post_author;
            }

            // delete this message for the current user only
            $meta_key = "{$user_role}_status";

            update_post_meta( $message_id, $meta_key, 'permanent_deleted' );

            if ( add_user_meta( $user_id, '_pm_blocked_users', $blocked_users, true ) ) {
                update_user_meta( $user_id, '_pm_blocked_users', $blocked_users );
            }
        }

        // send success message
        return sprintf(
                esc_html__('You have successfully blocked the %s.', 'wyzi-business-finder'),
                _n('user', 'users', count( $list ), 'wyzi-business-finder' )
            );
    }

    private function _report_message( $selected_items, $action_name )
    {
        $action_meta_key = 'reported_spam';

        if ( 'report-harassment' == $action_name ) {
            $action_meta_key = 'reported_harassment';
        }

        $user_id = get_current_user_id();

        foreach ( $selected_items as $message_id ) {

            if ( ! $this->is_valid_message_id( $message_id ) || ! ( $user_role = $this->user_message_role( $message_id ) ) ) {

                return new \WP_Error(
                    'restricted', esc_html__('You do not have permission on one of the selected message.', 'wyzi-business-finder')
                );
            }
            
            $post_parent = private_message_get_parent_id( $message_id );
            update_post_meta( $post_parent, $action_meta_key, true );
            
            $user_role = private_message_the_user_role( $message_id );
            
            $meta_key = "{$user_role}_status";
            update_post_meta( $post_parent, $meta_key, 'permanent_deleted' );
            
            // $blocked_users_list = get_user_meta( $user_id, 'pm_blocked_users_list', true );
            
            // if ( ! $blocked_users_list ) {
            //     $blocked_users_list = array();
            // }
            
            // $sender_id = get_post_meta( $post_parent, 'message_sender_id', true );
            
            // if ( ! in_array( $sender_id, $blocked_users_list ) ) {
            //     $blocked_users_list[] = $sender_id;    
            // }
            
            // if ( ! add_user_meta( $user_id, 'pm_blocked_users_list', $blocked_users_list, true ) ) {
            //     update_user_meta( $user_id, 'pm_blocked_users_list', $blocked_users_list );
            // }
            
            // @TODO: report to admin
        }

        return esc_html__('Thank you for reporting.', 'wyzi-business-finder');
    }


    private function additional_meta_fields()
    {
        return array(
            'receiver_status'       => 'inbox', // trash, permanent_deleted
            'sender_status'         => 'sent_item', // trash, permanent_deleted
            'reported_spam'         => false,
            'reported_harassment'   => false,
            'read_status'           => false,
            'sender_read_status'    => true,
            'have_replies'          => false,
            'last_reply_date'       => '',
            'business_id_sent_from' => ''
        );
    }

    public static function the_submit_buttom(){
        if ( empty( self::$SubmitButton ) ) {

            self::$SubmitButton = '<button class="XMB__form-submit"><i class="fa fa-send"></i>' . esc_html__('Send', 'wyzi-business-finder') . '</button>';
        }
        echo self::$SubmitButton;
    }
}

new Messages;

add_action( 'init', function(){
    if ( ! isset( $_GET['download_attachment'] ) ) 
        return;
    global $wpdb;
    
    if ( empty( $_GET['download_attachment'] ) ) {
        wp_die( esc_html__( 'Invalid Request, Please Try Again', 'wyzi-business-finder' ) );
    }

    $attachment_hash = $_GET['download_attachment'];

    $attachments = new WP_Query( array(
        'post_type'     => 'attachment',
        'post_status'   => 'inherit',
        'meta_query' => array(
            array(
                'key' => 'attachment_hash',
                'value' => $attachment_hash,
            )
        ),
        'posts_per_page'=> 1,
    ) );
    
    $no_access_message = esc_html__( 'You are not allowed to view this attachment', 'wyzi-business-finder' );

    if ( ! $attachments->have_posts() ) {
        wp_die( $no_access_message );
    }
    $attachment = current( $attachments->posts );

    $attachment_link = get_the_guid( $attachment->ID );

    $url_parts = explode('/', $attachment_link );

    $attachment_name = end( $url_parts );

    $attachment_path = get_attached_file( $attachment->ID );

    header_remove();
    // header( "HTTP/1.1 200 OK" );
    header("Pragma: public");
    header("Expires: 0");
    header('Content-Type: application/octet-stream');
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    
    header("Content-Disposition: attachment; filename=$attachment_name");
    header("Content-Transfer-Encoding: Binary");
    header('Content-Length: ' . filesize( $attachment_path ) );
    
    ob_clean();
    flush();
    readfile( $attachment_path );
    exit();
});

add_filter( 'woocommerce_is_purchasable', function( $is, $product ){
    $id = $product->get_id();
    if ( $product->get_status() == 'pending' && get_post_meta($id, 'client_id', true) == get_current_user_id() )
        return true;
    return $is;
},10,2);

add_action( 'wp_loaded', function(){
    if ( isset( $_GET['service_message'] ) ){
       wyz_handle_new_service_message_request();
   } elseif ( isset( $_POST['serv-approval'] ) )
        wyz_handle_service_message_approval();
});
function wyz_handle_new_service_message_request() {
    if ( ! class_exists('WooCommerce'))return;
    $token_content = array();
    if ( ! $token_content = wyz_parse_chat_service_token( $_GET['service_message'] ) )return;

    $id = $token_content['comment']->comment_ID;

    $message_sender_id = 
    $name = $token_content['name'];
    $price = $token_content['price'];
    $duration = $token_content['duration'];
    $parent = $token_content['comment']->comment_post_ID;

    if ( 'yes' == get_post_meta( $parent, 'chat_has_purchase', true ) )
        return;

    /*setup new product*/
    $users_query = new WP_User_Query( array( 
        'role' => 'administrator', 
        'orderby' => 'display_name'
    ) );
    $results = $users_query->get_results();
    $admin = 1;
    if ( ! empty( $results ) ) {
        $admin = $results[0]->data->ID;

    }

    $vendor_id = get_comment_meta( $id, 'message_sender_id', true );
    $client_id = get_comment_meta( $id, 'message_receiver_id', true );

    if ( $client_id != get_current_user_id() )return;
    $service_product = array(
        'post_author' => $vendor_id,
        'post_content' => '<h3>Service Message</h3>' . 
                          '<p>Vendor id: '. $vendor_id .'</p>' .
                          '<p>Client id: '. $client_id .'</p>' .
                          '<p>Service name: '. $name .'</p>' .
                          '<p>Service Price: '. $price .'</p>' .
                          '<p>Service Duration: '. $duration .'</p>',
        'post_status' => 'publish',
        'post_title' => $name,
        'post_type' => "product"
    );

    $product_id = wp_insert_post( $service_product );
    if ( is_wp_error( $product_id ) )return;

    update_post_meta($product_id, '_regular_price', $price);
    update_post_meta($product_id, '_price', $price);
    update_post_meta($product_id, 'total_sales', '0');

    update_post_meta($product_id, 'service_comment', $id );

    update_post_meta($product_id, 'vendor_id', $vendor_id);
    update_post_meta($product_id, 'client_id', $client_id);
    update_post_meta($product_id, '_virtual', 'yes');

    // Assign Product to Vendor so he sees it in his Products List in WCMP Dashboard
    $vendor_term = get_user_meta($vendor_id, '_vendor_term_id', true);
    $term        = get_term($vendor_term, 'dc_vendor_shop');
    if(!is_wp_error($term)){
        wp_delete_object_term_relationships($product_id, 'dc_vendor_shop');
        wp_set_post_terms($product_id, $term->name, 'dc_vendor_shop', true);
    }

    //Add new product to user cart
    $found = false;
    //check if product already in cart
    if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
        foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
            $_product = $values['data'];
            if ( $_product->get_id() == $product_id )
                $found = true;
        }
        // if product not found, add it
        if ( ! $found )
            WC()->cart->add_to_cart( $product_id );
    } else {
        // if no products in cart, add it
        WC()->cart->add_to_cart( $product_id );
    }

    $checkout_id = get_option( 'woocommerce_checkout_page_id' );
    if ( empty( $checkout_id ) ) return;
    $url = get_permalink( $checkout_id ); 
    wp_redirect( $url );
    exit();
}

function wyz_handle_service_message_approval() {
    if ( ! function_exists( 'wc_get_order' ) )
        return;
    $token = explode( ':::', WyzHelpers::encrypt( $_POST['serv-approval'], 'd' ) );
    if ( count( $token ) != 2 )
        return;
    $comment_id = $token[0];
    $conf = hash('sha256', 'serv_approv_' . get_current_user_id() . '_' . $comment_id );
    if ( $conf != $token[1] )
        return;

    $comment = get_comment( $comment_id );
    if ( ! $comment )
        return;
    $parent = $comment->comment_post_ID;
    //set order status to complete
    $order_id = get_post_meta( $parent, 'chat_purchase_order_id', true );
    $order = wc_get_order( $order_id );
    if ( ! is_a( $order, 'WC_Order' ) )
        return;
    
    $order->update_status( 'completed' );
    //update chat messages to reflect approval
    update_post_meta( $parent, 'chat_purchase_completed', 'yes' );
    update_comment_meta( $comment_id, 'chat_purchase_completed', 'yes' );

    $data = array(
        'comment_post_ID' => $parent,
        'comment_content' => '',
        'user_id' => get_current_user_id(),
        'comment_date' => current_time('mysql')
    );


    $serv_start_comment = wp_insert_comment($data);

    update_comment_meta( $serv_start_comment, 'is_serv_approval_comment', 'yes' );

    //send emails
    if ( function_exists( 'wyz_get_option' ) ) {

        $message_sender_id = get_post_meta( $parent, 'message_sender_id', true );
        $message_receiver_id = get_post_meta( $parent, 'message_receiver_id', true );

        $subject = wyz_get_option( 'new-chat-service-approved-email-subject' );
        if ( empty( $subject ) )
            $subject = 'Service Approved';

        $message = wyz_get_option( 'new-chat-service-approved-email' );

        $sender_data = get_user_meta( $message_sender_id );
        $receiver_data = get_user_meta( $message_receiver_id );


        $service_name = get_comment_meta( $comment_id, 'service_name', true );
        $service_duration = intval( get_comment_meta( $comment_id, 'service_duration', true ) );
        $service_price = get_comment_meta( $comment_id, 'service_price', true );
        $service_start_at = get_comment_meta( $comment_id, 'service_start_at', true );
        
        $service_info = sprintf( '<p>' . esc_html__( 'Service Name: %s', 'wyzi-business-finder' ) . '</p>' .
                         '<p>' . esc_html__( 'Service Price: %s %s', 'wyzi-business-finder' ) . '</p>' .
                         '<p>' . esc_html__( 'Service Scheduled Duration: %s day(s)', 'wyzi-business-finder' ) . '</p>' .
                         '<p class="duration"><span>' . ( $service_start_at != '' ? esc_html__( 'Starts at: %s (%s)', 'wyzi-business-finder' ) : esc_html__( 'Starts upon payment', 'wyzi-business-finder' ) . '%s' ),
                           $service_name, $service_price, get_woocommerce_currency(), $service_duration, $service_start_at, WyzHelpers::get_wp_timezone() );

        $message1 = str_replace( '%FIRST_NAME%', $sender_data->first_name, $message );
        $message1 = str_replace( '%LAST_NAME%', $sender_data->last_name, $message1 );
        $message1 = str_replace( '%SERVICE_INFO%', $service_info, $message1 );

        WyzHelpers::wyz_mail( $sender_data->user_email, $subject, $message1, 'chat-service-complete' );

        $message2 = str_replace( '%FIRST_NAME%', $receiver_data->first_name, $message );
        $message2 = str_replace( '%LAST_NAME%', $receiver_data->last_name, $message2 );
        $message2 = str_replace( '%SERVICE_INFO%', $service_info, $message2 );

        WyzHelpers::wyz_mail( $receiver_data->user_email, $subject, $message2, 'chat-service-complete' );
    }
}
?>