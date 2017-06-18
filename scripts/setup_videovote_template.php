<?php
function verify_base_category_exists($con, $queryID) {
	$sql = "SELECT * FROM base_categories WHERE id=:baseid";
	$stmt = $con->prepare($sql);
	$stmt->bindValue("baseid",  $queryID, PDO::PARAM_INT );
	$stmt->execute();
	if ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
		return true;
	} else {
		return false;
	}
}

function setup_videovote_template($videovote_tmpl, $con, $sitedb, $video_pair_id, $RO_SESSION) {

	$videovote_tmpl->video_pair_id = $video_pair_id;

	$videoPairInfo = $sitedb->get_vote_pair_info($video_pair_id);
	$vidVotes = $sitedb->get_votes($video_pair_id);

	if (!array_key_exists('a_id', $videoPairInfo)
		|| !array_key_exists('b_id', $videoPairInfo)
		|| $videoPairInfo['a_id'] == null
		|| $videoPairInfo['b_id'] == null
		) {
		$videovote_tmpl->has_videos = false;
	} else {
		$videovote_tmpl->has_videos = true;
		$videoPairInfo['a_votecount'] = $vidVotes[0];
		$videoPairInfo['b_votecount'] = $vidVotes[1];
		$videovote_tmpl->vidinfo = $videoPairInfo;
	}

	if ($RO_SESSION['logged_in']) {
		$videovote_tmpl->account_vote = $sitedb->get_account_vote($RO_SESSION['account']['id'], $video_pair_id);
	} else {
		$videovote_tmpl->account_vote = false;
	}

	//$videovote_tmpl->title = "Video Pair #".$video_pair_id;
}