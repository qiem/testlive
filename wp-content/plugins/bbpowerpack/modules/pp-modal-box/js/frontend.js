;(function($) {

	PPModalBox = function(settings) {
		this.id 			= settings.id;
		this.settings 		= settings;
		this.type			= settings.type;
		this.cookieKey		= 'pp_modal_' + this.id;
		this.cookieTime 	= settings.display_after;
		this.triggerType 	= settings.trigger_type;
		this.layout			= settings.layout;
		this.wrap 			= $('#modal-' + this.id);
		this.container 		= this.wrap.find('.pp-modal-container');
		this.element 		= this.wrap.find('.pp-modal');
		this.isPreviewing 	= settings.previewing;
		this.isVisible		= settings.visible;
		this.eventClose		= false;

		this.init();
	};

	PPModalBox.prototype = {
		id			: '',
		settings	: {},
		type		: '',
		cookieKey	: '',
		cookieTime	: 0,
		triggerType	: '',
		layout		: '',
		wrap		: '',
		element		: '',
		isActive	: false,
		isPreviewing: false,
		isVisible	: false,
		eventClose  : false,

		init: function()
		{
			if ( parseInt( this.cookieTime ) === 0 || this.cookieTime < 0 || this.cookieTime === '' ) {
				this.removeCookie();
			}
			if ( ( 'exit_intent' === this.triggerType || 'auto' === this.triggerType ) && this.getCookie() && ! this.isPreviewing ) {
				return;
			}
			if ( ! this.isPreviewing && 'undefined' !== typeof this.isVisible && ! this.isVisible ) {
				return;
			}
			if ( this.isActive ) {
				return;
			}

			this.setResponsive();
			this.bindEvents();
			this.show();
		},

		setResponsive: function()
		{
			if ( window.innerWidth <= this.settings.breakpoint ) {
                this.element.removeClass('layout-standard').addClass('layout-fullscreen');
            }
            if ( window.innerWidth <= this.element.width() ) {
				this.element.css('width', window.innerWidth - 20 + 'px');
            }
		},

		bindEvents: function()
		{
			var self = this;

			// close modal box on Esc key press.
			$(document).keyup(function(e) {
                if ( self.settings.esc_exit && 27 == e.which && self.isActive && $('form[data-type="pp-modal-box"]').length === 0 ) {
					self.eventClose = true;
                    self.hide();
                }
			});

			// close modal box by clicking on outside of modal box element in document.
			$(document).on('click', function(e) {
                if ( self.settings.click_exit && $(e.target).parents('.pp-modal').length === 0 && self.isActive && ! self.isPreviewing && ! self.element.is(e.target) && self.element.has(e.target).length === 0 && ! $(e.target).hasClass('modal-' + self.id) && $(e.target).parents('.modal-' + self.id).length === 0 && e.which ) {
					self.eventClose = true;
                    self.hide();
                }
			});
			
			// close modal box by clicking on the close button.
            $(self.wrap).find('.pp-modal-close').on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				self.eventClose = true;
                self.hide();
			});

			$(window).resize( $.proxy( this.setResponsive, this ) );
			$(window).resize( $.proxy( this.setPosition, this ) );
		},

		setPosition: function()
		{
			if ( 'fullscreen' !== this.layout ) {
                if ( typeof this.settings.height === 'undefined' ) {

                    this.wrap.addClass('pp-modal-height-auto');
                    var modalHeight = this.element.outerHeight();
                    this.wrap.removeClass('pp-modal-height-auto');

                    if ( 'photo' === this.type ) {
                        this.element.find( '.pp-modal-content-inner img' ).css( 'max-width', '100%' );
                    }

                    var topPos = ( $(window).height() - modalHeight ) / 2;
                    if ( topPos < 0 ) {
                        topPos = 0;
                    }
                    this.element.css( 'top', topPos + 'px' );
                } else {
                    var topPos = ( $(window).height() - this.settings.height ) / 2;
					if ( topPos < 0 ) {
                        topPos = 0;
                    }
                    this.element.css( 'top', topPos + 'px' );
                }
			}
		},

		show: function()
		{
			var self = this;

			this.setPosition();
			
			setTimeout( function() {
				self.element.trigger('beforeload');

				$('html').addClass( 'pp-modal-active-' + self.id );

				self.wrap.addClass( 'pp-modal-active' );

				self.container
					.removeClass( self.settings.animation_load + ' animated' )
					.addClass( 'modal-visible' )
					.addClass( self.settings.animation_load + ' animated' );

				if ( ! $('body').hasClass('wp-admin') ) {
					self.container.one( 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
						self.eventClose = false;
						$(this).removeClass( self.settings.animation_load + ' animated' );
						self.setup();
					} );
				} else {
					self.setup();
				}

				self.isActive = true;
				
                if ( 'exit_intent' === self.triggerType || 'auto' === self.triggerType ){
                    if ( ! self.isPreviewing ) {
                        self.setCookie();
                    }
				}
				
                self.restruct();

				self.element.trigger('afterload');
				$(document).trigger( 'pp_modal_box_rendered', [self.element] );
				
            }, self.settings.auto_load ? parseFloat(self.settings.delay) * 1000 : 0);
		},

		setup: function()
		{
			if ( 'url' == this.type ) {
				var original_src = this.element.find('.pp-modal-iframe').attr('src');
				var src = this.element.find('.pp-modal-iframe').data('url');
				if ( original_src === undefined || original_src === '' ) {
					this.element.find('.pp-modal-iframe').attr( 'src', src );
				}
			}
			var iframeAndSource = this.element.find('iframe, source');
			if ( iframeAndSource.length > 0 && iframeAndSource.closest( '.fl-module' ).length === 0 ) {
				var src = '';
				var m_src = iframeAndSource.attr('src');
				
				if ( m_src === undefined || m_src === '' ) {
					src = iframeAndSource.data('url');
				} else {
					src = iframeAndSource.attr('src');
				}

				if ( src ) {
					if ( ( src.search('youtube') !== -1 || src.search('vimeo') !== -1) && src.search('autoplay=1') == -1 ) {
						if ( typeof src.split('?')[1] === 'string' ) {
							src = src + '&autoplay=1&rel=0';
						} else {
							src = src + '?autoplay=1&rel=0';
						}
					}
					iframeAndSource.attr('src', src);
				}
				
				if ( this.element.find('video').length ) {
					this.element.find('video')[0].play();
				}
			}
		},

		reset: function()
		{
            var iframeAndSource = this.element.find('iframe, source');
			if ( iframeAndSource.length > 0 && iframeAndSource.closest( '.fl-module' ).length === 0 ) {
				var src = iframeAndSource.attr('src');
				if ( '' !== src ) {
					iframeAndSource.attr('data-url', src).attr('src', '');
				}
				
				if ( this.element.find('video').length > 0 ) {
                    this.element.find('video')[0].pause();
                }
            }
        },

		restruct: function()
		{
			var mH = 0, hH = 0, cH = 0, eq = 0;
			var self = this;

            setTimeout( function() {
                if ( self.isActive ) {
                    if ( 'fullscreen' === self.layout ) {
                        var marginTop 		= parseInt( self.element.css('margin-top') );
                        var marginBottom 	= parseInt( self.element.css('margin-bottom') );
                        var modalHeight 	= $(window).height() - (marginTop + marginBottom);
						
						self.element.css( 'height', modalHeight + 'px' );
                    }
                    eq = 6;
                    mH = self.element.outerHeight(); // Modal height.
                    hH = self.element.find('.pp-modal-header').outerHeight(); // Header height.

                    if ( self.settings.auto_height && 'fullscreen' !== self.layout ) {
                        return;
					}
					
					var cP = parseInt( self.element.find('.pp-modal-content').css('padding') ); // Content padding.
					
					self.element.find('.pp-modal-content').css( 'height', mH - (hH + eq) + 'px' );
					
                    if ( ! self.settings.auto_height && self.element.find('.pp-modal-header').length === 0) {
                        self.element.find('.pp-modal-content').css('height', mH + 'px');
                    }
				   
					// Adjust iframe height.
                    if ( 'url' === self.type ) {
                        self.element.find('.pp-modal-iframe').css('height', self.element.find('.pp-modal-content-inner').outerHeight() + 'px');
                    }
                    if ( 'video' === self.type ) {
                        self.element.find('iframe').css({'height':'100%', 'width':'100%'});
                    }
                }
            }, self.settings.auto_load ? parseFloat(self.settings.delay) * 1000 : 0);
		},

		hide: function()
		{
			var self = this;

			this.element.trigger('beforeclose');

            this.container
                .removeClass( self.settings.animation_exit + ' animated' )
				.addClass( self.settings.animation_exit + ' animated' );
				
			if ( ! $('body').hasClass('wp-admin') ) {
				this.container.one( 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
					if ( self.eventClose ) {
						self.close();
					}
				});
			} else {
				self.close();
			}
				
            if ( window.location.hash ) {
                if ( '#modal-' + self.id === window.location.hash ) {
                    var scrollTop = self.settings.scrollTop || $(window).scrollTop();
                    window.location.href = window.location.href.split('#')[0] + '#';
                    $(window).scrollTop(scrollTop);
                }
			}
			
			this.element.trigger('afterclose');
			$(document).trigger( 'pp_modal_box_after_close', [this.element] );
		},

		close: function()
		{
			this.container.removeClass( this.settings.animation_exit + ' animated' ).removeClass('modal-visible');
			this.container.find('.pp-modal-content').removeAttr('style');
			this.wrap.removeClass( 'pp-modal-active' );

			$('html').removeClass( 'pp-modal-active-' + this.id );

			this.isActive = false;
			this.eventClose = false;
			this.reset();
		},

		setCookie: function()
		{
			if ( parseInt( this.cookieTime ) > 0 ) {
				return $.cookie( this.cookieKey, this.cookieTime, {expires: this.cookieTime, path: '/'} );
			} else {
				this.removeCookie();
			}
		},

		getCookie: function()
		{
			return $.cookie( this.cookieKey );
		},

		removeCookie: function()
		{
			$.cookie( this.cookieKey, 0, {expires: 0, path: '/'} );
		}
	};
})(jQuery);
