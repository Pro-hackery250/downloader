<?php include __DIR__ . '/index.php'; ?>
<script>
window.addEventListener('load', function () {
  setTimeout(function () {
    var out = [];
    document.querySelectorAll('main > section, .site-footer').forEach(function (s) {
      var cs = getComputedStyle(s);
      var rect = s.getBoundingClientRect();
      out.push({
        cls: s.className,
        id: s.id,
        top: Math.round(rect.top + window.scrollY),
        height: Math.round(rect.height),
        padTop: cs.paddingTop,
        padBottom: cs.paddingBottom,
        firstChildTop: s.firstElementChild ? Math.round(s.firstElementChild.getBoundingClientRect().top + window.scrollY) : null,
        firstChildTag: s.firstElementChild ? s.firstElementChild.className : null
      });
    });
    var pre = document.createElement('pre');
    pre.id = '__measure';
    pre.textContent = JSON.stringify({
      width: window.innerWidth,
      pageHeight: document.documentElement.scrollHeight,
      sections: out
    });
    document.body.appendChild(pre);
  }, 800);
});
</script>
