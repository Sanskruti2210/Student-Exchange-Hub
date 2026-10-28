<?php
require_once 'db.php';
/* About & Contact page – demonstrates Unit 2 (HTML5 media, iframe, tables, lists, forms & input types, meta tags)
and Unit 5 (form processing with validation, query string, session flash message). */
$errors = array();
$old = array('name' => '', 'email' => '', 'phone' => '', 'topic' => 'General', 'visit' => '', 'rating' => '8', 'message' => '');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();
    foreach ($old as $k => $_) {

        $old[$k] = trim((string)($_POST[$k] ?? $old[$k]));
    }

    if (!preg_match("/^[A-Za-z][A-Za-z .'-]{
        1,59
    }
    $/", $old['name'])) $errors[] = 'Enter a valid name (letters only).';
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if ($old['phone'] !== '' && !preg_match('/^[0-9+\-\s]{
        8,15
    }
    $/', $old['phone'])) $errors[] = 'Phone must be 8-15 digits.';
    if (!in_array($old['topic'], array('General', 'Report a listing', 'Account help', 'Suggestion'), true)) $errors[] = 'Choose a topic.';
    if (mb_strlen($old['message']) < 10 || mb_strlen($old['message']) > 1000) $errors[] = 'Message must be 10-1000 characters.';
    if (empty($_POST['agree'])) $errors[] = 'Please accept the terms.';
    if (!$errors) {

        $_SESSION['last_contact_topic'] = $old['topic'];     // session variable
        set_flash('success', 'Thank you ' . $old['name'] . '! Your message about "' . $old['topic'] . '" was received.');
        redirect('about.php?sent=1#contact');                 // query string state

    }

}

$sent = isset($_GET['sent']) && $_GET['sent'] === '1';
?>
<!doctype html>
<html lang="en" data-theme="<?= h(current_theme()) ?>" data-bs-theme="<?= current_theme() === 'dark' ? 'dark' : 'light' ?>">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>About &amp; Contact | Student Exchange Hub</title><meta name="description" content="About Student Exchange Hub Pro, campus map, safety video and contact form."><?php include 'head_assets.php'; ?>
</head>
<body>
<?php include 'header.php'; ?>
<main class="page-width content-page">
<div class="page-title-row">
<div><span class="eyebrow">ABOUT US</span>
<h1>Made by students, for students.</h1>
<p class="muted">Learn how the Hub works, find us on the map and send us a message.</p>
</div>
</div>
<?php show_flash(); ?>

<section class="row g-4 mb-5">
<article class="col-lg-7">
<h2 style="font-family:Manrope,sans-serif">What is Student Exchange Hub?</h2>
<p class="muted">A campus-first marketplace where students buy, sell, exchange or give away useful second-hand things such as books, gadgets and furniture.</p>
<h3 class="h5">Why use it?</h3>
<ul class="muted">
<li>Save money on textbooks and electronics.</li>
<li>Reduce waste by giving items a second life.</li>
<li>Meet students from your own campus.</li>
</ul>
<h3 class="h5">Getting started</h3>
<ol class="muted">
<li>Create a free account.</li>
<li>List an item or browse the marketplace.</li>
<li>Message the other student and agree on a safe meetup.</li>
</ol>
<dl class="muted">
<dt>Sell</dt><dd>Set a price and wait for offers.</dd>
<dt>Exchange</dt><dd>Swap one item for another.</dd>
<dt>Free</dt><dd>Give something away to a fellow student.</dd>
</dl>
</article>
<aside class="col-lg-5">
<div class="table-wrap">
<table class="data-table" style="min-width:0">
<caption class="p-3 fw-bold">Listing types at a glance</caption>
<thead>
<tr>
<th scope="col">Type</th>
<th scope="col">Price</th>
<th scope="col">Best for</th>
</tr>
</thead>
<tbody>
<tr>
<td>Sell</td>
<td>Your price</td>
<td>Books, gadgets</td>
</tr>
<tr>
<td>Exchange</td>
<td>No money</td>
<td>Swapping items</td>
</tr>
<tr>
<td>Free</td>
<td>₹0</td>
<td>Moving out</td>
</tr>
</tbody>
</table>
</div>
</aside>
</section>

<section id="contact" class="form-panel wide-panel">
<h2>Contact us</h2>
<p class="muted">Fields marked * are required.</p>
<?php if ($errors): ?>
<div class="notice error">
<ul><?php foreach ($errors as $e): ?>
<li><?= h($e) ?>
</li><?php endforeach; ?>
</ul>
</div><?php endif; ?>
<form method="post" action="about.php#contact" class="stack-form" id="contactForm" novalidate>
<input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
<div class="form-two">
<label>Full name *<input type="text" name="name" required maxlength="60" value="<?= h($old['name']) ?>" placeholder="Your name">
</label>
<label>Email *<input type="email" name="email" required value="<?= h($old['email']) ?>" placeholder="you@college.edu">
</label>
</div>
<div class="form-two">
<label>Phone<input type="tel" name="phone" value="<?= h($old['phone']) ?>" placeholder="+91 98765 43210">
</label>
<label>Preferred contact date<input type="date" name="visit" value="<?= h($old['visit']) ?>">
</label>
</div>
<label>Topic
<select name="topic"><?php foreach (array('General', 'Report a listing', 'Account help', 'Suggestion') as $t): ?>
<option <?= $old['topic'] === $t ? 'selected' : '' ?>><?= h($t) ?>
</option><?php endforeach; ?>
</select>
</label>
<label>How easy is the Hub to use? <output id="ratingOut"><?= h($old['rating']) ?> / 10</output>
<input type="range" name="rating" min="1" max="10" value="<?= h($old['rating']) ?>">
</label>
<label>Message * <textarea name="message" rows="5" required maxlength="1000" placeholder="Tell us how we can help"><?= h($old['message']) ?>
</textarea>
</label>
<label class="check-row"><input type="checkbox" name="agree" value="1"> <span>I agree to be contacted about this message.</span>
</label>
<div class="form-actions">
<button class="button" type="submit">Send message</button>
<button class="button button-light" type="reset">Reset</button>
</div>
</form>
</section>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
