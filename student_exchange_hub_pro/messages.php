<?php
require_once 'db.php';
require_login();
$uid=current_user_id();
$errors=array();
$selectedTo=filter_input(INPUT_GET,'to',FILTER_VALIDATE_INT);
$productId=filter_input(INPUT_GET,'product_id',FILTER_VALIDATE_INT);
if($_SERVER['REQUEST_METHOD']==='POST') {

    verify_csrf();
    $receiver=(int)($_POST['receiver_id']??0);
    $pid=(int)($_POST['product_id']??0);
    $body=trim($_POST['message']??'');
    if($receiver<1||$receiver===$uid)$errors[]='Choose a valid recipient.';
    if($body===''||mb_strlen($body)>2000)$errors[]='Message must contain 1–2,000 characters.';
    $check=mysqli_prepare($conn,'SELECT id FROM users WHERE id=?');
    mysqli_stmt_bind_param($check,'i',$receiver);
    mysqli_stmt_execute($check);
    $exists=mysqli_fetch_assoc(mysqli_stmt_get_result($check));
    mysqli_stmt_close($check);
    if(!$exists)$errors[]='That recipient was not found.';
    if($pid>0) {

        $check=mysqli_prepare($conn,'SELECT id FROM products WHERE id=? AND status IN ("Available","Sold")');
        mysqli_stmt_bind_param($check,'i',$pid);
        mysqli_stmt_execute($check);
        $pr=mysqli_fetch_assoc(mysqli_stmt_get_result($check));
        mysqli_stmt_close($check);
        if(!$pr)$errors[]='That listing was not found.';

    }
    else $pid=0;
    if(!$errors) {

        $stmt=mysqli_prepare($conn,'INSERT INTO messages(sender_id,receiver_id,product_id,message) VALUES(?,?,NULLIF(?,0),?)');
        mysqli_stmt_bind_param($stmt,'iiis',$uid,$receiver,$pid,$body);
        if(mysqli_stmt_execute($stmt)) {

            set_flash('success','Message sent.');
            redirect('messages.php?to='.$receiver.($pid?'&product_id='.$pid:''));

        }
        else {

            $errors[]='Could not send the message.';

        }
        mysqli_stmt_close($stmt);

    }

}

$contactsSql="SELECT u.id,u.name,MAX(m.created_at) AS latest FROM messages m JOIN users u ON u.id=IF(m.sender_id=?,m.receiver_id,m.sender_id) WHERE m.sender_id=? OR m.receiver_id=? GROUP BY u.id,u.name ORDER BY latest DESC";
$stmt=mysqli_prepare($conn,$contactsSql);
mysqli_stmt_bind_param($stmt,'iii',$uid,$uid,$uid);
mysqli_stmt_execute($stmt);
$contacts=mysqli_stmt_get_result($stmt);
if(!$selectedTo) {

    $first=mysqli_fetch_assoc($contacts);
    if($first)$selectedTo=(int)$first['id'];
    mysqli_data_seek($contacts,0);

}

$contact=null;
$conversation=null;
if($selectedTo && $selectedTo!==$uid) {

    $stmt=mysqli_prepare($conn,'SELECT id,name,email FROM users WHERE id=?');
    mysqli_stmt_bind_param($stmt,'i',$selectedTo);
    mysqli_stmt_execute($stmt);
    $contact=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if($contact) {

        $stmt=mysqli_prepare($conn,'SELECT m.*,s.name AS sender_name,p.title AS product_title FROM messages m JOIN users s ON s.id=m.sender_id LEFT JOIN products p ON p.id=m.product_id WHERE (m.sender_id=? AND m.receiver_id=?) OR (m.sender_id=? AND m.receiver_id=?) ORDER BY m.created_at ASC LIMIT 200');
        mysqli_stmt_bind_param($stmt,'iiii',$uid,$selectedTo,$selectedTo,$uid);
        mysqli_stmt_execute($stmt);
        $conversation=mysqli_stmt_get_result($stmt);

    }

}

?>
<!doctype html>
<html lang="en" data-theme="<?= h(current_theme()) ?>" data-bs-theme="<?= current_theme() === 'dark' ? 'dark' : 'light' ?>">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Messages | Student Exchange Hub</title><?php include 'head_assets.php'; ?>
</head>
<body><?php include 'header.php'; ?>
<main class="page-width content-page">
<div class="page-title-row">
<div><span class="eyebrow">STUDENT TO STUDENT</span>
<h1>Messages</h1>
<p class="muted">Discuss item details and agree on a safe exchange.</p>
</div>
</div><?php show_flash(); ?><?php if($errors): ?>
<div class="notice error">
<ul><?php foreach($errors as $e): ?>
<li><?= h($e) ?>
</li><?php endforeach; ?>
</ul>
</div><?php endif; ?>
<div class="messages-layout">
<aside class="conversation-list">
<h2>Your conversations</h2><?php if(mysqli_num_rows($contacts)):while($c=mysqli_fetch_assoc($contacts)): ?><a class="conversation-link <?= $selectedTo===(int)$c['id']?'selected':'' ?>" href="messages.php?to=<?= (int)$c['id'] ?>"><span class="contact-avatar"><?= h(strtoupper(substr($c['name'],0,1))) ?></span><span><strong><?= h($c['name']) ?></strong><small>Open conversation</small></span></a><?php endwhile;
else: ?>
<div class="empty-inline">
<p>No conversations yet.</p><small>Open a listing and select “Message seller” to start.</small><a href="browse.php">Browse listings →</a>
</div><?php endif; ?>
</aside>
<section class="conversation-panel"><?php if($contact): ?>
<div class="conversation-header"><span class="contact-avatar"><?= h(strtoupper(substr($contact['name'],0,1))) ?></span>
<div>
<h2><?= h($contact['name']) ?>
</h2><small><?= h($contact['email']) ?></small>
</div>
</div>
<div class="message-history"><?php if(mysqli_num_rows($conversation)):while($m=mysqli_fetch_assoc($conversation)): ?>
<article class="message-bubble <?= (int)$m['sender_id']===$uid?'mine':'theirs' ?>">
<p><?= nl2br(h($m['message'])) ?>
</p><?php if($m['product_title']): ?><small>About: <?= h($m['product_title']) ?></small><?php endif; ?><time><?= h(date('M j, g:i a',strtotime($m['created_at']))) ?></time>
</article><?php endwhile;
else: ?>
<div class="empty-inline">
<p>Start the conversation.</p><small>Ask about condition, availability or a suitable meetup.</small>
</div><?php endif; ?>
</div>
<form method="post" class="message-compose"><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><input type="hidden" name="receiver_id" value="<?= (int)$contact['id'] ?>"><input type="hidden" name="product_id" value="<?= (int)($productId?:0) ?>">
<label class="visually-hidden" for="messageText">Your message</label>
<textarea id="messageText" name="message" maxlength="2000" required rows="3" placeholder="Write a message...">
</textarea>
<button class="button" type="submit">Send message</button>
</form><?php else: ?>
<div class="conversation-empty"><span class="empty-icon"><svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg></span>
<h2>Your conversations, in one place.</h2>
<p>Choose a conversation or browse a listing to contact its seller.</p><a class="button" href="browse.php">Explore listings</a>
</div><?php endif; ?>
</section>
</div>
</main><?php include 'footer.php'; ?>
</body>
</html>
