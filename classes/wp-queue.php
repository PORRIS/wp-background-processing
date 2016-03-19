<?php

if ( ! class_exists( 'WP_Queue' ) ) {
	class WP_Queue {

		/**
		 * @var WP_Queue
		 */
		protected static $instance;

		/**
		 * @var string
		 */
		protected $table;

		/**
		 * Protected constructor to prevent creating a new instance of the
		 * class via the `new` operator from outside of this class.
		 */
		protected function __construct() {
			// Singleton
		}

		/**
		 * As this class is a singleton it should not be clone-able.
		 */
		protected function __clone() {
			// Singleton
		}

		/**
		 * As this class is a singleton it should not be able to be unserialized.
		 */
		protected function __wakeup() {
			// Singleton
		}

		/**
		 * Make this class a singleton.
		 *
		 * Use this instead of __construct()
		 *
		 * @return WP_Queue
		 */
		public static function get_instance() {
			if ( ! isset( static::$instance ) && ! ( self::$instance instanceof WP_Queue ) ) {
				static::$instance = new WP_Queue();

				static::$instance->init();
			}

			return static::$instance;
		}

		/**
		 * Init WP_Queue.
		 */
		protected function init() {
			global $wpdb;

			$this->table = $wpdb->prefix . 'queue';
		}

		/**
		 * Push a job onto the queue.
		 *
		 * @param string $class
		 * @param mixed  $job
		 * @param int    $delay
		 *
		 * @return $this
		 */
		public function push( $class, $job, $delay ) {
			global $wpdb;

			$data = array(
				'action'       => $class,
				'job'          => maybe_serialize( $job ),
				'available_at' => $this->datetime( $delay ),
				'created_at'   => $this->datetime(),
			);

			$wpdb->insert( $this->table, $data );

			return $this;
		}

		/**
		 * Get MySQL datetime.
		 *
		 * @param int $offset Seconds, can pass negative int.
		 *
		 * @return string
		 */
		protected function datetime($offset = 0) {
			$timestamp = time() + $offset;

			return gmdate( 'Y-m-d H:i:s', $timestamp );
		}
	}
}