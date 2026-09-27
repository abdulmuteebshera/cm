@if(!empty($seoViewId))
<script>
(function () {
    var viewId = {{ (int) $seoViewId }};
    var endpoint = @json(route('seo.beacon'));
    var started = Date.now();
    var maxScroll = 0;
    var lastSent = 0;

    function duration() {
        return Math.max(0, Math.round((Date.now() - started) / 1000));
    }

    function scrollDepth() {
        var doc = document.documentElement;
        var body = document.body;
        var height = Math.max(doc.scrollHeight, body ? body.scrollHeight : 0) - window.innerHeight;
        if (height <= 0) return 100;
        return Math.min(100, Math.round((window.scrollY / height) * 100));
    }

    function send(payload) {
        payload.view_id = viewId;
        payload.duration = duration();
        payload.scroll_depth = Math.max(maxScroll, scrollDepth());
        payload.path = location.pathname;
        try {
            var body = JSON.stringify(payload);
            if (navigator.sendBeacon) {
                navigator.sendBeacon(endpoint, new Blob([body], { type: 'application/json' }));
            } else {
                fetch(endpoint, { method: 'POST', body: body, headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, keepalive: true, credentials: 'same-origin' });
            }
        } catch (e) {}
    }

    window.addEventListener('scroll', function () {
        maxScroll = Math.max(maxScroll, scrollDepth());
    }, { passive: true });

    document.addEventListener('click', function (e) {
        var a = e.target.closest('a, button');
        if (!a) return;
        var href = a.getAttribute('href') || '';
        var label = (a.getAttribute('aria-label') || a.innerText || '').replace(/\s+/g, ' ').trim().slice(0, 160);
        send({
            action: 'click',
            href: href,
            label: label,
            text: label,
            tag: (a.tagName || '').toLowerCase(),
            x: e.clientX || 0,
            y: e.clientY || 0
        });
    }, true);

    setInterval(function () {
        if (document.visibilityState === 'hidden') return;
        if (duration() - lastSent < 15) return;
        lastSent = duration();
        send({ action: 'heartbeat' });
    }, 15000);

    function leave() {
        send({ action: 'leave' });
    }
    window.addEventListener('pagehide', leave);
    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'hidden') leave();
    });
})();
</script>
@endif
