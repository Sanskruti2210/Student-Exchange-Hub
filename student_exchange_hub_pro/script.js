$(document).ready(function () {

    // Friendly client-side checks; PHP repeats validation on the server.
    $("#registerForm").on("submit", function (event) {

        var password = $(this).find("[name='password']").val();
        var confirmPassword = $(this).find("[name='confirm_password']").val();
        if (password !== confirmPassword) {

            event.preventDefault();
            alert("The passwords do not match.");
            $(this).find("[name='confirm_password']").focus();

        }

    }
    );

    $("#productForm").on("submit", function (event) {

        var title = $.trim($(this).find("[name='title']").val() || "");
        var description = $.trim($(this).find("[name='description']").val() || "");
        if (!title || !description) {

            event.preventDefault();
            alert("Please add an item title and description.");

        }

    }
    );

    $("#itemType").on("change", function () {

        if ($(this).val() === "Free") {

            $("#price").val("0").prop("readonly", true);

        }
        else {

            $("#price").prop("readonly", false);

        }

    }
    ).trigger("change");

    $("#descriptionCount").each(function () {

        var counter = $(this);
        var textarea = counter.closest("label").find("textarea");
        function updateCount() {

            counter.text((textarea.val() || "").length);
        }

        textarea.on("input", updateCount);
        updateCount();

    }
    );

    $(".delete-form").on("submit", function () {

        return window.confirm("Delete this listing permanently? This action cannot be undone.");

    }
    );

    // Clear search input with Escape; filtering is submitted to the server.
    $("#liveSearch").on("keydown", function (event) {

        if (event.key === "Escape") $(this).val("");

    }
    );

}
);

