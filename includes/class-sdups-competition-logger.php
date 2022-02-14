<?php
require_once plugin_dir_path( __FILE__ ) . 'class-sdups-competition-log-levels.php';

class SDUPS_Competition_Logger {
	private $class;
	private $level;
	private $log_file;

	public function __construct( $class, $level = E_NOTICE ) {
		$this->class    = $class;
		$this->level    = $level;
		$this->log_file = plugin_dir_path( __DIR__ ) . 'debug.log';
	}

	public function error( $message ) {
		if ( $this->level >= SDUPS_Competition_Log_Level::ERROR ) {
			$this->log( $message, 'ERROR' );
		}
	}

	public function warn( $message ) {
		if ( $this->level >= SDUPS_Competition_Log_Level::WARN ) {
			$this->log( $message, 'WARN' );
		}
	}

	public function info( $message ) {
		if ( $this->level >= SDUPS_Competition_Log_Level::INFO ) {
			$this->log( $message, 'INFO' );
		}
	}

	public function debug( $message ) {
		if ( $this->level >= SDUPS_Competition_Log_Level::DEBUG ) {
			$this->log( $message, 'DEBUG' );
		}
	}

	private function log( $message, $level ) {
		$bt             = debug_backtrace();
		$caller         = $bt[1];
		$caller['file'] = str_replace( plugin_dir_path( __DIR__ ), '', $caller['file'] );
		$caller         = $caller['file'] . ":" . $caller['line'];
		error_log( wp_date( "[D M j G:i:s.v Y] " ) .
		           "[$level] [$this->class] [$caller] $message\n", 3, $this->log_file );
	}
}