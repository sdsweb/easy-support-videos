<?php do_action( 'easy_support_videos_contextual_videos_section_before' ); ?>

<p><?php _e( 'Use these options to adjust contextual videos within Easy Support Videos.', 'easy-support-videos' ); ?></p>

<p>
	<?php
		// Grab the Easy Support Videos Contextual Videos included context IDs
		$included_context_ids = apply_filters( 'easy_support_videos_contextual_videos_section_included_context_ids', Easy_Support_Videos_Admin_Contextual_Videos::get_included_context_ids() );

		// If we have included context IDs
		if ( ! empty( $included_context_ids ) ) :
			// Grab the Easy Support Videos Contextual Videos instance
			$esv_contextual_videos = Easy_Support_Videos_Admin_Contextual_Videos();

			// Grab the included context IDs last index
			$included_context_ids_last_index = ( count ( $included_context_ids ) - 1 );

			// Displayed included contextual videos contexts
			$displayed_included_contextual_videos_contexts = array();

			// Loop through the included context IDs
			foreach ( $included_context_ids as $included_context_id_index => $included_context_id ) {
				// Grab the context data for this context
				$contextual_videos_context_data = $esv_contextual_videos->get_context_data_for_context( $included_context_id );

				// Displayed contextual videos context
				$displayed_contextual_videos_context = '';

				// If we have a contextual videos context menu item
				if ( ! empty( $contextual_videos_context_data['menu'] ) ) {
					// If we have a contextual videos context URL
					if ( $contextual_videos_context_data['url'] )
						// Set the displayed contextual videos context
						$displayed_contextual_videos_context = sprintf( __( '<a href="%1$s">%2$s</a>', 'easy-support-videos' ), $contextual_videos_context_data['url'], $contextual_videos_context_data['menu'][0] );
					// Otherwise we don't have a contextual videos context URL
					else
						// Set the displayed contextual videos context
						$displayed_contextual_videos_context = $contextual_videos_context_data['menu'][0];
				}
				// Otherwise we don't have a contextual videos context menu item
				else
					// Set the displayed contextual videos context
					$displayed_contextual_videos_context = $contextual_videos_context_data['id'];

				// If this isn't the last index
				if ( $included_context_id_index !== $included_context_ids_last_index )
					// Append a separator to the displayed contextual videos context
					$displayed_contextual_videos_context .= ( ( $included_context_id_index + 1 ) !== $included_context_ids_last_index ) ? ',' : _x( ', and', 'last index context separator on options page', 'easy-support-videos' );

				// Add this displayed contextual videos context to the displayed included contextual videos contexts
				$displayed_included_contextual_videos_contexts[] = $displayed_contextual_videos_context;
			}

			printf( __( 'The following is a list of contexts where Easy Support Videos can be applied: %1$s.', 'easy-support-videos' ), implode( ' ', $displayed_included_contextual_videos_contexts ) );
		// Otherwise we don't have included context IDs
		else:
			_e( 'Easy Support Videos can be applied to all contexts.', 'easy-support-videos' );
		endif;
	?>
</p>

<?php do_action( 'easy_support_videos_contextual_videos_section_after' ); ?>