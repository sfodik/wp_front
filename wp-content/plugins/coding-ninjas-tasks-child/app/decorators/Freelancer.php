<?php

namespace codingninjaschild;

use codingninjas\Task;
use \WP_Post;
use WP_Query;

class Freelancer {
	/**
	 * post instance
	 * @var WP_Post
	 */
	protected $post;

	/**
	 * post type name
	 */
	const POST_TYPE = 'freelancer';

	/**
	 * Task constructor.
	 *
	 * @param WP_Post $post
	 */
	public function __construct( WP_Post $post ) {
		$this->post = $post;
	}

	/**
	 * freelancer id
	 * @return string
	 */
	public function id() {
		$id = $this->post->ID;

		return apply_filters( 'cn_freelancer_id', $id, $this->post );
	}

	/**
	 * freelancer name
	 * @return string
	 */
	public function name() {
		$name = $this->post->post_title;

		return apply_filters( 'cn_freelancer_name', $name, $this->post );
	}

	/**
	 * Count freelancer task
	 *
	 * @return int
	 */
	public function countTasks() {
		$args = array(
			'posts_per_page'   => -1,
			'post_type'        => Task::POST_TYPE,
			'meta_query' => array(
				array(
					'key' => '_freelancer',
					'value' => $this->post->ID,
					'compare' => '=',
				)
			)
		);
		$query = new WP_Query( $args );

		return apply_filters( 'cn_freelancer_name', $query->found_posts, $this->post );
	}
}