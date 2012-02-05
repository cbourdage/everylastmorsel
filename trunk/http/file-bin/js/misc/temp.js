/* -*- coding:utf-8-unix;tab-width:4 -*- vim:set fileenc=utf-8 ff=unix ts=4: */
/*!	$Revision: 1119 $ */
/*jslint browser: true, confusion: true, eqeqeq: true, immed: true,
         newcap: true, nomen: true, onevar: true, plusplus: true, regexp: true,
         sloppy: true, undef: true, white: true */
/*global console, iScroll, jQuery, swfobject, window */

/*
 * Allow jQuery to co-exist with other libraries that use "$" as their magic
 * variable.
 */
jQuery.noConflict();

(function ($) {
	/*
	 * fdt is the top-level namespace for all custom JavaScript methods and
	 * fields used by the Fantasy Draft Tool.
	 */
	var fdt = {};
	fdt = {
		/*
		 * Cached references to frequently-accessed elements.
		 */
		elems: {
			help: null,
			helpTrig: null,
			leagueSettings: null,
			leagueSettingsTrig: null,
			overlay: null,
			hiddenRankingFields: [],
			scrollers: [],
			top: null
		},
		/*
		 * Hide the #fdt-details (center) container.  Accepts a single, optional
		 * parameter of type function to be executed after the container is
		 * hidden.
		 */
		collapseDetails: function (fn) {
			var detailsCon = $('#fdt-details');
			if (!detailsCon.hasClass('fdt-collapsed')) {
				detailsCon.slideUp('fast', function () {
					detailsCon.
						addClass('fdt-collapsed').
						removeClass('fdt-expanded');
					fdt.execFn(fn);
				});
			} else {
				fdt.execFn(fn);
			}
			return false;
		},
		/*
		 * Set the #fdt-teams (left-hand) container to it's minimal-width state.
		 * Accepts a single, optional parameter of type function to be executed
		 * after the container is minimized.
		 */
		collapseTeams: function (fn) {
			var teamsCon = $('#fdt-teams'),
				width = teamsCon.width();
			if (!teamsCon.hasClass('fdt-collapsed') && width > 0) {
				teamsCon.animate({width: '230px'}, 'fast', function () {
					teamsCon.addClass('fdt-collapsed').removeClass('fdt-expanded');
					fdt.execFn(fn);
				});
			} else {
				fdt.execFn(fn);
			}
			return false;
		},
		/*
		 * Set the #fdt-rankings (right-hand) container to it's minimal-width
		 * state.  Accepts a single, optional parameter of type function to be
		 * executed after the container is minimized.
		 */
		collapseRankings: function (fn) {
			var rankingsCol = $('#fdt-rankings'),
				width = rankingsCol.width();
			if (!rankingsCol.hasClass('fdt-collapsed') && width > 0) {
				fdt.rankingFieldsHide();
				rankingsCol.addClass('fdt-collapsed').removeClass('fdt-expanded');
			} else {
				fdt.execFn(fn);
			}
			return false;
		},
		/*
		 * Hide the #fdt-team-analysis container.  Accepts a single, optional
		 * parameter of type function to be executed after the container is
		 * hidden.
		 */
		collapseTeamAnalysis: function (fn) {
			var con = $('#fdt-team-analysis');
			if (!con.hasClass('fdt-collapsed')) {
				con.slideUp(300, function () {
					con.addClass('fdt-collapsed').removeClass('fdt-expanded');
					fdt.execFn(fn);
				});
			} else {
				fdt.execFn(fn);
			}
			return false;
		},
		/*
		 * Calls console.log() for all arguments, if it exists.
		 */
		debugLog: function () {
			var i = 0,
				j = arguments.length;
			if (typeof console === 'object' && console !== null &&
					typeof console.log === 'function') {
				for (; i < j; i += 1) {
					console.log(arguments[i]);
				}
			}
		},
		/*
		 * Executes the parameter fn if it is a function.  Execution is wrapped
		 * in a try/catch block.  Any exceptions caught will be printed via
		 * fdt.debugLog().
		 */
		execFn: function (fn) {
			if (typeof fn === 'function') {
				try {
					fn();
				} catch (e) {
					fdt.debugLog(e);
				}
			}
		},
		/*
		 * Restores (makes visible) the #fdt-details (center) container.
		 * Accepts a single, optional parameter of type function to be executed
		 * after the container is restore to view.
		 */
		expandDetails: function (fn) {
			var detailsCon = $('#fdt-details');
			if (detailsCon.hasClass('fdt-collapsed')) {
				detailsCon.slideDown('fast', function () {
					detailsCon.removeClass('fdt-collapsed').addClass('fdt-expanded');
					fdt.refreshMobileScroll();
					fdt.execFn(fn);
				});
			} else {
				fdt.execFn(fn);
			}
			return false;
		},
		/*
		 * Maximizes the #fdt-teams (left-hand) container.  Accepts a single,
		 * optional parameter of type function to be executed after the
		 * container is maximized.
		 */
		expandTeams: function (fn) {
			alert('Deprecated.');
			/*
			var teamsCon = $('#fdt-teams');
			fdt.collapseDetails();
			fdt.collapseRankings();
			fdt.collapseTeamAnalysis();
			if (teamsCon.hasClass('fdt-collapsed')) {
				teamsCon.
					animate({width: '690px'}, 'fast', function () {
						teamsCon.
							removeClass('fdt-collapsed').
							addClass('fdt-expanded');
						fdt.execFn(fn);
					});
			} else {
				fdt.execFn(fn);
			}
			*/
			return false;
		},
		/*
		 * Maximizes the #fdt-rankings (right-hand) container.  Accepts a
		 * single, optional parameter of type function to be executed after the
		 * container is maximized.
		 */
		expandRankings: function (fn) {
			var rankingsCol = $('#fdt-rankings');
			fdt.collapseDetails();
			fdt.collapseTeams();
			fdt.collapseTeamAnalysis();
			if (rankingsCol.hasClass('fdt-collapsed')) {
				rankingsCol.animate({width: '660px'}, '1000', function () {
					rankingsCol.removeClass('fdt-collapsed').addClass('fdt-expanded');
					fdt.rankingFieldsShow();
					fdt.execFn(fn);
				});
			} else {
				fdt.execFn(fn);
			}
			return false;
		},
		/*
		 * Displays the #fdt-team-analysis container.  Accepts a single,
		 * optional parameter of type function to be executed after the
		 * container is shown.
		 */
		expandTeamAnalysis: function (fn) {
			var con = $('#fdt-team-analysis');
			if (!con.hasClass('fdt-expanded')) {
				fdt.collapseRankings();
				fdt.collapseTeams();
				fdt.collapseDetails(function () {
					con.slideDown(300, function () {
						fdt.refreshMobileScroll();
					})
					con.removeClass('fdt-collapsed').addClass('fdt-expanded');
					fdt.execFn(fn);
				});
			} else {
				fdt.execFn(fn);
			}
			
			window.setTimeout(function() {
				fdt.resizeTeamAnalysisGraph(con); 
			}, 600);
			return false;
		},
		
		resizeTeamAnalysisGraph: function(con) {
			con.find('.fdt-graph-wrapper').each(function(key, item) {			
				var $graphWrap = $(this);				
				var graphWrapWidth = $graphWrap.parent().outerWidth(true);	
				
				if ($graphWrap.children().length <= 6) {					
					var graphsWidth = 0;
					$graphWrap.children().each(function(key, item) {
						graphsWidth += $(this).outerWidth(true);
					});
					
					var diff = graphWrapWidth - graphsWidth;
					$graphWrap.children(':first-child').css('margin-left', (diff / 2) - 5 + 'px');
				}
			});	
		},
		
		/*
		 * Returns a new, randomly generated unique ID suitable for assigning to
		 * an element.
		 */
		generateId: function () {
			var pfx = 'fdt-gid-',
				tmp;
			do {
				tmp = pfx + Math.
					floor(Math.random() * 0xFFFFFFFF).
					toString(16);
			} while (null !== document.getElementById(tmp));
			return tmp;
		},

		/*
		 * Initialize tab navigation of content panels inside the edit scoring
		 * section of the league settings container.
		 */
		initEditScoringSubnav: function () {
			var triggers = $('#fdt-edit-scoring .fdt-child-nav a'),
				cons = $('#fdt-edit-scoring .fdt-input-group');
			triggers.each(function (i) {
				var trigger = $(this),
					con = $(trigger.attr('href'));
				if (i < 1) {
					trigger.addClass('fdt-active');
					con.removeClass('fdt-nodisplay').show();
				} else {
					trigger.removeClass('fdt-active');
					con.hide().removeClass('fdt-nodisplay');
				}
				trigger.click(function () {
					if (!trigger.hasClass('fdt-active')) {
						triggers.removeClass('fdt-active');
						cons.hide();
						trigger.addClass('fdt-active');
						con.fadeIn();
						
						if (con.attr('id') == 'fdt-edit-scoring-roto') {
							con.parent().find('.fdt-action').addClass('indent');
						} else {
							con.parent().find('.fdt-action').removeClass('indent');
						}
					}
					return false;
				});
			});
		},
		/*
		 * Initialize expanding/collapsing of tables in ranking section.
		 */
		initExpandingRankingHeaders: function () {
			var lis = $('#fdt-position-list > li');
			$('.fdt-ranking-table thead th').click(function () {
				var $trig = $(this),
					li = $trig.parents('li:first'),
					table = li.find('table:first'),
					tbody = table.find('tbody:first');
					
				if (li.hasClass('fdt-collapsed')) {
					lis.not(li).not('.fdt-collapsed').each(function () {
						var $tmp = $(this);
						$tmp.addClass('fdt-collapsed');
						//$tmp.animate({'height', '35px'}, 200);
						/*$tmp.find('tbody:first')
							.hide(1, function () {
								$tmp.addClass('fdt-collapsed');
							});
						*/
							
						//$tmp.find('th.fdt-display').removeClass('fdt-display').addClass('fdt-nodisplay');
					});
					
					//tbody.hide(1);
					li.parent().find('.fdt-expanded').removeClass('fdt-expanded');
					li.removeClass('fdt-collapsed').addClass('fdt-expanded');
					//$tmp.animate({'height', '525px'}, 200);
					//tbody.slideDown(1000); //show();
				}
				
				if ($('#fdt-rankings').hasClass('fdt-expanded')) {
					//li.find('.fdt-nodisplay').removeClass('fdt-nodisplay').addClass('fdt-display');
				}
			});
		},
		
		/**
		 * Initializes the click event to expand the rankings 
		 * menu to full screen
		 */
		initRankingsSlideOut: function() {
			var column = $('#fdt-rankings');
			$('#fdt-position-list a[href="#expand-rankings"]').addClass('expand-me');
			
			// Expansion click
			$('#fdt-position-list a[href="#expand-rankings"]').click(function(e) {
				e.preventDefault();
				var li = $(this).parent();
				
				/* if collapsed */
				if (column.hasClass('fdt-collapsed')) {
					column.removeClass('fdt-collapsed').addClass('fdt-expanded');
					column.find('th.closed').removeClass('closed');
					window.setTimeout(function() {
						$('#fdt-position-list a[href="#expand-rankings"]').addClass('collapse-me').removeClass('expand-me');
					}, 200);
				}
				else {
					column.removeClass('fdt-expanded').addClass('fdt-collapsed');
					window.setTimeout(function() {
						$('#fdt-position-list a[href="#expand-rankings"]').addClass('expand-me').removeClass('collapse-me');
						column.find('th.fdt-first').addClass('closed');
					}, 200);
				}
			});
		},
		
		initRecommendedToggle: function() {
			$('#fdt-suggested-queue-toggle a').click(function(e) {
				e.preventDefault();
				var ul = $(this).parent().parent(),
					id = $(this).attr('href');
				
				if (!$(this).hasClass('fdt-active')) {
					ul.find('a.fdt-active').removeClass('fdt-active');
					$(this).addClass('fdt-active');
					//$('.fdt-graph-wrapper:visible').hide();
					//$(id).show();
				}
			});
		},
		
		initGraphToggle: function() {
			$('.fdt-ppp-heading a').click(function(e) {
				e.preventDefault();
				var ul = $(this).parent().parent(),
					id = $(this).attr('href');
				
				if (!$(this).hasClass('active')) {
					ul.find('a.active').removeClass('active');
					$(this).addClass('active');
					$('.fdt-graph-wrapper:visible').hide();					
					$(id).show();
				}
			});
		},
		
		/*
		 * Initialize help dialog.
		 */
		initHelp: function () {
			fdt.elems.helpTrig = $('#fdt-help-trigger');
			fdt.elems.help = $('#fdt-help');
			fdt.elems.help.hide()
				.removeClass('fdt-nodisplay')
				.find('input[name="b"]')
				.val(navigator.userAgent);
			fdt.elems.helpTrig.click(function () {
				if (fdt.elems.helpTrig.hasClass('fdt-active')) {
					fdt.helpHide(function () {
						fdt.elems.helpTrig.focus();
					});
				} else if (fdt.elems.leagueSettingsTrig.hasClass('fdt-active')) {
					fdt.elems.leagueSettings.hide(function () {
						fdt.elems.leagueSettingsTrig.removeClass('fdt-active');
						fdt.helpShow();
					});
				} else if (fdt.elems.auctionTrig.hasClass('fdt-active')) {
					fdt.elems.auction.hide(function () {
						fdt.elems.auctionTrig.removeClass('fdt-active');
						fdt.helpShow();
					});
				} else {
					fdt.helpShow();
				}
				return false;
			});
			fdt.elems.help.find('.fdt-close a').click(function () {
				fdt.elems.helpTrig.click();
				return false;
			});
		},

		/*
		 * Hide the league-settings container.  Accepts a single, optional
		 * parameter of type function to be executed after the container is
		 * hidden.
		 */
		helpHide: function (fn) {
			fdt.elems.help.hide(function () {
				fdt.elems.helpTrig.removeClass('fdt-active');
				fdt.overlayHide(fn);
			});
		},

		/*
		 * Show the league-settings container.  Accepts a single, optional
		 * parameter of type function to be executed after the container is
		 * shown.
		 */
		helpShow: function (fn) {
			var pos = $('#fdt-content').position();
			var left = $(window).width() - fdt.elems.help.outerWidth(true);
			left = parseInt(left / 2, 10) - pos.left;

			if (!isNaN(left)) {
				fdt.elems.help.css('left', left + 'px');
			}
			fdt.overlayShow(function () {
				fdt.elems.help.show(function () {
					fdt.elems.helpTrig.addClass('fdt-active');
					fdt.elems.help.find('a:first').focus();
					fdt.execFn(fn);
				});
			});
		},

		initNavigationOverlays: function () {
			$('#fdt-nav').find('li').each(function(key, item) {
				var link = $(this).find('a:first-child');
				var idValue = link.attr('href').replace('#', '');
				
				fdt.elems[idValue + 'Trig'] = link;
				fdt.elems[idValue] = $('#' + idValue);
				fdt.elems[idValue].hide()
					.removeClass('fdt-nodisplay')
					.find('input[name="b"]')
					.val(navigator.userAgent);
					
				fdt.elems[idValue + 'Trig'].click(function () {
					if (fdt.elems[idValue + 'Trig'].hasClass('fdt-active')) {
						fdt.menuItemHide(function () {
							fdt.elems[idValue + 'Trig'].focus();
						});
					} else if (fdt.elems.helpTrig.hasClass('fdt-active')) {
						fdt.elems.help.hide(function () {
							fdt.elems.helpTrig.removeClass('fdt-active');
							fdt.menuItemShow(idValue);
						});
					} else if (fdt.elems.leagueSettingsTrig.hasClass('fdt-active')) {
						fdt.elems.leagueSettings.hide(function () {
							fdt.elems.leagueSettingsTrig.removeClass('fdt-active');
							fdt.menuItemShow(idValue);
						});
					} else if (fdt.elems.auctionTrig.hasClass('fdt-active')) {
						fdt.elems.auction.hide(function () {
							fdt.elems.auctionTrig.removeClass('fdt-active');
							fdt.menuItemShow(idValue);
						});
					} else {
						fdt.menuItemShow(idValue);
					}
					return false;
				});
			});
			
			// Bind close event
			fdt.elems[idValue + 'Trig'].find('.fdt-close a').click(function () {
				fdt.elems[idValue + 'Trig'].click();
				return false;
			});
		},
		
		menuItemHide: function (id, fn) {
			fdt.elems[id].hide(function () {
				fdt.elems[id + 'Trig'].removeClass('fdt-active');
				fdt.overlayHide(fn);
			});
		},

		menuItemShow: function (id, fn) {
			var pos = $('#fdt-content').position();
			var left = $(window).width() - fdt.elems[id].outerWidth(true);
			left = parseInt(left / 2, 10) - pos.left;
			
			if (!isNaN(left)) {
				fdt.elems[id].css('left', left + 'px');
			}
			fdt.overlayShow(function () {
				fdt.elems[id].show(function () {
					fdt.elems[id + 'Trig'].addClass('fdt-active');
					fdt.elems[id].find('a:first').focus();
					fdt.execFn(fn);
				});
			});
		},
		
		/*
		 * Initialize hiding/display of the league settings container.
		 */
		initLeagueSettings: function () {
			fdt.elems.leagueSettingsTrig = $('#fdt-league-settings-trigger');
			fdt.elems.leagueSettings = $('#fdt-league-settings');
			fdt.elems.leagueSettings.hide().removeClass('fdt-nodisplay');
			fdt.elems.leagueSettingsTrig.click(function () {
				if (fdt.elems.leagueSettingsTrig.hasClass('fdt-active')) {
					fdt.leagueSettingsHide(function () {
						fdt.elems.leagueSettingsTrig.focus();
					});
				} else if (fdt.elems.helpTrig.hasClass('fdt-active')) {
					fdt.elems.help.hide(function () {
						fdt.elems.helpTrig.removeClass('fdt-active');
						fdt.leagueSettingsShow();
					});
				} else {
					fdt.leagueSettingsShow();
				}
				return false;
			});
			fdt.elems.leagueSettings
				.find('p.fdt-close a').click(function () {
					fdt.elems.leagueSettingsTrig.click();
					return false;
			});
		},
		
		/*
		 * Initialize tab navigation of content panels inside the league
		 * settings container.
		 */
		initLeagueSettingsSubnav: function () {
			var triggers = $('#fdt-league-settings .fdt-subcontent-nav a'),
				cons = $('#fdt-league-settings .fdt-subcontent');
			
			triggers.each(function (i) {
				var trigger = $(this),
					con = $(trigger.attr('href'));
				if (i < 1) {
					trigger.addClass('fdt-active');
					con.removeClass('fdt-nodisplay').show();
				} else {
					trigger.removeClass('fdt-active');
					con.hide().removeClass('fdt-nodisplay');
				}
				
				
				var $searchInput = $('#fdt-add-player-q');
				var $searchResults = $('#fdt-add-player-q-results');
				var lastSearch = '';
				
				// Search input keyup for searching
		        $searchInput.bind('keyup', function(e) {
		          	if ($(this).val().length > 1 && $(this).val() != lastSearch) {          
		            	var sVal = $(this).val().toLowerCase();
		            
		            	// make ajax call and return json
		            	var results = [
		              		{'id' : 123213, 'name' : 'Brandon Frazier', 'available' : false}, 
		              		{'id' : 234234, 'name' : 'Brandon Jacobs', 'available' : true},
		              		{'id' : 234236, 'name' : 'Brandon Withers', 'available' : true}
		            	];
		            
		            	$searchResults.html('').css('display','block');
		            	jQuery.each(results, function(key, item) {
		              		var li = ''
		              		item.name = item.name.toLowerCase().replace(sVal, '<span>' + sVal + '</span>');
		              		if (!item.available) {
		                		li = '<li><a href="#add-this-player" class="strike"><strike>' + item.name + '</strike></a></li>';
		              		} else {
		                		li = '<li><a href="#add-this-player">' + item.name + '</a></li>';                
		                
		              		}
		              		$searchResults.append(li);
		            	});
		            
		            	// Bind the li click to display player and move name into search field
		            	$('#fdt-search-player-results li').click(function(e) {
		            		$searchResults.css('display','none').html('');
		              		$searchInput.val($(this).html());
		              		lastSearch = $searchInput.val();
		              		// TODO: Show player profile section 
		            	});
		          	}
		          	else {
		            	$searchResults.css('display','none');
		          	}
		        });
		        
		        trigger.click(function () {
		          	if (!trigger.hasClass('fdt-active')) {
		            	triggers.removeClass('fdt-active');
		            	cons.hide();
		            	trigger.addClass('fdt-active');
		            	con.fadeIn();
		          	}
		          	return false;
		        });
			});
		},		
				
		/*
		 * Initialize scrollability for "overflow:hidden" containers on mobile
		 * webkit browsers.  The iScroll library (http://cubiq.org/iscroll-4) is
		 * used to actually implement the scrolling.  All elements in a page
		 * possessing the class fdt-mobile-scroll will have their first child
		 * element set as the scrollable content.
		 */
		initMobileScroll: function () {
			$('.fdt-mobile-scroll').each(function () {
				var scroller = $(this),
					scrollSnap = scroller.find('li').length > 0 ? 'li' : 'tr';
				if ('' === $.trim(scroller.attr('id'))) {
					this.id = fdt.generateId();
				}
				
				if ($.browser.msie) {
					scroller.css('overflow', 'auto');
				} else {
					fdt.elems.scrollers[fdt.elems.scrollers.length] = new iScroll(this.id, {
						hScroll: false,
						snap: scrollSnap,
						vScrollbar: false
					});
				}
			});
		},
		
		/*
		 * Initialize dropdown child menus for top-level nav.
		 */
		initNavigation: function () {
			var nav = $('#fdt-nav'),
				lis = nav.find('> li'),
				topOffset = 1 + lis.first().height();
			lis.focusin(function () {
				var jq = $(this),
					offset,
					ul = jq.find('div'),
					toid = ul.data('timeoutId');
				if (toid !== null) {
					clearTimeout(toid);
					ul.data('timeoutId', null);
				}
				if (ul.length > 0) {
					offset = jq.offset();
					ul.css('left', (offset.left - 5) + 'px');
					ul.css('top', (offset.top + topOffset) + 'px');
				}
				ul.show(1);
				return false;
			}).mouseenter(function () {
				var jq = $(this),
					offset,
					ul = jq.find('div'),
					toid = ul.data('timeoutId');
				if (toid !== null) {
					clearTimeout(toid);
					ul.data('timeoutId', null);
				}
				if (ul.length > 0) {
					offset = jq.offset();
					ul.css('left', (offset.left - 5) + 'px');
					ul.css('top', (offset.top + topOffset) + 'px');
				}
				ul.show(1);
				return false;
			}).
			focusout(function () {
				var ul = $(this).find('div');
				ul.data('timeoutId', (function () {
					return setTimeout(function () {
						ul.slideUp('fast');
					}, 300);
				}()));
				return false;
			}).
			mouseleave(function () {
				var ul = $(this).find('div');
				ul.data('timeoutId', (function () {
					return setTimeout(function () {
						ul.slideUp('fast');
					}, 300);
				}()));
				return false;
			});
		},
		
		/*
		 * Initialize expanding/collapsing behavior of the #fdt-team-analysis
		 * when a team in #fdt-teams is clicked.
		 */
		initTeamAnalysis: function () {
			var teams = $('#fdt-team-list > li');
			$('#fdt-team-analysis').hide()
				.removeClass('fdt-nodisplay')
				.find('.fdt-close a')
				.click(function () {
					//teams
					fdt.collapseTeamAnalysis(function () {
						teams.removeClass('fdt-show-team');
						fdt.expandDetails();
					});
					return false;
				});
			teams.live('click', function () {
				var team = $(this);
				if (team.hasClass('fdt-show-team')) {
					fdt.collapseTeamAnalysis(function () {
						fdt.expandDetails();
						team.removeClass('fdt-show-team');
					});
				} else {
					teams.removeClass('fdt-show-team');
					fdt.expandTeamAnalysis(function () {
						 // Add call to populate team analysis container
						 // with appropriate team's data
					});					
					team.addClass('fdt-show-team');
				}
				return false;
			});
		},
		
		/**
		 * Player search 
		 */
		initPlayerSearch: function() {
			var $searchInput = $('#fdt-search-player');
			var $searchResults = $('#fdt-search-player-results');
			var defaultText = $('#fdt-search-player').val();
			var lastSearch = '';
			
			// Submit button click event
			$('#fdt-search-player-submit').click(function(e) {
				$searchResults.css('display','none').html('');
				lastSearch = $searchInput.val();
				// TODO: Show player profile section
				return false;
			});			
			
			// Search input focus and blur
			$searchInput.bind('focus', function(e) {
				if ($(this).val().toLowerCase() == defaultText.toLowerCase()) {
					$(this).val('');
				}
			});
			$searchInput.bind('blur', function(e) {
				if ($(this).val() == '') {
					$(this).val(defaultText);
				}
			})
			
			// Search input keyup for searching
			$searchInput.bind('keyup', function(e) {
				if ($(this).val().length > 1 && $(this).val() != lastSearch) {					
					var sVal = $(this).val().toLowerCase();
					
					// make ajax call and return json
					var results = [
						{"id" : 123213, 'name' : 'Player 1'}, 
						{'id' : 234234, 'name' : 'Player 2'}
					];
					
					$searchResults.html('').css('display','block');
					jQuery.each(results, function(key, item) {
						item.name = item.name.toLowerCase().replace(sVal, '<span>' + sVal + '</span>');
						$searchResults.append('<li>' + item.name + '</li>');
					});
					
					// Bind the li click to display player and move name into search field
					$('#fdt-search-player-results li').click(function(e) {
						$searchResults.css('display','none').html('');
						$searchInput.val($(this).html());
						lastSearch = $searchInput.val();
						// TODO: Show player profile section 
					});
				}
				else {
					$searchResults.css('display','none');
				}
			});
		},
		
		/**
		 * Draft player
		 */
		initDraftPlayer: function () {
			fdt.isAuction = true;
			$('a[href="#draft"]').click(function(e) {
				if (fdt.isAuction) {
					fdt.auctionShow();
				}
				else {
					alert('draft player');
				}
			});
		},
		
		initMessages: function() {
			fdt.elems.message = $('#fdt-message');
			fdt.elems.message.hide()
				.removeClass('fdt-nodisplay');
			
			var $messages = $('#fdt-message').find('.msg');
			fdt.elems.message.find('a[href="#close"]').click(function(e) {
				var $temp = $(this);
				fdt.overlayHide(function() {
					$temp.closest('.msg').removeClass('fdt-active').addClass('fdt-nodisplay');
				});
			});
		},
		
		showMessage: function(selector) {
			var $msgEl = $(selector);
			
			// Position calculations
			var pos = $('#fdt-content').position();
			var left = $(window).width() - fdt.elems.message.outerWidth(true);
			left = parseInt(left / 2, 10) - pos.left;
			var top = $(window).height() - fdt.elems.message.outerHeight(true);
			top = parseInt(top / 2, 10) - pos.top - 150;
			
			fdt.elems.message.css('left', left + 'px');
			fdt.elems.message.css('top', top + 'px');			
			
			fdt.overlayShow(function () {
				fdt.elems.message.show(function() {
					$msgEl.removeClass('fdt-nodisplay').addClass('fdt-active');
				});
			});
		},
		
		/*
		 * Apply the fdt-small-window class to the top-level page structure
		 * container (#fdt-top) if the window size (visible page-area) is less
		 * than 748 pixels in height.
		 */
		initWindowHeight: function () {
			if (748 > $(window).height()) {
				fdt.elems.top.addClass('fdt-small-window');
			} else {
				fdt.elems.top.removeClass('fdt-small-window');
			}
			fdt.refreshMobileScroll();
		},
		
		/*
		 * Top-level initialization method.  This method will be queued to
		 * execute via jQuery's $(document).ready().  All other initialization
		 * should be fired off from here.
		 */
		init: function () {
			fdt.elems.top = $('#fdt-top');
			fdt.elems.content = fdt.elems.top.find('#fdt-content');
			fdt.elems.overlay = fdt.elems.top.find('#fdt-ui-overlay');
			fdt.elems.hiddenRankingFields = fdt.elems.content
				.find('#fdt-rankings th.fdt-nodisplay, #fdt-rankings td.fdt-nodisplay')
				.get();
			fdt.elems.overlay.hide().removeClass('fdt-nodisplay');
			fdt.initWindowHeight();
			$(window).bind('orientationchange', fdt.initWindowHeight)
				.bind('resize', fdt.initWindowHeight);
			fdt.initEditScoringSubnav();
			fdt.initLeagueSettingsSubnav();
			fdt.initLeagueSettings();
			fdt.initHelp();
			fdt.initDraftPlayer();
			fdt.initAuction();
			fdt.initMessages();
			//fdt.initNavigation();
			ftd.initNavigationOverlays();
			fdt.initGraphToggle();
			fdt.initRecommendedToggle();
			fdt.initExpandingRankingHeaders();
			fdt.initRankingsSlideOut();
			fdt.initTeamAnalysis();
			fdt.initPlayerSearch();
			fdt.initMobileScroll();
			
			if ($.browser.mozilla) {
				$('body').addClass('firefox');
			}
			
			if (window.location.href.match('#thank-you')) {
				fdt.showMessage('#fdt-message-thank-you');
			}
			else if (window.location.href.match('#reset')) {
				fdt.showMessage('#fdt-message-reset');
			}			
		},
		/*
		 * Hide the league-settings container.  Accepts a single, optional
		 * parameter of type function to be executed after the container is
		 * hidden.
		 */
		leagueSettingsHide: function (fn) {
			fdt.elems.leagueSettings.hide(function () {
				fdt.elems.leagueSettingsTrig.removeClass('fdt-active');
				fdt.overlayHide(fn);
			});
		},
		/*
		 * Show the league-settings container.  Accepts a single, optional
		 * parameter of type function to be executed after the container is
		 * shown.
		 */
		leagueSettingsShow: function (fn) {
			fdt.overlayShow(function () {
				fdt.elems.leagueSettings.show(function () {
					fdt.elems.leagueSettingsTrig.addClass('fdt-active');
					fdt.elems.leagueSettings.find('a:first').focus();
					fdt.execFn(fn);
				});
			});
		},
		
		initAuction: function() {
			fdt.elems.auction = $('#fdt-auction');			
			fdt.elems.auction.hide()
				.removeClass('fdt-nodisplay')
				.find('input[name="b"]').val(navigator.userAgent);
			
			fdt.elems.auction.find('.fdt-close a').click(function () {
				fdt.auctionHide();
				return false;
			});			
		},
		
		/*
		 * Show the league-settings container.  Accepts a single, optional
		 * parameter of type function to be executed after the container is
		 * shown.
		 */
		auctionShow: function (fn) {
			var pos = $('#fdt-content').position();
			var left = $(window).width() - fdt.elems.auction.outerWidth(true);
			left = parseInt(left / 2, 10) - pos.left;
			
			if (!isNaN(left)) {
				fdt.elems.auction.css('left', left + 'px');
			}
			fdt.overlayShow(function () {
				fdt.elems.auction.show(function () {
					fdt.elems.auction.find('a:first').focus();
					fdt.execFn(fn);
				});
				fdt.elems.auction.find('.scroll-pane').jScrollPane();
			});
		},

		auctionHide: function (fn) {
			fdt.elems.auction.hide(function () {
				fdt.overlayHide(fn);
			});
		},
		
		/*
		 * Hide the lightbox-style page-overlay.  Accepts a single, optional
		 * parameter of type function to be executed after the overlay is
		 * hidden.
		 */
		overlayHide: function (fn) {
			fdt.elems.overlay.
				fadeOut('fast', function () {
					fdt.execFn(fn);
				});
		},
		/*
		 * Show the lightbox-style page-overlay.  Accepts a single, optional
		 * parameter of type function to be executed after the overlay is shown.
		 */
		overlayShow: function (fn) {
			fdt.elems.overlay
				.css({
					height: fdt.elems.content.outerHeight() + 'px',
					top: fdt.elems.content.offset().top + 'px',
					width: $(window).width()
				})
				.fadeIn('fast', function () {
					fdt.execFn(fn);
				});
		},
		/*
		 * Experimental:
		 * Remove fdt-nodisplay classes from all TD and TH elements in the
		 * rankings container (#fdt-rankings).
		 */
		rankingFieldsHide: function (fn) {
			$(fdt.elems.hiddenRankingFields).addClass('fdt-nodisplay');
			fdt.execFn(fn);
		},
		/*
		 * Experimental:
		 * Re-add fdt-nodisplay classes to all TD and TH elements in the
		 * rankings container (#fdt-rankings) that previously had them removed
		 * by fdt.rankingFieldsHide().
		 */
		rankingFieldsShow: function (fn) {
			$(fdt.elems.hiddenRankingFields).removeClass('fdt-nodisplay');
			fdt.execFn(fn);
		},
		/*
		 * The iScroll library we use to implement scrolling of
		 * "overflow:hidden" containers on mobile webkit relies on knowing the
		 * exact height of the pertinent elements.  Calling this method will
		 * cause all scrolling containers (that were initialized using
		 * fdt.initMobileScroll()) to refresh their height calculation.  This is
		 * intended to be called on window resize, or on any other event or
		 * action that causes a browser window's viewport dimension to change.
		 */
		refreshMobileScroll: function () {
			var i = 0,
				j = fdt.elems.scrollers.length;
			for (; i < j; i += 1) {
				fdt.elems.scrollers[i].refresh();
			}
		}
	};
	/*
	 * Place fdt into the global namespace.
	 */
	window.fdt = fdt;
	
			
	/*
	 * Queue the top-level initialization function to be executed when the DOM
	 * is ready.
	 */
	$(document).ready(fdt.init);
	//window.setTimeout("fdt.refreshMobileScroll()", 1000);
	
}(jQuery));
