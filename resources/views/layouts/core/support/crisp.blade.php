<script type="text/javascript">
  $crisp=[];CRISP_WEBSITE_ID="144f48f7-3604-4483-a8e1-107106d86484";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.im/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();
  $crisp.push(["safe", true])
  window.CRISP_READY_TRIGGER = function() {
    if (!$crisp.is("chat:opened") === true) {
      $crisp.push(["do", "chat:hide"])
    }
  };
  @if(\Auth::check() && \Auth::user()->processing)
      $crisp.push(["set", "user:email", "{{ auth()->user()->email }}"]);
      $crisp.push(["set", "user:nickname", "({{ auth()->user()->id }} ) {{ auth()->user()->name }} "]);
  @endif

  document.getElementById('getHelp').onclick = function(e) {
    e.preventDefault();
    $crisp.push(["do", "chat:open"])
    $crisp.push(["do", "chat:show"])
  }
</script>