<?php

class ScanController extends ControllerBase {

	/**
	 * Affiche les informations relatives à l'id du disque passé en paramétre
	 * @param int $idDisque id du disque à afficher
	 */
	public function indexAction($idDisque) {
		//TODO 4.3
		$disque=Disque::findFirst(array(
			"conditions" => "id = :id:",
			"bind" => array("id" => $idDisque)));
		$diskName=$disque->getNom();
		$services=$disque->getServices();
		$occupation=ModelUtils::getDisqueOccupation($this->config->cloud,$disque);
		$tarif=Tarif::findFirst(ModelUtils::getDisqueTarif($disque));
		$usage=round((($occupation/ModelUtils::sizeConverter($tarif->getUnite()))/$tarif->getQuota()),2);

		$user=Utilisateur::findFirst(array(
			"conditions"=>"id=:id:",
			"bind"=> array("id"=>$disque->getIdUtilisateur())
		));
		$liste=$this->jquery->bootstrap()->htmlListgroup("liste");
		$liste->addItem(array("Nom :".$disque->getNom()."&nbsp".
				$this->jquery->bootstrap()->htmlButton("btModifNom","Modifier","default")->getOnClick("Scan/frm/".$disque->getId(),"#content",array("attr"=>"data-ajax"))));
		$liste->addItem("Propriétaire : ".$user->getLogin()." (".$user->getPrenom()." ".$user->getNom().")");
		$liste->addItem(array(
			"Occupation",
			round(($occupation/ModelUtils::sizeConverter($tarif->getUnite())),2).$tarif->getUnite()." (".($usage*100)."%) sur ".$tarif->getQuota()." ".$tarif->getUnite()
		));
		if($usage*100<10)$liste->getItem(2)->addLabel("Peu occupé","info");
		else if($usage*100<50)$liste->getItem(2)->addLabel("RAS","success");
		else if($usage*100<80)$liste->getItem(2)->addLabel("Forte occupation","warning");
		else $liste->getItem(2)->addLabel("Proche Saturation","danger");

		$liste->addItem(array(
			"Tarification",
			"prix : ".$tarif->getPrix()."€, Marge de dépassement : ".($tarif->getMargeDepassement()*100)."%, coût dépassement : ".$tarif->getCoutDepassement()."€",
			//$this->jquery->bootstrap()->htmlButton("btModifTarif","Modifier","default")
		));

		$liste->addItem(array(
			"Services"
		));
		foreach($services as $service){
			$liste->getItem(4)->addLabel($service->getNom(),"info");
		}
		$this->jquery->execOn("click", "#ckSelectAll", "$('.toDelete').prop('checked', $(this).prop('checked'));$('#btDelete').toggle($('.toDelete:checked').length>0)");
		$this->jquery->execOn("click","#btUpload","$('#tabsMenu a:last').tab('show');");
		$this->jquery->doJQueryOn("click","#btDelete", "#panelConfirmDelete", "show");
		$this->jquery->postOn("click", "#btConfirmDelete", "scan/delete","$('.toDelete:checked').serialize()","#ajaxResponse");
		$this->jquery->doJQueryOn("click", "#btFrmCreateFolder", "#panelCreateFolder", "toggle");
		$this->jquery->postFormOn("click", "#btCreateFolder", "Scan/createFolder", "frmCreateFolder","#ajaxResponse");
		$this->jquery->exec("window.location.hash='';scan('".$diskName."')",true);

		$bt=$this->jquery->bootstrap()->htmlButton("btRetour","Fermer et retourner à Mes disque","primary");
		$bt->setProperty("data-ajax", "MyDisques");
		$this->jquery->getOnClick("a.btn, button.btn","","#content",array("attr"=>"data-ajax"));

		$this->view->setVars(array("liste"=>$liste,"disque"=>$disque));
		$this->jquery->compile($this->view);
	}

