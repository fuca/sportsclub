<?php

namespace App\WallsModule\Forms;

use \App\Forms\BaseForm,
    \App\Model\Misc\Enum\FormMode,
    \Nette\Application\UI\Form,
    \App\Services\Exceptions\DuplicateEntryException,
    \Nette\DateTime,
    Vodacek\Forms\Controls\DateInput,
    \App\Model\Misc\Enum\CommentMode,
    \App\Model\Misc\Enum\ArticleStatus;

/**
 * Form for creating and updating wallposts
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 * @package sportsclub
 */
final class WallPostForm extends BaseForm {
    
    /** @var Sport group list */
    private $sportGroups;

    /** @var users list */
    private $users;
    
    public function getStates() {
	return ArticleStatus::getOptions();
    }

    private function getCommentModes() {
	return CommentMode::getOptions();
    }

    private function getSportGroups() {
	if (!isset($this->sportGroups)) {
	    throw new \Nette\InvalidStateException("Property sportGroups is not setted up correctly, use appropriate setter first");
	}
	return $this->sportGroups;
    }

    public function getUsers() {
	return $this->users;
    }

    public function setUsers($users) {
	$this->users = $users;
    }

    public function setSportGroups($groups) {
	$this->sportGroups = $groups;
    }

    public function initialize() {
	
	$this->addHidden("id");
	$this->addHidden("counter");
	
	$this->addText("title", "Titulek")
		->addRule(Form::FILLED, "Titulek musí být zadán")
		->setRequired("Titulek musí být zadán");
	
	$this->addDate("showFrom", "Zobrazit od", DateInput::TYPE_DATE)
		->addRule(Form::FILLED, "Začátek zobrazení musí být zadán")
		->setRequired("Začátek zobrazení musí být zadán");
	
	$this->addDate("showTo", "Zobrazit do", DateInput::TYPE_DATE)
		->addRule(Form::FILLED, "Konec zobrazení musí být zadán")
		->setRequired("Konec zobrazení musí být zadán");
	
	$this->addTextArea("content", "Obsah")
		->addRule(Form::FILLED, "Obsah musí být zadán")
		->setRequired("Obsah musí být zadán");
	
	$this->addCheckbox("highlight", "Zvýraznit");
	
	$this->addSelect("status", "Stav", $this->getStates());
	
	$this->addSelect("commentMode", "Komentáře", $this->getCommentModes());
	
	$this->addCheckboxList("groups", "Skupiny", $this->getSportGroups());
	
	if ($this->isUpdate()) {
	    $this->addSelect("author", "Autor", $this->getUsers());
	    $this->addSelect("editor", "Poslední změna", $this->getUsers());
	}
	
	$this->addSubmit("submitButton");
	
	$this->onSuccess[] = callback($this->parent, 'wallPostFormSubmitted');
    }
}
