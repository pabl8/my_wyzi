<?php 
$favorites_count = $this->get_favorites_count();
$visits = $this->get_visits_count();
$posts_count = $this->get_posts_count();
$likes_count = $this->get_likes_count();
$comments_count = $this->get_comments_count();

$total_rates = $this->get_rates();

if ( ! current_user_can( 'manage_options') ) {
	$earnings = $this->get_vendor_earnings();
	$sales = $this->get_vendor_sales();
	$products_sold = $this->get_vendor_sold_products();
	$products_count = $this->get_products_count();
}
$inbox_stats = wyz_get_private_message_status_count();
$user_info = get_userdata( $this->user_id );
if ( ! empty( $user_info->first_name ) || ! empty( $user_info->last_name ) )
	$user_creds = $user_info->first_name . ' ' . $user_info->last_name;
else
	$user_creds = $user_info->display_name;
?>
<div class="row-fluid-n">		
	<div class="col-sm-12 mb-4">
        <div class="card col-lg-2 col-md-4 col-sm-6 col-xs-12 no-padding ">
            <div class="card-body">
                <div class="h1 text-muted text-right mb-4">
                    <i class="fa fa-users"></i>
                </div>

                <div class="h4 mb-0">
                    <span class="count stats-counter"><?php echo $visits;?></span>
                </div>

                <small class="text-muted text-uppercase font-weight-bold"><?php esc_html_e('Visitors','wyzi-business-finder');?></small>
                <div class="progress progress-xs mt-3 mb-0 bg-flat-color-1" style="width: 40%; height: 5px;"></div>
            </div>
        </div>
        <div class="card col-lg-2 col-md-4 col-sm-6 col-xs-12 no-padding ">
            <div class="card-body">
                <div class="h1 text-muted text-right mb-4">
                    <i class="fa fa-heart"></i>
                </div>
                <div class="h4 mb-0">
                    <span class="count stats-counter"><?php echo $favorites_count;?></span>
                </div>
                <small class="text-muted text-uppercase font-weight-bold"><?php esc_html_e('Favorites','wyzi-business-finder');?></small>
                <div class="progress progress-xs mt-3 mb-0 bg-flat-color-2" style="width: 40%; height: 5px;"></div>
            </div>
        </div>
        <div class="card col-lg-2 col-md-4 col-sm-6 col-xs-12 no-padding ">
            <div class="card-body">
                <div class="h1 text-muted text-right mb-4">
                    <i class="fa fa-thumb-tack"></i>
                </div>
                <div class="h4 mb-0">
                    <span class="count stats-counter"><?php echo $posts_count;?></span>
                </div>
                <small class="text-muted text-uppercase font-weight-bold"><?php esc_html_e('Posts','wyzi-business-finder');?></small>
                <div class="progress progress-xs mt-3 mb-0 bg-flat-color-3" style="width: 40%; height: 5px;"></div>
            </div>
        </div>
        <div class="card col-lg-2 col-md-4 col-sm-6 col-xs-12 no-padding ">
            <div class="card-body">
                <div class="h1 text-muted text-right mb-4">
                    <i class="fa fa-thumbs-up"></i><!-- fa-cart-plus fa-pie-chart -->
                </div>
                <div class="h4 mb-0">
                    <span class="count stats-counter"><?php echo $likes_count;?></span>
                </div>
                <small class="text-muted text-uppercase font-weight-bold"><?php esc_html_e('Likes','wyzi-business-finder');?></small>
                <div class="progress progress-xs mt-3 mb-0 bg-flat-color-4" style="width: 40%; height: 5px;"></div>
            </div>
        </div>
        <div class="card col-lg-2 col-md-4 col-sm-6 col-xs-12 no-padding ">
            <div class="card-body">
                <div class="h1 text-muted text-right mb-4">
                    <i class="fa fa-star"></i>
                </div>
                <div class="h4 mb-0"><?php echo $total_rates;?></div>
                <small class="text-muted text-uppercase font-weight-bold"><?php esc_html_e( 'Avg. Ratings', 'wyzi-business-finder' );?></small>
                <div class="progress progress-xs mt-3 mb-0 bg-flat-color-5" style="width: 40%; height: 5px;"></div>
            </div>
        </div>
        <div class="card col-lg-2 col-md-4 col-sm-6 col-xs-12 no-padding ">
            <div class="card-body">
                <div class="h1 text-muted text-right mb-4">
                    <i class="fa fa-comments-o"></i>
                </div>
                <div class="h4 mb-0">
                    <span class="count stats-counter"><?php echo $comments_count;?></span>
                </div>
                <small class="text-muted text-uppercase font-weight-bold"><?php esc_html_e('Comments','wyzi-business-finder');?></small>
                <div class="progress progress-xs mt-3 mb-0 bg-flat-color-1" style="width: 40%; height: 5px;"></div>
            </div>
        </div>
    </div>

    <div class="card-group mb-4 clear-both">
	    <div class="col-lg-8 col-xs-12">
	        <div class="card">
	            <div class="card-body">
	                <h4 class="mb-3"><?php esc_html_e( 'Visitors Over the last week', 'wyzi-business-finder' );?> </h4>
	                <canvas id="barChart"></canvas>
	            </div>
	        </div>
	    </div>

	    <div class="col-lg-4 col-xs-12">
	        <aside class="profile-nav alt">
	            <section class="card">
	                <div class="card-header user-header alt bg-dark">
	                    <div class="media">
	                        <a href="#">
	                        	<?php echo get_avatar( $this->user_id, 85, '', '', array( 'class' => 'align-self-center rounded-circle mr-3') ); ?>
	                        </a>
	                        <div class="media-body">
	                            <h2 class="text-light display-6"><?php echo $user_creds;?></h2>
	                        </div>
	                    </div>
	                </div>

	                <?php $inb_lnk = get_the_permalink() . '?page=inbox';?>
	                <ul class="list-group list-group-flush">
	                    <li class="list-group-item">
	                        <a href="<?php echo $inb_lnk;?>"> <i class="fa fa-envelope-o"></i> <?php esc_html_e( 'Inbox', 'wyzi-business-finder' );?> <span class="badge badge-primary pull-right"><?php echo $inbox_stats['inbox'];?></span></a>
	                    </li>
	                    <li class="list-group-item">
	                        <a href="<?php echo "$inb_lnk&action=sent_items";?>"> <i class="fa fa-sign-out"></i> <?php esc_html_e( 'Sent', 'wyzi-business-finder' );?> <span class="badge badge-success pull-right"><?php echo $inbox_stats['sent'];?></span></a>
	                    </li>
	                    <li class="list-group-item">
	                        <a href="<?php echo "$inb_lnk&action=trash";?>"> <i class="fa fa-trash-o"></i> <?php esc_html_e( 'Trash', 'wyzi-business-finder' );?> <span class="badge badge-danger pull-right"><?php echo $inbox_stats['trash'];?></span></a>
	                    </li>
	                    <li class="list-group-item">
	                        <a href="#"> <i class="fa fa-envelope-open-o"></i> <?php esc_html_e( 'Not Read', 'wyzi-business-finder' );?> <span class="badge badge-warning pull-right r-activity"><?php echo $inbox_stats['not_read'];?></span></a>
	                    </li>
	                </ul>

	            </section>
	        </aside>
	    </div>
	</div>

    <?php if ( ! current_user_can( 'manage_options') && WyzHelpers::is_user_vendor( $this->user_id ) ) {?>
    <div class="mb-4 clear-both">
	    <div class="col-lg-3 col-md-6">
	        <div class="card">
	            <div class="card-body">
	                <div class="stat-widget-one">
	                    <div class="stat-icon dib"><i class="ti-money text-primary border-primary fa fa-usd"></i></div>
	                    <div class="stat-content dib">
	                        <div class="stat-text"><?php esc_html_e( 'Total Sales', 'wyzi-business-finder' );?></div>
	                        <div class="stat-digit"><div class="stats-counter"><?php echo $sales;?></div> <span><?php echo get_woocommerce_currency_symbol();?></span></div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>

	    <div class="col-lg-3 col-md-6">
	        <div class="card">
	            <div class="card-body">
	                <div class="stat-widget-one">
	                    <div class="stat-icon dib"><i class="ti-layout-grid2 text-success border-success fa fa-credit-card"></i></div>
	                    <div class="stat-content dib">
	                        <div class="stat-text"><?php esc_html_e( 'Total Profit', 'wyzi-business-finder' );?></div>
	                        <div class="stat-digit"><div class="stats-counter"><?php echo $earnings;?></div> <span><?php echo get_woocommerce_currency_symbol();?></span></div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>


	    <div class="col-lg-3 col-md-6">
	        <div class="card">
	            <div class="card-body">
	                <div class="stat-widget-one">
	                    <div class="stat-icon dib"><i class="ti-link text-warning border-warning fa fa-shopping-bag"></i></div>
	                    <div class="stat-content dib">
	                        <div class="stat-text"><?php esc_html_e( 'Number of Orders');?></div>
	                        <div class="stat-digit stats-counter"><?php echo $products_sold;?></div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	    <div class="col-lg-3 col-md-6">
	        <div class="card">
	            <div class="card-body">
	                <div class="stat-widget-one">
	                    <div class="stat-icon dib"><i class="ti-user text-danger border-danger fa fa-shopping-cart"></i></div>
	                    <div class="stat-content dib">
	                        <div class="stat-text"><?php esc_html_e( 'Products Number', 'wyzi-business-finder' );?></div>
	                        <div class="stat-digit stats-counter"><?php echo $products_count;?></div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	    <div class="clear-both"></div>
	</div>
	<?php }?>
</div>