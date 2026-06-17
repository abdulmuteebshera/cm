(function () {
  'use strict';

  var header = document.getElementById('cmHeader');
  var toggle = document.getElementById('cmNavToggle');
  var menu = document.getElementById('cmNavMenu');

  function onScroll() {
    if (!header) return;
    header.classList.toggle('is-scrolled', window.scrollY > 24);
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  if (toggle && menu) {
    toggle.addEventListener('click', function () {
      var open = menu.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    menu.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        menu.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
      });
    });
  }

  var reveals = document.querySelectorAll('.cm-reveal');
  if (reveals.length && 'IntersectionObserver' in window) {
    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.1, rootMargin: '0px 0px -32px 0px' }
    );
    reveals.forEach(function (el) { observer.observe(el); });
  } else {
    reveals.forEach(function (el) { el.classList.add('is-visible'); });
  }

  var counters = document.querySelectorAll('[data-count]');
  if (counters.length && 'IntersectionObserver' in window) {
    var countObserver = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          var el = entry.target;
          var target = parseInt(el.getAttribute('data-count'), 10);
          var suffix = el.getAttribute('data-suffix') || '';
          var span = el.querySelector('span');
          var duration = 1800;
          var startTime = null;

          function step(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            var value = Math.floor(eased * target);
            if (span) span.textContent = value;
            else if (suffix === '%') el.textContent = value + suffix;
            else if (suffix === '+') el.textContent = value + suffix;
            else if (el.textContent.indexOf('$') === 0) el.innerHTML = '$<span>' + value + '</span>M';
            else el.textContent = value + suffix;
            if (progress < 1) requestAnimationFrame(step);
            else {
              if (span) span.textContent = target;
              else if (suffix) el.textContent = target + suffix;
            }
          }

          requestAnimationFrame(step);
          countObserver.unobserve(el);
        });
      },
      { threshold: 0.35 }
    );
    counters.forEach(function (c) { countObserver.observe(c); });
  }

  var heroStage = document.getElementById('cmHeroStage');
  var hero = document.getElementById('cmHero');
  if (heroStage && hero && window.matchMedia('(min-width: 768px)').matches) {
    hero.addEventListener('mousemove', function (e) {
      var rect = hero.getBoundingClientRect();
      var x = (e.clientX - rect.left) / rect.width - 0.5;
      var y = (e.clientY - rect.top) / rect.height - 0.5;
      heroStage.style.transform = 'translate(' + (x * 8) + 'px, ' + (y * 6) + 'px)';
    });

    hero.addEventListener('mouseleave', function () {
      heroStage.style.transform = '';
    });
  }
})();
