<?php

namespace codingninjaschild;

use codingninjas\ModelTasks;
use codingninjas\Task;
use Exception;

class AppChild {

	/**
	 * Instance of App
	 * @var null
	 */
	public static $instance = null;

	/**
	 * Plugin main file
	 * @var
	 */
	public static $main_file;

	/**
	 * Path to app folder
	 * @var string
	 */
	public static $app_path;

	/**
	 * Url to app folder
	 * @var string
	 */
	public static $app_url;

	/**
	 * Current route
	 * @var
	 */
	public static $route;

	/**
	 * Current page title
	 * @var
	 */
	public static $page_title;

	/**
	 * App constructor.
	 *
	 * @param $main_file
	 */
	public function __construct( $main_file ) {
		self::$main_file = $main_file;
		self::$app_path  = dirname( $main_file ) . '/app';
		self::$app_url   = plugin_dir_url( $main_file ) . 'app';
		spl_autoload_register( array( &$this, 'autoloader' ) );

		$this->initActions();
		$this->initFilters();
	}

	/** Run App
	 *
	 * @param $main_file
	 *
	 * @return AppChild|null
	 */
	public static function run( $main_file ) {
		if ( ! self::$instance ) {
			self::$instance = new self( $main_file );
		}

		return self::$instance;
	}

	/**
	 * Classes autoloader
	 *
	 * @param $class
	 *
	 * @return mixed
	 */
	public function autoloader( $class ) {
		$folders = [
			'decorators',
			'models'
		];

		$parts = explode( '\\', $class );
		array_shift( $parts );
		$class_name = array_shift( $parts );

		foreach ( $folders as $folder ) {
			$file = self::$app_path . '/' . $folder . '/' . $class_name . '.php';
			if ( ! file_exists( $file ) ) {
				continue;
			}

			return require_once $file;

			if ( ! class_exists( $class ) ) {
				continue;
			}
		}
	}

	/**
	 * Init wp actions
	 */
	private function initActions() {
		add_action( 'add_meta_boxes', array( &$this, 'freelancers_meta_box' ) );
		add_action( 'save_post', array( &$this, 'freelancer_save_postdata' ) );
		add_action( 'init', array( &$this, 'onInitPostTypes' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'onInitScripts' ), 21 );
		add_action( 'wp_enqueue_scripts', array( &$this, 'onInitStyles' ), 21 );
		add_action( 'document_title_parts', array( &$this, 'get_document_title' ) );
		add_action( 'wp_footer', array( &$this, 'add_new_task_popup' ), 30 );
		add_action( 'wp_ajax_nopriv_create_new_task', array( &$this, 'create_new_task_callback' ) );
		add_action( 'wp_ajax_create_new_task', array( &$this, 'create_new_task_callback' ) );
		add_shortcode( 'cn_dashboard', array( &$this, 'cn_dashboard_callback' ) );
	}

	/**
	 * Dashboard shortcode
	 *
	 * @return string
	 * @throws Exception
	 */
	public function cn_dashboard_callback() {
		$all_freelancers   = ( new ModelFreelancers() )->getAll();
		$count_freelancers = count( $all_freelancers );

		$all_tasks   = ( new ModelTasks() )->getAll();
		$count_tasks = count( $all_tasks );

		$dashboard = AppChild::view(
			[ 'dashboard.php' ],
			[
				'count_freelancers' => $count_freelancers,
				'count_tasks'       => $count_tasks
			]
		);

		return $dashboard;
	}

	/**
	 * Callback ajax create new post
	 */
	public function create_new_task_callback() {
		$response = array(
			'type'    => 'error',
			'message' => ''
		);

		if ( isset( $_POST['freelancer'] ) && ! empty( $_POST['freelancer'] ) ) {
			$freelancer = sanitize_text_field( $_POST['freelancer'] );
		} else {
			$response['message'] = __( "Please select available freelancer", 'ch' );
		}

		if ( isset( $_POST['task_title'] ) && ! empty( $_POST['task_title'] ) ) {
			$task_title = sanitize_user( $_POST['task_title'], true );
			$task_title = wp_strip_all_tags( $task_title );
		} else {
			$response['message'] = __( "Please complete tack title", 'ch' );
		}

		if ( ! empty( $response['message'] ) ) {
			die( wp_json_encode( $response ) );
		} else {
			$user_id = get_current_user_id();

			$post_data = array(
				'post_type'   => Task::POST_TYPE,
				'post_title'  => $task_title,
				'post_status' => 'publish',
				'post_author' => $user_id,
				'meta_input'  => array(
					'_freelancer' => $freelancer
				),
			);

			$new_task = wp_insert_post( $post_data, true );

			if ( is_wp_error( $new_task ) ) {
				die( wp_json_encode( $new_task->get_error_message() ) );
			} else {
				$response = array(
					'type'    => 'success',
					'message' => __( "Success", 'ch' ),
				);
			}
		}

		die( wp_json_encode( $response ) );
	}

