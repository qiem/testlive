(function($) {

	PPContentGrid = function(settings)
	{
		this.settings       = settings;
		this.nodeClass      = '.fl-node-' + settings.id;
		this.wrapperClass   = this.nodeClass + ' .pp-content-post-' + this.settings.layout;
		this.postClass      = this.wrapperClass + ' .pp-content-' + this.settings.layout + '-post';
		this.matchHeight	= settings.matchHeight == 'yes' ? true : false;
		this.style			= settings.style;
		this.masonry		= settings.masonry == 'yes' ? true : false;
		this.perPage 		= settings.perPage;
		this.filters 		= settings.filters;
		this.filterTax 		= settings.filterTax;
		this.filterType 	= settings.filterType;
		this.isFiltering	= false;
		this.isDefaultFilter	= false;
		this.activeFilter 	= '';
		this.totalPages 	= 1;
		this.currentPage 	= 1;
		this.cacheData		= {};
		this.ajaxData 		= {};
		this.includeSettings = true;

		if(this._hasPosts()) {
			this._initLayout();
		}
	};

	PPContentGrid.prototype = {
		settings        : {},
		nodeClass       : '',
		wrapperClass    : '',
		postClass       : '',
		perPage			: '',
		filters			: false,
		filterTax		: '',
		filterType		: '',
		filterData		: {},
		isFiltering		: false,
		isDefaultFilter	: false,
		activeFilter	: '',
		totalPages		: 1,
		currentPage		: 1,
		cacheData		: {},
		ajaxData 		: {},
		includeSettings	: true,
		matchHeight		: false,
		masonry			: false,
		style			: '',

		_hasPosts: function()
		{
			return $(this.postClass).length > 0;
		},

		_initIsotopeData: function() {
			var postFilterData = {
				itemSelector: '.pp-content-post',
				percentPosition: true,
				transitionDuration: '0.3s',
				hiddenStyle: {
					opacity: 0
				},
				visibleStyle: {
					opacity: 1
				},
				isOriginLeft: ! $('body').hasClass( 'rtl' ),
			};

			if ( !this.masonry ) {
				postFilterData = $.extend( {}, postFilterData, {
					layoutMode: 'fitRows',
					fitRows: {
						gutter: '.pp-grid-space'
				  	},
				} );
			}

			if ( this.masonry ) {

				postFilterData = $.extend( {}, postFilterData, {
					masonry: {
						columnWidth: '.pp-content-post',
						gutter: '.pp-grid-space'
					},
				} );
			}

			// set filter data globally to use later for ajax scroll pagination.
			this.filterData = postFilterData;

			// set default filter as active filter.
			if ( this.settings.defaultFilter !== '' ) {
				this.activeFilter = this.settings.defaultFilter;
			}
		},

		_initLayout: function()
		{
			if ( $(this.nodeClass).find('.pp-posts-wrapper').hasClass('pp-posts-initiated') ) {
				return;
			}

			switch(this.settings.layout) {

				case 'grid':
					this._initIsotopeData();
					this._gridLayout();
					this._initPagination();
					this._reLayout();
					break;

				case 'carousel':
					this._carouselLayout();
					break;
			}

			$(this.postClass).css('visibility', 'visible');

			var self = this;

			$(window).on('load', function() {
				if ( ! $( self.nodeClass ).hasClass( 'pp-cg-no-page-scroll' ) ) {
					FLBuilderLayout._scrollToElement( $( self.nodeClass + ' .pp-paged-scroll-to' ) );
				}
			});

			$(this.nodeClass).on( 'grid.afterInit carousel.afterInit', function() {
				$(self.nodeClass).find('.pp-posts-wrapper').addClass('pp-posts-initiated');

				if ( $(self.nodeClass).find( '.owl-nav' ).length > 0 && self.settings.carousel.autoplay ) {
					var carousel = $(self.nodeClass).find( '.owl-carousel' );
					$(self.nodeClass).find( '.owl-nav button' ).on( 'mouseover', function() {
						carousel.trigger( 'stop.owl.autoplay' );
					} ).on( 'mouseleave', function() {
						carousel.trigger( 'play.owl.autoplay' );
					} );
				}
			} );

			// Fix native lazy load issue.
			$(this.nodeClass).find('.pp-posts-wrapper img').on('load', function() {
				if ( 'lazy' !== $(this).attr('loading') ) {
					return;
				}
				var postsWrapper = $(self.nodeClass).find('.pp-content-post-grid');
				if ( 'undefined' !== typeof $.fn.isotope ) {
					setTimeout(function() {
						if ( postsWrapper.data( 'isotope' ) ) {
							postsWrapper.isotope('layout');
						}
					}, 500);
				}
			});
		},

		_gridLayout: function()
		{
			var wrap = $(this.wrapperClass);
			var postFilterData = this.filterData;

			wrap.imagesLoaded( $.proxy( function() {

				var node = $(this.nodeClass);
				var base = this;
				var postFilters = ! wrap.data( 'isotope' ) ? wrap.isotope( postFilterData ) : wrap;

                if ( this.settings.filters || this.masonry ) {

					var filterWrap = $(this.nodeClass).find('.pp-post-filters');
					var filterToggle = $(this.nodeClass).find('.pp-post-filters-toggle');

					filterToggle.on('click', function () {
						filterWrap.slideToggle(function () {
							if ($(this).is(':visible')) {
								filterToggle.addClass('pp-post-filters-open');
							}
							if (!$(this).is(':visible')) {
								filterToggle.removeClass('pp-post-filters-open');
							}
						});
					});

					filterWrap.on('click keyup', '.pp-post-filter', function(e) {
						var shouldFilter = 'click' === e.type || ( 'keyup' === e.type && ( 13 === e.keyCode || 13 === e.which ) )
						if ( ! shouldFilter ) {
							return;
						}
						// set active filter globally to use later for ajax scroll pagination.
						base.activeFilter = $(this).data('term');
						base.isFiltering = true;

						if ('static' === base.filterType) {
							var filterVal = $(this).attr('data-filter');
							postFilters.isotope({ filter: filterVal });
						} else {
							var term = $(this).data('term');
							$(base.wrapperClass).addClass('pp-is-filtering');
							base._getPosts(term, postFilterData);
						}

						filterWrap.find('.pp-post-filter').removeClass('pp-filter-active');
						$(this).addClass('pp-filter-active');

						filterToggle.find('span.toggle-text').html($(this).text());
						if (filterToggle.hasClass('pp-post-filters-open')) {
							filterWrap.slideUp();
							filterToggle.removeClass('pp-post-filters-open');
						}

						$(base.nodeClass).trigger('grid.filter.change');
					});
					
					if ('dynamic' === base.filterType) {
						$(base.nodeClass).find('.fl-builder-pagination a').off('click').on('click', function (e) {
							e.preventDefault();
							var pageNumber = base._getPageNumber( this );
							base.currentPage = pageNumber;
							base._getPosts('', postFilterData, pageNumber);
						});
					}

					// Default filter.
					if ( base.settings.defaultFilter !== '' ) {
						var defaultFilter = base.settings.defaultFilter;
						if ( filterWrap.find('li[data-term="' + defaultFilter + '"]').length > 0 ) {
							self.isDefaultFilter = true;
							filterWrap.find('li[data-term="' + defaultFilter + '"]').trigger('click');
						}
					}

					// Trigger filter by hash parameter in URL.
					if ( '' !== location.hash ) {
						var filterHash = location.hash.split('#')[1];

						filterWrap.find('li[data-term="' + filterHash + '"]').trigger('click');
					}

					// Trigger filter on hash change in URL.
					$(window).on('hashchange', function() {
						if ( '' !== location.hash ) {
							var filterHash = location.hash.split('#')[1];
	
							filterWrap.find('li[data-term="' + filterHash + '"]').trigger('click');
						}
					});
                }

                if( ! this.masonry ) {
                    setTimeout( function() {
						if ( base.settings.filters && 'static' === base.filterType ) {
							node.find('.pp-filter-active').trigger('click');
						}
						base._gridLayoutMatchHeight();
						wrap.isotope('layout');
                    }, 1000 );
                }

				$(this.nodeClass).trigger('grid.afterInit');

			}, this ) );
		},

		_carouselLayout: function()
		{
			var wrap = $(this.nodeClass + ' .pp-content-post-carousel .pp-content-posts-inner');

			var owlOptions = {
				onInitialized: $.proxy(this._gridLayoutMatchHeightSimple, this),
				onResized: $.proxy(this._gridLayoutMatchHeightSimple, this),
				onRefreshed: $.proxy(this._gridLayoutMatchHeightSimple, this),
				onLoadedLazy: $.proxy(this._gridLayoutMatchHeightSimple, this),
				rtl: $('body').hasClass( 'rtl' ),
				URLhashListener: true
			};
			if ( $(this.postClass).length < this.settings.carousel.items ) {
				this.settings.carousel.slideBy = 'page';
				this.settings.carousel.loop = false;
			}
			if ( this.settings.carousel.lazyLoad ) {
				$( this.postClass ).each(function() {
					var src = $( this ).find( '.pp-post-image img' ).addClass( 'owl-lazy' ).attr( 'src' );
					$( this ).find( '.pp-post-image img' ).removeAttr( 'src' ).attr( 'data-src', src );
				});
			}

			owlOptions = $.extend({}, this.settings.carousel, owlOptions);
			$(this.nodeClass).trigger( 'carousel.beforeInit', [ owlOptions ] );

			wrap.imagesLoaded( $.proxy( function() {
				wrap.owlCarousel( owlOptions );

				$(this.nodeClass).trigger('carousel.afterInit');
			}, this));
		},

		_getPosts: function (term, isotopeData, paged) {
			var processAjax = false,
				filter 		= term,
				paged 		= (!paged || 'undefined' === typeof paged) ? 1 : paged;

			if ('undefined' === typeof term || '' === term) {
				filter = 'all';
			}

			this._getTotalPages();

			var cacheData = this._getCacheData(filter);

			if ('undefined' === typeof cacheData) {
				processAjax = true;
			} else {
				var cachedResponse = cacheData.page[paged];
				if ('undefined' === typeof cachedResponse) {
					processAjax = true;
				} else {
					$(this.nodeClass).trigger('grid.beforeRender');
					this._renderPosts(cachedResponse, {
						term: term,
						isotopeData: isotopeData,
						page: paged
					});
				}
			}

			if (processAjax) {
				this._getAjaxPosts(term, isotopeData, paged);
			}
		},

		_getAjaxPosts: function (term, isotopeData, paged) {
			var taxonomy = this.filterTax,
				perPage = this.perPage,
				paged = 'undefined' === typeof paged ? false : paged,
				self = this;

			var currentPage = this.settings.current_page.split('?')[0];

			var data = {
				pp_action: 'get_ajax_posts',
				node_id: this.settings.id,
				paged: !paged ? this.settings.page : paged,
				current_page: currentPage,
			};

			if ( 'undefined' !== typeof this.includeSettings && this.includeSettings ) {
				data.settings = this.settings.fields;
			}

			if ( 'undefined' !== typeof this.settings.template_id ) {
				data.template_id = this.settings.template_id;
				data.template_node_id = this.settings.template_node_id;
			}

			// Archive.
			if ( 'undefined' !== typeof this.settings.is_archive ) {
				data['is_archive'] = true;
			}

			// Term.
			if ('undefined' !== typeof term && '' !== term) {
				data['term'] = term;
			} else if ( this.settings.is_tax && this.settings.current_term ) {
				data['is_tax'] = true;
				data['taxonomy'] = this.settings.current_tax;
				data['term'] = this.settings.current_term;
			}

			// Author.
			if ( this.settings.is_author && this.settings.current_author ) {
				data['is_author'] = true;
				data['author_id'] = this.settings.current_author;
			}

			if ( this.settings.is_search ) {
				data['is_search'] = true;
				data['search_term'] = this.settings.search_term;
			}

			if ('undefined' !== typeof this.settings.orderby || '' !== this.settings.orderby) {
				data['orderby'] = this.settings.orderby;
			}

			this.ajaxData = data;

			$(this.wrapperClass).addClass('pp-is-filtering');

			$.post(
				location.href.split('#').shift().replace( location.search, '' ),
				data,
				function (response) {
					// if ( 'undefined' !== typeof response.success && ! response.success ) {
					// 	self.includeSettings = true;
					// 	self._getAjaxPosts( term, isotopeData, paged );
					// 	return;
					// }
					self.includeSettings = true;
					self._setCacheData(term, response, paged);
					$(self.nodeClass).trigger('grid.beforeRender');
					self._renderPosts(response, {
						term: term,
						isotopeData: isotopeData,
						page: paged
					});
					$(self.wrapperClass).removeClass('pp-is-filtering');
				}
			).fail( function( xhr ) {
				if ( 404 == xhr.status && 'undefined' !== xhr.responseJSON.data ) {
					var response = xhr.responseJSON;
					self.includeSettings = true;
					self._setCacheData(term, response, paged);
					$(self.nodeClass).trigger('grid.beforeRender');
					self._renderPosts(response, {
						term: term,
						isotopeData: isotopeData,
						page: paged
					});
					$(self.wrapperClass).removeClass('pp-is-filtering');
				}
			} );
		},

		_renderPosts: function (response, args) {
			var self = this,
				wrap = $(this.wrapperClass),
				posts = $(response.data),
				notFoundText = false;

			if ( ( 'load_more' !== self.settings.pagination && 'scroll' !== self.settings.pagination ) || self.isFiltering ) {
				wrap.isotope('remove', $(this.postClass));
			}

			wrap.isotope( 'remove', $( '.pp-posts-not-found-text' ) );
			if (!this.masonry) {
				wrap.isotope('insert', posts, $.proxy(this._gridLayoutMatchHeight, this));
				wrap.imagesLoaded($.proxy(function () {
					setTimeout(function () {
						self._gridLayoutMatchHeight();
					}, 150);
				}, this));
			} else {
				wrap.isotope('insert', posts);
			}
			
			wrap.find('.pp-grid-space').remove();
			wrap.append('<div class="pp-grid-space"></div>');

			wrap.imagesLoaded($.proxy(function () {
				setTimeout(function () {
					if (!this.masonry) {
						self._gridLayoutMatchHeight();
					}
					wrap.isotope('layout');
				}, 500);
			}, this));

			if ( 'load_more' === self.settings.pagination ) {
				$(self.nodeClass).find('.pp-content-grid-load-more').remove();
			}
			if ( 'scroll' === self.settings.pagination ) {
				$(self.nodeClass).find('.pp-content-grid-loader').remove();
			}

			if (response.pagination) {
				var $pagination = $(response.pagination);

				$(self.nodeClass).find('.fl-builder-pagination').remove();
				$(self.nodeClass).find( '> .fl-module-content' ).append($pagination);
				if ( 'load_more' === self.settings.pagination ) {
					setTimeout(function() {
						self._getTotalPages();
						if ( self.totalPages !== self.currentPage ) {
							$(self.nodeClass).find('.fl-module-content .pp-content-grid-load-more').fadeIn();
							self._initPagination();
						} else {
							$(self.nodeClass).find('.fl-module-content .pp-content-grid-load-more').slideUp();
						}
					}, 250);
				}
				$(self.nodeClass).find('.pp-ajax-pagination a').off('click').on('click', function (e) {
					e.preventDefault();
					var pageNumber = self._getPageNumber( this );
					self.currentPage = pageNumber;
					self._getPosts(args.term, args.isotopeData, pageNumber);
				});
			} else {
				$(self.nodeClass).find('.fl-builder-pagination').remove();
			}

			if ( ('load_more' !== self.settings.pagination && 'scroll' !== self.settings.pagination) || self.isFiltering ) {
				if ( self.settings.scrollTo && ! self.isDefaultFilter ) {
					var offsetTop = wrap.offset().top - 200;
					$('html, body').stop().animate({
						scrollTop: offsetTop
					}, 300);
					self.isDefaultFilter = false;
				}
			}

			if ( self.isFiltering ) {
				self.isFiltering = false;
				$(self.nodeClass).trigger( 'grid.filter.complete' );
			}
			wrap.removeClass('pp-is-filtering');

			$(self.nodeClass).trigger('grid.rendered');
		},

		_getPageNumber: function( pageElement )
		{
			var pageNumber = parseInt( $(pageElement).text() ); //$(pageElement).attr('href').split('#page-')[1];

			if ( $(pageElement).hasClass('next') ) {
				pageNumber = parseInt( $(pageElement).parents('.pp-content-grid-pagination').find('.current').text() ) + 1;
			}
			if ( $(pageElement).hasClass('previous') ) {
				pageNumber = parseInt( $(pageElement).parents('.pp-content-grid-pagination').find('.current').text() ) - 1;
			}

			return pageNumber;
		},

		_setCacheData: function (filter, response, paged) {
			if ('undefined' === typeof filter || '' === filter) {
				filter = 'all';
			}
			if ('undefined' === typeof paged || !paged) {
				paged = 1;
			}

			if ('undefined' === typeof this.cacheData.ajaxCache) {
				this.cacheData.ajaxCache = {};
			}
			if ('undefined' === typeof this.cacheData.ajaxCache[filter]) {
				this.cacheData.ajaxCache[filter] = {};
			}
			if ('undefined' === typeof this.cacheData.ajaxCache[filter].page) {
				this.cacheData.ajaxCache[filter].page = {};
			}

			this.cacheData.ajaxCache[filter].page[paged] = response;
		},

		_getCacheData: function (filter) {
			var cacheData = this.cacheData;

			if ('undefined' === typeof cacheData.ajaxCache) {
				cacheData.ajaxCache = {};
			}

			return cacheData.ajaxCache[filter];
		},

		_gridLayoutMatchHeight: function()
		{
			var highestBox = 0;
			var contentHeight = 0;
			var postElements = $(this.postClass + ':visible');
			var columns = this.settings.postColumns.desktop;

			if (! this.matchHeight || 1 === columns) {
				return;
			}

			if ( 'style-9' === this.style ) {
				return;
			}

			if ( this.settings.layout === 'grid' ) {
				if ( this.masonry ) {
					return;
				}

				if (window.innerWidth <= 980) {
					columns = this.settings.postColumns.tablet;
				}
				if (window.innerWidth <= 767) {
					columns = this.settings.postColumns.mobile;
				}

				if ( 1 === columns ) {
					return;
				}

				postElements.css('height', 'auto');

				var rows = Math.round(postElements.length / columns);

				if ( postElements.length % columns > 0 ) {
					rows = rows + 1;
				}

				// range.
				var j = 1,
					k = columns;

				for( var i = 0; i < rows; i++ ) {
					// select number of posts in the current row.
					var postsInRow = $(this.postClass + ':visible:nth-child(n+' + j + '):nth-child(-n+' + k + ')');

					// get height of the larger post element within the current row.
					postsInRow.css('height', '').each(function () {
						if ($(this).height() > highestBox) {
							highestBox = $(this).height();
							contentHeight = $(this).find('.pp-content-post-data').outerHeight();
						}
					});
					// apply the height to all posts in the current row.
					postsInRow.height(highestBox);

					// increment range.
					j = k + 1;
					k = k + columns;
					if ( k > postElements.length ) {
						k = postElements.length;
					}
					highestBox = 0;
				}
			} else {
				// carousel layout.
				postElements.css('height', '').each(function(){

					if($(this).height() > highestBox) {
						highestBox = $(this).height();
						contentHeight = $(this).find('.pp-content-post-data').outerHeight();
					}
				});

				postElements.height(highestBox);
			}
            //$(this.postClass).find('.pp-content-post-data').css('min-height', contentHeight + 'px').addClass('pp-content-relative');
		},

		_gridLayoutMatchHeightSimple: function () {
			if ( ! this.matchHeight ) {
				return;
			}

			if ( 'style-9' === this.style ) {
				return;
			}

			var highestBox = 0;
			var contentHeight = 0;
			var postElements = $(this.postClass);

			var columns = this.settings.postColumns.desktop;

			if (window.innerWidth <= 980) {
				columns = this.settings.postColumns.tablet;
			}
			if (window.innerWidth <= 767) {
				columns = this.settings.postColumns.mobile;
			}

			if ( 1 === columns && this.settings.layout === 'grid' ) {
				return;
			}

			postElements.css('height', '').each(function () {

				if ($(this).height() > highestBox) {
					highestBox = $(this).height();
					contentHeight = $(this).find('.pp-content-post-data').outerHeight();
				}
			});

			postElements.height(highestBox);
		},

		_initPagination: function()
		{
			var self = this;

			setTimeout(function() {
				self._getTotalPages();

				if ( self.settings.pagination === 'load_more' ) {
					self._initLoadMore();
				}
				if ( self.settings.pagination === 'scroll' && typeof FLBuilder === 'undefined' ) {
					self._initScroll();
				}
			}, 500);
		},

		_getTotalPages: function()
		{
			var pages = $( this.nodeClass + ' .pp-content-grid-pagination' ).find( 'li .page-numbers:not(.next)' );

			if ( pages.length > 1) {
				var total = pages.last().text().replace( /\D/g, '' )
				this.totalPages = parseInt( total );
			} else {
				this.totalPages = 1;
			}

			return this.totalPages;
		},

		_initLoadMore: function()
		{
			var self 		= this,
				$button 	= $(this.nodeClass).find('.pp-grid-load-more-button'),
				currentPage = self.currentPage,
				activeFilter = self.activeFilter,
				isAjaxPagination = 'dynamic' === self.filterType;

			if ( ! self.filters || 'dynamic' !== self.filterType ) {
				activeFilter = '';
			}

			$(self.nodeClass).on('grid.filter.change', function() {
				// re-assign active filter.
				if ( self.filters && 'dynamic' === self.filterType ) {
					activeFilter = self.activeFilter
				}
			});

			$button.off('click').on('click', function(e) {
				e.preventDefault();

				$(this).addClass('disabled loading');
				self.isFiltering = false;

				currentPage = parseInt( currentPage ) + 1;

				self._getPosts(activeFilter, self.filterData, currentPage);
				self.currentPage = currentPage;
				self.paginating = true;
			});

			$(self.nodeClass).on('grid.rendered', function() {
				$button.removeClass( 'disabled loading' );

				if ( currentPage >= self.totalPages ) {
					$button.parent().hide();
				}
			});

			// Reset pagination index on filter.
			$(self.nodeClass).on('grid.filter.complete', function() {
				if ( $(self.nodeClass).find( '.pp-content-grid-pagination' ).length > 0 ) {
					self._getTotalPages();
					self.currentPage = currentPage = 1;
					$button.parent().show();
				} else {
					$button.parent().hide();
				}
			});
		},

		_initScroll: function()
		{
			var	self			= this,
				gridOffset 		= $(this.wrapperClass).offset(),
				gridHeight		= $(this.wrapperClass).height(),
				winHeight		= $(window).height(),
				currentPage 	= this.currentPage,
				activeFilter	= self.activeFilter,
				rendered		= false,
				loaded			= false;

			if ( ! self.filters || 'dynamic' !== self.filterType ) {
				activeFilter = '';
			}

			$(window).on('scroll', $.proxy( function() {
				if ( loaded ) {
					return;
				}
				var scrollPos = $(window).scrollTop();

				if ( scrollPos >= gridOffset.top - ( winHeight - gridHeight ) ) {
					self.isFiltering = false;
					currentPage = parseInt( currentPage ) + 1;

					$(self.nodeClass).find('.pp-content-grid-loader').show();

					if ( currentPage <= self.totalPages ) {
						loaded = true;
						self._getPosts(activeFilter, self.filterData, currentPage);
						self.paginating = true;
					} else {
						loaded = true;
						$(self.nodeClass).find('.pp-content-grid-loader').hide();
					}

					self.currentPage = currentPage;
				}
			}, this ) );

			$(self.nodeClass).on('grid.filter.change', function() {
				// re-assign active filter.
				if ( self.filters && 'dynamic' === self.filterType ) {
					activeFilter = self.activeFilter
				}
				
				// get container height.
				gridHeight = $(self.wrapperClass).height();
				self._gridLayoutMatchHeightSimple();

				$(self.wrapperClass).isotope('layout');

				if ( 'dynamic' === self.filterType ) {
					self._getTotalPages();
					self.currentPage = currentPage = 1;
					loaded = false;
				}
			});

			$(self.nodeClass).on('grid.rendered', function() {
				// get gridHeight again after render.
				gridHeight = $(self.wrapperClass).height();

				if ( ! rendered ) {
					self._getTotalPages();
				}
				
				$(self.nodeClass).find('.pp-content-grid-loader').hide();

				setTimeout(function() {
					self._gridLayoutMatchHeightSimple();
					$(self.wrapperClass).isotope('layout');
				}, 500);

				// set loaded flag.
				if ( currentPage >= self.totalPages ) {
					loaded = true;
				} else {
					loaded = false;
				}

				rendered = true;
			});

			// Reset pagination index on filter.
			$(self.nodeClass).on('grid.filter.complete', function() {
				if ( $(self.nodeClass).find( '.pp-content-grid-pagination' ).length > 0 ) {
					self._getTotalPages();
					self.currentPage = currentPage = 1;
				}
			});
		},

		_reLayout: function()
		{
			var self = this;
			var wrap = $(this.wrapperClass);

			$(document).on('sf:ajaxfinish', '.searchandfilter', function(){
				self._gridLayout();
			});

			$(document).on('facetwp-loaded', function() {
				if ( 'undefined' !== typeof FWPBB && 'undefined' !== typeof FWPBB.modules ) {
					FWPBB.modules[ self.settings.id ] = self.settings;
				}

				wrap.imagesLoaded(function() {
					if ( $('body').hasClass('fl-builder-active') ) {
						return;
					}
					if ( ! self.masonry ) {
						self._gridLayoutMatchHeight();
					}

					setTimeout(function() {
						if ( wrap.data( 'isotope' ) ) {
							wrap.isotope('destroy');
						}
						//wrap.isotope( self.filterData );
						self.facetWPLoaded = true;
						self._gridLayout();
						self._initPagination();
					}, 500);
				});
			});
		}
	};

})(jQuery);
