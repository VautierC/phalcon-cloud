<?php

class MyDisquesController extends \ControllerBase {
	/**
	 * Affiches les disque de l'utilisateur
	 */
	public function indexAction(){
		$cpt=0;
		$user=Auth::getUser($this);
		if(!(is_null($user))){
			$disquesUser=Disque::find(array(
				"conditions" => "idUtilisateur = :id:",
				"bind" => array("id" => $user->id)));
			$listGroup=$this->jquery->bootstrap()->htmlListgroup("list",array());
			foreach($disquesUser as $disque){
				$occupation=ModelUtils::getDisqueOccupation($this->config->cloud,$disque);
				$tarif=Tarif::findFirst(ModelUtils::getDisqueTarif($disque));
				$cap=ModelUtils::sizeConverter($tarif->getUnite())*$tarif->getQuota();
				$usage=round((($occupation/ModelUtils::sizeConverter($tarif->getUnite()))/$tarif->getQuota()),2);
				if($usage<0.1)$style="info";
				else if($usage<0.5)$style="success";
				else if($usage<0.8)$style="warning";
				else $style="danger";

				$bootstrap=$this->jquery->bootstrap();
				$bt=$bootstrap->htmlButton("btScan".$disque->getId(),"Ouvrir","info");
				$bt->getOnClick("Scan/index/".$disque->getId(),"#content",array("attr"=>"data-ajax"));

				$liste=$this->jquery->bootstrap()->htmlListgroup("list"+$cpt,array(
					$disque->getNom(),
					$this->jquery->bootstrap()->htmlProgressbar("progress"+$cpt,$style,$usage*100)->setStriped(true)->showCaption(true),//->setStyleLimits(array("info"=>10,"success"=>50,"warning"=>80,"danger"=>100))
					$bt
				));
				$badge=round(($occupation/ModelUtils::sizeConverter($tarif->getUnite())),2)." ".$tarif->getUnite()." / ".$tarif->getQuota()." ".$tarif->getUnite();
				$liste->getItem(0)->addBadge($badge);
				$listGroup->addItem($liste);

				$cpt++;
			}
			$btCrea=$this->jquery->bootstrap()->htmlButton("btCrea","Créer un disque","primary");
			$btCrea->getOnClick("Disques/frm","#content",array("attr"=>"data-ajax"));

			$this->view->setVars(array("userCo"=>true,"liste"=>$listGroup,"disquesUser"=>$disquesUser,"user"=>$user));
			$this->jquery->compile($this->view);

		}
		else{
			$bt=$this->jquery->bootstrap()->htmlButton("btCo","Se connecter","connexion");
			$bt->getOnClick("Auth/index","#content",array("attr"=>"data-ajax"));

			$this->view->setVars(array("userCo"=>false));
			$this->jquery->compile($this->view);
		}

	}
}