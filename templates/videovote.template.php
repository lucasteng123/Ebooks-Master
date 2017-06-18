<div class="pagebox videovotebox">
	<!--<pre>
	<?php
	/*
		if (isset($vidinfo)) {
			print_r($vidinfo);
		}
	*/
	?>
	</pre>-->
	<!--
		TODO: Figure out how to change order of
		these boxes when screen size is smaller
	-->
	<?php if ($has_videos && !$request_error) { ?>
		<div class="col col-xs-12">
			<ul class="share-buttons rfloat">
			<!-- Facebook Like -->
				<li>
					<div class="fb-like" data-href="<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>" data-layout="button_count" data-action="like" data-show-faces="true" data-share="true"></div>
				</li>
				<!-- Twitter Tweet -->
				<li>
					<a class="twitter-share-button" href="https://twitter.com/intent/tweet?text=Take%20a%20look%20at%20this!">Tweet</a>
				</li>
				<!-- Google +1 -->
				<li>
					<div class="g-plusone" data-size="medium"></div>
				</li>
			</ul>
			<h3><?php echo $title; ?></h3>
		</div>
		<span>
			<div class="col col-xs-6 no-spacing video-container">
				<div class="playbox jq-video-play" data-vidid="<?php echo $vidinfo['a_youtube_id']; ?>">
					<div class="playbox-text">
						Click to Load<br />
						<small><?php echo $vidinfo['a_video_title']; ?></small>
					</div>
				</div>
				<!--
				<iframe class="video" <?php /*width="420" height="315"*/ ?> src="https://www.youtube.com/embed/kTcRRaXV-fg" frameborder="0" allowfullscreen></iframe>
				-->
			</div>
			<div class="col col-xs-6 no-spacing video-container">
				<div class="playbox jq-video-play" data-vidid="<?php echo $vidinfo['b_youtube_id']; ?>">
					<div class="playbox-text">
						Click to Load<br />
						<small><?php echo $vidinfo['b_video_title']; ?></small>
					</div>
				</div>
				<!--
				<iframe class="video" <?php /*width="420" height="315"*/ ?> src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>
				-->
			</div>
		</span>
		<span class="vote-buttons-container">
			<?php if ($account_vote) {
				$isL = $account_vote == 'left';
				$isR = $account_vote == 'right';
			?>
				<div class="col col-xs-6 no-spacing">
					<div class="nohover vote<?php if ($isL) echo ' green'; ?>" data-category="<?php echo $video_pair_id; ?>" data-videoside="left"><?php echo ($isL) ? 'Voted! ' : ""; ?><span class="badge"><?php echo $vidinfo['a_votecount']; ?></span></div>
				</div>
				<div class="col col-xs-6 no-spacing">
					<div class="nohover vote<?php if ($isR) echo ' green'; ?>" data-category="<?php echo $video_pair_id; ?>" data-videoside="right"><?php echo ($isR) ? 'Voted! ' : ""; ?><span class="badge"><?php echo $vidinfo['b_votecount']; ?></span></div>
				</div>
			<?php } else { ?>
				<div class="col col-xs-6 no-spacing">
					<div class="ajax-videovote vote" data-category="<?php echo $video_pair_id; ?>" data-videoside="left">Vote! <span class="badge"><?php echo $vidinfo['a_votecount']; ?></span></div>
				</div>
				<div class="col col-xs-6 no-spacing">
					<div class="ajax-videovote vote" data-category="<?php echo $video_pair_id; ?>" data-videoside="right">Vote! <span class="badge"><?php echo $vidinfo['b_votecount']; ?></span></div>
				</div>
			<?php } ?>
		</span>
	<?php } else { ?>
		<!--<h1>I Exist</h1>-->
	<?php } ?>
</div>
