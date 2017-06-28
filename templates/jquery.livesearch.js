(function ($) {
	$.fn.liveUpdate = function (list, line) {
		return this.each(function () {
			new $.liveUpdate(this, list, line);
		});
	};

	$.liveUpdate = function (e, list, line) {
		this.field = $(e);
		this.line = line;
		this.list = $(list);
		this.top_element = undefined;

		if (this.list.length > 0) {
			this.init();
		} else {
			$('#block_combinedsearch_0').hide();
		}
	};

	$.liveUpdate.prototype = {
		init:function () {
			var self = this;
			this.setupCache();

			this.field.on('keyup keypress', function (e) {
				self.filter(e);
			});
			self.filter();
			this.field.closest('form').off('keyup keypress');
		},

		filter:function (e) {
			// open the top element when pressing enter (key code 13)
			if (e !== undefined) {
				var code = e.keyCode || e.which;
				if (code  == 13) {
					if (this.top_element !== undefined) {
						var url = this.top_element.find('h4.il_ContainerItemTitle > a');
						if (jQuery.type(url.attr('href')) === 'string') {
							window.location.href = url.attr('href');
						}
					}
					e.preventDefault();
					return;
				}
			}

			if ($.trim(this.field.val()) == '') {
				this.list.find(this.line).show();
                this.list.find(this.line).parent().show();
				this.list.find(this.line + ' .subitem').show();
				this.list.find('.ilPDBlockSubHeader').show();
				this.field.parent().find('#SearchInTree').attr('disabled','disabled');
				this.top_element = this.rows[0];
				this.field.focus();
			} else {
				this.field.parent().find('#SearchInTree').removeAttr('disabled');
				this.displayResults(this.getScores(this.field.val().toLowerCase()));
			}
		},

		setupCache:function () {
			var self = this;
			this.rows = [];
			this.cache = [];
			this.list.find(this.line).each(function () {
				// TODO recursively look for children? Currently only one level of subitems is supported
				var $children = $(this).find('.subitem h4.il_ContainerItemTitle');
				var c = [];
				for (var i = 0; i < $children.length; i++) {
					c[i] = {txt : $($children[i]).text().toLowerCase()};
				}
				self.rows.push($(this));
				var item = {
					txt : $(this).find('.il_ContainerListItem .il_ContainerItemTitle h4.il_ContainerItemTitle').not('.subitem h4.il_ContainerItemTitle').text().toLowerCase(),
					children : c
				};

                self.cache.push(item);

			});
			this.cache_length = this.cache.length;
		},

		displayResults:function (scores) {
            var self = this;

            if(this.list.find(this.line).attr("class") != "ilHeader ilContainerBlockHeader") {
                this.list.find(this.line).hide();
            };
            if(this.list.find(this.line).parent().attr("class") != "ilContainerBlock container-fluid form-inline") {
                this.list.find(this.line).parent().hide();
            };


			this.list.find('.ilPDBlockSubHeader').hide();
			$.each(scores, function (i, score) {
                self.rows[score.index].show();
                self.rows[score.index].parent().show();

				var $subitems = self.rows[score.index].find('.subitem');
                $subitems.hide();
                for (var j = 0; j < score.children.length; j++) {
					$($subitems[score.children[j].index]).show();
				}

            });

			// Update the element that should be opened when pressing enter
			if (scores.length > 0) {
				this.top_element = self.rows[scores[0].index];
			} else {
				this.top_element = undefined;
			}
		},

		getScores:function (term) {
			var scores = [];
			for (var i = 0; i < this.cache_length; i++) {
				var score = this.cache[i].txt.score(term, 1);

				var child_scores = [];
				for (var j = 0; j < this.cache[i].children.length; j++) {
					var child_score = this.cache[i].children[j].txt.score(term, 1);
					if (child_score > 0) {
						child_scores.push({score : child_score, index : j});
					}
				}

				if (score > 0 || child_scores.length > 0) {
					scores.push({score : score, index : i, children : child_scores});
					// console.log('term: ' + term + ' index: ' + i + ' score: ' + score + ' number of children: ' + child_scores.length)
				}
			}

			return scores;
		}
	}
})(jQuery);