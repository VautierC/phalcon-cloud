<?php
class DisquesController extends \DefaultController {
	public function initialize(){
		parent::initialize();
		$this->model="Disque";
	}

    /**
     * Affecte membre à membre les valeurs du tableau associatif $_POST aux membres de l'objet $object<br>
     * Prévoir une sur-définition de la méthode pour l'affectation des membres de type objet<br>
     * Cette méthode est utilisée par update()
     * @see DefaultController::update()
     * @param multitype:$className $object
     */
	protected function setValuesToObject(&$object) {
		parent::setValuesToObject($object);
		//TODO 4.4.1
	}

	public function frmAction($id=NULL){
		$disque=$this->getInstance($id);
		$this->view->setVars(array("disque"=>$disque,"siteUrl"=>$this->url->getBaseUri(),"baseHref"=>"Disque"));
		parent::frmAction($id);
	}

	public function addDisqueAction($disque){
		$newDisque=new Disque();
		$newDisque->setNom($_POST['nom']);
		$newDisque->setId($disque->getId());
		$newDisque->setIdUtilisateur($disque->getIdUtilisateur());
		if($newDisque->save() == false)
			echo "Erreur d'enregistrement";
		else echo "Succes de l'enregistrement";
	}
	/**
	 * Action à exécuter après update
	 * par défaut forward vers l'index du contrôleur en cours
	 * @param array $params
	 */
	protected function _postUpdateAction($params){
		//TODO 4.4.1
	}


	protected function _deleteMessage($object){
		return "Confirmez-vous la suppression du disque <b>".$object."</b> ?";
	}
}