	/**
	 * Etablit le listing au format JSON du contenu d'un disque
	 * @param string $dir Disque dont le contenu est à lister
	 */
	public function filesAction($dir="Datas"){
		$this->view->disable();
		$cloud=$this->config->cloud;
		$root=$cloud->root.$cloud->prefix.$this->session->get("activeUser")->getLogin()."/";
		$response = DirectoryUtils::scan($root.$dir,$root);
		header('Content-type: application/json');
		echo json_encode(array(
				"name" => $dir,
				"type" => "folder",
				"path" => $dir,
				"items" => $response,
				"root" => $root
		));
	}

	public function frmAction($id=NULL){
		$disque=Disque::findFirst($id);
		$this->view->setVars(array("disque"=>$disque,"siteUrl"=>$this->url->getBaseUri(),"baseHref"=>$this->dispatcher->getControllerName()));
	}

	public function updateAction(){
		$newDisque=new Disque();
		$newDisque->setNom($_POST['nom']);
		$newDisque->setId($_POST['id']);
		$newDisque->setIdUtilisateur($_POST['idUtil']);
		if($newDisque->save() == false)
			echo "Erreur d'enregistrement";
		else echo "Succes de l'enregistrement";

		$this->_postUpdateAction($_POST);
	}

	protected function _postUpdateAction($params){
		$this->dispatcher->forward(array("controller"=>"Scan","action"=>"index","params"=>array($params['id'])));
	}
	/**
	 * Action d'upload d'un fichier
	 */
	public function uploadAction(){
		$this->view->disable();
		header('Content-Type: application/json');
		$allowed = array('png', 'jpg', 'gif','zip');
		if(isset($_FILES['upl']) && $_FILES['upl']['error'] == 0){
			$extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);
			if(!in_array(strtolower($extension), $allowed)){
				echo '{"status":"error"}';
				exit;
			}
			if(move_uploaded_file($_FILES['upl']['tmp_name'], $_POST["activeFolder"].'/'.$_FILES['upl']['name'])){
				echo '{"status":"success"}';
				exit;
			}
		}
		echo '{"status":"error"}';
	}

	/**
	 * Action de suppresion d'un fichier
	 */
	public function deleteAction(){
		$message=array();
		if(array_key_exists("toDelete", $_POST)){
			foreach ($_POST["toDelete"] as $f){
				if(DirectoryUtils::deleteFile($f)===false){
					$message[]="Impossible de supprimer `{$f}`";
				}
			}
			if(sizeof($message)==0){
				$this->jquery->exec("scan()",true);
			}else{
				echo $this->showMessage(implode("<br>", $message), "warning");
			}
			$this->jquery->doJquery("#panelConfirmDelete", "hide");
			echo $this->jquery->compile();
		}
	}

	public function createFolderAction(){
		if(array_key_exists("folderName", $_POST)){
			$pathname=$_POST["activeFolder"].DIRECTORY_SEPARATOR.$_POST["folderName"];
			if(DirectoryUtils::mkdir($pathname)===false){
				echo $this->showMessage("Impossible de créer le dossier `".$pathname."`", "warning");
			}else{
				$this->jquery->exec("scan()",true);
			}
			$this->jquery->doJquery("#panelCreateFolder", "hide");
			echo $this->jquery->compile();
		}
	}

	/**
	 * Action permettant de mettre à jour l'historique du jour de tous les diques
	 */
	public function updateAllDaySizeAction(){
		$cloud=$this->config->cloud;
		DirectoryUtils::updateAllDaySize($cloud);
	}

	/**
	 * Affiche un message dans une alert Bootstrap
	 * @param String $message
	 * @param String $type Class css du message (info, warning...)
	 * @param number $timerInterval Temps d'affichage en ms
	 * @param string $dismissable Alert refermable
	 * @param string $visible
	 */
	public function showMessage($message,$type,$timerInterval=5000,$dismissable=true,$visible=true){
		$message=new DisplayedMessage($message,$type,$timerInterval,$dismissable,$visible);
		return $message->compile($this->jquery);
	}
}