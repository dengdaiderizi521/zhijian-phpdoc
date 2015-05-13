$(function() {

	var $wndw = $(window),
		$html = $('html'),
		$body = $('body'),
		$both = $('body, html');


	String.prototype.capitalize = function() {
		 return this.charAt(0).toUpperCase() + this.slice(1);
	}



	//	Auto submenu
	var submenu = '';
	$('.submenutext')
		.each(
			function( i )
			{
				var $h = $(this).parent(),
					id = $h.attr( 'id' ) || 'h' + i;

				$h.attr( 'id', id );

				submenu += '<li><a href="#' + id + '">' + $(this).text().capitalize() + '</a></li>';
			}
		);

	if ( submenu.length )
	{
		var $submenu 	= $('<div class="submenu"><div><ul>' + submenu + '</ul></div></div>')
				.insertAfter( 'h1' );
		
		var $subfixed	= $submenu
				.clone()
				.addClass( 'fixed Fixed' )
				.insertAfter( $submenu );

		var fixed = false,
			start = $submenu.offset().top;

		$submenu
			.add( $subfixed )
			.find( 'a' )
			.on( 'click',
				function( e )
				{
					e.preventDefault();
					$both.animate({
						scrollTop: $($(this).attr( 'href' )).offset().top - 120
					});
				}
			);

		$wndw
			.on( 'scroll.submenu',
				function( e )
				{
					var offset = $wndw.scrollTop();
					if ( fixed )
					{
						if ( offset < start )
						{
							$body.removeClass( 'fixedsubmenu' );
							fixed = false;
						}
					}
					else
					{
						if ( offset >= start )
						{
							$body.addClass( 'fixedsubmenu' );
							fixed = true;
						}
					}
				}
			)
			.trigger( 'scroll.submenu' );
	}


	
	//	Show more examples
	$('a[href="#more-examples"]')
		.on( 'click',
			function( e )
			{
				e.preventDefault();
				$body.addClass( 'more-examples' );
				$both.animate({
					scrollTop: $($(this).attr('href')).offset().top - 55
				});	
			}
		);



	//	Scroll to more info
	$('a[href="#more-info"], a[href="#wp-features"]')
		.on( 'click',
			function( e )
			{
				e.preventDefault();
				$both.animate({
					scrollTop: $($(this).attr('href')).offset().top + 55
				});	
			}
		);



	//	Compose email link, please stop sending me spam...
	setTimeout(function() {
		var b = 'frebsite' + '.' + 'nl',
			o = 'info',
			t = 'mail' + 'to';

		$('#emaillink').attr( 'href', t + ':' + o + '@' + b );
	}, 1000);



	//	Collapse tablerows
	$('.table-collapsed')
		.find( '.sub-start' )
		.each(
			function()
			{
				var $parent = $(this).prev().find( 'td' ).eq( 1 ).addClass( 'toggle' ),
					$args = $parent.find( 'span' ),
					$subs = $(this);
	
				var searching = true;
				$(this).nextAll().each(
					function()
					{
						if ( searching )
						{
							$subs = $subs.add( this );
							if ( !$(this).is( '.sub' ) )
							{
								searching = false;
							}
						}
					}
				);
				$subs.hide();
				$parent.click(
					function()
					{
						$args.toggle();
						$subs.toggle();
					}
				);
			}
		);
	


	//	Open menu in examples
	var $phones = $('.phone');
	if ( $phones.length )
	{
		var offsets = {};
		
		$phones
			.each(
				function()
				{
					var offset = $(this).offset().top - 150;
					if ( offset < 0 )
					{
						offset = 0;
					}

					if ( !offsets[ offset ] )
					{
						offsets[ offset ] = $();
					}
					offsets[ offset ] = offsets[ offset ].add( this );
				}
			);

		$wndw
			.on( 'scroll.phones',
				function()
				{
					var offset = $wndw.scrollTop();
					for ( var o in offsets )
					{
						if ( offset > o )
						{
							offsets[ o ]
								.each(
									function( i )
									{
										var iframe = $(this).find( 'iframe' )[ 0 ].contentWindow;
										var interv = setInterval(
											function()
											{
												if ( iframe.$ )
												{	
													var API = iframe.$('#menu').data( 'mmenu' );
													if ( API )
													{
														if ( API.open )
														{
															API.open();
														}
														clearInterval( interv );
													}
												}
											}, 250 + ( i * 250 )
										);
									}
								);
							
							delete offsets[ o ];
						}
					}
					
					for ( var o in offsets )
					{
						return;
					}
					$(this).off( 'scroll.phones' );
				}
			);

		setTimeout(
			function()
			{
				$wndw.trigger( 'scroll.phones' );
			}, 2500
		);
	}

});