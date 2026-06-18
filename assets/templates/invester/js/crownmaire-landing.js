(function () {
  'use strict';

  var header = document.getElementById('cmHeader');
  var toggle = document.getElementById('cmNavToggle');
  var menu = document.getElementById('cmNavMenu');
  var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var scrollTicking = false;
  var navOverlay = document.createElement('div');
  navOverlay.className = 'cm-nav-overlay';
  navOverlay.setAttribute('aria-hidden', 'true');
  document.body.appendChild(navOverlay);

  function setNavOpen(open) {
    if (!menu || !toggle) {
      return;
    }

    menu.classList.toggle('is-open', open);
    navOverlay.classList.toggle('is-visible', open);
    document.body.classList.toggle('cm-nav-open', open);
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  }

  function closeNav() {
    setNavOpen(false);
  }

  function onScroll() {
    if (!header) {
      return;
    }

    if (scrollTicking) {
      return;
    }

    scrollTicking = true;
    requestAnimationFrame(function () {
      header.classList.toggle('is-scrolled', window.scrollY > 24);
      scrollTicking = false;
    });
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  if (toggle && menu) {
    toggle.addEventListener('click', function () {
      setNavOpen(!menu.classList.contains('is-open'));
    });

    navOverlay.addEventListener('click', closeNav);

    menu.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', closeNav);
    });

    window.addEventListener('resize', function () {
      if (window.innerWidth > 991) {
        closeNav();
      }
    });
  }

  document.querySelectorAll('a[href*="#"]').forEach(function (link) {
    link.addEventListener('click', function (event) {
      var href = link.getAttribute('href');

      if (!href || href === '#') {
        return;
      }

      var url;

      try {
        url = new URL(link.href, window.location.href);
      } catch (error) {
        return;
      }

      if (url.pathname !== window.location.pathname || !url.hash || url.hash.length < 2) {
        return;
      }

      var target = document.querySelector(url.hash);

      if (!target) {
        return;
      }

      event.preventDefault();

      var offset = header ? header.offsetHeight + 12 : 0;
      var top = target.getBoundingClientRect().top + window.scrollY - offset;

      window.scrollTo({
        top: top,
        behavior: prefersReducedMotion ? 'auto' : 'smooth'
      });

      history.replaceState(null, '', url.hash);
    });
  });

  if (window.location.hash) {
    window.requestAnimationFrame(function () {
      var target = document.querySelector(window.location.hash);

      if (!target) {
        return;
      }

      var offset = header ? header.offsetHeight + 12 : 0;
      var top = target.getBoundingClientRect().top + window.scrollY - offset;

      window.scrollTo({
        top: top,
        behavior: prefersReducedMotion ? 'auto' : 'smooth'
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
      { threshold: 0.08, rootMargin: '0px 0px -24px 0px' }
    );

    reveals.forEach(function (el) {
      observer.observe(el);
    });
  } else {
    reveals.forEach(function (el) {
      el.classList.add('is-visible');
    });
  }

  var counters = document.querySelectorAll('[data-count]');

  if (counters.length && 'IntersectionObserver' in window && !prefersReducedMotion) {
    var countObserver = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) {
            return;
          }

          var el = entry.target;
          var target = parseInt(el.getAttribute('data-count'), 10);
          var suffix = el.getAttribute('data-suffix') || '';
          var span = el.querySelector('span');
          var duration = 1400;
          var startTime = null;

          function step(timestamp) {
            if (!startTime) {
              startTime = timestamp;
            }

            var progress = Math.min((timestamp - startTime) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            var value = Math.floor(eased * target);

            if (span) {
              span.textContent = value;
            } else if (suffix === '%') {
              el.textContent = value + suffix;
            } else if (suffix === '+') {
              el.textContent = value + suffix;
            } else if (el.textContent.indexOf('$') === 0) {
              el.innerHTML = '$<span>' + value + '</span>M';
            } else {
              el.textContent = value + suffix;
            }

            if (progress < 1) {
              requestAnimationFrame(step);
            } else if (span) {
              span.textContent = target;
            } else if (suffix) {
              el.textContent = target + suffix;
            }
          }

          requestAnimationFrame(step);
          countObserver.unobserve(el);
        });
      },
      { threshold: 0.35 }
    );

    counters.forEach(function (counter) {
      countObserver.observe(counter);
    });
  } else if (counters.length) {
    counters.forEach(function (el) {
      var target = el.getAttribute('data-count');
      var suffix = el.getAttribute('data-suffix') || '';
      var span = el.querySelector('span');

      if (span) {
        span.textContent = target;
      } else {
        el.textContent = target + suffix;
      }
    });
  }

  var heroStage = document.getElementById('cmHeroStage');
  var hero = document.getElementById('cmHero');

  if (heroStage && hero && !prefersReducedMotion && window.matchMedia('(min-width: 768px) and (hover: hover)').matches) {
    var parallaxTicking = false;
    var lastX = 0;
    var lastY = 0;

    hero.addEventListener('mousemove', function (event) {
      lastX = event.clientX;
      lastY = event.clientY;

      if (parallaxTicking) {
        return;
      }

      parallaxTicking = true;
      requestAnimationFrame(function () {
        var rect = hero.getBoundingClientRect();
        var x = (lastX - rect.left) / rect.width - 0.5;
        var y = (lastY - rect.top) / rect.height - 0.5;
        heroStage.style.transform = 'translate3d(' + (x * 6) + 'px, ' + (y * 4) + 'px, 0)';
        parallaxTicking = false;
      });
    }, { passive: true });

    hero.addEventListener('mouseleave', function () {
      heroStage.style.transform = '';
    });
  }
})();
