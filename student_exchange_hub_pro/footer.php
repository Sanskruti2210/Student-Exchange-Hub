<footer class="footer">
<div class="footer-inner">
<div><strong>Student Exchange Hub Pro</strong>
<p>A campus-first marketplace for useful second-hand finds.</p>
</div>
<div class="footer-links"><a href="#" id="cookieSettings" data-bs-toggle="modal" data-bs-target="#cookieModal">Cookie settings</a><a href="browse.php">Browse items</a><a href="how_it_works.php">Safety & how it works</a><a href="about.php">About & contact</a><a href="sitemap.php">Sitemap</a><a href="mailto:support@example.com">Contact site support</a>
</div>
<small>© <?= date('Y') ?> Student Exchange Hub Pro. Meet locally and exchange responsibly.</small>
</div>
</footer>

<!-- Back-to-top control: appears when the visitor reaches the bottom of the page. -->
<button class="back-to-top" id="backToTop" type="button" aria-label="Back to top" title="Back to top">^</button>
<?php $cc = consent_categories(); ?>
<!-- Cookie banner (shown until the visitor makes a choice) -->
<div class="cookie-banner<?= consent_chosen() ? ' hide' : '' ?>" id="cookieBanner" role="dialog" aria-label="Cookie consent">
<div class="cookie-text"><strong>We value your privacy</strong>We use essential cookies to keep you signed in and optional cookies to remember your theme and last visit. You can accept all, reject the optional ones, or choose.</div>
<div class="cookie-actions">
<button class="button button-light button-small" type="button" data-consent="essential">Reject optional</button>
<button class="button button-light button-small" type="button" data-bs-toggle="modal" data-bs-target="#cookieModal">Customize</button>
<button class="button button-small" type="button" data-consent="all">Accept all</button>
</div>
</div>

<!-- Floating cookie button (reopens preferences, like most websites) -->
<button class="cookie-fab<?= consent_chosen() ? '' : ' hide' ?>" id="cookieFab" type="button" data-bs-toggle="modal" data-bs-target="#cookieModal" aria-label="Cookie preferences" title="Cookie preferences"><svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.5A9 9 0 1 1 11.5 3a4 4 0 0 0 4.5 4.5 3.5 3.5 0 0 0 5 5z"/><path d="M8.5 10v.01M12 15v.01M15.5 13v.01M8 14.5v.01"/></svg>
</button>

<!-- Preferences dialog (Bootstrap modal) -->
<div class="modal fade" id="cookieModal" tabindex="-1" aria-labelledby="cookieModalTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
<form class="modal-content cookie-modal" id="cookieForm" action="set_consent.php" method="post">
<input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
<input type="hidden" name="return" value="<?= h(basename($_SERVER['PHP_SELF'] ?? 'index.php')) ?>">
<input type="hidden" name="choice" value="custom">
<div class="modal-header">
<h2 class="modal-title" id="cookieModalTitle">Cookie preferences</h2>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
</button>
</div>
<div class="modal-body">
<p class="muted">Choose which cookies you allow. Your choice is saved for one year.</p>
<div class="cookie-row">
<div><strong>Essential</strong><small>Keeps you signed in and protects forms (session cookie). Always on.</small>
</div>
<div class="form-check form-switch"><input class="form-check-input" type="checkbox" checked disabled>
</div>
</div>
<div class="cookie-row">
<div><strong>Preferences</strong><small>Remembers your colour theme and your email on the sign-in page.</small>
</div>
<div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="pref" value="1" id="ckPref" <?= in_array('pref', $cc, true) ? 'checked' : '' ?>>
</div>
</div>
<div class="cookie-row">
<div><strong>Statistics</strong><small>Remembers your last visit to show a “welcome back” message.</small>
</div>
<div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="stats" value="1" id="ckStats" <?= in_array('stats', $cc, true) ? 'checked' : '' ?>>
</div>
</div>
</div>
<div class="modal-footer">
<button type="submit" class="button button-light button-small" name="choice" value="essential" data-consent="essential">Reject optional</button>
<button type="submit" class="button button-light button-small" name="choice" value="custom" data-consent="custom">Save my choices</button>
<button type="submit" class="button button-small" name="choice" value="all" data-consent="all">Accept all</button>
</div>
</form>
</div>
</div>
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:1200">
<div id="cookieToast" class="toast align-items-center" role="status" aria-live="polite" aria-atomic="true">
<div class="d-flex">
<div class="toast-body"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg> Cookie preferences saved.</div>
<button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close">
</button>
</div>
</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>
<script src="script.js">
</script>
