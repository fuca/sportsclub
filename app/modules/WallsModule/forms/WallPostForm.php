<?php

namespace App\WallsModule\Forms;

use \App\Forms\BaseForm,
    \App\Model\Misc\Enum\FormMode,
    \Nette\Application\UI\Form,
    \App\Services\Exceptions,
    \Nette\DateTime,
    Vodacek\Forms\Controls\DateInput,
    \App\Model\Misc\Enum\CommentMode,
    \App\Model\Misc\Enum\WallPostStatus;

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
	return WallPostStatus::getOptions();
    }

    private function getCommentModes() {
	return CommentMode::getOptions();
    }

    private function getSportGroups() {
	if (!isset($this->sportGroups)) {
	    throw new Exceptions\InvalidStateException("Property sportGroups is not setted up correctly, use appropriate setter first");
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

    /**
     * Initializes form's elements
     */
    public function initialize() {
	
	$this->addHidden("id");
	
	$this->addText("title", "wallsModule.admin.wallPostForm.title")
		->addRule(Form::FILLED, "wallsModule.admin.wallPostForm.titleMustFill")
		->setRequired("wallsModule.admin.wallPostForm.titleMustFill");
	
	$this->addDate("showFrom", "wallsModule.admin.wallPostForm.showFrom", DateInput::TYPE_DATE)
		->addRule(Form::FILLED, "wallsModule.admin.wallPostForm.showFromMustFill")
		->setRequired("wallsModule.admin.wallPostForm.showFromMustFill");
	
	$this->addDate("showTo", "wallsModule.admin.wallPostForm.showTo", DateInput::TYPE_DATE)
		->addRule(Form::FILLED, "wallsModule.admin.wallPostForm.showToMustFill")
		->setRequired("wallsModule.admin.wallPostForm.showToMustFill");
	
	$this->addTextArea("content", "wallsModule.admin.wallPostForm.content", 55, 15)
		->addRule(Form::FILLED, "wallsModule.admin.wallPostForm.contentMustFill")
		->setRequired("wallsModule.admin.wallPostForm.contentMustFill");
	
	$this->addCheckbox("highlight", "wallsModule.admin.wallPostForm.highlight");
	
	$this->addSelect("status", "wallsModule.admin.wallPostForm.status", $this->getStates());
	
	$this->addSelect("commentMode", "wallsModule.admin.wallPostForm.commentMode", $this->getCommentModes());
	
	$this->addCheckboxList("groups", "wallsModule.admin.wallPostForm.groups", $this->getSportGroups());
	
	if ($this->isUpdate()) {
	    $this->addSelect("author", "wallsModule.admin.wallPostForm.author", $this->getUsers());
	    $this->addSelect("editor", "wallsModule.admin.wallPostForm.editor", $this->getUsers());
	}
	
	$this->addSubmit("submitButton", "system.forms.submitButton.label");
	
	$this->onSuccess[] = callback($this->parent, 'wallPostFormSubmitted');
    }
}
