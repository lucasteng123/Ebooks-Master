<?php
class ClientFormPtr {
	private $id;
	function __construct($id) {
		$this->id = $id;
	}
	function id() {
		return $this->id;
	}
}
class ClientFormLib_Field {
	const REQUIRED = 1;
	private $name = '';
	private $type = 'string';
	private $flags = 0;

	private $miscVars = array();
	function __set($name, $value) {
		$this->miscVars[$name] = $value;
	}
	function __get($name) {
		return $this->miscVars[$name];
	}

	function __construct($name, $type) {
		$this->name = $name;
		$this->type = $type;
	}
	function get_name() {
		return $this->name;
	}
	function get_type() {
		return $this->type;
	}
	function get_flags() {
		return $this->flags;
	}
	function set_flags($flags) {
		$this->flags = $flags;
	}
	function check_flag($flag) {
		return (($flags & $flag) > 0);
	}
	function set_required($bool) {
		if ($bool) {
			$this->flags = $this->flags | self::REQUIRED;
		} else {
			$this->flags = $this->flags ^ self::REQUIRED;
		}
	}
	static function get_text_field_array() {
		$field_names = func_get_args();
		$field_objects = array();
		foreach ($field_names as $name) {
			$obj = new ClientFormLib_Field($name, "string");
			$field_objects[] = $obj;
		}
		return $field_objects;
	}
}

// TODO:
// Turn ClientFormLib into a factory class used to obtain a query object.
class ClientFormLib {
	private $pdo = null;
	private $wd = null;
	function __construct($pdo) {
		if ( !(gettype($pdo) === 'object') ) {
			FrameworkException::throw_datatype_exception(VarTools::what_is($pdo), 'PDO');
		}
		$this->pdo = $pdo;
		$this->wd = realpath(dirname(__FILE__));
	}
	function setup() {
		$sql = file_get_contents($this->wd . "/table_creation.sql");
		$this->pdo->query($sql);
	}

	// === CREATION FUNCTIONS
	function apply_fields($form, $fields, $makeRequired = true) {
		$form_id = ($form instanceof ClientFormPtr) ? $form->id() : $this->get_id_from_form($form);
		// it's assumed that $form_id is valid here, since the exception
		// would've otherwise been thrown by get_id_from_form (todo)
		foreach ($fields as $field) {
			$field->set_required($makeRequired);
			if ($this->check_field_not_dupe($form_id,$field)) {
				$sql = "INSERT INTO cfl_fields (form_id,name,type,flags) VALUES (:form_id, :name, :type, :flags)";
				$stmt = $this->pdo->prepare($sql);

				$stmt->bindValue( "form_id", $form_id, PDO::PARAM_STR );
				$stmt->bindValue( "name", $field->get_name(), PDO::PARAM_STR );
				$stmt->bindValue( "type", $field->get_type(), PDO::PARAM_STR );
				$stmt->bindValue( "flags", $field->get_flags(), PDO::PARAM_INT );
				$stmt->execute();
			}
		}
	}
	public function create_form($name) {
		$sql = "INSERT INTO cfl_forms (name) VALUES (:name)";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("name", $name);
		$stmt->execute();
		$formId = $this->pdo->lastInsertId();
		return $formId;
	}
	public function add_entry_from_value_array($form, $values_dict) {
		echo "THIS FUNCTION WAS CALLED";
		$form_id = $this->get_id_from_form($form);

		// Get existing fields from database
		$fields = $this->get_fields_from_form($form_id);
		$valuesToUse = array();

		// Loop through existing fields
		foreach ($fields as $field) {
			$fname = $field->get_name();
			// Check that required keys exist
			if (array_key_exists($fname, $values_dict)) {
				$valuesToUse[$fname] = $values_dict[$fname];
				unset($values_dict[$fname]);

			} else {
				if ( $field->check_flag(ClientFormLib_Field::REQUIRED) ) {
					throw new Exception("ClientFormLib: Missing a required field in value dictionary.");
				}
				$valuesToUse[$fname] = null;
			}
		}

		// Loop through reamining entry fields
		if (count($values_dict) > 0) {
			echo "MUFFIN";
			$fieldsToAdd = array();
			foreach ($values_dict as $key => $value) {
				$fieldsToAdd[] = new ClientFormLib_Field($key,gettype($value));
			}
			$this->apply_fields( new ClientFormPtr($form_id) , $fieldsToAdd , false );
			$fields = $this->get_fields_from_form($form_id);
			foreach ($values_dict as $key => $value) {
				$valuesToUse[$key] = $value;
			}
		}

		// Create entry
		$sql = "INSERT INTO cfl_entries (form_id, entry_date) VALUES (:form_id, now())";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("form_id", $form_id);
		$stmt->execute();
		$entryId = $this->pdo->lastInsertId();

		foreach ($fields as $field) {
			$sql = "INSERT INTO cfl_values (entry_id, field_id, value) VALUES (:entry_id, :field_id, :value)";
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindValue("entry_id", $entryId);
			$stmt->bindValue("field_id", $field->id);
			$stmt->bindValue("value", $valuesToUse[$field->get_name()]);
			$stmt->execute();
		}


	}

