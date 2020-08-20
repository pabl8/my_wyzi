<?php

namespace Wyzi\core;

class MessagePost
{
    public function __construct()
    {
        add_action( 'init', array( &$this, 'register_post_type' ) );

        // add_action( 'admin_head', array( &$this, 'remove_media_buttons') );

        add_action( 'manage_private-message_posts_columns' , array( &$this, 'custom_columns' ) );
        add_action( 'manage_private-message_posts_custom_column', array( &$this, 'custom_column_data' ), 10, 2 );
    }

    public function register_post_type()
    {
        $labels = array(
            'name'                  => esc_html_x( 'Private Messages', 'Post Type General Name', 'wyzi-business-finder' ),
            'singular_name'         => esc_html_x( 'Private Message', 'Post Type Singular Name', 'wyzi-business-finder' ),
            'menu_name'             => esc_html__( 'Private Messages', 'wyzi-business-finder' ),
            'name_admin_bar'        => esc_html__( 'Private Message', 'wyzi-business-finder' ),
            'archives'              => esc_html__( 'Private Message Archives', 'wyzi-business-finder' ),
            'attributes'            => esc_html__( 'Private Message Attributes', 'wyzi-business-finder' ),
            'parent_item_colon'     => esc_html__( 'Parent Private Message:', 'wyzi-business-finder' ),
            'all_items'             => esc_html__( 'All Private Messages', 'wyzi-business-finder' ),
            'add_new_item'          => esc_html__( 'Add New Private Message', 'wyzi-business-finder' ),
            'add_new'               => esc_html__( 'Add New', 'wyzi-business-finder' ),
            'new_item'              => esc_html__( 'New Private Message', 'wyzi-business-finder' ),
            'edit_item'             => esc_html__( 'Edit Private Message', 'wyzi-business-finder' ),
            'update_item'           => esc_html__( 'Update Private Message', 'wyzi-business-finder' ),
            'view_item'             => esc_html__( 'View Private Message', 'wyzi-business-finder' ),
            'view_items'            => esc_html__( 'View Private Messages', 'wyzi-business-finder' ),
            'search_items'          => esc_html__( 'Search Private Message', 'wyzi-business-finder' ),
            'not_found'             => esc_html__( 'Not found', 'wyzi-business-finder' ),
            'not_found_in_trash'    => esc_html__( 'Not found in Trash', 'wyzi-business-finder' ),
            'featured_image'        => esc_html__( 'Featured Image', 'wyzi-business-finder' ),
            'set_featured_image'    => esc_html__( 'Set featured image', 'wyzi-business-finder' ),
            'remove_featured_image' => esc_html__( 'Remove featured image', 'wyzi-business-finder' ),
            'use_featured_image'    => esc_html__( 'Use as featured image', 'wyzi-business-finder' ),
            'insert_into_item'      => esc_html__( 'Insert into Private Message', 'wyzi-business-finder' ),
            'uploaded_to_this_item' => esc_html__( 'Uploaded to this Private Message', 'wyzi-business-finder' ),
            'items_list'            => esc_html__( 'Private Messages list', 'wyzi-business-finder' ),
            'items_list_navigation' => esc_html__( 'Private Messages list navigation', 'wyzi-business-finder' ),
            'filter_items_list'     => esc_html__( 'Filter Private Messages list', 'wyzi-business-finder' ),
        );

        $args = array(
            'label'               => esc_html__( 'Private Message', 'wyzi-business-finder' ),
            'description'         => esc_html__( 'Private message between users', 'wyzi-business-finder' ),
            'labels'              => $labels,
            'menu_icon'           => 'dashicons-email-alt',
            'supports'            => array( 'title', 'editor', 'author', 'page-attributes', 'comments' ),
            'taxonomies'          => array(),
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 80,
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => false,
            'can_export'          => true,
            'has_archive'         => false,
            'hierarchical'        => true,
            'exclude_from_search' => true,
            'show_in_rest'        => false,
            'publicly_queryable'  => false,
            'capability_type'     => 'post',
        );

        register_post_type( 'private-message', $args );
    }

    public function remove_media_buttons()
    {
        global $current_screen;

        if ( 'private-message' == $current_screen->post_type ) {
            remove_action('media_buttons', 'media_buttons');
        }
    }

    public function custom_columns( $columns )
    {
        return array(
            'cb'            => '<input type="checkbox" />',
            'title'         => esc_html__('Subject', 'wyzi-business-finder'),
            'sender_name'   => esc_html__('Sender', 'wyzi-business-finder'),
            'receiver_name' => esc_html__('Recevier', 'wyzi-business-finder'),
            'date'          => $columns['date']
        );
    }

    public function custom_column_data( $column, $post_id )
    {
        switch( $column ) {

            case "sender_name" :
            case "receiver_name" :

                $meta_key = ( "sender_name" == $column ) ? "message_sender_id" : "message_receiver_id";

                $user_id = get_post_meta( $post_id, $meta_key, true );
                $userdata = get_user_by( 'id', $user_id );

                if ( ! $userdata ) {
                    return null;
                }

                $username = $userdata->display_name;

                if ( ! $username ) {
                    $username = $userdata->user_nicename;
                }

                echo '<a href="'. get_edit_user_link( $userdata->ID ) .'">'. esc_attr( $username ) .'</a>';

            break;
        }
    }
}

new MessagePost;