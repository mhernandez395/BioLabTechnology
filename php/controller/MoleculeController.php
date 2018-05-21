<?php
/**
* toDoClass class
* it controls the hole server part of the application
*/
require_once "ControllerInterface.php";
require_once "../model/Molecule.php";
require_once "../model/persist/MoleculeADO.php";


class MoleculeController implements ControllerInterface {
	private $action;
	private $jsonData;

	function __construct($action, $jsonData) {
		$this->setAction($action);
		$this->setJsonData($jsonData);
	}

	public function getAction() {
		return $this->action;
	}

	public function getJsonData() {
		return $this->jsonData;
	}

	public function setAction($action) {
		$this->action = $action;
	}
	public function setJsonData($jsonData) {
		$this->jsonData = $jsonData;
	}

	public function doAction()
	{
		$outPutData = array();

		switch ( $this->getAction() )
		{
			case 10000:
			$outPutData = $this->entryMolecule();
			break;
			case 10010:
			$outPutData = $this->loadMolecule();
			break;
			case 10020:
			$outPutData = $this->modifyMolecule();
			break;
			case 10030:
			$outPutData = $this->deleteMolecule();
			break;
			case 10040:
			$outPutData = $this->similarMolecule();
			break;
			default:
			$errors = array();
			$outPutData[0]=false;
			$errors[]="Sorry, there has been an error. Try later";
			$outPutData[]=$errors;
			error_log("Action not correct in MoleculeController, value: ".$this->getAction());
			break;
		}
		return $outPutData;
	}

	private function entryMolecule()	{
		$moleculesArray = json_decode(stripslashes($this->getJsonData()));
		$idsArray = array();
		foreach ($moleculesArray as $moleculeObj) {
			$molecule = new Molecule();
			$molecule->setAll(0, $moleculeObj->moleculeFormula, $moleculeObj->moleculeWeight, $moleculeObj->moleculeSimilarity1, $moleculeObj->moleculeSimilarity2);
			$molecule->setId(MoleculeADO::create($molecule));
			$idsArray[]=$molecule->getId();
		}
		$outPutData = array();
		$outPutData[]= true;
		//the senetnce returns de id's of the molecules inserted
		$outPutData[]= $idsArray;
		return $outPutData;
	}

	private function loadMolecule()	{
		$outPutData = array();
		$moleculesArray = MoleculeADO::findAll();
		if(count($moleculesArray) == 0)	{
			$outPutData[]= false;
			$errors = array();
			$errors[]="No molecules found in the database";
			$outPutData[] = $errors;
		}
		else {
			$outPutData[]= true;
			$moleculesToLocal = array();
			foreach ($moleculesArray as $molecule) {
				$moleculesToLocal[]=$molecule->getAll();
			}
			$outPutData[] = $moleculesToLocal;
		}
		return $outPutData;
	}
    
	private function modifyMolecule() {
		
		$moleculesArray = json_decode(stripslashes($this->getJsonData()));
		$outPutData = array();
		$outPutData[0]= true;
		foreach($moleculesArray as $moleculeObj) {
			$molecule = new Molecule();
			$molecule->setAll($moleculeObj->id, $moleculeObj->userId, $moleculeObj->rate, $moleculeObj->opinion);
			MoleculeADO::update($molecule);
		}
       
		return $outPutData;
	}

	private function deleteMolecule() {
		
		$moleculesArray = json_decode(stripslashes($this->getJsonData()));
		$outPutData = array();
		$outPutData[0]= true;
		foreach($moleculesArray as $moleculeObj) {
			$molecule = new Molecule();
			$molecule->setAll($moleculeObj->id, $moleculeObj->userId, $moleculeObj->rate, $moleculeObj->opinion);
			MoleculeADO::delete($molecule);
		}
		return $outPutData;
	}


	private function similarMolecule() {
		$outPutData = array();
		$moleculesArray = json_decode(MoleculeADO::findAllSimilary("CN1C(=O)C=C(c2cccc(Cl)c2)c3cc(ccc13)[C@@](N)(c4ccc(Cl)cc4)c5cncn5C")));
	var_dump($moleculesArray);
	exit();
		if(count($moleculesArray) == 0)	{
			$outPutData[]= false;
			$errors = array();
			$errors[]="No molecules found in the database";
			$outPutData[] = $errors;
		}
		else {
			$outPutData[]= true;
			$moleculesToLocal = array();
			foreach ($moleculesArray as $molecule) {
				$moleculesToLocal[]=$molecule->getAll();
			}
			$outPutData[] = $moleculesToLocal;
		}
		return $outPutData;
	}
}
?>