( function( theme, $, wp ) {

	if ( 'undefined' === typeof theme.name ) {
		console.error( 'Missing WP theme object. Aborting...' );
		return;
	}

	$.extend( theme, {
		init: function() {
			if ( theme.settings.init_tooltips && $.fn.tooltip ) {
				theme.apply( '[rel="tooltip"]', function( $tooltip ) {
					$tooltip.tooltip();
				});
			}

			if ( theme.settings.init_popovers && $.fn.popover ) {
				theme.apply( '[rel="popover"]', function( $popover ) {
					$popover.popover();
				});
			}

			if ( theme.settings.init_fancybox && $.fn.fancybox ) {
				theme.apply( 'a[href$=".jpg"],a[href$=".jpeg"],a[href$=".png"],a[href$=".gif"]', function( $link ) {
					$link.each( function() {
						var $this = $( this );
						var $wrap = $this.parents( '.gallery' );
						if ( 0 < $wrap.length ) {
							$this.attr( 'rel', $wrap.attr( 'id' ) );
						}
					}).fancybox({
						type: 			'image',
						maxWidth: 		'90%',
						maxHeight: 		'90%',
						openEffect: 	'elastic',
						closeEffect: 	'elastic',
						nextEffect: 	'elastic',
						prevEffect: 	'elastic'
					});
				});
			}

			if ( theme.settings.wrap_embeds ) {
				theme.apply( 'iframe, embed, object, [class*="embed-responsive-item"]', function( $embed ) {
					$embed.each( function() {
						var $this = $( this );
						if ( $this.data( 'nowrap' ) ) {
							return;
						}

						var ratio = $this.data( 'ratio' );
						if ( ! ratio ) {
							ratio = [
								$this.width(),
								$this.height()
							];
						} else {
							ratio = ratio.split( ':' );
						}

						if ( 2 !== ratio.length ) {
							return;
						}

						var padding = 100.0 * ( ratio[1] / ( 1.0 * ratio[0] ) );

						var $wrap = $( '<div class="embed-responsive"></div>' );
						$wrap.css({ 'padding-bottom': '' + padding + '%' });

						if ( $this.wrap( $wrap ).parent().parent().is( 'p' ) ) {
							$this.parent().unwrap();
						}
					});
				});
			}

			var isWebkit = navigator.userAgent.toLowerCase().indexOf( 'webkit' ) > -1,
			    isOpera  = navigator.userAgent.toLowerCase().indexOf( 'opera' )  > -1,
			    isIe     = navigator.userAgent.toLowerCase().indexOf( 'msie' )   > -1;

			if ( ( isWebkit || isOpera || isIe ) && document.getElementById && window.addEventListener ) {
				window.addEventListener( 'hashchange', function() {
					var id = location.hash.substring( 1 ),
						element;

					if ( ! ( /^[A-z0-9_-]+$/.test( id ) ) ) {
						return;
					}

					element = document.getElementById( id );

					if ( element ) {
						if ( ! ( /^(?:a|select|input|button|textarea)$/i.test( element.tagName ) ) ) {
							element.tabIndex = -1;
						}

						element.focus();
					}
				}, false );
			}

			if ( $( '#screen-reader-check-form' ).length ) {
				if ( ! window.screenReaderCheck ) {
					console.error( theme.i18n.plugin_missing );
				} else {
					theme.init_checker();
				}
			}
		},

		init_checker: function() {
			var busy = false;
			$( '#toggle-main-input' ).on( 'click', function( e ) {
				e.preventDefault();

				if ( busy ) {
					return;
				}

				var $button = $( this );

				busy = true;

				var target = $button.data( 'target' );
				var hide = $button.data( 'hide' );
				var new_text = $button.data( 'text' );
				var old_text = $button.text();

				$( target ).find( '.form-control' ).prop( 'required', true ).attr( 'aria-required', 'true' ).prop( 'disabled', false ).attr( 'aria-disabled', 'false' );
				$( hide ).find( '.form-control' ).prop( 'required', false ).attr( 'aria-required', 'false' ).prop( 'disabled', true ).attr( 'aria-disabled', 'true' );

				$( hide ).fadeOut( 400, function() {
					$button.text( new_text ).data( 'text', old_text ).data( 'target', hide ).data( 'hide', target );

					$( target ).fadeIn( 400, function() {
						busy = false;
					})
				});
			});

			var running_submission = false;
			$( '#screen-reader-check-form' ).on( 'submit', function( e ) {
				e.preventDefault();

				if ( running_submission ) {
					return;
				}

				var $form = $( this );
				var $submit = $( '#screen-reader-check-form-submit' );

				running_submission = true;

				var values = theme.get_form_values( $form );

				$submit.prop( 'disabled', true ).attr( 'aria-disabled', 'true' );
				$( '#results-loader' ).fadeIn( 400 );

				function finish( prevent_reset ) {
					if ( ! prevent_reset ) {
						$( '#url' ).val( '' );
						$( '#html' ).val( '' );
						$( '#advanced-options-content .form-control' ).val( '' );
					}

					$( '#results-loader' ).fadeOut( 400 );

					running_submission = false;
					$submit.prop( 'disabled', false ).attr( 'aria-disabled', 'false' );
				}

				window.screenReaderCheck.createCheck( values )
					.done( function( response ) {
						theme.run_tests( response.id, finish );
					})
					.fail( function( message ) {
						theme.handle_error( message, finish );
					});
			});
		},

		run_tests: function( check_id, finish_callback ) {
			function continue_callback( args ) {
				if ( ! args ) {
					args = {};
				}

				window.screenReaderCheck.runNextTest( check_id, args )
					.done( function( response ) {
						if ( response.check_complete ) {
							theme.handle_test_result( response, finish_callback );
						} else {
							theme.handle_test_result( response, continue_callback );
						}
					})
					.fail( function( message ) {
						theme.handle_error( message, finish_callback );
					});
			}

			continue_callback();
		},

		handle_test_result: function( result, callback ) {
			var update = true;

			var type = 'error' === result.type ? 'danger' : result.type;

			var $li = $( '#test_' + result.test_slug );
			if ( ! $li.length ) {
				update = false;
				$li = $( '<li id="test_' + result.test_slug + '"></li>' );
			}

			if ( ! $li.hasClass( 'result-' + type ) ) {
				$li.attr( 'class', 'result result-' + type );
			}

			var $div = $( '<div class="result-inner"></div>' );
			$div.append( '<h4>' + result.test_title + '</h4>' );

			for ( var i in result.messages ) {
				$div.append( '<p>' + result.messages[ i ] + '</p>' );
			}

			var request_fields = [];
			for ( var i in result.request_data ) {
				if ( result.request_data[ i ].value && ( 'string' !== typeof result.request_data[ i ].value || result.request_data[ i ].value.length ) ) {
					continue;
				}

				var $field = $( '<div class="form-group"></div>' );
				$field.append( '<label for="test_' + result.test_slug + '-' + result.request_data[ i ].slug + '">' + result.request_data[ i ].label + '</label>' );
				$field.append( '<input type="' + result.request_data[ i ].type + '" id="test_' + result.test_slug + '-' + result.request_data[ i ].slug + '" name="' + result.request_data[ i ].slug + '" value="' + result.request_data[ i ].default + '" class="form-control" aria-describedby="test_' + result.test_slug + '-' + result.request_data[ i ].slug + '_description" aria-required="true" required>' );
				$field.append( '<p id="test_' + result.test_slug + '-' + result.request_data[ i ].slug + '_description" class="form-text text-muted">' + result.request_data[ i ].description + '</p>' );

				request_fields.push( $field );
			}

			if ( request_fields.length ) {
				var i_break = parseInt( Math.ceil( request_fields.length / 2 ), 10 );

				var $form = $( '<form method="post" novalidate"></form>' );
				var $row = $( '<div class="row"></div>' );
				var $col1 = $( '<div class="col-md-6"></div>' );
				var $col2 = $( '<div class="col-md-6"></div>' );

				var i = 0;
				while ( i < i_break ) {
					$col1.append( request_fields[ i ] );
					i++;
				}
				while ( i < request_fields.length ) {
					$col2.append( request_fields[ i ] );
					i++;
				}

				$row.append( $col1 );
				$row.append( $col2 );

				$form.append( '<p><strong>' + theme.i18n.action_required + '</strong></p>' );
				$form.append( $row );
				$form.append( '<div class="result-submit-wrap"><button type="submit" class="btn btn-primary">' + theme.i18n.submit + '</button></div>' );

				$div.append( $form );

				$( '#results-loader' ).fadeOut( 400 );

				$form.on( 'submit', function( e ) {
					e.preventDefault();

					$form.find( '.result-submit-wrap > button' ).prop( 'disabled', true ).attr( 'aria-disabled', 'true' );
					$( '#results-loader' ).fadeIn( 400 );

					callback( theme.get_form_values( $form ) );
				});
			}

			if ( update ) {
				$li.html( $div );
			} else {
				$li.append( $div );
				$( '#results-log' ).append( $li );
			}

			if ( ! request_fields.length ) {
				callback();
			}
		},

		handle_error: function( message, finish_callback ) {
			var $error = $( '#screen-reader-check-error' );
			if ( ! $error.length ) {
				$error = $( '<div id="screen-reader-check-error" class="alert alert-danger alert-dismissible fade" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="' + theme.i18n.close + '"><span aria-hidden="true">&times;</span></button><h4 class="alert-heading">' + theme.i18n.an_error_occurred + '</h4><p>' + message + '</p></div>' );
				$( '#screen-reader-check-form' ).append( $error );
				$error.addClass( 'in' );
			} else {
				$error.find( 'p' ).html( message );
			}

			finish_callback( true );
		},

		get_form_values: function( $form ) {
			var serialized = $form.serializeArray();
			var values = {};

			for ( var i in serialized ) {
				if ( serialized[ i ].name.search( /\[/g ) >= 0 ) {
					var parts = serialized[ i ].name.replace( ']', '' ).split( '[' );
					if ( ! values[ parts[0] ] ) {
						values[ parts[0] ] = {};
					}

					values[ parts[0] ][ parts[1] ] = serialized[ i ].value;
				} else {
					values[ serialized[ i ].name ] = serialized[ i ].value;
				}
			}

			return values;
		},

		apply: function( selector, callback ) {
			callback( $( selector ) );
			$( document ).on( 'wp_theme.insert_content', function( e, top_selector ) {
				if ( 'undefined' === typeof top_selector ) {
					return;
				}
				callback( $( top_selector ).find( selector ) );
			});
		},

		insert: function( selector ) {
			$( document ).trigger( 'wp_theme.insert_content', [ selector ]);
		}
	});

	theme.init();

}( window.wp_theme || {}, jQuery, wp ) );


