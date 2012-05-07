<!--[if lt IE 7]><html class="lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if IE 7]><html class="lt-ie9 lt-ie8"><![endif]-->
<!--[if IE 8]><html class="lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--><html class=""><!--<![endif]-->
<head>
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
	<meta http-equiv="Content-Language" content="en-US" >
	<title>Every Last Morsel</title>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,400,300' rel='stylesheet' type='text/css'>
	<link href="/file-bin/css/960/reset.css" media="screen" rel="stylesheet" type="text/css" >
	<link href="/file-bin/css/960/960.css" media="screen" rel="stylesheet" type="text/css" >
	<link href="/file-bin/css/welcome.css" media="screen" rel="stylesheet" type="text/css" >
	<link rel="shortcut icon" href="/favicon.ico">

	<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js" type="text/javascript"></script>
	<script src="/file-bin/js/welcome/main.js" type="text/javascript"></script>
</head>

<body id="page">
<div class="container">
	<header class="clearfix">
		<h1><a href="#" title="Every Last Morsel">Every Last Morsel</a></h1>
		<ul role="navigation">
			<li><a href="#how-it-works" title="Hot it works">How It Works</a></li>
			<li><a href="http://www.facebook.com/everylastmorsel" target="_blank" class="social" title="Facebook">Facebook</a></li>
			<li><a href="http://www.twitter.com/@everylastmorsel" target="_blank" class="social" title="Twitter">Twitter</a></li>
		</ul>
	</header>
</div>
<div class="container hero">
	<div id="hero" class="clearfix">
		<h3>mary, mary, quite contrary where does your garden grow?</h3>

	<?php if ($message !== null) : ?>
		<p class="thank-you"><?php echo $message; ?></p>
	<?php else : ?>
		<form role="newsletter" class="group" action="" method="post">
			<p>
				<b>We know you're<br /> eager to dig in!</b><br />
				Let us know where<br /> we can find you when<br /> we're ready to launch;<br /> until then<br /> mum's the word.
			</p>
			<ul>
				<li>
					<label for="email">Email:</label>
					<div><input type="email" name="email" id="email" data-placeholder="Your email address" value="Your email address" /></div>
				</li>
				<li>
					<label for="email">Region:</label>
					<div><input type="text" name="region" id="region" data-placeholder="Your location (chicago, etc)" value="Your location (chicago, etc)" /></div>
				</li>
			</ul>
			<p><input type="submit" name="submit" value="Sign Up" /></p>
		</form>
	<?php endif; ?>
	</div>
</div>
<div class="container" id="how-it-works">
	<div></div>
</div>
<div class="container roots">
	<div id="roots">
		<ol>
			<li class="location">
				<h3>Claim your space</h3>
				<p>Simply drop a pin to map your garden's location</p>
			</li>
			<li class="growth">
				<h3>Track your progress</h3>
				<p>Keep tabs on your plants &<br /> share results with neighbors<br /> and friends.</p>
			</li>
			<li class="learn">
				<h3>Lean from others</h3>
				<p>Pick-up on tricks of the trade by following city farms and<br /> green thumbs.</p>
			</li>
			<li class="share">
				<h3>Share your bumper crop</h3>
				<p>Donate, barter, or exchange IOU's - just don't let it go to waste!</p>
			</li>
			<li class="last">
				<h3>Build a community</h3>
				<p>Establish a potential garden and rally your neighbors to help build it!</p>
			</li>
		</ol>
	</div>
	<footer>
		<p class="copyright">Copyright 2012 Every Last Morsel</p>
	</footer>
</div>
</body>
</html>