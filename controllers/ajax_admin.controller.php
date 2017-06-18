<?php

/*
*** IMPORTANT ***
Post data is NOT strictly sanitized, since
only admins have access to this page.

It should, however, be sanitized enough that
if a hacker managed to login they would not
be able to perform injected database operations.
*/

function check_video_url($url) {
	/* Not good enough -_-
	$props = array();
	parse_str(parse_url($url, PHP_URL_QUERY), $props);
	if (array_key_exists('v', $props)) {
		return $props['v'];
	} else {
		return false;
	}
	*/
	// Awesome code from http://stackoverflow.com/questions/3392993 !!
	$matches = array();
	preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $url, $matches);
	if (count($matches) < 1) {
		return false;
		return ''; // why did I ever even do this?
	} else {
		return $matches[0];
	}
}

session_start();

$methods = array();

$methods['run'] = function($instance) {

	$r = $instance->route;

	$json = array(
		'status' => 'server_error',
		'message' => "No response generated"
	);

	ob_start();
	print_r($_POST);
	$post_data = ob_get_clean();

	try {

		// Generate a SiteDB instance
		$con = $instance->tools['con_manager']->get_connection();
		$sitedb = new SiteDB($con);
		$eblog = new EbooksBlogMgr($con);
		$imgdb = new ImageDB($con);
		$ss = new SiteStrings($con);
		$gc = new GuessContest($con);

		if ($_SESSION['logged_in'] === AccountMgr::SESSION_OKAY
			&& $_SESSION['is_admin_user'] === "yes") {

			if ($r[0] == "ticker") {
				switch($_POST['submitted']) {
					case 'update':
						$sitedb->update_ticker_message($_POST['message'], $_POST['linkurl'], $_POST['id']);
						$json['status'] = "good";
						$json['message'] = "Ticker message updated!";
						break;
					case 'remove':
						$sitedb->remove_ticker_message($_POST['id']);
						$json['status'] = "good";
						$json['message'] = "Ticker message removed!";
						break;
					case 'addnew':
						$sitedb->create_ticker_message($_POST['message'], $_POST['linkurl']);
						$json['status'] = "good";
						$json['message'] = "Ticker message added!";
						break;
					default:
						$json['status'] = "error";
						$json['message'] = "invalid request";
				}
			}
			else if ($r[0] == "categories") {
				switch($_POST['submitted']) {
					case 'update':
						$sitedb->rename_base_category($_POST['id'], $_POST['name']);
						$json['status'] = "good";
						$json['message'] = "Base-category renamed!";
						break;
					case 'remove':
						if (false === $sitedb->remove_base_category($_POST['id'])) {
							$json['status'] = "error";
							$json['message'] = "You must remove all sub-categories to remove a base category";
						} else {
							$json['status'] = "good";
							$json['message'] = "Base-category removed!";
						}
						break;
					case 'addnew':
						$sitedb->create_base_category($_POST['name']);
						$json['status'] = "good";
						$json['message'] = "Base-category added!";
						break;
					default:
						$json['status'] = "error";
						$json['message'] = "invalid request";
				}
			}
			else if ($r[0] == "subcats") {
				switch($_POST['submitted']) {
					
					case 'update':
						$sitedb->rename_category($_POST['id'], $_POST['name']);
						$json['status'] = "good";
						$json['message'] = "Category renamed!";
						break;
					case 'remove':
						$sitedb->remove_category_nullify_books($_POST['id']);
						$json['status'] = "good";
						$json['message'] = "Category is gone now!";
						break;
					case 'addnew':
						$sitedb->create_category($_POST['basecat'],$_POST['name']);
						$json['status'] = "good";
						$json['message'] = "Sub-category added!";
						break;
					default:
						$json['status'] = "error";
						$json['message'] = "invalid request";
				}
			}
			else if ($r[0] == "videovote") {
				switch ($_POST['submitted']) {
					case "set":
						$vid_a = check_video_url($_POST['vid_a']);
						$vid_b = check_video_url($_POST['vid_b']);
						$baseid = $_POST['baseid'];
						if ($vid_a === false || $vid_b === false ) {
							if ($baseid == "homepage") {
								$ss->set_value("homepage.videovote_id","none");
							} else {
								$sitedb->nullify_video_pair_in_basecat($baseid);
							}
							$json['message'] = "Video vote was nullified!";
						} else {
							$vid_a_title = $_POST['vid_a_title'];
							$vid_b_title = $_POST['vid_b_title'];
							$vid_a_id = $sitedb->insert_video($vid_a, $vid_a_title, 0);
							$vid_b_id = $sitedb->insert_video($vid_b, $vid_b_title, 0);
							$video_pair_id = $sitedb->insert_video_vote_pair($vid_a_id,$vid_b_id);
							if ($baseid == "homepage") {
								$ss->set_value("homepage.videovote_id",$video_pair_id);
							} else {
								$sitedb->apply_video_pair_to_basecat($video_pair_id, $baseid);
							}
							$json['message'] = "Video vote was changed!";
						}
						$json['status'] = "good";
						break;
					case "fix":
						$vid_a_id = $_POST['vid_a_id'];
						$vid_b_id = $_POST['vid_b_id'];

						$vid_a_title = $_POST['vid_a_title'];
						$vid_b_title = $_POST['vid_b_title'];

						$vid_a_url = check_video_url($_POST['vid_a']);
						$vid_b_url = check_video_url($_POST['vid_b']);

						if ($vid_a === false || $vid_b === false ) {
							$json['status'] = "bad";
							$json['message'] = "Invalid URL was entered :/";
						} else {
							$sitedb->update_video_info($vid_a_url, $vid_a_title, $vid_a_id);
							$sitedb->update_video_info($vid_b_url, $vid_b_title, $vid_b_id);
							$json['message'] = "Video vote was updated!";
							$json['status'] = "good";
						}
						break;
					default:
						$json['status'] = "bad";
						$json['message'] = "Invalid request :/ (not sure what went wrong)";
						$json['submitted'] = $_POST['submitted'];
				}
			}
			else if ($r[0] == "sitestrings") {
				$name = $_POST['name'];
				$value = $_POST['value'];
				$ss->set_value($name, $value);
				$json['message'] = "Site string was updated!";
				$json['status'] = "good";
				
			}
			else if ($r[0] == "mod_book_feature") {
				$json['status'] = "good";
				$json['message'] = "The book was";
				$json['message'] .= ($_POST['submitted'] === "feature") ? " added to" : " removed from";
				$json['message'] .= "features!";
				$val = ($_POST['submitted'] === "feature") ? 1 : 0;
				$json['val'] = $val;
				$json['submitted'] = $_POST['submitted'];
				$json['id'] = $_POST['id'];
				$sitedb->update_book_feature($_POST['id'],$val);
			}
			else if ($r[0] == "mod_book_link") {
				$json['status'] = "good";
				$json['message'] = "The book link was changed!";
				$json['id'] = $_POST['id'];
				$val = $_POST['link'];
				$sitedb->update_book_link($_POST['id'],$val);
			}
			else if ($r[0] == "ebooks_blog_post_remove") {
				$json['status'] = "good";
				$json['message'] = "The post was removed!";
				$json['id'] = $_POST['id'];
				$eblog->remove_blog_post($_POST['id']);
			}
			else if ($r[0] == "mod_book_simple") {
				$json['status'] = "good_dnr";
				if ($_POST['submitted'] === "delete") {
					$json['message'] = "The book was deleted!";
					$sitedb->delete_book($_POST['id']);
					ob_clean();
					echo json_encode($json);
					return;
				}
				else if ($_POST['submitted'] === "set_unpaid") {
					$json['message'] = "The book was set to unpaid!";
					$sitedb->update_book_visibility($_POST['id'],"unpaid");
					ob_clean();
					echo json_encode($json);
					return;
				}
				else if ($_POST['submitted'] === "set_unchecked") {
					$json['message'] = "The book was set to unchecked!";
					$sitedb->update_book_visibility($_POST['id'],"unchecked");
					ob_clean();
					echo json_encode($json);
					return;
				}
				else if ($_POST['submitted'] === "set_public") {
					$json['message'] = "The book was set to public!";
					$sitedb->update_book_visibility($_POST['id'],"public");
					ob_clean();
					echo json_encode($json);
					return;
				}
			}
			else if ($r[0] == "guess_contest") {
				if ($_POST['submitted'] === "begin") {
					$gc->begin_contest(trim($_POST['sword']));
					$json['status'] = "good";
					$json['message'] = "The contest has begun!";
				}
				else if ($_POST['submitted'] === "end") {
					$gc->end_contest();
					$json['status'] = "good";
					$json['message'] = "The contest has been closed!";
				}
				else if ($_POST['submitted'] === "add_winner") {
					$name = $_POST['name'];
					$gc->add_name_to_roll($_POST['name']);
					$json['status'] = "good";
					$json['message'] = "A winner has been added!";
				}
				else if ($_POST['submitted'] === "remove_winner") {
					$id = $_POST['id'];
					$gc->remove_name($id);
					$json['status'] = "good";
					$json['message'] = "A winner has been deleted!";
				}
				else if ($_POST['submitted'] === "update_prize") {
					$value = $_POST['value'];
					$ss->set_value("gc.prize_string", $value);
					$json['message'] = "Prize string was updated!";
					$json['status'] = "good";
				}
				else if ($_POST['submitted'] === "update_video") {
					$video = check_video_url($_POST['video']);
					if ($video === false) {
						$ss->set_value("gc.video","none");
					} else {
						$ss->set_value("gc.video",$video);
					}
					$json['message'] = "Winners video was updated!";
					ob_start(); var_dump($video); $video_out = ob_get_clean();
					$json['debug'] = $video_out;
					$json['status'] = "good";
				}
			}
			else if ($r[0] == "emailout") {
				$mailer = new SiteMail($con);
				$tmpl = new Template();
				$tmpl->set_template_file(SITE_PATH.'/templates/email_newsletter.template.php');
				$tmpl->title = $_POST['title'];
				$tmpl->contents = $_POST['contents'];
				if (DEV_MODE) $mailer->set_fake_mail(true);
				// Loop through all subscribers
				$mails = $sitedb->get_newsletter_mails();
				foreach ($mails as $mail) {
					$hash = SecretHashThing::hash($mail);
					$tmpl->link = "http://bestebooks.ca/?location=unsub/".urlencode($mail)."/".$hash;
					$mailer->send_user_email($mail,"BestEbooks.ca Newsletter",$tmpl);
				}
				$json['status'] = "good";
				$json['message'] = "The newsletter was sent! [deprecated mail script used]";
				ob_clean();
				echo json_encode($json);
				return;
			}
			else if ($r[0] == "ebooks_post") {
				$title    = $_POST['title'];
				$contents = $_POST['contents'];
				$imageID  = $imgdb->add_from_post_request('post_image',"Post Image");
				$eblog->insert_blog_post($title, $contents, $imageID);

				$json['status'] = "good";
				$json['message'] = "This post was posted to the /ebooks blog!";
				ob_clean();
				echo json_encode($json);
			}
			else {
				$json['message'] = "This type of operation doesn't exist.";
			}

		} else {
			$json = array(
				'status' => 'error',
				'message' => "Not logged in. (session expired?)"
			);
		}

	} catch (PDOException $e) {
		$json = array(
			'status' => 'error',
			'message' => "The following internal error occured: ".$e->getMessage()
		);
		ob_clean();
		echo json_encode($json);
		return;
	} catch (Exception $e) {
		/*switch ($e->getCode()) {
			case AccountMgrException::INCORRECT_PASSWORD:
				if (isset($_SESSION['attempts'])) $_SESSION['attempts'] += 1;
				else $_SESSION['attempts'] = 1;
		}*/
		$json = array(
			'status' => 'error',
			'message' => $e->getMessage()
		);
		ob_clean();
		echo json_encode($json);
		return;
	}

	ob_clean();
	echo json_encode($json);
};

$page_controller = new Controller($methods);
unset($methods);

/*

$methods = array();

$methods['run'] = function($instance) {
	$r = $instance->route;
	$page = new Template();
	$page->set_template_file(SITE_PATH.'/templates/home.template.php');
	if (VarTools::key_exists_equals(0,$r,"ajax")) {
		$page->run();
	} else {
		$html = new Template();
		$html->set_template_file(SITE_PATH.'/templates/full.template.php');
		$html->subTemplate = $page;
		$html->run();
	}
};

$page_controller = new Controller($methods);
unset($methods);

*/