	/**
	 * Adds popup in footer
	 *
	 * @throws Exception
	 */
	public function add_new_task_popup() {
		$popup = AppChild::view(
			[ 'add_new_task_popup.php' ],
			[
				'freelancers' => ( new ModelFreelancers() )->getAll()
			]
		);

		echo $popup;
	}

	/**
	 * Render template
	 *
	 * @param $path
	 * @param array $params
	 *
	 * @return string
	 * @throws Exception
	 */
	public static function view( $path, $params = [] ) {
		if ( is_array( $path ) ) {
			$path = implode( '/', $path );
		}

		$file = self::getViewPath( $path );

		if ( ! file_exists( $file ) ) {
			throw new Exception( 'View not found ' . $file );
		}

		if ( $params ) {
			extract( $params );
		}

		ob_start();

		include $file;

		$content = ob_get_clean();

		return $content;
	}

	/**
	 * Get template path
	 *
	 * @param string $file
	 *
	 * @return string
	 */
	private static function getViewPath( $file = '' ) {
		$path = self::$app_path . '/views/' . $file;

		return apply_filters( 'cn_view_path', $path, $file, self::$route );
	}

	/**
	 * Get document title
	 *
	 * @param $title
	 *
	 * @return mixed
	 */
	public function get_document_title( $title ) {
		if ( self::$page_title ) {
			$title['title'] = self::$page_title;
		} else {
			unset( $title['title'] );
		}

		return $title;
	}

	/**
	 * Set document title
	 *
	 * @param $menu
	 * @param $route
	 *
	 * @return mixed
	 */
	public function set_document_title( $menu, $route ) {
		foreach ( $menu as $key => $value ) {
			$key = trim( $key, '/' );

			if ( $key == $route ) {
				self::$page_title = $value['title'];
			}
		}

		return $menu;
	}

	/**
	 * Init js scripts
	 */
	public function onInitScripts() {
		wp_enqueue_script(
			'jquery.dataTables',
			self::$app_url . '/vendor/datatables/jquery.dataTables.min.js',
			array( 'jquery' ),
			'1.10.19',
			true
		);

		wp_enqueue_script(
			'dataTables.bootstrap4',
			self::$app_url . '/vendor/datatables/dataTables.bootstrap4.min.js',
			array( 'jquery', 'jquery.dataTables' ),
			'1.10',
			true
		);

		wp_enqueue_script(
			'taskTable',
			self::$app_url . '/assets/js/taskTable.js',
			array( 'jquery', 'jquery.dataTables', 'dataTables.bootstrap4' ),
			null,
			true
		);

		wp_localize_script( 'taskTable', 'taskTable', array(
			'ajax_url' => admin_url( 'admin-ajax.php' )
		) );
	}

	/**
	 * Init styles
	 */
	public function onInitStyles() {
		wp_enqueue_style(
			'dataTables.bootstrap4',
			self::$app_url . '/vendor/datatables/dataTables.bootstrap4.min.css'
		);

		wp_enqueue_style(
			'font-awesome',
			self::$app_url . '/vendor/font-awesome/css/all.min.css'
		);

		wp_enqueue_style(
			'styles',
			self::$app_url . '/assets/css/style.css'
		);
	}

	/**
	 * Init wp filters
	 */
	private function initFilters() {
		add_filter( 'cn_tasks_thead_cols', array( &$this, 'freelancer_column_for_tasks_thead_cols' ), 10, 1 );
		add_filter( 'cn_tasks_tbody_row_cols', array( &$this, 'freelancer_column_for_tasks_tbody_row_cols' ), 10, 2 );
		add_filter( 'cn_menu', array( &$this, 'set_document_title' ), 10, 2 );
		add_filter( 'cn_menu', array( &$this, 'new_task_sidebar_menu' ), 15, 2 );
	}

	/**
	 * Adds an option "Add New Task" to the sidebar menu
	 *
	 * @param $menu
	 * @param $route
	 *
	 * @return mixed
	 */
	public function new_task_sidebar_menu( $menu, $route ) {
		if ( $route === 'tasks' ) {
			$menu['/add_new_task'] = [
				'title' => __( 'Add new task', 'cn' ),
				'icon'  => 'fa-plus-circle',
				'url'   => '#add_new_task'
			];
		}

		return $menu;
	}


