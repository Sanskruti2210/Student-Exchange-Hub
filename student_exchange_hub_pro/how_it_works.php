<?php require_once 'db.php'; ?>
<!doctype html>
<html lang="en" data-theme="<?= h(current_theme()) ?>" data-bs-theme="<?= current_theme() === 'dark' ? 'dark' : 'light' ?>">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>How it works | Student Exchange Hub</title><?php include 'head_assets.php'; ?>
</head>
<body><?php include 'header.php'; ?>
<main class="page-width content-page">
<section class="how-hero"><span class="eyebrow">GOOD THINGS GO AROUND</span>
<h1>Make student life<br><em>a little more circular.</em>
</h1>
<p>Student Exchange Hub helps you pass on useful items, discover affordable finds and connect with other students.</p>
</section>
<div class="how-steps">
<article><span>01</span>
<h2>Create an account</h2>
<p>Register with your name and email. Add optional course and college details to your profile.</p><a href="register.php">Join the Hub ↗</a>
</article>
<article><span>02</span>
<h2>Discover or list</h2>
<p>Search by title, category, type and price—or create a listing with a clear description and condition.</p><a href="browse.php">Browse items ↗</a>
</article>
<article><span>03</span>
<h2>Message the student</h2>
<p>Ask questions about the item, agree on the price or exchange, and choose a convenient public meetup.</p><a href="messages.php">Open messages ↗</a>
</article>
<article><span>04</span>
<h2>Close the loop</h2>
<p>Inspect the item before paying, then mark your listing as sold or exchanged so others know it is no longer available.</p><a href="my_products.php">Manage listings ↗</a>
</article>
</div>
<section class="safety-banner">
<div><span class="eyebrow">A QUICK SAFETY NOTE</span>
<h2>Keep every exchange sensible.</h2>
</div>
<ul>
<li>Meet in a public, well-lit campus location.</li>
<li>Inspect the item before handing over money.</li>
<li>Do not share account passwords or verification codes.</li>
<li>Report suspicious behaviour to your campus or site administrator.</li>
</ul>
</section>

<section class="my-4"><span class="eyebrow">QUESTIONS</span>
<h2 style="font-family:Manrope,sans-serif">Frequently asked questions</h2>
<div class="faq-item">
<button class="faq-q" type="button" aria-expanded="false">Is it free to list an item?</button>
<div class="faq-a">Yes. Listing, browsing and messaging are free. Choose Sell, Exchange or Free when you add an item.</div>
</div>
<div class="faq-item">
<button class="faq-q" type="button" aria-expanded="false">Where should I meet the other student?</button>
<div class="faq-a">Pick a public, familiar campus place such as the library entrance or canteen, and go during the day.</div>
</div>
<div class="faq-item">
<button class="faq-q" type="button" aria-expanded="false">Why was I signed out automatically?</button>
<div class="faq-a">For your safety, a session ends after 30 minutes of inactivity. Just sign in again.</div>
</div>
<div class="faq-item">
<button class="faq-q" type="button" aria-expanded="false">What do the cookies do?</button>
<div class="faq-a">Essential cookies keep you signed in. Optional cookies remember your theme, your email on the sign-in page and your last visit. You can change this any time from Cookie settings.</div>
</div>
</section>
</main><?php include 'footer.php'; ?>
</body>
</html>
