(function(e) {
  var n = {
    initial: "* * * * *",
    minuteOpts: {
      minWidth: 100,
      itemWidth: 30,
      columns: 4,
      rows: undefined,
      title: "Minutes Past the Hour"
    },
    timeHourOpts: {
      minWidth: 100,
      itemWidth: 20,
      columns: 2,
      rows: undefined,
      title: "Time: Hour"
    },
    domOpts: {
      minWidth: 100,
      itemWidth: 30,
      columns: undefined,
      rows: 10,
      title: "Day of Month"
    },
    monthOpts: {
      minWidth: 100,
      itemWidth: 100,
      columns: 2,
      rows: undefined,
      title: undefined
    },
    dowOpts: {
      minWidth: 100,
      itemWidth: undefined,
      columns: undefined,
      rows: undefined,
      title: undefined
    },
    timeMinuteOpts: {
      minWidth: 100,
      itemWidth: 20,
      columns: 4,
      rows: undefined,
      title: "Time: Minute"
    },
    effectOpts: {
      openSpeed: 400,
      closeSpeed: 400,
      openEffect: "slide",
      closeEffect: "slide",
      hideOnMouseOut: true
    },
    url_set: undefined,
    customValues: undefined,
    onChange: undefined,
    useGentleSelect: false
  };
  var y = "";
  for (var u = 0; u < 60; u++) {
    var t = u < 10 ? "0" : "";
    y += "<option value='" + u + "'>" + t + u + "</option>\n";
  }
  var d = "";
  for (var u = 0; u < 24; u++) {
    var t = u < 10 ? "0" : "";
    d += "<option value='" + u + "'>" + t + u + "</option>\n";
  }
  var v = "";
  for (var u = 1; u < 32; u++) {
    if (u == 1 || u == 21 || u == 31) {
      var c = "st";
    } else {
      if (u == 2 || u == 22) {
        var c = "nd";
      } else {
        if (u == 3 || u == 23) {
          var c = "rd";
        } else {
          var c = "th";
        }
      }
    }
    v += "<option value='" + u + "'>" + u + c + "</option>\n";
  }
  var h = "";
  var l = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December"
  ];
  for (var u = 0; u < l.length; u++) {
    h += "<option value='" + (u + 1) + "'>" + l[u] + "</option>\n";
  }
  var s = "";
  var g = [
    "Sunday",
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday"
  ];
  for (var u = 0; u < g.length; u++) {
    s += "<option value='" + u + "'>" + g[u] + "</option>\n";
  }
  var r = "";
  var b = ["minute", "hour", "day", "week", "month", "year"];
  for (var u = 0; u < b.length; u++) {
    r += "<option value='" + b[u] + "'>" + b[u] + "</option>\n";
  }
  var p = {
    minute: [],
    hour: ["mins"],
    day: ["time"],
    week: ["dow", "time"],
    month: ["dom", "time"],
    year: ["dom", "month", "time"]
  };
  var w = {
    minute: /^(\*\s){4}\*$/,
    hour: /^\d{1,2}\s(\*\s){3}\*$/,
    day: /^(\d{1,2}\s){2}(\*\s){2}\*$/,
    week: /^(\d{1,2}\s){2}(\*\s){2}\d{1,2}$/,
    month: /^(\d{1,2}\s){3}\*\s\*$/,
    year: /^(\d{1,2}\s){4}\*$/
  };
  function a(i) {
    if (typeof i == "undefined") {
      return false;
    } else {
      return true;
    }
  }
  function q(i) {
    return !a(i) || typeof i == "object";
  }
  function z(G) {
    var E = /^((\d{1,2}|\*)\s){4}(\d{1,2}|\*)$/;
    if (typeof G != "string" || !E.test(G)) {
      e.error("cron: invalid initial value");
      return undefined;
    }
    var F = G.split(" ");
    var D = [0, 0, 1, 1, 0];
    var A = [59, 23, 31, 12, 6];
    for (var C = 0; C < F.length; C++) {
      if (F[C] == "*") {
        continue;
      }
      var j = parseInt(F[C]);
      if (a(j) && j <= A[C] && j >= D[C]) {
        continue;
      }
      e.error(
        "cron: invalid value found (col " + (C + 1) + ") in " + o.initial
      );
      return undefined;
    }
    for (var B in w) {
      if (w[B].test(G)) {
        return B;
      }
    }
    e.error("cron: valid but unsupported cron format. sorry.");
    return undefined;
  }
  function f(j, i) {
    if (!a(z(i.initial))) {
      return true;
    }
    if (!q(i.customValues)) {
      return true;
    }
    return false;
  }
  function k(B) {
    var i = B.data("block");
    var j = (hour = day = month = dow = "*");
    var A = i.period.find("select").val();
    switch (A) {
      case "minute":
        break;
      case "hour":
        j = i.mins.find("select").val();
        break;
      case "day":
        j = i.time.find("select.cron-time-min").val();
        hour = i.time.find("select.cron-time-hour").val();
        break;
      case "week":
        j = i.time.find("select.cron-time-min").val();
        hour = i.time.find("select.cron-time-hour").val();
        dow = i.dow.find("select").val();
        break;
      case "month":
        j = i.time.find("select.cron-time-min").val();
        hour = i.time.find("select.cron-time-hour").val();
        day = i.dom.find("select").val();
        break;
      case "year":
        j = i.time.find("select.cron-time-min").val();
        hour = i.time.find("select.cron-time-hour").val();
        day = i.dom.find("select").val();
        month = i.month.find("select").val();
        break;
      default:
        return A;
    }
    return [j, hour, day, month, dow].join(" ");
  }
  var x = {
    init: function(i) {
      var G = i ? i : {};
      var B = e.extend([], n, G);
      var j = e.extend({}, n.effectOpts, G.effectOpts);
      e.extend(B, {
        minuteOpts: e.extend({}, n.minuteOpts, j, G.minuteOpts),
        domOpts: e.extend({}, n.domOpts, j, G.domOpts),
        monthOpts: e.extend({}, n.monthOpts, j, G.monthOpts),
        dowOpts: e.extend({}, n.dowOpts, j, G.dowOpts),
        timeHourOpts: e.extend({}, n.timeHourOpts, j, G.timeHourOpts),
        timeMinuteOpts: e.extend({}, n.timeMinuteOpts, j, G.timeMinuteOpts)
      });
      if (f(this, B)) {
        return this;
      }
      var C = [],
        A = "",
        D = B.customValues;
      if (a(D)) {
        for (var F in D) {
          A += "<option value='" + D[F] + "'>" + F + "</option>\n";
        }
      }
      C.period = e(
        "<span class='cron-period'>Every <select name='cron-period'>" +
          A +
          r +
          "</select> </span>"
      )
        .appendTo(this)
        .data("root", this);
      var E = C.period.find("select");
      E.bind("change.cron", m.periodChanged).data("root", this);
      if (B.useGentleSelect) {
        E.gentleSelect(j);
      }
      C.dom = e(
        "<span class='cron-block cron-block-dom'> on the <select name='cron-dom'>" +
          v +
          "</select> </span>"
      )
        .appendTo(this)
        .data("root", this);
      E = C.dom.find("select").data("root", this);
      if (B.useGentleSelect) {
        E.gentleSelect(B.domOpts);
      }
      C.month = e(
        "<span class='cron-block cron-block-month'> of <select name='cron-month'>" +
          h +
          "</select> </span>"
      )
        .appendTo(this)
        .data("root", this);
      E = C.month.find("select").data("root", this);
      if (B.useGentleSelect) {
        E.gentleSelect(B.monthOpts);
      }
      C.mins = e(
        "<span class='cron-block cron-block-mins'> at <select name='cron-mins'>" +
          y +
          "</select> minutes past the hour </span>"
      )
        .appendTo(this)
        .data("root", this);
      E = C.mins.find("select").data("root", this);
      if (B.useGentleSelect) {
        E.gentleSelect(B.minuteOpts);
      }
      C.dow = e(
        "<span class='cron-block cron-block-dow'> on <select name='cron-dow'>" +
          s +
          "</select> </span>"
      )
        .appendTo(this)
        .data("root", this);
      E = C.dow.find("select").data("root", this);
      if (B.useGentleSelect) {
        E.gentleSelect(B.dowOpts);
      }
      C.time = e(
        "<span class='cron-block cron-block-time'> at <select name='cron-time-hour' class='cron-time-hour'>" +
          d +
          "</select>:<select name='cron-time-min' class='cron-time-min'>" +
          y +
          " </span>"
      )
        .appendTo(this)
        .data("root", this);
      E = C.time.find("select.cron-time-hour").data("root", this);
      if (B.useGentleSelect) {
        E.gentleSelect(B.timeHourOpts);
      }
      E = C.time.find("select.cron-time-min").data("root", this);
      if (B.useGentleSelect) {
        E.gentleSelect(B.timeMinuteOpts);
      }
      C.controls = e(
        "<span class='cron-controls'>&laquo; save <span class='cron-button cron-button-save'></span> </span>"
      )
        .appendTo(this)
        .data("root", this)
        .find("span.cron-button-save")
        .bind("click.cron", m.saveClicked)
        .data("root", this)
        .end();
      this.find("select").bind("change.cron-callback", m.somethingChanged);
      this.data("options", B).data("block", C);
      this.data("current_value", B.initial);
      return x.value.call(this, B.initial);
    },
    value: function(B) {
      if (!B) {
        return k(this);
      }
      var J = z(B);
      if (!a(J)) {
        return false;
      }
      var C = this.data("block");
      var G = B.split(" ");
      var I = { mins: G[0], hour: G[1], dom: G[2], month: G[3], dow: G[4] };
      var E = this.data("options").useGentleSelect;
      var F = p[J];
      for (var D = 0; D < F.length; D++) {
        var A = F[D];
        if (A == "time") {
          var H = C[A].find("select.cron-time-hour").val(I.hour);
          if (E) {
            H.gentleSelect("update");
          }
          H = C[A].find("select.cron-time-min").val(I.mins);
          if (E) {
            H.gentleSelect("update");
          }
        } else {
          var H = C[A].find("select").val(I[A]);
          if (E) {
            H.gentleSelect("update");
          }
        }
      }
      var j = C.period.find("select").val(J);
      if (E) {
        j.gentleSelect("update");
      }
      j.trigger("change");
      return this;
    }
  };
  var m = {
    periodChanged: function() {
      var A = e(this).data("root");
      var E = A.data("block"),
        C = A.data("options");
      var D = e(this).val();
      A.find("span.cron-block").hide();
      if (p.hasOwnProperty(D)) {
        var j = p[e(this).val()];
        for (var B = 0; B < j.length; B++) {
          E[j[B]].show();
        }
      }
    },
    somethingChanged: function() {
      root = e(this).data("root");
      if (a(root.data("options").url_set)) {
        if (x.value.call(root) != root.data("current_value")) {
          root.addClass("cron-changed");
          root.data("block")["controls"].fadeIn();
        } else {
          root.removeClass("cron-changed");
          root.data("block")["controls"].fadeOut();
        }
      } else {
        root.data("block")["controls"].hide();
      }
      var i = root.data("options").onChange;
      if (a(i) && e.isFunction(i)) {
        i.call(root);
      }
    },
    saveClicked: function() {
      var j = e(this);
      var i = j.data("root");
      var A = x.value.call(i);
      if (j.hasClass("cron-loading")) {
        return;
      }
      j.addClass("cron-loading");
      e.ajax({
        type: "POST",
        url: i.data("options").url_set,
        data: { cron: A },
        success: function() {
          i.data("current_value", A);
          j.removeClass("cron-loading");
          if (A == x.value.call(i)) {
            i.removeClass("cron-changed");
            i.data("block").controls.fadeOut();
          }
        },
        error: function() {
          alert("An error occured when submitting your request. Try again?");
          j.removeClass("cron-loading");
        }
      });
    }
  };
  e.fn.cron = function(i) {
    if (x[i]) {
      return x[i].apply(this, Array.prototype.slice.call(arguments, 1));
    } else {
      if (typeof i === "object" || !i) {
        return x.init.apply(this, arguments);
      } else {
        e.error("Method " + i + " does not exist on jQuery.cron");
      }
    }
  };
})(jQuery);