	// === ALTER FUNCTIONS
	public function remove_form($form) {
		$form_id = $this->get_id_from_form($form);
		$sql = <<<'SQL'
DELETE v.* FROM cfl_values v
INNER JOIN cfl_fields f
ON v.field_id = f.id
WHERE f.form_id = :form_id;

DELETE FROM cfl_fields WHERE form_id=:form_id;

DELETE FROM cfl_entries WHERE form_id=:form_id;

DELETE FROM cfl_forms WHERE id=:form_id;
SQL;
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("form_id", $form_id);
		$stmt->execute();
	}

	// === QUERY FUNCTIONS
	public function get_everything_from_form($form) {
		$form_id = $this->get_id_from_form($form);
		$arrayOfThings = array();

		$sql = <<<'SQL'
SELECT v.entry_id, f.name, v.value FROM testing_db.cfl_values v
INNER JOIN testing_db.cfl_fields f
ON f.id = v.field_id
INNER JOIN testing_db.cfl_forms t
ON t.id = f.form_id
WHERE t.id = :form_id;
SQL;
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue("form_id", $form_id);
		$stmt->execute();

		// Populate list of entries
		while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
			$arrayOfThings[$row['entry_id']][$row['name']] = $row['value'];
		}
		
		return $arrayOfThings;
	}
	private function get_id_from_field($field) {

	}

	// === PRIVATE METHODS
	private function create_entry($form, $values_dict) {
		

		$fields = $this->get_fields_from_form();
		foreach ($fields as $field) {
			$sql = "INSERT INTO cfl_values (entry_id, field_id) VALUES (:entry_id, :field_id)";
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindValue("entry_id", $entryId);
			$stmt->bindValue("field_id", $field->id);
			$stmt->execute();
			$entryId = $this->pdo->lastInsertId();
		}
		return array($entryId, $fields);
	}
	private function get_fields_from_form($form_id) {
		$sql = "SELECT id,name,type,flags FROM cfl_fields WHERE form_id=:form_id";

		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue( "form_id", $form_id, PDO::PARAM_STR );
		$stmt->execute();

		$fields = array();
		while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
			$obj = new ClientFormLib_Field($row['name'], $row['type']);
			$obj->set_flags($row['flags']);
			$obj->id = $row['id'];
			$fields[] = $obj;
		}
		
		return $fields;
	}
	private function check_field_not_dupe($form_id, $field) {
		$sql = "SELECT id FROM cfl_fields WHERE name=:name AND form_id=:form_id";

		// Obtain id of form
	 	$stmt = $this->pdo->prepare($sql);
	 	$stmt->bindValue( "form_id", $form_id, PDO::PARAM_STR );
	 	$stmt->bindValue( "name", $field->get_name(), PDO::PARAM_STR );
	 	$stmt->execute();
	 	$row = '';
	 	if ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	 		return false;
	 	} else {
	 		return true;
	 	}
	}
	private function get_id_from_form($form) {
		$sql = "SELECT id FROM cfl_forms WHERE name=:name";

		// Obtain id of form
	 	$stmt = $this->pdo->prepare($sql);
	 	$stmt->bindValue( "name", $form, PDO::PARAM_STR );
	 	$stmt->execute();
	 	$row = '';
	 	if (!( $row = $stmt->fetch(PDO::FETCH_ASSOC) )) {
	 		$this->create_form($form);
	 		$stmt = $this->pdo->prepare($sql);
		 	$stmt->bindValue( "name", $form, PDO::PARAM_STR );
		 	$stmt->execute();
		 	if (!( $row = $stmt->fetch(PDO::FETCH_ASSOC) )) {
		 		throw new Exception("ClientFormLib: Failed to create a new form! :O");
		 	}
	 	}
	 	$form_id = $row['id'];
	 	// TODO: Check that id is a number, else throw exception
	 	return $form_id;
	}
}
$path = realpath(dirname(__FILE__));
?>