	/**
	 * Adds freelancer cols to table head
	 *
	 * @param $cols
	 *
	 * @return mixed
	 */
	public function freelancer_column_for_tasks_thead_cols( $cols ) {
		$date_cols = $cols[2];
		$cols[2]   = __( 'Freelancer', 'cn' );
		$cols[]    = $date_cols;

		return $cols;
	}

	/**
	 * Adds freelancer cols to table body
	 *
	 * @param $cols
	 * @param $task
	 *
	 * @return mixed
	 */
	public function freelancer_column_for_tasks_tbody_row_cols( $cols, $task ) {
		$task_id       = trim( $task->id(), '#' );
		$freelancer_id = get_post_meta( $task_id, '_freelancer', true );
		$freelancer    = get_the_title( $freelancer_id ) ?: __( 'Not selected', 'cn' );

		$date_cols = $cols[2];
		$cols[2]   = $freelancer;
		$cols[]    = $date_cols;

		return $cols;
	}

	/**
	 * Init post type task
	 */
	public function onInitPostTypes() {
		$labels = array(
			'name'               => __( 'Freelancers', 'cn' ),
			'singular_name'      => __( 'Freelancer', 'cn' ),
			'menu_name'          => __( 'Freelancers', 'cn' ),
			'name_admin_bar'     => __( 'Freelancer', 'cn' ),
			'add_new'            => __( 'Add New', 'cn' ),
			'add_new_item'       => __( 'Add New Freelancer', 'cn' ),
			'new_item'           => __( 'New Freelancer', 'cn' ),
			'edit_item'          => __( 'Edit Freelancer', 'cn' ),
			'view_item'          => __( 'View Freelancer', 'cn' ),
			'all_items'          => __( 'All Freelancers', 'cn' ),
			'search_items'       => __( 'Search Freelancers', 'cn' ),
			'parent_item_colon'  => __( 'Parent Freelancers:', 'cn' ),
			'not_found'          => __( 'No freelancer found.', 'cn' ),
			'not_found_in_trash' => __( 'No freelancer found in Trash.', 'cn' )
		);

		$args = array(
			'labels'               => $labels,
			'public'               => true,
			'publicly_queryable'   => true,
			'show_ui'              => true,
			'show_in_menu'         => true,
			'query_var'            => true,
			'rewrite'              => array( 'slug' => 'freelancer' ),
			'menu_icon'            => 'dashicons-businessman',
			'capability_type'      => 'post',
			'has_archive'          => true,
			'hierarchical'         => false,
			'menu_position'        => null,
			'supports'             => array( 'title', 'editor', 'thumbnail' ),
			'register_meta_box_cb' => array( &$this, 'freelancers_meta_box' )
		);

		register_post_type( Freelancer::POST_TYPE, $args );
	}

	/**
	 * Freelancer meta_box
	 */
	function freelancers_meta_box() {
		add_meta_box(
			'global-notice',
			__( 'Freelancer', 'cn' ),
			array( &$this, 'freelancer_meta_box_callback' ),
			Task::POST_TYPE,
			'side'
		);
	}

	/**
	 * Meta_box callback, adds freelancer select.
	 *
	 * @param $post
	 * @param $meta
	 */
	function freelancer_meta_box_callback( $post, $meta ) {
		$value = get_post_meta( $post->ID, '_freelancer', 1 );

		wp_nonce_field( plugin_basename( __FILE__ ), 'coding-ninjas-tasks-child_noncename' );

		$freelancers = ( new ModelFreelancers() )->getAll();

		echo '<select id="freelancer" name="_freelancer">';
		echo '<option>' . __( 'Select freelancer', 'cn' ) . '</option>';

		foreach ( $freelancers as $freelancer ) {
			$id   = $freelancer->id();
			$name = $freelancer->name();
			echo "<option value={$id}" . selected( $value, $id ) . ">$name</option>";
		}

		echo '</select>';
	}

	/**
	 * Save task past with freelancer meta field
	 *
	 * @param $post_id
	 */
	function freelancer_save_postdata( $post_id ) {

		if ( ! isset( $_POST['_freelancer'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['coding-ninjas-tasks-child_noncename'], plugin_basename( __FILE__ ) ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$freelancer = sanitize_text_field( $_POST['_freelancer'] );

		update_post_meta( $post_id, '_freelancer', $freelancer );
	}
}