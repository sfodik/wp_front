<?php

namespace codingninjaschild;

class ModelFreelancers {
	public function getAll() {
		$args = array(
			'numberposts' => - 1,
			'post_type'   => Freelancer::POST_TYPE
		);

		$posts = get_posts( $args );

		if ( ! $posts ) {
			return false;
		}

		foreach ( $posts as &$post ) {
			$post = new Freelancer( $post );
		}

		return $posts;
	}
}