<?php
/**
 * Plugin Name: Archive Diversity
 * Description: Include Pages, attachments or custom post types in your category, tag, date or author archives.
 * Author: Carlo Manf
 * Author URI: http://carlomanf.id.au
 * Version: 1.0.1
 */

// Diversify the archives
add_filter( 'pre_get_posts', 'archive_diversity_diversify' );
function archive_diversity_diversify( $query ) {

	if ( $query->is_archive() && !$query->is_post_type_archive() && !is_admin() ) {
		$options = archive_diversity_get_options();

		if ( $query->is_category() )
			$query->set( 'post_type', array_merge(
				array( 'post' ),
				$options[ 'category_diversity' ]
			) );

		if ( $query->is_tag() )
			$query->set( 'post_type', array_merge(
				array( 'post' ),
				$options[ 'tag_diversity' ]
			) );

		if ( $query->is_date() )
			$query->set( 'post_type', array_merge(
				array( 'post' ),
				$options[ 'date_diversity' ]
			) );

		if ( $query->is_author() )
			$query->set( 'post_type', array_merge(
				array( 'post' ),
				$options[ 'author_diversity' ]
			) );
	}

	return $query;

}

// Settings page
function archive_diversity_settings() {
	if ( !empty( $_POST[ 'diversity' ] ) )
		archive_diversity_process();

	$options = archive_diversity_get_options();

	?><div class="wrap">
		<h2>Archive Diversity</h2>
		<p>Select the post types you wish to include in your archives.</p>
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<table class="widefat">
				<thead>
					<tr>
						<th scope="col">Post Type</th>
						<th scope="col">Category Archives</th>
						<th scope="col">Tag Archives</th>
						<th scope="col">Date Archives</th>
						<th scope="col">Author Archives</th>
					</tr>
				</thead>
				<tbody><?php

	$post_types = array_merge(
		array( 'page', 'attachment' ),
		array_values( get_post_types( array( 'public' => true, '_builtin' => false ) ) )
	);

	?><tr>
		<th scope="row">post</th>
		<td><input type="checkbox" checked disabled></td>
		<td><input type="checkbox" checked disabled></td>
		<td><input type="checkbox" checked disabled></td>
		<td><input type="checkbox" checked disabled></td>
	</tr><?php

	foreach ( $post_types as $key => $type ) {

		if ( 0 === $key % 2 )
			$alternate = ' class="alternate"';
		else
			$alternate = '';

		?><tr<?php echo $alternate; ?>>
			<th scope="row"><?php echo $type; ?></th>
			<td><input type="checkbox" name="category_diversity[]" value="<?php echo $type ?>"<?php echo in_array( $type, $options[ 'category_diversity' ] ) ? ' checked' : ''; ?>></td>
			<td><input type="checkbox" name="tag_diversity[]" value="<?php echo $type ?>"<?php echo in_array( $type, $options[ 'tag_diversity' ] ) ? ' checked' : ''; ?>></td>
			<td><input type="checkbox" name="date_diversity[]" value="<?php echo $type ?>"<?php echo in_array( $type, $options[ 'date_diversity' ] ) ? ' checked' : ''; ?>></td>
			<td><input type="checkbox" name="author_diversity[]" value="<?php echo $type ?>"<?php echo in_array( $type, $options[ 'author_diversity' ] ) ? ' checked' : ''; ?>></td>
		</tr><?php

	}

		 	?></table>
			<p class="submit"><input type="submit" name="diversity" class="button button-primary" value="Save Changes"></p>
		</form>
	</div><?php

}

// Add options page
add_action( 'admin_menu', 'archive_diversity_options' );
function archive_diversity_options() {
	add_options_page( 'Archive Diversity', 'Archive Diversity', 'manage_options', 'archive_diversity', 'archive_diversity_settings' );
}

// Process settings input
function archive_diversity_process() {
	$slugs = array( 'category_diversity', 'tag_diversity', 'date_diversity', 'author_diversity' );

	foreach ( $slugs as $slug ) {
		if ( !isset( $_POST[ $slug ] ) )
			$_POST[ $slug ] = array();

		$options[ $slug ] = $_POST[ $slug ];
	}

	update_option( 'archive_diversity', $options );

	echo '<div class="updated"><p>Settings saved.</p></div>';
}

// Retrieve options
function archive_diversity_get_options() {
	$defaults = array();
	$slugs = array( 'category_diversity', 'tag_diversity', 'date_diversity', 'author_diversity' );

	foreach ( $slugs as $slug )
		$defaults[ $slug ] = array();

	$options = get_option( 'archive_diversity' );
	if ( !is_array( $options ) ) {
		$options = $defaults;
		update_option( 'archive_diversity', $options );
	}

	return $options;
}
