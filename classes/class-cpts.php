<?php
/**
 * Created by PhpStorm.
 * User: ezeidan
 * Date: 16/01/18
 * Time: 17:34
 */

if(!class_exists('ivsCpt')) {
	class ivsCpt {
		public function __construct() {
			$this->initHooks();
		}

		public function initHooks() {
			add_action('init', array($this, 'galleryPostType'));
			add_action('init', array($this, 'videosPostType'));
			add_action( 'init', array($this, 'createTagForGallery'), 0 );
			add_action( 'init', array($this, 'createTagForVideos'), 0 );
		}

		public function galleryPostType() {
		register_post_type( 'gallery',
			array(
				'labels' => array(
					'name' => __( 'Image Gallery' ),
					'singular_name' => __( 'Images Gallery' )
				),
				'public' => true,
				'has_archive' => true,
				'rewrite' => array('slug' => 'galeria'),
				'supports' => array('title'),
				'menu_icon' => 'dashicons-images-alt'
			)
		);

		}

		public function videosPostType() {
			register_post_type( 'videos',
				array(
					'labels'      => array(
						'name'          => __( 'Video Gallery' ),
						'singular_name' => __( 'Videos Gallery' )
					),
					'public'      => true,
					'has_archive' => true,
					'rewrite'     => array( 'slug' => 'videos' ),
					'supports'    => array( 'title' ),
					'menu_icon'   => 'dashicons-video-alt'
				)
			);
		}

		public function createTagForGallery()
		{
			$labels = array(
				'name' => _x( 'Tags', 'taxonomy general name' ),
				'singular_name' => _x( 'Tag', 'taxonomy singular name' ),
				'search_items' =>  __( 'Search Tags' ),
				'popular_items' => __( 'Popular Tags' ),
				'all_items' => __( 'All Tags' ),
				'parent_item' => null,
				'parent_item_colon' => null,
				'edit_item' => __( 'Edit Tag' ),
				'update_item' => __( 'Update Tag' ),
				'add_new_item' => __( 'Add New Tag' ),
				'new_item_name' => __( 'New Tag Name' ),
				'separate_items_with_commas' => __( 'Separate tags with commas' ),
				'add_or_remove_items' => __( 'Add or remove tags' ),
				'choose_from_most_used' => __( 'Choose from the most used tags' ),
				'menu_name' => __( 'Tags' ),
			);

			register_taxonomy('tag','gallery',array(
				'hierarchical' => false,
				'labels' => $labels,
				'show_ui' => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var' => true,
				'rewrite' => array( 'slug' => 'tag' ),
			));
		}

		public function createTagForVideos()
		{
			$labels = array(
				'name' => _x( 'Tags', 'taxonomy general name' ),
				'singular_name' => _x( 'Tag', 'taxonomy singular name' ),
				'search_items' =>  __( 'Search Tags' ),
				'popular_items' => __( 'Popular Tags' ),
				'all_items' => __( 'All Tags' ),
				'parent_item' => null,
				'parent_item_colon' => null,
				'edit_item' => __( 'Edit Tag' ),
				'update_item' => __( 'Update Tag' ),
				'add_new_item' => __( 'Add New Tag' ),
				'new_item_name' => __( 'New Tag Name' ),
				'separate_items_with_commas' => __( 'Separate tags with commas' ),
				'add_or_remove_items' => __( 'Add or remove tags' ),
				'choose_from_most_used' => __( 'Choose from the most used tags' ),
				'menu_name' => __( 'Tags' ),
			);

			register_taxonomy('video_tags','videos',array(
				'hierarchical' => false,
				'labels' => $labels,
				'show_ui' => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var' => true,
				'rewrite' => array( 'slug' => 'tag' ),
			));
		}
	}

	new ivsCpt();
}