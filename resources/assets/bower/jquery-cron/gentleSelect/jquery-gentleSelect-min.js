(function(d) {
  var g = {
    minWidth: 100,
    itemWidth: undefined,
    columns: undefined,
    rows: undefined,
    title: undefined,
    prompt: "Make A Selection",
    maxDisplay: 0,
    openSpeed: 400,
    closeSpeed: 400,
    openEffect: "slide",
    closeEffect: "slide",
    hideOnMouseOut: true
  };
  function h(i) {
    if (typeof i == "undefined") {
      return false;
    } else {
      return true;
    }
  }
  function a(j, i) {
    if (h(i.columns) && h(i.rows)) {
      d.error("gentleSelect: You cannot supply both 'rows' and 'columns'");
      return true;
    }
    if (h(i.columns) && !h(i.itemWidth)) {
      d.error(
        "gentleSelect: itemWidth must be supplied if 'columns' is specified"
      );
      return true;
    }
    if (h(i.rows) && !h(i.itemWidth)) {
      d.error(
        "gentleSelect: itemWidth must be supplied if 'rows' is specified"
      );
      return true;
    }
    if (
      !h(i.openSpeed) ||
      (typeof i.openSpeed != "number" &&
        (typeof i.openSpeed == "string" &&
          (i.openSpeed != "slow" && i.openSpeed != "fast")))
    ) {
      d.error('gentleSelect: openSpeed must be an integer or "slow" or "fast"');
      return true;
    }
    if (
      !h(i.closeSpeed) ||
      (typeof i.closeSpeed != "number" &&
        (typeof i.closeSpeed == "string" &&
          (i.closeSpeed != "slow" && i.closeSpeed != "fast")))
    ) {
      d.error(
        'gentleSelect: closeSpeed must be an integer or "slow" or "fast"'
      );
      return true;
    }
    if (
      !h(i.openEffect) ||
      (i.openEffect != "fade" && i.openEffect != "slide")
    ) {
      d.error("gentleSelect: openEffect must be either 'fade' or 'slide'!");
      return true;
    }
    if (
      !h(i.closeEffect) ||
      (i.closeEffect != "fade" && i.closeEffect != "slide")
    ) {
      d.error("gentleSelect: closeEffect must be either 'fade' or 'slide'!");
      return true;
    }
    if (!h(i.hideOnMouseOut) || typeof i.hideOnMouseOut != "boolean") {
      d.error(
        'gentleSelect: hideOnMouseOut must be supplied and either "true" or "false"!'
      );
      return true;
    }
    return false;
  }
  function f(j, i) {
    if (j.attr("multiple")) {
      i.hideOnMouseOut = true;
    }
  }
  function c(j, k) {
    if (j.length < 1) {
      return k.prompt;
    }
    if (k.maxDisplay != 0 && j.length > k.maxDisplay) {
      var i = j.slice(0, k.maxDisplay).map(function() {
        return d(this).text();
      });
      i.push("...");
    } else {
      var i = j.map(function() {
        return d(this).text();
      });
    }
    return i.get().join(", ");
  }
  var b = {
    init: function(w) {
      var l = d.extend({}, g, w);
      if (a(this, l)) {
        return this;
      }
      f(this, l);
      this.hide();
      label_text = c(this.find(":selected"), l);
      var s = d("<span class='gentleselect-label'>" + label_text + "</span>")
        .insertBefore(this)
        .bind("mouseenter.gentleselect", e.labelHoverIn)
        .bind("mouseleave.gentleselect", e.labelHoverOut)
        .bind("click.gentleselect", e.labelClick)
        .data("root", this);
      this.data("label", s).data("options", l);
      var p = d("<ul></ul>");
      this.find("option").each(function() {
        var i = d("<li>" + d(this).text() + "</li>")
          .data("value", d(this).attr("value"))
          .data("name", d(this).text())
          .appendTo(p);
        if (d(this).attr("selected")) {
          i.addClass("selected");
        }
      });
      var q = d("<div class='gentleselect-dialog'></div>")
        .append(p)
        .insertAfter(s)
        .bind("click.gentleselect", e.dialogClick)
        .bind("mouseleave.gentleselect", e.dialogHoverOut)
        .data("label", s)
        .data("root", this);
      this.data("dialog", q);
      if (h(l.columns) || h(l.rows)) {
        p
          .css("float", "left")
          .find("li")
          .width(l.itemWidth)
          .css("float", "left");
        var n = p.find("li:first");
        var k =
          l.itemWidth +
          parseInt(n.css("padding-left")) +
          parseInt(n.css("padding-right"));
        var t = p.find("li").length;
        if (h(l.columns)) {
          var r = parseInt(l.columns);
          var v = Math.ceil(t / r);
        } else {
          var v = parseInt(l.rows);
          var r = Math.ceil(t / v);
        }
        q.width(k * r);
        for (var m = 0; m < v * r - t; m++) {
          d(
            "<li style='float:left' class='gentleselect-dummy'><span>&nbsp;</span></li>"
          ).appendTo(p);
        }
        var j = [];
        var u = 0;
        p.find("li").each(function() {
          if (u < v) {
            j[u] = d(this);
          } else {
            var i = u % v;
            d(this).insertAfter(j[i]);
            j[i] = d(this);
          }
          u++;
        });
      } else {
        if (typeof l.minWidth == "number") {
          q.css("min-width", l.minWidth);
        }
      }
      if (h(l.title)) {
        d("<div class='gentleselect-title'>" + l.title + "</div>").prependTo(q);
      }
      d(document).bind("keyup.gentleselect", e.keyUp);
      return this;
    },
    update: function() {
      var k = this.data("options");
      var i = this.attr("multiple") ? this.val() : [this.val()];
      d("li", this.data("dialog")).each(function() {
        var m = d(this);
        var l = d.inArray(m.data("value"), i) != -1;
        m.toggleClass("selected", l);
      });
      var j = c(this.find(":selected"), k);
      this.data("label").text(j);
      return this;
    }
  };
  var e = {
    labelHoverIn: function() {
      d(this).addClass("gentleselect-label-highlight");
    },
    labelHoverOut: function() {
      d(this).removeClass("gentleselect-label-highlight");
    },
    labelClick: function() {
      var l = d(this);
      var m = l.position();
      var i = l.data("root");
      var k = i.data("options");
      var j = i
        .data("dialog")
        .css("top", m.top + l.height())
        .css("left", m.left + 1);
      if (k.openEffect == "fade") {
        j.fadeIn(k.openSpeed);
      } else {
        j.slideDown(k.openSpeed);
      }
    },
    dialogHoverOut: function() {
      var i = d(this);
      if (i.data("root").data("options").hideOnMouseOut) {
        i.hide();
      }
    },
    dialogClick: function(k) {
      var m = d(k.target);
      var l = d(this);
      var n = l.data("root");
      var i = n.data("options");
      if (!n.attr("multiple")) {
        if (i.closeEffect == "fade") {
          l.fadeOut(i.closeSpeed);
        } else {
          l.slideUp(i.closeSpeed);
        }
      }
      if (m.is("li") && !m.hasClass("gentleselect-dummy")) {
        var p = m.data("value");
        var j = m.data("name");
        var o = l.data("label");
        if (l.data("root").attr("multiple")) {
          m.toggleClass("selected");
          var r = l.find("li.selected");
          o.text(c(r, i));
          var q = r.map(function() {
            return d(this).data("value");
          });
          n.val(q.get()).trigger("change");
        } else {
          l.find("li.selected").removeClass("selected");
          m.addClass("selected");
          o.text(m.data("name"));
          n.val(p).trigger("change");
        }
      }
    },
    keyUp: function(i) {
      if (i.keyCode == 27) {
        d(".gentleselect-dialog").hide();
      }
    }
  };
  d.fn.gentleSelect = function(i) {
    if (b[i]) {
      return b[i].apply(this, Array.prototype.slice.call(arguments, 1));
    } else {
      if (typeof i === "object" || !i) {
        return b.init.apply(this, arguments);
      } else {
        d.error("Method " + i + " does not exist on jQuery.gentleSelect");
      }
    }
  };
})(jQuery);
