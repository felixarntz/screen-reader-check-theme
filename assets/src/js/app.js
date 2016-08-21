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
				theme.init_checker();
			}
		},

		init_checker: function() {
			$( '#show-screen-reader-check-html-input' ).on( 'click', function( e ) {
				$( '#screen-reader-check-url-input' ).fadeOut( 400, function() {
					$( '#screen-reader-check-html-input' ).fadeIn( 400 );
					$( '#html' ).focus();
				});

				e.preventDefault();
			});

			$( '#show-screen-reader-check-url-input' ).on( 'click', function( e ) {
				$( '#screen-reader-check-html-input' ).fadeOut( 400, function() {
					$( '#screen-reader-check-url-input' ).fadeIn( 400 );
					$( '#url' ).focus();
				});

				e.preventDefault();
			});
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


