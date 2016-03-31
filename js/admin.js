/**
 * Plugin functions file.
 *
 */
if( "undefined"==typeof jQuery )throw new Error( "Advanced Posts Widget's JavaScript requires jQuery" );

(function ( $ ) {

    'use strict';

	function change_avatar_div( e ){
		var field = $( e.currentTarget );
		var apw_avatar_wrap = field.closest( '.apw-thumb-size-wrap' );
		var apw_avatar_div = apw_avatar_wrap.find( '.apw-avatar-preview' );

		if( apw_avatar_div.length ) {
			var avatar = $( '.apw-avatar', apw_avatar_div );
			var width = parseInt ( ( $.trim( $( '.apw-thumb-width', apw_avatar_wrap ).val() ) * 1 ) + 0 );
			var height = parseInt ( ( $.trim( $( '.apw-thumb-height', apw_avatar_wrap ).val() ) * 1 ) + 0 );

			//var size = parseInt ( ( $.trim( field.val() ) * 1 ) + 0 );
			apw_avatar_div.css( {
				'height' : height + 'px',
				'width' : width + 'px'
			} );
			avatar.css( { 'font-size' : height + 'px' } );
		}

		return;
	};

	// Customizer Screen
	$( '#customize-controls, #wpcontent' ).on( 'change', '.apw-thumb-size', function ( e ) {
		change_avatar_div( e );
		return;
	});

	// Customizer Screen
	$( '#customize-controls, #wpcontent' ).on( 'keyup', '.apw-thumb-size', function ( e ) {
		setTimeout( function(){
			change_avatar_div( e );
		}, 300 );
		return;
	});

	function change_excerpt_size( e ) {
		var field = $( e.currentTarget );
		var apw_excerpt_div = field.closest( '.apw-excerpt-size-wrap' ).find( '.apw-excerpt' );
		var size = parseInt ( ( $.trim( field.val() ) * 1 ) + 0 );

		if( apw_excerpt_div.length ) {
			apw_excerpt_div.html( apw_script_vars.sample_excerpt.substring( 0, size) + "&hellip;" );
		}

	}

	// Customizer Screen
	$( '#customize-controls, #wpcontent' ).on( 'change', '.apw-excerpt-length', function ( e ) {
		change_excerpt_size( e );
		return;
	});

	// Customizer Screen
	$( '#customize-controls, #wpcontent' ).on( 'keyup', '.apw-excerpt-length', function ( e ) {
		setTimeout( function(){
			change_excerpt_size( e );
		}, 300 );
		return;
	});


}(jQuery) );
