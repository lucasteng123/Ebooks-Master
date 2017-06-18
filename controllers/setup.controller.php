<?php

// Disable file to protect database
// die("This file is currently disabled.");

/**
 * BestEbooks Database Setup
 * Up-to-date as of 2016-03-06
 */

session_start();

$methods = array();

$methods['run'] = function($instance) {
	if (
		// $_SESSION['is_admin_user'] !== "yes"
		// $_SERVER['REMOTE_ADDR'] != "127.0.0.1"
		false
		) {
			die('ERR: ACCESS VIOLATION: '.$_SERVER['REMOTE_ADDR']);
	}
	echo "<pre>";
	//session_start();
	//session_destroy();
	$con = $instance->tools['con_manager']->get_connection();

	echo "sitedb pre-setup:<br />";
	$sitedb = new SiteDB($con);
	$sitedb->destroy_records();

	$accmgr = new AccountMgr($con);
	echo "accountmgr setup:<br />";
	$accmgr->setup();
	echo "<br />";
	$accountID = null;
	$accountID2 = null;
	try {
		$accountID = $accmgr->new_account("tester","temp1234","Angela","onlyevv@gmail.com");
		$accountID2 = $accmgr->new_account("iamtest","temp1234","Angela","test@dubede.com");
	} catch (AccountMgrException $e) {
		echo ("AccountMgrException: ".$e->getMessage());
	} catch (PDOException $e) {
		echo ("PDOException: ".$e->getMessage());
	}
	try {
		$accmgr->add_permission("tester","site.admin");
	} catch (AccountMgrException $e) {
		echo ("AccountMgrException: ".$e->getMessage());
	} catch (PDOException $e) {
		echo ("PDOException: ".$e->getMessage());
	}
	echo "<br />[done] AccountMgr configured.<br /><br />";

	echo "imgupld setup:\n";
	$imgupld = new ImageDB($con);
	$imgupld->setup();
	$imgupld->reset_all_tables();

	// --- add some default images ---
	$image_cover_1 = $imgupld->insert_image_directly("Book Cover","1.jpg",null);
	$image_cover_2 = $imgupld->insert_image_directly("Book Cover","2.jpg",null);
	$image_cover_3 = $imgupld->insert_image_directly("Book Cover","3.jpg",null);
	$image_cover_4 = $imgupld->insert_image_directly("Book Cover","4.jpg",null);
	$image_cover_5 = $imgupld->insert_image_directly("Book Cover","5.jpg",null);
	$image_cover_6 = $imgupld->insert_image_directly("Book Cover","6.jpg",null);
	$image_cover_7 = $imgupld->insert_image_directly("Book Cover","7.jpg",null);

	echo "sitedb setup:\n";
	$sitedb->setup();

	echo "sitedb setup newsletters:\n";
	$sitedb->setup_newsletter();

	// --- create default ticker messages ---
	$sitedb->insert_ticker_message_directly("Quick ask Zoey; what stops X-Rays?", null);
	$sitedb->insert_ticker_message_directly("Red fish vanish, then grow bigger", null);
	$sitedb->insert_ticker_message_directly("Yaks hear noises under Jack's mattress.", null);

	// --- create default categories ---
	$base_fiction = $sitedb->create_base_category("Fiction");
	$cat_horr = $sitedb->create_category($base_fiction, "Horror");
	$cat_fant = $sitedb->create_category($base_fiction, "Fantasy");
	$cat_myst = $sitedb->create_category($base_fiction, "Mystery");
	$base_nonfict = $sitedb->create_base_category("Non-Fiction");
	$cat_comp = $sitedb->create_category($base_nonfict, "Computers");
	$cat_geoc = $sitedb->create_category($base_nonfict, "Geography & Culture");
	$cat_heal = $sitedb->create_category($base_nonfict, "Health");
	$cat_scin = $sitedb->create_category($base_nonfict, "Science");

	// --- create default videos ---
	$vida = $sitedb->insert_video("MFbPKib9Y-A","Auto Test Video",0);
	$vidb = $sitedb->insert_video("MFbPKib9Y-A","Auto Test Video",0);
	$sitedb->insert_video_pair_into_basecat($vida,$vidb,$base_fiction);

	$ADD_THOSE_BOOKS = true;
	if ($ADD_THOSE_BOOKS === true) {
		// --- create default books ---
		$exisbn = "978-3-16-148410-0";
		$sitedb->insert_book_from_sanitized_data($image_cover_1,$accountID,$cat_horr,$exisbn,"The Tale of the Horror Thing","Billy Joe","Meaningful Description","http://www.amazon.ca/Goosebumps-Hall-Horrors-Dont-Scream-ebook/dp/B006N4GGNC/ref=sr_1_3?s=digital-text&ie=UTF8&qid=1432835205&sr=1-3&keywords=rl+stine","$5","public");
		$sitedb->insert_book_from_sanitized_data($image_cover_2,$accountID,$cat_horr,$exisbn,"Actual Book About Onions","Lutelda Kaspin","Meaningful Description","http://www.amazon.ca/Onions-Health-Benefits-Eating-ebook/dp/B00S4YOI6G/ref=sr_1_1?s=digital-text&ie=UTF8&qid=1432835507&sr=1-1&keywords=onions","$3.55","public");
		$sitedb->insert_book_from_sanitized_data($image_cover_3,$accountID,$cat_fant,$exisbn,"Fantast Book","Jilly Boe","Meaningful Description","http://www.amazon.ca/Goosebumps-Hall-Horrors-Dont-Scream-ebook/dp/B006N4GGNC/ref=sr_1_3?s=digital-text&ie=UTF8&qid=1432835205&sr=1-3&keywords=rl+stine","$6","public");

		$sitedb->insert_book_from_sanitized_data($image_cover_1,$accountID,$cat_heal,$exisbn,"Onions Are Okay to Eat","Lutelda Kaspin","Meaningful Description","http://www.amazon.ca/Onions-Health-Benefits-Eating-ebook/dp/B00S4YOI6G/ref=sr_1_1?s=digital-text&ie=UTF8&qid=1432835507&sr=1-1&keywords=onions","$3.55","public");

		$categories = array($cat_horr,$cat_fant,$cat_myst,$cat_comp,$cat_geoc,$cat_heal,$cat_scin);
		$covers = array(
			$image_cover_1,
			$image_cover_2,
			$image_cover_3,
			$image_cover_4,
			$image_cover_5,
			$image_cover_6,
			$image_cover_7
		);
		$words1 = array("Book of ","Title of ","Encyclopedia ","The Haunted ","All about ","Mr Bean's ");
		$words2 = array("people","history","way","art","world","information","map","two","family","government","health","system","computer","meat","year","thanks","music","person","reading","method","data","food","understanding","theory","law","bird","literature","problem","software","control","knowledge","power","ability","economics","love","internet","television","science","library","nature","fact","product","idea","temperature","investment","area","society","activity","story","industry","media","thing","oven","community","definition","safety","quality","development","language","management","player","variety","video","week","security","country","exam","movie","organization","equipment","physics","analysis","policy","series","thought","basis","boyfriend","direction","strategy","technology","army","camera","freedom","paper","environment","child","instance","month","truth","marketing","university","writing","article","department","difference","goal","news","audience","fishing","growth","income","marriage","user","combination","failure","meaning","medicine","philosophy","teacher","communication","night","chemistry","disease","disk","energy","nation","road","role","soup","advertising","location","success","addition","apartment","education","math","moment","painting","politics","attention","decision","event","property","shopping","student","wood","competition","distribution","entertainment","office","population","president","unit","category","cigarette","context","introduction","opportunity","performance","driver","flight","length","magazine","newspaper","relationship","teaching","cell","dealer","finding","lake","member","message","phone","scene","appearance","association","concept","customer","death","discussion","housing","inflation","insurance","mood","woman","advice","blood","effort","expression","importance","opinion","payment","reality","responsibility","situation","skill","statement","wealth","application","city","county","depth","estate","foundation","grandmother","heart","perspective","photo","recipe","studio","topic","collection","depression","imagination","passion","percentage","resource","setting","ad","agency","college","connection","criticism","debt","description","memory","patience","secretary","solution","administration","aspect","attitude","director","personality","psychology","recommendation","response","selection","storage","version","alcohol","argument","complaint","contract","emphasis","highway","loss","membership","possession","preparation","steak","union","agreement","cancer","currency","employment","engineering","entry","interaction","mixture","preference","region","republic","tradition","virus","actor","classroom","delivery","device","difficulty","drama","election","engine","football","guidance","hotel","owner","priority","protection","suggestion","tension","variation","anxiety","atmosphere","awareness","bath","bread","candidate","climate","comparison","confusion","construction","elevator","emotion","employee","employer","guest","height","leadership","mall","manager","operation","recording","sample","transportation","charity","cousin","disaster","editor","efficiency","excitement");
		$words3 = array("Elaine Callejas","Otis Corle","Kip Mcclelland","Sarita Gallegos","Jay Keogh","Tandy Haffey","Louetta Easterly","Gerri Fegan","Georgie Zellers","Francina Leiser","Ali Hinderliter","Carlotta Hufford","Hermelinda Albright","Camille Ohl","Pattie Lesperance","Jerome Shima","Delma Vidales","Sudie Palma","Herb Meaux","Dione Dacus","Guillermina Ikerd","Kenia Blackledge","Hellen Balch","Anjelica Burnside","Foster Kaneshiro","Rea Necaise","Raymond Pettway","Garth Yutzy","Lorinda Toland","Arletha Loaiza","Synthia Mims","Ok Batson","Ahmad Stegman","Berta Killam","Tianna Krob","Aurore Lovick","Marquis Willams","Santo Sankey","Joaquina Malone","Greg Ciampa","Shin Glessner","Timmy Tafoya","Carmelita Gandee","Adelle Frese","Bertie Putnam","Margareta Carrell","Dean Palmieri","Yetta Feely","Raquel Vandergrift","Paulene Walworth");
		$wordsF = array("nice","strong","frequent","lamentable","fat","aggressive","organic","internal","clever","tasteless","apathetic","stingy","mature","sophisticated","feeble","ragged","nostalgic","vivacious","terrible","like","wide-eyed","unused","solid","wary","shaggy","chemical","nervous","malicious","shrill","talented","classy","obtainable","black-and-white","roasted","tense","enthusiastic","secretive","flippant","learned","lean","beneficial","dirty","bent","roomy","cut","late","difficult","splendid","minor","scandalous","big","ready","humdrum","phobic","comfortable","flashy","moaning","stereotyped","spooky","annoying","drab","abrupt","workable","untidy","jittery","hungry","plastic","panicky","uptight","maniacal","subsequent","ruddy","talented","woebegone","little","silent","tight","lean","evanescent","inconclusive","hard-to-find","plausible","penitent","handy","calculating","glorious","noiseless","low","attractive","charming","abnormal","sore","synonymous","blue-eyed","fearful","motionless","measly","married","tender","numberless","belligerent","soft","aberrant","cultured","historical","functional","unruly","robust","vengeful","violent","possible","fuzzy","resolute","adaptable","creepy","fantastic","bouncy","ceaseless","imperfect","safe","smooth","nice","muddled","far-flung","public","gorgeous","nifty","fanatical","calm","tame","sad","first","grey","complete","earthy","gamy","likeable","uncovered","protective","elfin","kindly","certain","bad","wrathful","chunky","ambiguous","aspiring","deep","distinct","yummy","hurt","slippery","undesirable","homely","snobbish","alleged","lying","faithful","telling","jazzy","direful","physical","lazy","enthusiastic","hurried","vague","next","enchanted","organic","secretive","living","aboard","threatening","daffy","tense","milky","trashy","dead","obeisant","incandescent","long","outstanding","messy","intelligent","wooden","spotty","gigantic","zippy","pricey","secret","spiky","discreet","imminent","quack","slow","offbeat","odd","rapid","bitter","overconfident","bizarre","cooperative","null","succinct","conscious","thoughtless","gratis","scientific","nervous","impartial","sophisticated","fretful","gentle","sick","shivering","womanly","neighborly","silent","woozy","fluffy","selective","romantic","piquant","spiffy","boorish","wet","outgoing","natural","whimsical","nappy","hard","tangible","minor","exotic","lavish","lyrical","abusive","infamous","tricky","lackadaisical","ad hoc","boundless","efficient","repulsive","special","eatable","great","cold","idiotic","oafish");

		foreach ($categories as $category) {
			echo "Populating ".$category." with 100 books...\n";
			for ($i=0; $i < 20; $i++) {
				$title = ucwords($words1[rand(0,count($words1)-1)] . $wordsF[rand(0,count($wordsF)-1)].' '.$words2[rand(0,count($words2)-1)]);
				$author = $words3[rand(0,count($words3)-1)];
				$cat = $category;
				$image_cover = $covers[rand(0,count($covers)-1)];
				echo "\t". $title . " by " . $author ."\n";
				$desc = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam neque purus, auctor vitae dignissim at, pharetra sed dolor. Phasellus et pharetra sem. Pellentesque sed rutrum enim. Nullam eget tortor ultrices, gravida purus sed, semper ipsum. Aliquam erat volutpat. Mauris consectetur fringilla consequat. Maecenas quis nisl ut turpis cursus porta nec iaculis lacus. Duis vitae ipsum nunc. In hac habitasse platea dictumst. Suspendisse sed tortor metus. Suspendisse sed dolor at turpis dignissim mollis eu ut lorem. In commodo urna dictum tempor imperdiet.";
				$sitedb->insert_book_from_sanitized_data($image_cover,$accountID,$cat,$exisbn,$title,$author,$desc,"http://www.amazon.ca/Onions-Health-Benefits-Eating-ebook/dp/B00S4YOI6G/ref=sr_1_1?s=digital-text&ie=UTF8&qid=1432835507&sr=1-1&keywords=onions",''.rand(0,9).".".rand(0,9)."5","public");
			}
			for ($i=0; $i < 7; $i++) {
				$title = ucwords($words1[rand(0,count($words1)-1)] . $wordsF[rand(0,count($wordsF)-1)].' '.$words2[rand(0,count($words2)-1)]);
				$author = $words3[rand(0,count($words3)-1)];
				$cat = $category;
				$image_cover = $covers[rand(0,count($covers)-1)];
				echo "\t". $title . " by " . $author ."\n";
				$desc = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam neque purus, auctor vitae dignissim at, pharetra sed dolor. Phasellus et pharetra sem. Pellentesque sed rutrum enim. Nullam eget tortor ultrices, gravida purus sed, semper ipsum. Aliquam erat volutpat. Mauris consectetur fringilla consequat. Maecenas quis nisl ut turpis cursus porta nec iaculis lacus. Duis vitae ipsum nunc. In hac habitasse platea dictumst. Suspendisse sed tortor metus. Suspendisse sed dolor at turpis dignissim mollis eu ut lorem. In commodo urna dictum tempor imperdiet.";
				$sitedb->insert_book_from_sanitized_data($image_cover,$accountID,$cat,$exisbn,$title,$author,$desc,"http://www.amazon.ca/Onions-Health-Benefits-Eating-ebook/dp/B00S4YOI6G/ref=sr_1_1?s=digital-text&ie=UTF8&qid=1432835507&sr=1-1&keywords=onions",''.rand(0,9).".".rand(0,9)."5","unchecked");
			}
			for ($i=0; $i < 2; $i++) {
				$title = ucwords($words1[rand(0,count($words1)-1)] . $wordsF[rand(0,count($wordsF)-1)].' '.$words2[rand(0,count($words2)-1)]);
				$author = $words3[rand(0,count($words3)-1)];
				$cat = $category;
				$image_cover = $covers[rand(0,count($covers)-1)];
				echo "\t". $title . " by " . $author ."\n";
				$desc = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam neque purus, auctor vitae dignissim at, pharetra sed dolor. Phasellus et pharetra sem. Pellentesque sed rutrum enim. Nullam eget tortor ultrices, gravida purus sed, semper ipsum. Aliquam erat volutpat. Mauris consectetur fringilla consequat. Maecenas quis nisl ut turpis cursus porta nec iaculis lacus. Duis vitae ipsum nunc. In hac habitasse platea dictumst. Suspendisse sed tortor metus. Suspendisse sed dolor at turpis dignissim mollis eu ut lorem. In commodo urna dictum tempor imperdiet.";
				$sitedb->insert_book_from_sanitized_data($image_cover,$accountID,$cat,$exisbn,$title,$author,$desc,"http://www.amazon.ca/Onions-Health-Benefits-Eating-ebook/dp/B00S4YOI6G/ref=sr_1_1?s=digital-text&ie=UTF8&qid=1432835507&sr=1-1&keywords=onions",''.rand(0,9).".".rand(0,9)."5","awaiting-account");
			}
			for ($i=0; $i < 2; $i++) {
				$title = ucwords($words1[rand(0,count($words1)-1)] . $wordsF[rand(0,count($wordsF)-1)].' '.$words2[rand(0,count($words2)-1)]);
				$author = $words3[rand(0,count($words3)-1)];
				$cat = $category;
				$image_cover = $covers[rand(0,count($covers)-1)];
				echo "\t". $title . " by " . $author ."\n";
				$desc = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam neque purus, auctor vitae dignissim at, pharetra sed dolor. Phasellus et pharetra sem. Pellentesque sed rutrum enim. Nullam eget tortor ultrices, gravida purus sed, semper ipsum. Aliquam erat volutpat. Mauris consectetur fringilla consequat. Maecenas quis nisl ut turpis cursus porta nec iaculis lacus. Duis vitae ipsum nunc. In hac habitasse platea dictumst. Suspendisse sed tortor metus. Suspendisse sed dolor at turpis dignissim mollis eu ut lorem. In commodo urna dictum tempor imperdiet.";
				$sitedb->insert_book_from_sanitized_data($image_cover,$accountID2,$cat,$exisbn,$title,$author,$desc,"http://www.amazon.ca/Onions-Health-Benefits-Eating-ebook/dp/B00S4YOI6G/ref=sr_1_1?s=digital-text&ie=UTF8&qid=1432835507&sr=1-1&keywords=onions",''.rand(0,9).".".rand(0,9)."5","unpaid");
			}
			for ($i=0; $i < 2; $i++) {
				$title = ucwords($words1[rand(0,count($words1)-1)] . $wordsF[rand(0,count($wordsF)-1)].' '.$words2[rand(0,count($words2)-1)]);
				$author = $words3[rand(0,count($words3)-1)];
				$cat = $category;
				$image_cover = $covers[rand(0,count($covers)-1)];
				echo "\t". $title . " by " . $author ."\n";
				$desc = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam neque purus, auctor vitae dignissim at, pharetra sed dolor. Phasellus et pharetra sem. Pellentesque sed rutrum enim. Nullam eget tortor ultrices, gravida purus sed, semper ipsum. Aliquam erat volutpat. Mauris consectetur fringilla consequat. Maecenas quis nisl ut turpis cursus porta nec iaculis lacus. Duis vitae ipsum nunc. In hac habitasse platea dictumst. Suspendisse sed tortor metus. Suspendisse sed dolor at turpis dignissim mollis eu ut lorem. In commodo urna dictum tempor imperdiet.";
				$sitedb->insert_book_from_sanitized_data($image_cover,$accountID2,$cat,$exisbn,$title,$author,$desc,"http://www.amazon.ca/Onions-Health-Benefits-Eating-ebook/dp/B00S4YOI6G/ref=sr_1_1?s=digital-text&ie=UTF8&qid=1432835507&sr=1-1&keywords=onions",''.rand(0,9).".".rand(0,9)."5","unchecked");
			}
		}
		echo "</pre>";
	}

	echo "SITE STRINGS:<br />";
	$ss = new SiteStrings($con);
	$ss->setup();
	$vida = $sitedb->insert_video("Jixl-ZV8HTg","Homepage Test Video",0);
	$vidb = $sitedb->insert_video("8Gv0H-vPoDc","Homepage Test Video",0);
	$pairID = $sitedb->insert_video_vote_pair($vida,$vidb,$base_fiction);
	$ss->set_value("homepage.videovote_id",$pairID);
	$ss->set_value("homepage.videovote_title","Test Title");
	$ss->set_value("email.account_pass_reset","We received a password reset request from your account! If you did not try to reset your password, or accidentally clicked the \"forgot\" button, just ignore this message. To reset your password, click the button below.");
	$ss->set_value("email.account_welcome","Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam neque purus, auctor vitae dignissim at, pharetra sed dolor. Phasellus et pharetra sem. Pellentesque sed rutrum enim. Nullam eget tortor ultrices, gravida purus sed, semper ipsum. Aliquam erat volutpat.");
	$ss->set_value("email.account_activate","Activating your account proves to us that you own this email address. Your account may be removed later if you do not activate it.");

	echo "GUESS CONTEST:<br />";
	// Setup for GuessContest 2015-07-26
	$gc = new GuessContest($con);
	$gc->setup();

	echo "EBOOKS BLOG MANAGER:<br />";
	$ebooks = new EbooksBlogMgr($con);

	// Setup for database modifications 2015-10-21
	$ebooks->destroy_records();
	$ebooks->setup();


	echo "Everything worked out fine, I think...<br />";
};

$page_controller = new Controller($methods);
unset($methods);
