<script type="text/html" id="tmpl-send-message-form">
    <div class="X__msg-box open">
        <div class="XMB__content">

            <div class="XMB__header">
                <h4 class="XMBH__title">
                    <?php esc_html_e('New Message', 'wyzi-business-finder'); ?>
                </h4>

                <span class="XMBH__close"><i class="fa fa-times" aria-hidden="true"></i></span>
            </div>
            
            <div id="private_message_form">
                <form method="post" class="XMB__form" action="<?php echo $_SERVER['REQUEST_URI'];?>" id="send_message_form" enctype="multipart/form-data">
                    <div class="XMB__body">
    
                        <div class="xmb-form-field xmb-subject">
                            <input type="text" name="subject" placeholder="<?php esc_attr_e( 'Subject', 'wyzi-business-finder' ); ?>" required>
                        </div>
    
                        <div class="xmb-form-field xmb-editor">
                            <textarea name="message" rows="6" placeholder="<?php esc_attr_e( 'Your message', 'wyzi-business-finder' ); ?>" required></textarea>
                        </div>
                        
                        <div class="xmb-form-field xmb-editor">
                            <input type="file" name="attachment" />
                        </div>
                    </div>
    
                    <div class="XMB__footer">
                        <?php Messages::the_submit_buttom();?>
                    </div>
    
                    <input type="hidden" name="action" value="oz_send_message" />
                    <input type="hidden" name="send_message_to" value="{{{ data.receiverId }}}" />
                    <input type="hidden" name="current__id" value="<?php global $post; echo $post->ID; ?>" />
                    <input type="hidden" name="secret" value="<?php echo wp_create_nonce( "private-message" ); ?>" />
                </form>
            </div>
        </div>
    </div>
</script>