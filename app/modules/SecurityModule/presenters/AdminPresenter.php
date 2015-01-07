<?php

/*
 * Copyright 2014 Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\SecurityModule\Presenters;

use \App\SystemModule\Presenters\SystemAdminPresenter,
    \Nette\ArrayHash,
    \Grido\Grid,
    \App\SecurityModule\Forms\RoleForm,
    \App\Model\Misc\Enum\FormMode,
    \App\Model\Entities\Role,
    \App\Model\Misc\Exceptions,
    \App\SecurityModule\Forms\AclRuleForm,
    \App\Model\Entities\AclRule,
    \App\Model\Entities\Position,
    \App\SecurityModule\Forms\PositionForm,
    \App\Model\Misc\Enum\AclMode,
    \App\SecurityModule\Model\Misc\Annotations\Secured,
    \App\Model\Misc\Enum\AclPrivilege;

/**
 * AdminSecurityPresenter
 * @Secured(resource="SecurityAdmin")
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class AdminPresenter extends SystemAdminPresenter {

    /**
     * @inject
     * @var \App\Model\Service\IRoleService
     */
    public $roleService;

    /**
     * @inject
     * @var \App\Model\Service\IAclRuleService
     */
    public $ruleService;

    /**
     * @inject
     * @var \App\SecurityModule\Model\Service\IPositionService
     */
    public $positionService;

    /**
     * @inject
     * @var \App\UsersModule\Model\Service\IUserService
     */
    public $userService;

    /**
     * @inject
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    public $sportGroupService;

    /**
     * @inject
     * @var \App\SecurityModule\Model\Service\IResourceService
     */
    public $resourcesService;

    public function startup() {
	parent::startup();
    }

    public function beforeRender() {
	parent::beforeRender();
    }

    /**
     * Default presenter action
     * @Secured(resource="default")
     */
    public function actionDefault() {
	
    }

    // <editor-fold desc="Administration of ROLES">

    /**
     * Method for creating new roles (called top-down)
     * @param \Nette\ArrayHash $values
     */
    public function createRole(ArrayHash $values) {
	$r = new Role((array) $values);
	try {
	    $this->roleService->createRole($r);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave(null, "this", $ex);
	}
	$this->redirect('default');
    }

    /**
     * Action for creating role page
     * @Secured(resource="addRole")
     */
    public function actionAddRole() {
	
    }

    /**
     * Presenter action for role update page
     * @Secured(resource="updateRole")
     * @param integer $id
     */
    public function actionUpdateRole($id) {
	if (!$id)
	    $this->handleBadArgument($id);

	try {
	    $dbRole = $this->roleService->getRole((integer) $id);
	    if ($dbRole !== null) {
		$form = $this->getComponent('updateRoleForm');
		$dbRole->setParents($dbRole->getParents()
				->map(
					function(Role $e) {
				    return $e->getId();
				})->toArray());
		$form->setDefaults($dbRole->toArray());
	    } else {
		$this->handleEntityNotExists($id);
	    }
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "default", $ex);
	}
    }

    /**
     * Handler for updating role topdown
     * @param \Nette\ArrayHash $values
     */
    public function updateRole(ArrayHash $values) {
	$role = new Role((array) $values);
	try {
	    $this->roleService->updateRole($role);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($role->getId(), "this", $ex);
	}
	$this->redirect('default');
    }

    /**
     * Delete role signal handler
     * @Secured(resource="deleteRole")
     * @param integer $id
     */
    public function handleDeleteRole($id) {
	if (!$id) $this->handleBadArgument($id);
	$this->doDeleteRole($id);
	$this->redirect('this');
    }
    
    public function doDeleteRole($id) {
	try {
	    $this->roleService->deleteRole($id);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataDelete($id, "this", $ex);
	}
    }

    /**
     * Component factory of RoleForm for creating roles
     * @param string $name
     * @return \App\SecurityModule\Forms\RoleForm
     */
    public function createComponentAddRoleForm($name) {
	$form = $this->prepareRoleForm($name);
	$form->initialize();
	return $form;
    }

    /**
     * Component factory of RoleForm for updating role
     * @param string$name
     * @return \App\SecurityModule\Forms\RoleForm
     */
    public function createComponentUpdateRoleForm($name) {
	$form = $this->prepareRoleForm($name, $this->getEntityId());
	$form->setMode(FormMode::UPDATE_MODE);
	$form->initialize();
	return $form;
    }

    /**
     * Form success submission handler
     * @param \Nette\Application\UI\Form $form
     */
    public function roleFormSubmitted(RoleForm $form) {
	$values = $form->getValues();
	try {
	    switch ($form->getMode()) {
		case FormMode::CREATE_MODE:
		    $this->createRole($values);
		    break;
		case FormMode::UPDATE_MODE:
		    $this->updateRole($values);
		    break;
	    }
	} catch (Exceptions\DuplicateEntryException $e) {
	    $form->addError(
		    $this->t("securityModule.admin.messages.roleNameExists", null, ["name" => $values->name]));
	}
    }

    private function prepareRoleForm($name, $id = null) {
	$form = new RoleForm($this, $name, $this->getTranslator());
	try {
	    $roles = $this->roleService->getSelectRoles($id !== null ? $id : null);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "default", $ex);
	}
	$form->setRoles($roles);
	return $form;
    }

    /**
     * Component factory for Roles administration grid
     * @param string $name
     */
    public function createComponentRolesGrid($name) {
	
	$grid = new \Grido\Grid($this, $name);
	$grid->setModel($this->roleService->getRolesDatasource());

	$grid->addColumnNumber('id', '#')
		->cellPrototype->class[] = 'center';
	$headerId = $grid->getColumn('id')->headerPrototype;
	$headerId->class[] = 'center';
	$headerId->rowspan = "2";
	$headerId->style['width'] = '0.1%';

	$grid->addColumnText('name', 'Jméno')
		->setSortable()
		->setFilterText();
	
	$grid->getColumn('name')->getEditableControl()->setRequired('Name is required.');
	$headerName = $grid->getColumn('name')->headerPrototype;
	$headerName->class[] = 'center';

	$grid->addColumnText('parents', 'Rodiče')
		->setSortable()
		->setFilterText();
	
	$grid->getColumn('parents')->setCustomRender(callback($this, 'roleParColToString'));

	$headerParent = $grid->getColumn('parents')->headerPrototype;
	$headerParent->class[] = 'center';

	$grid->addColumnDate('added', 'Přidáno')
		->setSortable();
	$headerAdded = $grid->getColumn('added')->headerPrototype;
	$headerAdded->class[] = 'center';

	$grid->addColumnText('note', 'Poznámka')
		->setCustomRender($this->noteGridRender)
		->setSortable()
		->setTruncate(20)
		->setFilterText();
	$headerNote = $grid->getColumn('note')->headerPrototype;
	$headerNote->class[] = 'center';

	$grid->addActionHref('delete', '', 'deleteRole!')
		->setIcon('trash')
		->setElementPrototype(\Nette\Utils\Html::el("a")->addAttributes(["title" => $this->tt("securityModule.admin.grid.delete")]))
		->setConfirm(function($u) {
		    return $this->tt("securityModule.admin.grid.rlyDeleteRole", null, ["pos" => $u]);
		});
	
	$grid->addActionHref('edit', '', 'updateRole')
		->setIcon('pencil')
		->setElementPrototype(\Nette\Utils\Html::el("a")->addAttributes(["title" => $this->tt("securityModule.admin.grid.update")]));
	
	$grid->setOperation(["delete" => $this->tt("system.common.delete")], $this->roleGridOpsHandler);
	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-roles " . date("Y-m-d H:i:s", time()));
    }
    
    public function noteGridRender($e) {
	return \Nette\Utils\Html::el("span")
			->setText($e->getNote())
			->addAttributes(["title" => $e->getNote()]);
    }
    
    public function roleGridOpsHandler($op, $ids) {
	switch ($op) {
	    case "delete":
		foreach ($ids as $id) {
		    $this->doDeleteRole($id);
		}
		break;
	}
	$this->redirect("this");
    }

    /**
     * Grid column parents render callback
     * @param \App\Model\Entities\Role $item
     * @return string of parents roles
     */
    public function roleParColToString(Role $item) {
	$res = "";
	$it = $item->getParents()->getIterator();
	while ($it->current() !== null) {
	    $res .= $it->current()->getName();
	    $it->next();
	    if ($it->valid())
		$res .= ", ";
	}
	return $res;
    }

    // </editor-fold >
    // <editor-fold desc="Administration of RULES">

    /**
     * @Secured(resource="addRule")
     */
    public function actionAddRule() {
	
    }

    /**
     *  @Secured(resource="updateRule")     
     */
    public function actionUpdateRule($id) {
	if (!$id) $this->handleBadArgument($id);
	try {
	    $dbRule = $this->ruleService->getRule((integer) $id);
	    if ($dbRule !== null) {
		$form = $this->getComponent('updateRuleForm');
		$form->setDefaults($dbRule->toArray());
	    } else {
		$this->handleEntityNotExists($id);
	    }
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "default", $ex);
	}
    }

    public function handleGetPrivileges($value) {
	$resourcesSelect = $this->resourcesService->getSelectResources();
	
	if ($value) {
	    // 100% PresenterResource, becouse we changed deepFlatten method
	    
	    $resource = $this->resourcesService->getResource($value);
	    $privileges = $resource->getPrivileges();
	    
	    $this['addRuleForm']['resource']->setValue($value);
	    
	    if ($privileges) {
		$this['addRuleForm']['privilege']
			->setPrompt(AclRuleForm::SLCT_ACTION)
			->setItems($privileges);
		$this->payload->message = 'Success';
	    }
	    
	} else {
	    $this['addRuleForm']['resource']
		    ->setPrompt(AclRuleForm::SLCT_RESOURCE)
		    ->setItems($resourcesSelect);

	    $this['addRuleForm']['privilege']
		    ->setPrompt(AclRuleForm::SLCT_RESOURCE)
		    ->setItems([]);
	}
	
	$this->redrawControl("flash");
	$this->redrawControl("privilegesSnippet");
    }

    /**
     *  @Secured(resource="deleteRule")     
     */
    public function handleDeleteRule($id) {
	if (!$id) $this->handleBadArgument($id);
	$this->doDeleteRule($id);
	$this->redirect('this');
    }
    
    public function doDeleteRule($id) {
	try {
	    $this->ruleService->deleteRule((integer) $id);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataDelete($id, "this", $ex);
	}
    }

    public function updateRule(ArrayHash $rule) {
	$r = new AclRule((array) $rule);
	try {
	    $this->ruleService->updateRule($r);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($r->getId(), "default", $ex);
	}
	$this->redirect('default');
    }

    public function createRule(ArrayHash $values) {
	$r = new AclRule((array) $values);
	try {
	    $this->ruleService->createRule($r);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($r->getId(), "this", $ex);
	}
	$this->redirect('default');
    }

    public function createComponentRulesGrid($name) {
	
	$grid = new Grid($this, $name);
	$grid->setModel($this->ruleService->getRulesDatasource());
	$grid->setTranslator($this->getTranslator());

	$grid->addColumnNumber('id', '#')
		->cellPrototype->class[] = 'center';
	$headerId = $grid->getColumn('id')->headerPrototype;
	$headerId->class[] = 'center';
	$headerId->rowspan = "2";
	$headerId->style['width'] = '0.1%';

	$grid->addColumnText('role', "securityModule.admin.grid.role")
		->setSortable()
		->setFilterSelect([null=>null]+$this->getSelectRoles());
	$headerParent = $grid->getColumn('role')->headerPrototype;
	$headerParent->class[] = 'center';

	$grid->addColumnText('resource', "securityModule.admin.grid.resource")
		->setSortable()
		->setCustomRender($this->resourceRender)
		->setFilterText();
	$headerAdded = $grid->getColumn('resource')->headerPrototype;
	$headerAdded->class[] = 'center';

	$grid->addColumnText('privilege', "securityModule.admin.grid.privilege")
		->setSortable()
		->setCustomRender($this->privilegeRender)
		->setTruncate(15)
		->setFilterText();
	$headerNote = $grid->getColumn('privilege')->headerPrototype;
	$headerNote->class[] = 'center';

	
	$modesList = [null=>null]+AclMode::getOptions();
	$grid->addColumnText('mode', "securityModule.admin.grid.mode")
		->setSortable()
		->setCustomRender($this->modeRender)
		->setTruncate(15)
		->setFilterSelect($modesList);
	$headerNote = $grid->getColumn('mode')->headerPrototype;
	$headerNote->class[] = 'center';

	$grid->addActionHref('delete', '', 'deleteRule!')
		->setIcon('trash')
		->setElementPrototype(\Nette\Utils\Html::el("a")->addAttributes(["title" => $this->tt("securityModule.admin.grid.delete")]))
		->setConfirm(function($u) {
		    return $this->tt("securityModule.admin.grid.rlyDeleteRule", null, ["pos" => $u]);
		});
	$grid->addActionHref('edit', '', 'updateRule')
		->setIcon('pencil')
		->setElementPrototype(\Nette\Utils\Html::el("a")->addAttributes(["title" => $this->tt("securityModule.admin.grid.update")]));
	
	$grid->setOperation(["delete" => $this->tt("system.common.delete")], $this->ruleGridOpsHandler);
	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-rules " . date("Y-m-d H:i:s", time()));
    }
    
    protected function getSelectRoles($id = null) {
	try {
	    return $this->roleService->getSelectRoles($id);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad(null, ":System:Default:adminRoot", $ex);
	}
    }
    
    protected function getSelectUsers() {
	try {
	    return $this->userService->getSelectUsers();
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad(null, ":System:Default:adminRoot", $ex);
	}
    }
    
    protected function getSelectGroups($id = null) {
	try {
	    return $this->sportGroupService->getSelectApplicablegroups($id);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad(null, ":System:Default:adminRoot", $ex);
	}
    }
    
    public function privilegeRender($e) {
	$priv = $e->getPrivilege();
	return \Nette\Utils\Html::el("span")
		->addAttributes(["title"=>$priv])
		->setText(substr($priv, strrpos($priv, "\\"), strlen($priv)));
    }
    
    public function resourceRender($e) {
	$res = $e->getResource();
	$text = str_replace("Presenter", "",str_replace("App\\", "", str_replace("Presenters", "", str_replace("Module\\", "", $res))));
	return \Nette\Utils\Html::el("span")->addAttributes(["title"=>$res])->setText($text);
    }
    
    public function modeRender($e) {
	return $this->tt(AclMode::getOptions()[$e->getMode()]);
    }

    public function ruleGridOpsHandler($op, $ids) {
	switch ($op) {
	    case "delete":
		foreach ($ids as $id) {
		    $this->doDeleteAclRule($id);
		}
		break;
	}
	$this->redirect("this");
    }

    public function createComponentAddRuleForm($name) {
	$form = $this->prepareAclRuleForm($name);
	$form->initialize();
	return $form;
    }

    public function createComponentUpdateRuleForm($name) {
	$form = $this->prepareAclRuleForm($name);
	$form->setMode(FormMode::UPDATE_MODE);
	$form->initialize();
	return $form;
    }

    private function prepareAclRuleForm($name) {
	$form = new AclRuleForm($this, $name, $this->getTranslator());
	$form->setModes(AclMode::getOptions());
	$form->setResources($this->resourcesService->getSelectResources());
	$form->setPrivileges([]);
	try {
	    $roles = $this->roleService->getSelectRoles();
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad(null, "default", $ex);
	}
	$form->setRoles($roles);
	return $form;
    }

    // </editor-fold>
    // <editor-fold desc="Administration of POSITIONS">

    /**
     * @Secured(resource="addPosition")
     */
    public function actionAddPosition() {
	
    }

    public function createPosition(ArrayHash $values) {
	$p = new Position((array) $values);
	try {
	    $this->positionService->createPosition($p);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($p->getId(), "this", $ex);
	}
	$this->redirect("default");
    }

    /**
     * @Secured(resource="updatePosition")
     */
    public function actionUpdatePosition($id) {
	if (!is_numeric($id))
	    $this->handleBadArgument($id);
	try {
	    $dbPosition = $this->positionService->getPosition($id);
	    if ($dbPosition !== null) {
		$form = $this->getComponent("updatePositionForm");
		$form->setDefaults($dbPosition->toArray());
	    } else {
		$this->handleEntityNotExists($id);
	    }
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "default", $ex);
	}
    }

    public function updatePosition(ArrayHash $values) {
	$pos = new Position((array) $values);
	try {
	    $this->positionService->updatePosition($pos);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($values->id, "this", $ex);
	}
	$this->redirect('default');
    }

    public function createComponentPositionsGrid($name) {
	$grid = new Grid($this, $name);
	$grid->setModel($this->positionService->getPositionsDatasource());

	$grid->addColumnNumber('id', '#')
		->cellPrototype->class[] = 'center';
	$headerId = $grid->getColumn('id')->headerPrototype;
	$headerId->class[] = 'center';
	$headerId->rowspan = "2";
	$headerId->style['width'] = '0.1%';

	$grid->addColumnText('owner', 'Uživatel')
		->setSortable()
		->setFilterSelect([null=>null]+$this->getSelectUsers());

	$grid->addColumnText('group', 'Skupina')
		->setSortable()
		->setFilterSelect([null=>null]+$this->getSelectGroups());

	$grid->addColumnText('role', 'Role')
		->setSortable()
		->setFilterSelect([null=>null]+$this->getSelectRoles());

	$grid->addColumnText('comment', 'Komentář')
		->setCustomRender($this->commentGridRender)
		->setTruncate(20)
		->setSortable()
		->setFilterText();

	$headerOwner = $grid->getColumn('owner')->headerPrototype;
	$headerOwner->class[] = 'center';

	$headerGroup = $grid->getColumn('group')->headerPrototype;
	$headerGroup->class[] = 'center';

	$headerRole = $grid->getColumn('role')->headerPrototype;
	$headerRole->class[] = 'center';

	$grid->addActionHref('delete', '', 'deletePosition!')
		->setIcon('trash')
		->setElementPrototype(\Nette\Utils\Html::el("a")->addAttributes(["title" => $this->tt("securityModule.admin.grid.delete")]))
		->setConfirm(function($u) {
		    return $this->tt("securityModule.admin.grid.rlyDeletePosition", null, ["pos" => $u]);
		});
		
	$grid->addActionHref('edit', '', 'updatePosition')
		->setIcon('pencil')
		->setElementPrototype(\Nette\Utils\Html::el("a")->addAttributes(["title" => $this->tt("securityModule.admin.grid.update")]));

	$grid->setOperation(["delete" => $this->tt("system.common.delete")], $this->positionGridOpsHandler);
	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-positions " . date("Y-m-d H:i:s", time()));
    }

    public function commentGridRender($e) {
	return \Nette\Utils\Html::el("span")
			->setText($e->getComment())
			->addAttributes(["title" => $e->getComment()]);
    }

    public function positionGridOpsHandler($ops, $ids) {
	switch ($ops) {
	    case "delete":
		foreach ($ids as $id) {
		    $this->doDeletePosition($id);
		}
		break;
	}
	$this->redirect("this");
    }

    public function createComponentAddPositionForm($name) {
	$form = $this->preparePositionForm($name);
	$form->initialize();
	return $form;
    }

    public function createComponentUpdatePositionForm($name) {
	$form = $this->preparePositionForm($name);
	$form->setMode(FormMode::UPDATE_MODE);
	$form->initialize();
	return $form;
    }

    private function preparePositionForm($name) {
	$form = new PositionForm($this, $name, $this->getTranslator());
	$roles = $this->roleService->getSelectRoles();
	$users = $this->userService->getSelectUsers();
	$groups = $this->sportGroupService->getSelectAllSportGroups();
	$form->setSportGroups($groups);
	$form->setRoles($roles);
	$form->setUsers($users);
	return $form;
    }

    public function doDeletePosition($id) {
	try {
	    $this->positionService->deletePosition((integer) $id);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataDelete($id, "this", $ex);
	}
    }

    /**
     *  @Secured(resource="deletePosition")     
     */
    public function handleDeletePosition($id) {
	if (!$id)
	    $this->handleBadArgument($id);
	$this->doDeletePosition($id);
	$this->redirect('this');
    }

    // </editor-fold>
    
    public function createComponentSubMenu($name) {
	$c = new \App\Components\MenuControl($this, $name);
	$c->setLabel("systemModule.navigation.options");
	$c->addNode("securityModule.admin.roleAdd", ":Security:Admin:addRole");
	$c->addNode("securityModule.admin.ruleAdd", ":Security:Admin:addRule");
	$c->addNode("securityModule.admin.posAdd", ":Security:Admin:addPosition");

	$c->addNode("securityModule.navigation.back", ":System:Default:adminRoot");
	return $c;
    }
    
    public function createComponentBackSubMenu($name) {
	$c = new \App\Components\MenuControl($this, $name);
	$c->setLabel("systemModule.navigation.options");
	$c->addNode("securityModule.navigation.back", ":Security:Admin:default");
	return $c;
    }
}
