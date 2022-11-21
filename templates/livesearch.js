var Livesearch = (function () {
  var liveUpdate = function (e, list, line) {

    this.field = e;
    this.line = line;
    this.list = document.querySelectorAll(list);
    this.is_active = undefined;

    if (this.list.length > 0) {
      this.init();
    } else {
      document.getElementById('block_combinedsearch_0').style.display = "none";
    }
  };

  liveUpdate.prototype = {
    init: function () {
      var self = this;
      this.setupCache();

      this.field.addEventListener("keyup", function (e) {
        self.filter(e);
      });
      this.field.addEventListener("keypress", function (e) {
        self.filter(e);
      });
      self.filter();

      // not really possible in pure js :  this.field.closest('form').off('keyup keypress');

    },

    filter: function (e) {
      // start search on full tree when pressing enter (key code 13)
      if (e !== undefined) {
        var code = e.keyCode || e.which;
        if (code == 13) {
          document.getElementById("combinedsearchform").submit();
          return false;
        }
      }
      if (this.field.value.trim() == '') {

        this.list.forEach((elem) => {
          elem.querySelectorAll(this.line).forEach((line) => {
            line.style.display = "block";
            line.parentElement.style.display = "block";
          });
          elem.querySelectorAll(this.line + ' .subitem').forEach((line) => {
            line.style.display = "block";
          });
          elem.querySelectorAll('.ilPDBlockSubHeader').forEach((line) => {
            line.style.display = "block";
          });
        });
        this.field.parentElement.querySelector('#searchInTree').setAttribute("disabled",
          "disabled");
        this.field.focus();
      } else {
        if (this.is_active) {
          this.field.parentElement.querySelector('#searchInTree').removeAttribute("disabled");
        } else if (this.is_active == undefined) {

          if (document.getElementById("combinedsearchform").getAttribute("action") != '') {
            this.is_active = true;
            this.field.parentElement.querySelector('#searchInTree').removeAttribute("disabled");
          } else {
            this.is_active = false;
          }
        }
        this.displayResults(this.getScores(this.field.value.toLowerCase()));
      }
    },

    setupCache: function () {

      var self = this;
      this.rows = [];
      this.cache = [];

      this.list.forEach((elem) => {

        elem.querySelectorAll(this.line).forEach((parent) => {

          var children = parent.querySelectorAll('div.il-item-title a');
          var c = [];

          for (var i = 0; i < children.length; i++) {
            c[i] = { txt: (children[i].textContent || children[i].innerText).toLowerCase() };
          }

          self.rows.push(parent);
          var x = parent.querySelector('div.il-item-description');
          var item = {
            //txt : $(this).find('.il_ContainerListItem .il_ContainerItemTitle h4.il_ContainerItemTitle').not('.subitem h4.il_ContainerItemTitle').text().toLowerCase(),
            txt: (x.textContent || x.innerText).toLowerCase(),
            children: c
          };

          self.cache.push(item);

        });
      });

      this.cache_length = this.cache.length;
    },

    displayResults: function (scores) {

      var self = this;
      this.list.forEach((elem) => {
        elem.querySelectorAll(this.line).forEach((line) => {

          if (line.getAttribute("class") != "ilHeader ilContainerBlockHeader") {
            line.style.display = "none";
          }
          ;

          if (line.parentElement.getAttribute("class") != "ilContainerBlock container-fluid form-inline") {
            line.parentElement.style.display = "none";
          }
          ;

        });

      });

      this.list.forEach((elem) => {
        elem.querySelectorAll('.ilPDBlockSubHeader').forEach((hdr) => {
          hdr.style.display = "none";
        });
      });

      scores.forEach((score) => {
        self.rows[score.index].style.display = "block";
        self.rows[score.index].parentElement.style.display = "block";
        var subitems = self.rows[score.index].querySelectorAll('.subitem');
        if (subitems.length) {
          subitems.forEach((subitem) => {
            self.rows[score.index].style.display = "none";
          });
          for (var j = 0; j < score.children.length; j++) {
            subitems[score.children[j].index].style.display = "block";
          }
        }
      });


    },

    getScores: function (term) {
      var scores = [];
      for (var i = 0; i < this.cache_length; i++) {
        var score = this.cache[i].txt.score(term, 1);

        var child_scores = [];
        for (var j = 0; j < this.cache[i].children.length; j++) {
          var child_score = this.cache[i].children[j].txt.score(term, 1);
          if (child_score > 0) {
            child_scores.push({ score: child_score, index: j });
          }
        }

        if (score > 0 || child_scores.length > 0) {
          scores.push({ score: score, index: i, children: child_scores });
          // console.log('term: ' + term + ' index: ' + i + ' score: ' + score + ' number of children: ' + child_scores.length)
        }
      }

      return scores;
    }
  }

  return {
    addLiveUpdate: function (e, list, line) {
      return new liveUpdate(e, list, line);
    }
  };

})();