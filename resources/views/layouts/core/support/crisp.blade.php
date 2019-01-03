<!-- Start of Async Drift Code -->
<script>
    "use strict";

    !function() {
        var t = window.driftt = window.drift = window.driftt || [];
        if (!t.init) {
            if (t.invoked) return void (window.console && console.error && console.error("Drift snippet included twice."));
            t.invoked = !0, t.methods = [ "identify", "config", "track", "reset", "debug", "show", "ping", "page", "hide", "off", "on" ],
                t.factory = function(e) {
                    return function() {
                        var n = Array.prototype.slice.call(arguments);
                        return n.unshift(e), t.push(n), t;
                    };
                }, t.methods.forEach(function(e) {
                t[e] = t.factory(e);
            }), t.load = function(t) {
                var e = 3e5, n = Math.ceil(new Date() / e) * e, o = document.createElement("script");
                o.type = "text/javascript", o.async = !0, o.crossorigin = "anonymous", o.src = "https://js.driftt.com/include/" + n + "/" + t + ".js";
                var i = document.getElementsByTagName("script")[0];
                i.parentNode.insertBefore(o, i);
            };
        }
    }();
    drift.SNIPPET_VERSION = '0.3.1';
    drift.load('c9yvcydd63sz');

    @if(\Auth::check() && \Auth::user()->processing)
        drift.identify(userId, {
            email: '{{ auth()->user()->email }}',
            user_id: '{{ auth()->user()->id }}',
        });
    @endif

    drift.on('ready',function(api) {
        api.widget.hide()

        drift.on('message',function(e){
            api.widget.show()
        });
    })

    document.getElementById('getHelp').onclick = function(e) {
        e.preventDefault();
        api.widget.show()
    }
</script>
<!-- End of Async Drift Code -->