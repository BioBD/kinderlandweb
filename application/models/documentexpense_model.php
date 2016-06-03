<?php

require_once APPPATH . 'core/CK_Model.php';
require_once APPPATH . 'core/document_expense.php';
require_once APPPATH . 'core/bankdata.php';

class documentexpense_model extends CK_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getAllDocumentsExpense() {
        $sql = " SELECT * FROM document_expense ORDER BY document_date DESC";
        $resultSet = $this->executeRows($this->db, $sql);

        $documentArray = array();

        if ($resultSet)
            foreach ($resultSet as $row)
                $documentArray[] = DocumentExpense::createDocumentExpenseObject($row);

        return $documentArray;
    }

    public function getDocumentExpensePaidById($documentexpenseId) {
        $sql = "select distinct document_expense_id
				from posting_expense
				where document_expense_id = ?
				";
        $resultSet = $this->executeRows($this->db, $sql, array($documentexpenseId));

        if ($resultSet)
            return $resultSet;
        else
            return null;
    }

    public function getAllBankData() {
        $sql = "SELECT * FROM bank_data";
        $resultSet = $this->executeRows($this->db, $sql);

        $bankdataArray = array();

        if ($resultSet)
            foreach ($resultSet as $row)
                $bankdataArray[] = BankData::createBankDataObject($row);

        return $bankdataArray;
    }

    public function getDocumentById($id) {
        $sql = "SELECT * FROM document_expense WHERE document_expense_id=?";
        $document = $this->executeRow($this->db, $sql, $id);
        if ($document) {
            $documentObject = DocumentExpense::createDocumentExpenseObject($document);
            return $documentObject;
        }
        return false;
    }

    public function insertNewPostingExpense($documentexpenseId, $postingDate, $postingValue, $postingType, $postingPortion) {
        $sql = "INSERT into posting_expense (document_expense_id, posting_date, posting_value, posting_type, posting_portions)
					VALUES (?,?,?,?,?)";
        $result = $this->execute($this->db, $sql, array($documentexpenseId, $postingDate, $postingValue, $postingType, $postingPortion));
        return $result;
    }


    public function insertNewBankTransferPayment($bankDataId, $documentexpenseId, $postingValue, $postingPortion) {
        $sql = "INSERT into posting_bank_transfer(bank_data_id, document_expense_id, posting_value, posting_portions)
					VALUES (?,?,?,?)";
        $Id = $this->execute($this->db, $sql, array($bankDataId, $documentexpenseId, $postingValue, $postingPortion));
        return $Id;
    }
    
    public function insertNewBankSlip($postingDate, $documentexpenseId, $postingValue, $postingPortion) {
    	$sql = "INSERT into posting_bank_slip(bank_slip_date, document_expense_id, posting_value, posting_portions)
					VALUES (?,?,?,?)";
    	$Id = $this->execute($this->db, $sql, array($postingDate, $documentexpenseId, $postingValue, $postingPortion));
    	return $Id;
    }
    
    public function insertNewBankData($bankNumber, $bankAgency, $accountNumber){
    	
    	$sql = "INSERT into bank_data (bank_number, bank_agency, account_number)
					VALUES (?,?,?)";
    	$Id = $this->executeReturningId($this->db, $sql, array($bankNumber, $bankAgency, $accountNumber));
    	return $Id;
    }

    public function inserNewBankCheckPayment($numberCheque, $documentexpenseId, $postingDate, $postingValue) {
        $sql = "INSERT into posting_bank_check(check_number, document_expense_id, posting_date, posting_value)
					VALUES (?,?,?,?)";

        $Id = $this->execute($this->db, $sql, array($numberCheque, $documentexpenseId, $postingDate, $postingValue));
        return $Id;
    }

    public function InsertNewDocument($date, $number, $description, $type, $value, $name) {
        $sql = "INSERT INTO document_expense (document_number,document_date,document_type,description,document_value, document_name)
                  VALUES (?,?,?,?,?,?)";
        $Id = $this->executeReturningId($this->db, $sql, array($number, $date, $type, $description, $value, $name));
        return $Id;
    }

    public function updateDocument($id, $date, $number, $description, $value, $name) {
        $sql = "UPDATE document_expense SET "
                . "document_date = ?, "
                . "document_number=?, "
                . "description=?, "
                . "document_value=?, "
                . "document_name=? "
                . "WHERE document_expense_id='?'";
        $resultSet = $this->execute($this->db, $sql, array($date, $number, $description, $value, $name, intval($id)));
        return $resultSet;
    }

    public function deleteAllExpenses($id) {
        $sql = "DELETE FROM posting_expense WHERE document_expense_id='?'";
        $resultSet = $this->execute($this->db, $sql, array(intval($id)));
        return $resultSet;
    }

    public function deleteDocument($id) {
        $sql = "DELETE FROM document_expense WHERE document_expense_id='?'";
        $resultSet = $this->execute($this->db, $sql, array(intval($id)));
        return $resultSet;
    }

    public function uploadDocument($fileName, $file,$operation) {
        $this->Logger->info("Running: " . __METHOD__);

        $splitByDot = explode(".", $fileName);
        $extension = $splitByDot[count($splitByDot) - 1];
        if (!
                (strcasecmp("jpg", $extension) == 0 || strcasecmp("jpeg", $extension) == 0 || strcasecmp("png", $extension) == 0 || strcasecmp("pdf", $extension) == 0)
        )
            return FALSE;
        $sql = 'INSERT INTO document_expense_upload (filename,extension,operation,file) VALUES (?, ?, ?, ?)';
        $returnId = $this->execute($this->db, $sql, array($fileName, $extension, $operation, pg_escape_bytea($file)));
        if ($returnId) {
            $this->Logger->info("Documento inserido com sucesso");
            return TRUE;
        }
        $this->Logger->error("Problema ao inserir documento");
        return FALSE;
    }
    
        public function updateUploadDocument($upload_id,$fileName, $file,$operation) {
        $this->Logger->info("Running: " . __METHOD__);
$this->Logger->info("O ID AQUI: " . $upload_id);
        $splitByDot = explode(".", $fileName);
        $extension = $splitByDot[count($splitByDot) - 1];
        if (!
                (strcasecmp("jpg", $extension) == 0 || strcasecmp("jpeg", $extension) == 0 || strcasecmp("png", $extension) == 0 || strcasecmp("pdf", $extension) == 0)
        )
            return FALSE;
        $sql = 'UPDATE document_expense_upload '
                . 'SET filename=?, extension=?, operation=?, file=? '
                . 'WHERE document_expense_upload_id=?';
        $returnId = $this->execute($this->db, $sql, array($fileName, $extension, $operation, pg_escape_bytea($file),intval($upload_id)));
        if ($returnId) {
            $this->Logger->info("Documento modificado com sucesso");
            return TRUE;
        }
        $this->Logger->error("Problema ao modificado documento");
        return FALSE;
    }
    
        public function getUploadById($id) {
        $this->Logger->info("Running: " . __METHOD__);

        $sql = 'Select * from document_expense_upload where document_expense_upload_id = ?';
        $row = $this->executeRow($this->db, $sql, array($id));

        $document = FALSE;

        if ($row){
                $this->Logger->info("Documento encontrado com sucesso, criando array");
                $document = array("data" => $row->file, "name" => $row->filename, "extension" => $row->extension);
                return $document;
            }
        $this->Logger->info("Nao achei o documento");
        return $document;
    }
    
    public function getUploadId($document_id){
        $sql = "SELECT document_expense_upload_id FROM document_expense WHERE document_expense_id=?";
        $uploadId=$this->executeRow($this->db,$sql,array(intval($document_id)));
        if ($uploadId){
            return $uploadId;
        }
        return FALSE;     
    }
    
    public function getNewUpload(){
        $sql="SELECT document_expense_upload_id FROM document_expense_upload ORDER BY date_created DESC LIMIT 1";
        $new=$this->executeRow($this->db,$sql);
        if($new)
            return $new->document_expense_upload_id;
        return FALSE;   
    }
    
    public function attatchUploadId($document_id,$upload_id){
        $sql="UPDATE document_expense SET document_expense_upload_id=? WHERE document_expense_id=?";
        $result=$this->execute($this->db,$sql,array(intval($upload_id),intval($document_id)));
    }
    
    public function deleteUpload($upload_id){
        $sql="DELETE FROM document_expense_upload WHERE document_expense_upload_id=?";
        $result=$this->execute($this->db,$sql,array(intval($upload_id)));
        return $result;
    }
   
}

?>