/* Juma Nasoro theme: navigation, reveal, explorer, filters, forms, share. */
(function () {
	'use strict';
	var doc = document.documentElement;
	doc.classList.remove('no-js');

	/* Mobile navigation ------------------------------------------------ */
	var toggle = document.querySelector('.nav-toggle');
	if (toggle) {
		toggle.addEventListener('click', function () {
			var open = document.body.classList.toggle('nav-open');
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && document.body.classList.contains('nav-open')) {
				document.body.classList.remove('nav-open');
				toggle.setAttribute('aria-expanded', 'false');
				toggle.focus();
			}
		});
		document.querySelectorAll('.nav a').forEach(function (a) {
			a.addEventListener('click', function () {
				document.body.classList.remove('nav-open');
				toggle.setAttribute('aria-expanded', 'false');
			});
		});
	}

	/* Header shadow on scroll ------------------------------------------ */
	var header = document.querySelector('.site-header');
	var onScroll = function () {
		if (header) header.classList.toggle('is-scrolled', window.scrollY > 8);
	};
	window.addEventListener('scroll', onScroll, { passive: true });
	onScroll();

	/* Reveal on scroll ------------------------------------------------- */
	if ('IntersectionObserver' in window) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) {
				if (en.isIntersecting) {
					en.target.classList.add('is-in');
					io.unobserve(en.target);
				}
			});
		}, { rootMargin: '0px 0px -8% 0px', threshold: 0.05 });
		document.querySelectorAll('.reveal').forEach(function (el) { io.observe(el); });
	} else {
		doc.classList.add('no-observer');
	}

	/* "What can we automate?" explorer (accessible tabs) ---------------- */
	document.querySelectorAll('[data-explorer]').forEach(function (root) {
		var tabs = Array.prototype.slice.call(root.querySelectorAll('[role="tab"]'));
		var select = function (tab, focus) {
			tabs.forEach(function (t) {
				var on = t === tab;
				t.setAttribute('aria-selected', on ? 'true' : 'false');
				t.tabIndex = on ? 0 : -1;
				var panel = document.getElementById(t.getAttribute('aria-controls'));
				if (panel) panel.hidden = !on;
			});
			if (focus) tab.focus();
		};
		tabs.forEach(function (tab, i) {
			tab.addEventListener('click', function () { select(tab, false); });
			tab.addEventListener('keydown', function (e) {
				var next = null;
				if (e.key === 'ArrowDown' || e.key === 'ArrowRight') next = tabs[(i + 1) % tabs.length];
				if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') next = tabs[(i - 1 + tabs.length) % tabs.length];
				if (e.key === 'Home') next = tabs[0];
				if (e.key === 'End') next = tabs[tabs.length - 1];
				if (next) { e.preventDefault(); select(next, true); }
			});
		});
	});

	/* Portfolio filters ------------------------------------------------- */
	var filters = document.querySelector('[data-filters]');
	var grid = document.querySelector('[data-filter-grid]');
	if (filters && grid) {
		var state = { section: '', status: '', q: '' };
		var active = filters.querySelector('[data-filter-section].is-active');
		state.section = active ? active.getAttribute('data-filter-section') : '';
		var cards = Array.prototype.slice.call(grid.querySelectorAll('.card'));
		var empty = document.querySelector('[data-filter-empty]');
		var apply = function () {
			var shown = 0;
			cards.forEach(function (c) {
				var ok = (!state.section || c.dataset.section === state.section) &&
					(!state.status || c.dataset.status === state.status) &&
					(!state.q || (c.dataset.search || '').indexOf(state.q) !== -1);
				c.classList.toggle('is-hidden', !ok);
				if (ok) { shown++; c.classList.add('is-in'); }
			});
			if (empty) empty.hidden = shown !== 0;
			document.querySelectorAll('[data-section-note]').forEach(function (n) {
				n.hidden = n.getAttribute('data-section-note') !== state.section;
			});
		};
		filters.querySelectorAll('[data-filter-section]').forEach(function (b) {
			b.addEventListener('click', function () {
				filters.querySelectorAll('[data-filter-section]').forEach(function (x) {
					x.classList.remove('is-active');
					x.setAttribute('aria-pressed', 'false');
				});
				b.classList.add('is-active');
				b.setAttribute('aria-pressed', 'true');
				state.section = b.getAttribute('data-filter-section');
				try {
					var u = new URL(window.location.href);
					if (state.section) u.searchParams.set('section', state.section); else u.searchParams.delete('section');
					history.replaceState(null, '', u.toString());
				} catch (e) {}
				apply();
			});
		});
		var st = filters.querySelector('[data-filter-status]');
		if (st) st.addEventListener('change', function () { state.status = st.value; apply(); });
		var q = filters.querySelector('[data-filter-search]');
		if (q) q.addEventListener('input', function () { state.q = q.value.trim().toLowerCase(); apply(); });
		apply();
	}

	/* Forms: validation + submit without page reload --------------------- */
	document.querySelectorAll('.js-form').forEach(function (form) {
		var status = form.querySelector('.form__status');
		var say = function (msg, ok) {
			if (!status) return;
			status.textContent = msg;
			status.className = 'form__status ' + (ok ? 'is-ok' : 'is-error');
		};
		form.addEventListener('submit', function (e) {
			// Client-side validation.
			var firstBad = null;
			form.querySelectorAll('[required]').forEach(function (f) {
				var bad = !f.value.trim() || (f.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(f.value.trim())) ||
					(f.minLength > 0 && f.value.trim().length < f.minLength);
				f.classList.toggle('is-invalid', bad);
				f.setAttribute('aria-invalid', bad ? 'true' : 'false');
				if (bad && !firstBad) firstBad = f;
			});
			if (firstBad) {
				e.preventDefault();
				say('Please check the highlighted fields.', false);
				firstBad.focus();
				return;
			}
			if (!window.fetch || !window.FormData) return; // Fall back to a normal post.
			e.preventDefault();
			var data = new FormData(form);
			data.append('jn_ajax', '1');
			form.classList.add('is-sending');
			say('Sending…', true);
			fetch(form.getAttribute('action'), { method: 'POST', body: data, credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
				.then(function (r) { return r.json().catch(function () { return { ok: false, message: 'Unexpected response. Please try again.' }; }); })
				.then(function (res) {
					form.classList.remove('is-sending');
					say(res.message || (res.ok ? 'Thank you.' : 'Something went wrong.'), !!res.ok);
					if (res.ok) {
						if (res.download) {
							var a = document.createElement('a');
							a.href = res.download;
							a.className = 'btn btn--primary';
							a.setAttribute('download', '');
							a.textContent = 'Download now';
							status.appendChild(document.createElement('br'));
							status.appendChild(a);
							form.querySelectorAll('.form__row, .check, button[type="submit"]').forEach(function (x) { x.style.display = 'none'; });
						} else if (form.dataset.kind === 'enquiry') {
							form.classList.add('is-done');
						} else {
							form.reset();
						}
					}
				})
				.catch(function () {
					form.classList.remove('is-sending');
					say('Your message could not be sent. Please check your connection and try again.', false);
				});
		});
	});

	/* Copy link ---------------------------------------------------------- */
	document.querySelectorAll('.share__copy').forEach(function (b) {
		b.addEventListener('click', function () {
			var url = b.getAttribute('data-copy');
			var done = function () { var t = b.textContent; b.textContent = 'Copied'; setTimeout(function () { b.textContent = t; }, 1600); };
			if (navigator.clipboard) navigator.clipboard.writeText(url).then(done, done); else done();
		});
	});
})();
