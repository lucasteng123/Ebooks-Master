<?php
class GuessContestException extends Exception {
}

class GuessContest {
	private $pdo = null;
	private $lastInsertId = null;

	function __construct($pdo) {
		if ( !(gettype($pdo) === 'object') ) {
			FrameworkException::throw_datatype_exception(VarTools::what_is($pdo), 'PDO (object)');
		}
		$this->pdo = $pdo;
		$this->wd = realpath(dirname(__FILE__));
	}
	function setup() {
		$sql = file_get_contents($this->wd . "/table_creation.sql");
		$qr = $this->pdo->exec($sql);
	}

	function add_contestant($name,$email,$guess) {
		$contestID = $this->obtain_contest_id();
		// Check if email is already entered in the contest
		$sql = "SELECT COUNT(*) FROM gc_contestants WHERE email=:email AND contest=:contest";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("email",    $email,     PDO::PARAM_STR );
		$stmt->bindValue("contest",  $contestID, PDO::PARAM_INT );
		$stmt->execute();
		if ($stmt->fetchColumn()) {
			throw new GuessContestException("This email has already been used!");
		}
		// Enter the new email into the contest
		$sql = "INSERT INTO gc_contestants (name, email, guess, contest, date_entered) VALUES (:name,:email,:guess,:contest, now())";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("name",     $name,    PDO::PARAM_STR );
		$stmt->bindValue("email",    $email,   PDO::PARAM_STR );
		$stmt->bindValue("guess",    $guess,   PDO::PARAM_STR );
		$stmt->bindValue("contest",  $contestID, PDO::PARAM_INT );
		$stmt->execute();
		$this->last_entry_id = $this->pdo->lastInsertId();
		return $this->last_entry_id;
		
	}
	function get_number_of_contestants() {
		$contestID = $this->obtain_contest_id();
		$sql = "SELECT COUNT(*) FROM gc_contestants WHERE contest=:contest;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("contest",  $contestID, PDO::PARAM_INT );
		$stmt->execute();
		return $stmt->fetchColumn();
	}
	function list_contestants_in($contestID) {
		$sql = "SELECT * FROM gc_contestants WHERE contest=:contest";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("contest",  $contestID, PDO::PARAM_INT );
		$stmt->execute();

		$results = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$results[] = $row;
		}
		return $results;
	}
	function end_contest() {
		$sql = "UPDATE gc_contests SET date_closed = now() WHERE date_closed IS NULL";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
	}
	function begin_contest($phrase) {
		/*
			I thought about hashing the phrase, but since it's going to
			be a word of a known length that is intended to be guess-able,
			I decided there was no worthwhile security gain in doing so.
		*/
		$sql = "INSERT INTO gc_contests (phrase, date_posted) VALUES (:phrase, now())";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("phrase",  $phrase, PDO::PARAM_STR );
		$stmt->execute();
		$this->last_entry_id = $this->pdo->lastInsertId();
		return $this->last_entry_id;
	}
	function check_contest_running() {
		$sql = "SELECT COUNT(*) FROM gc_contests WHERE date_closed IS NULL;";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("email",    $email,     PDO::PARAM_STR );
		$stmt->bindValue("contest",  $contestID, PDO::PARAM_INT );
		$stmt->execute();
		if ($stmt->fetchColumn() > 0) {
			return true;
		}
		return false;
	}
	function get_current_word() {
		$contest = $this->obtain_contest_record();
		return $contest['phrase'];
	}
	function get_current_word_length() {
		$contest = $this->obtain_contest_record();
		return strlen($contest['phrase']);
	}
	function get_contest_phrase($contestID) {
		$contest = $this->obtain_contest_record_from_id($contestID);
		return $contest['phrase'];
	}
	function list_contests() {
		$sql = "SELECT * FROM gc_contests ORDER BY date_posted DESC";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();

		$results = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$results[] = $row;
		}
		return $results;
	}

	// names roll
	function add_name_to_roll($name) {
		$sql = "INSERT INTO gc_names_roll (name, date_posted) VALUES (:name, now())";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("name",  $name, PDO::PARAM_STR );
		$stmt->execute();
		$this->last_entry_id = $this->pdo->lastInsertId();
		return $this->last_entry_id;
	}
	function list_roll() {
		$sql = "SELECT * FROM gc_names_roll ORDER BY date_posted DESC";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();

		$results = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$results[] = $row;
		}
		return $results;
	}
	function list_roll_limit($limit) {
		$sql = "SELECT * FROM gc_names_roll ORDER BY date_posted DESC LIMIT :lim";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("lim",  $limit, PDO::PARAM_INT );
		$stmt->execute();

		$results = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$results[] = $row;
		}
		return $results;
	}
	function remove_name($nameID) {
		$sql = "DELETE FROM gc_names_roll WHERE id=:id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("id",  $nameID, PDO::PARAM_INT );
		$stmt->execute();
	}

	private function obtain_contest_id() {
		$sql = "SELECT id FROM gc_contests WHERE date_closed IS NULL";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();

		if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			return intval($row['id']);
		} else {
			throw new GuessContestException("There is no contest running!");
		}
	}
	private function obtain_contest_record() {
		$sql = "SELECT * FROM gc_contests WHERE date_closed IS NULL";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();

		if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			return $row;
		} else {
			throw new GuessContestException("There is no contest running!");
		}
	}
	private function obtain_contest_record_from_id($id) {
		$sql = "SELECT * FROM gc_contests WHERE id=:id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("id",  $id, PDO::PARAM_INT );
		$stmt->execute();

		if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			return $row;
		} else {
			throw new GuessContestException("Tried to access non-existant contest!");
		}
	}
}