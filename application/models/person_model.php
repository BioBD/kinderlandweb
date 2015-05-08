<?php
require_once APPPATH . 'core/CK_Model.php';
class person_model extends CK_Model{

	public function __construct(){
		parent::__construct();
	}

	public function insertNewPerson($fullname, $gender, $email, $addressId){
		$this->Logger->info("Running: " . __METHOD__);

		$sql = 'INSERT INTO person (fullname, date_created, gender, email, address_id) VALUES (?, current_timestamp, ?, ?, ?)';
		$returnId = $this->executeReturningId($this->db, $sql, array($fullname, $gender, $email, intval($addressId)));
		if($returnId)
			return $returnId;

		return false;
	}

	public function insertPersonSimple($fullname, $gender){
		$this->Logger->info("Running: " . __METHOD__);

		$sql = 'INSERT INTO person (fullname, date_created, gender) VALUES (?, current_timestamp, ?)';
		$returnId = $this->executeReturningId($this->db, $sql, array($fullname, $gender));
		if($returnId)
			return $returnId;

		return false;
	}

 	public function updatePerson($fullname, $gender, $email, $person_id, $address_id) {
 		$this->Logger->info("Running: " . __METHOD__);
 		
        $sql = "UPDATE person SET fullname=?, gender=?, email=?, address_id=? WHERE person_id=?";
        if ($this->execute($this->db, $sql, array($fullname, $gender, $email, $address_id, intval($person_id))))
            return true;
        return false;
    } 

    public function getPersonById($personId){
    	$this->Logger->info("Running: " . __METHOD__);
    	$sql = "SELECT * FROM person WHERE person_id = ?";
    	$result = $this->executeRow($this->db, $sql, array(intval($personId)));

    	if($result)
    		return Person::createPersonObjectSimple($result);

    	return null;
    }

    public function getPersonFullById($personId){
    	$this->Logger->info("Running: " . __METHOD__);
    	$sql = "SELECT *, (SELECT phone_number FROM telephone WHERE person_id = ? LIMIT 1) AS phone1,
                    (SELECT phone_number FROM telephone WHERE person_id = ? LIMIT 1 OFFSET 1) AS phone2
    			FROM person p
    			LEFT JOIN address a on a.address_id = p.address_id
    			LEFT JOIN person_user pu on pu.person_id = p.person_id
    			WHERE p.person_id = ?";
    	$result = $this->executeRow($this->db, $sql, array(intval($personId), intval($personId), intval($personId)));

    	if(!$result)
    		return null;

    	return $result;
    }
	
	public function emailExists($email) {
		$sql = "SELECT * FROM person WHERE email=?";
		$resultSet = $this -> executeRow($this -> db, $sql, array($email));

		if ($resultSet)
			return true;

		return false;
	}
}
?>