<?php

abstract class AccountContent {

	protected $active;
	public $condition;
	public $tab_title;
	public $link;
	public $icon;
	protected $is_business_owner;
	protected $user_id;
	protected $template_type;
	public $link_pre;

	public function __construct( $is_business_owner, $user_id, $is_user_account_page = true ) {
		global $WYZ_USER_ACCOUNT_TYPE;
		$this->is_business_owner = $is_business_owner;
		$this->user_id = $user_id;
		$this->template_type = 1;
		$this->link_pre = !$is_user_account_page ? esc_url( home_url( '/user-account' ) ) : '';
		if ( function_exists( 'wyz_get_theme_template' ) )
			$this->template_type = wyz_get_theme_template();
		$this->the_condition();
		$this->tab_title();
		$this->active();
		$this->link();
		$this->link_pre = apply_filters( 'wyz_account_tab_link_pre', $this->link_pre, $this->link, $is_user_account_page );
		$this->link = apply_filters( 'wyz_account_tab_link', $this->link, $is_user_account_page );
		$this->icon();
	}

	abstract protected function the_condition();
	abstract protected function _active();
	abstract protected function tab_title();
	abstract protected function link();
	abstract protected function icon();
	abstract protected function notifications();
	abstract protected function content();

	private function active() {

		if ( $this->_active() ) {

			$this->active = ' active';
		} else {

			$this->active = '';
		}
	}


	public function the_tab($in_menu=false){
		if ( $this->condition ) {
			$link_w = substr($this->link, 1);
			echo '<li class="'. $this->active . ' ' . $link_w . '" ><div class="tab-overlay"></div><a class="profile-tab wyz-prim-color-txt-hover" data-link="' . $link_w . '" id="link-' . $link_w . ($in_menu ?'-m':'') . '" href="' . $this->link_pre . $this->link . '">' . $this->tab_title . '</a></li>';
		}
	}

	public function the_tab_drop(){
		if ( $this->condition ) {
			$val = substr($this->link, 1);
			echo '<option' . ( 'active' == $this->active ? ' selected' : '' ) . ' value="' . $val . '" class="'. $this->active . ' ' . $val . '" >' . $this->tab_title . '</option>';
		}
	}

	public function the_content() {

		if ( $this->condition ) {
			if ( substr($this->link, 0, 1) == "#" )
				$l = substr($this->link, 1);
			else
				$l = $this->link;
			echo '<div class="tab-pane' . $this->active . '" id="' . $l . '">';
			
			$this->notifications();
			$this->content();
			
			echo '</div>';
		}
		
	}
}

?>