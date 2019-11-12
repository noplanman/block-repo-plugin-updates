<?php
/**
 * Plugin Name: Block Repo Plugin Updates
 * Plugin URI: https://git.feneas.org/noplanman/block-repo-plugin-updates
 * Description: Blocks plugin updates for any plugins whose folder is a code repo. (Based on <a href="https://wordpress.org/plugins/block-specific-plugin-updates/">Block Specific Plugin Updates</a>)
 * Author: Armando Lüscher
 * Version: 1.0.3
 * Author URI: https://noplanman.ch
 */

/**
 * Get a list of blocked plugin paths.
 *
 * @since 1.0.0
 *
 * @return array
 */
function brpu_get_blocked_plugins() {
	static $blocked_plugins = null;

	if ( $blocked_plugins !== null ) {
		return $blocked_plugins;
	}

	/**
	 * List of files/folders that denote a repo.
	 *
	 * @since 1.0.0
	 *
	 * @param array $files
	 */
	$repo_files = (array) apply_filters( 'brpu_repo_files', [ '.git', '.svn' ] );

	// Get just the plugin name of plugins that are repos.
	$repo_plugin_files = array_map(
		function ( $plugin_dir ) {
			return explode( DIRECTORY_SEPARATOR, plugin_basename( $plugin_dir ) )[0];
		},
		glob( WP_PLUGIN_DIR . '/*/{' . implode( ',', $repo_files ) . '}', GLOB_BRACE )
	);

	// Fetch and update the list of blocked plugins.
	$blocked_plugins = get_plugins();
	foreach ( array_keys( $blocked_plugins ) as $plugin_file ) {
		$plugin_slug = explode( DIRECTORY_SEPARATOR, $plugin_file )[0];
		if ( ! in_array( $plugin_slug, $repo_plugin_files, true ) ) {
			unset ( $blocked_plugins[ $plugin_file ] );
		}
	}

	return $blocked_plugins;
}

/**
 * Remove blocked plugin update notices.
 *
 * @since 1.0.0
 *
 * @param mixed $value
 *
 * @return mixed
 */
function brpu_filter_plugin_updates( $value ) {
	if ( isset( $value->response ) && is_array( $value->response ) ) {
		$value->response = array_diff_key(
			$value->response,
			brpu_get_blocked_plugins()
		);
	}

	return $value;
}
add_filter( 'site_transient_update_plugins', 'brpu_filter_plugin_updates' );

/**
 * Filter HTTP requests and only react to plugin update checks.
 *
 * @since 1.0.0
 *
 * @param array  $r   An array of HTTP request argument
 * @param string $url The request URL.
 *
 * @return array
 */
function brpu_prevent_update_check( array $r, $url ) {
	if ( 0 !== strpos( $url, 'https://api.wordpress.org/plugins/update-check/1.1/' ) ) {
		return $r;
	}

	$plugins              = json_decode( $r['body']['plugins'], true );
	$plugins['plugins']   = array_diff_key(
		$plugins['plugins'],
		brpu_get_blocked_plugins()
	);
	$r['body']['plugins'] = json_encode( $plugins );

	return $r;
}
add_filter( 'http_request_args', 'brpu_prevent_update_check', 10, 2 );

/**
 * Add a note to blocked plugins in WP Admin.
 *
 * @since 1.0.0
 *
 * @param array  $links The array having default links for the plugin.
 * @param string $file  The name of the plugin file.
 *
 * @return array
 */
function brpu_plugin_action_links( array $links, $file ) {
	if ( array_key_exists( $file, brpu_get_blocked_plugins() ) ) {
		$links = array_merge( [
			'blocked_via_brpu' => '<strong class="dashicons-before dashicons-lock success" title="Blocked via Block Repo Plugin Updates">BRPU</strong>',
		], $links );
	}

	return $links;
}
add_filter( 'plugin_row_meta', 'brpu_plugin_action_links', 10, 2 );