/* =====================================================================
Unit 4 – JavaScript & jQuery: events, DOM manipulation, validation,
popup boxes, effects/animations, AJAX (jQuery + fetch API)
===================================================================== */
$(function () {

    var csrf = $('meta[name="csrf-token"]').attr('content') || '';

    /* ---------- 1. Theme switcher (jQuery AJAX POST; PHP stores session + cookie) ---------- */
    var $themeMenu = $('#themeMenu');
    $themeMenu.on('click', '.theme-option[name="theme"]', function (e) {

        e.preventDefault();
        var theme = $(this).val();
        document.documentElement.setAttribute('data-theme', theme);
        document.documentElement.setAttribute('data-bs-theme', theme === 'dark' ? 'dark' : 'light');
        $themeMenu.find('.theme-option').attr('aria-checked', 'false');
        $(this).attr('aria-checked', 'true');
        $.ajax({
            url: 'set_theme.php', method: 'POST', headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
            ,
            data: {
                theme: theme, csrf_token: csrf
            }

        }
        );

    }
    );

    /* ---------- 2. Cookie consent: banner + preferences dialog (like most websites) ---------- */
    function saveConsent(payload) {

        payload.csrf_token = csrf;
        $.ajax({
            url: 'set_consent.php', method: 'POST', headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
            , data: payload
        }
        )
        .done(function (res) {

            $('#cookieBanner').addClass('hide');
            $('#cookieFab').removeClass('hide');
            var modalEl = document.getElementById('cookieModal');
            if (window.bootstrap && modalEl) {

                var m = bootstrap.Modal.getInstance(modalEl); if (m) m.hide();
            }

            var v = res && res.value ? res.value : '';
            $('#ckPref').prop('checked', v === 'all' || v.indexOf('pref') > -1);
            $('#ckStats').prop('checked', v === 'all' || v.indexOf('stats') > -1);
            var t = document.getElementById('cookieToast');
            if (window.bootstrap && t) bootstrap.Toast.getOrCreateInstance(t, {
                delay: 2500
            }
            ).show();

        }
        )
        .fail(function () {

            alert('Could not save your cookie choice. Please try again.');
        }
        );

    }

    $('#cookieBanner').on('click', '[data-consent]', function () {

        saveConsent({
            choice: $(this).data('consent')
        }
        );
    }
    );
    $('#cookieForm').on('click', '[data-consent]', function (e) {

        e.preventDefault();
        var choice = $(this).data('consent'), payload = {
            choice: choice
        }
        ;
        if (choice === 'custom') {

            payload.pref = $('#ckPref').is(':checked') ? 1 : 0; payload.stats = $('#ckStats').is(':checked') ? 1 : 0;
        }

        saveConsent(payload);

    }
    ).on('submit', function (e) {

        e.preventDefault();
    }
    );

    /* ---------- 3. Session countdown (server sends seconds left; every page load resets it) ---------- */
    var $sc = $('#sessionCard');
    if ($sc.length) {

        var left = parseInt($sc.data('remaining'), 10) || 0;
        var paint = function () {

            var m = Math.floor(left / 60), s = left % 60;
            $('#sessionTimer').text((m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s);
            if (left === 0) {

                $sc.find('strong').text('Session expired');
            }

        }
        ;
        paint();
        setInterval(function () {

            if (left > 0) {

                left--; paint();
            }

        }
        , 1000);

    }

    /* ---------- 4. Bootstrap carousel: automatic sliding, never pauses ---------- */
    var carEl = document.getElementById('heroCarousel');
    if (carEl && window.bootstrap) {

        new bootstrap.Carousel(carEl, {
            interval: 4500, ride: 'carousel', pause: false, wrap: true, touch: true
        }
        );

    }

    /* ---------- 5. Real-time counters: fetch() API polled every 20 seconds ---------- */
    var $stats = $('[data-stat]');
    if ($stats.length && window.fetch) {

        var refreshStats = function () {

            fetch('api_stats.php', {
                cache: 'no-store'
            }
            )
            .then(function (r) {

                return r.json();
            }
            )
            .then(function (data) {

                $stats.each(function () {

                    var key = $(this).data('stat'), $el = $(this), val = Number(data[key]);
                    if (!isNaN(val) && $el.text().replace(/,/g, '') !== String(val)) {

                        $el.fadeOut(150, function () {

                            $el.text(val.toLocaleString()).fadeIn(250);
                        }
                        );

                    }

                }
                );

            }
            )
            .catch(function () {

                /* offline: keep the numbers that are already shown */
            }
            );

        }
        ;
        setInterval(refreshStats, 20000);

    }

    /* ---------- 6. Live search suggestions: jQuery AJAX ($.getJSON) -> stored procedure ---------- */
    var $search = $('#liveSearch'), $box = $('#suggestBox'), typingTimer = null;
    if ($search.length && $box.length) {

        $search.attr('autocomplete', 'off').on('input', function () {

            var term = $.trim($(this).val());
            clearTimeout(typingTimer);
            if (term.length < 2) {

                $box.hide().empty(); return;
            }

            typingTimer = setTimeout(function () {

                $.getJSON('api_search.php', {
                    q: term
                }
                , function (rows) {

                    $box.empty();
                    if (!rows.length) {

                        $box.hide(); return;
                    }

                    $.each(rows, function (i, row) {

                        // loop over the array of results
                        $('<a>').attr('href', 'product.php?id=' + row.id)
                        .append($('<span>').text(row.title))
                        .append($('<small>').text(row.category + ' · ' + row.item_type))
                        .appendTo($box);

                    }
                    );
                    $box.slideDown(150);

                }
                );

            }
            , 250);

        }
        );
        $(document).on('click', function (e) {

            if (!$(e.target).closest('.search-field').length) $box.hide();
        }
        );

    }

    /* ---------- 7. FAQ accordion: jQuery effects (slideToggle) ---------- */
    $('.faq-q').on('click', function () {

        $(this).toggleClass('open').attr('aria-expanded', $(this).hasClass('open'))
        .next('.faq-a').slideToggle(250);

    }
    );

    /* ---------- 8. Form validation: objects, functions, regex, conditions, loops ---------- */
    var validators = {

        name:    {
            test: function (v) {

                return /^[A-Za-z][A-Za-z .'-]{1,59}$/.test(v);
            }
            , msg: 'Enter your name (letters only, 2-60 characters).'
        }
        ,
        email:   {
            test: function (v) {

                return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v);
            }
            , msg: 'Enter a valid email address.'
        }
        ,
        phone:   {
            test: function (v) {

                return v === '' || /^[0-9+\-\s]{8,15}$/.test(v);
            }
            , msg: 'Phone must be 8-15 digits (optional).'
        }
        ,
        message: {
            test: function (v) {

                return v.length >= 10 && v.length <= 1000;
            }
            , msg: 'Message must be 10-1000 characters.'
        }

    }
    ;
    $('#contactForm').on('submit', function (event) {

        var problems = [], form = this;
        $(form).find('.field-error').remove();
        $(form).find('.is-bad').removeClass('is-bad');
        $.each(validators, function (field, rule) {

            // loop over validator object
            var $input = $(form).find('[name="' + field + '"]');
            if (!$input.length) return;
            if (!rule.test($.trim($input.val()))) {

                problems.push(rule.msg);
                $input.addClass('is-bad').after('<span class="field-error">' + rule.msg + '</span>');

            }

        }
        );
        if (!$(form).find('[name="agree"]').is(':checked')) {

            problems.push('Please accept the terms.');
        }

        if (problems.length) {

            event.preventDefault();
            alert('Please fix ' + problems.length + ' issue(s):\n- ' + problems.join('\n- '));   // popup box
            $(form).find('.is-bad').first().focus();

        }

    }
    );
    $('#contactForm [name="rating"]').on('input', function () {

        $('#ratingOut').text(this.value + ' / 10');
    }
    );

    /* ---------- 9. Logout confirmation (popup box) ---------- */
    $('form[action="logout.php"]').on('submit', function () {

        return window.confirm('Sign out of Student Exchange Hub?');
    }
    );

    /* ---------- 10. Fade in flash notices ---------- */
    $('.notice').hide().fadeIn(400);

    /* ---------- 11. Back-to-top control: show near the bottom of the page ---------- */
    var $backTop = $('#backToTop');
    if ($backTop.length) {

        var updateBackTop = function () {

            var doc = document.documentElement;
            var remaining = doc.scrollHeight - (window.scrollY + window.innerHeight);
            $backTop.toggleClass('show', remaining < 260);

        }
        ;
        $(window).on('scroll', updateBackTop);
        updateBackTop();
        $backTop.on('click', function () {

            window.scrollTo({
                top: 0, behavior: 'smooth'
            }
            );

        }
        );

    }

    /* ---------- 12. Reveal sections on scroll ---------- */
    var $rev = $('.reveal');
    if ('IntersectionObserver' in window) {

        var io = new IntersectionObserver(function (entries) {

            entries.forEach(function (en) {

                if (en.isIntersecting) {

                    $(en.target).addClass('in'); io.unobserve(en.target);
                }

            }
            );

        }
        , {
            threshold: .12
        }
        );
        $rev.each(function () {

            io.observe(this);
        }
        );

    }
    else {

        $rev.addClass('in');
    }

}
);
