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

use App\SystemModule\Presenters\SecuredPresenter,
    \Nette\ArrayHash,
    \Grido\Grid,
    \App\SecurityModule\Forms\RoleForm,
    \App\Model\Misc\Enum\FormMode,
    \App\Model\Entities\Role,
    \App\Services\Exceptions\DataErrorException,
    \App\SecurityModule\Forms\AclRuleForm,
    \App\Model\Entities\AclRule,
    \App\Model\Entities\Position,
    \App\SecurityModule\Forms\PositionForm,
    \App\Model\Misc\Enum\AclMode,
    \App\Model\Misc\Enum\AclPrivilege;

/**
 * AdminSecurityPresenter
 * @Secured resource={Bezpecnostni.modul} privileges={nejaka, properta}
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class AdminPresenter extends SecuredPresenter {

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
     * @var \App\Model\Service\IPositionService
     */
    public $positionService;
    
    /**
     * @inject
     * @var \App\Model\Service\IUserService
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
	//$this->template->_form = $this['addRuleForm'];
	//$this->template->_form = $this['updateRuleForm'];
    }

    /**
     * Default presenter action
     * @Secured resource={Bezpecnostni.modul} privileges={view, prdel}
     */
    public function actionDefault() {
	
	//dd($this->userService);
	//dd($this->roleService->getRole(10));
	//dd($this->roleService->getRole(11));
	//dd($this->roleService->getRole(12));
//	dd($this->roleService->getClassName());
//	dd($this->ruleService->getClassName());
    }

    // <editor-fold desc="Administration of ROLES">
    
    /**
     * Handler for creating new roles
     * @param \Nette\ArrayHash $values
     */
    public function createRole(ArrayHash $values) {
	$r = new Role((array) $values);
	try {
	    $this->roleService->createRole($r);
	} catch (DataErrorException $e) {
	    // TODO LOG ??
	    dd(['Role admin presenter 53', $e]);
	}
	$this->redirect('default');
    }

    /**
     * Presenter action for creating role page
     */
    public function actionAddRole() {
	
    }

    /**
     * Presenter action for role update page
     * @param integer $id
     */
    public function actionUpdateRole($id) {
	if (!$id) {
	    $this->flashMessage("Identifier of updated role has to be specified, '{$id}' given.", self::FM_WARNING);
	    $this->redirect("default");
	}
	try {
	    $dbRole = $this->roleService->getRole((integer) $id);
	    if ($dbRole !== null) {
		$form = $this->getComponent('updateRoleForm');
		$dbRole->setParents($dbRole->getParents()
				->map(
					function(Role $e) {
				    return $e->getId();
				}
				)
				->toArray());
		$form->setDefaults($dbRole->toArray());
	    } else {
		$this->flashMessage("Role with given id does not exist", self::FM_WARNING);
		// TODO LOG SECURITY VIOLANCE
	    }
	} catch (DataErrorException $e) {
	    dd($e);
	    // LOG 
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
	} catch (DataErrorException $e) {
	    dd($e);
	    // LOG
	}
	$this->redirect('default');
    }

    /**
     * Delete role signal handler
     * @param integer $id
     */
    public function handleDeleteRole($id) {
	if (!$id) {
	    $this->flashMessage("Identifier of role has to be specified, '{$id}' given", self::FM_ERROR);
	    $this->redirect("default");
	}
	try {
	    $this->roleService->deleteRole($id);
	} catch (DataErrorException $e) {
	    dd($e);
	}
	$this->redirect('this');
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
	} catch (DuplicateEntryException $e) {
	    $form->addError("Role with name '{$values->name}' already exists");
	    dd($e);
	}
    }
    
    private function prepareRoleForm($name, $id = null) {
	$form = new RoleForm($this, $name);
	try {
	    $roles = $this->roleService->getSelectRoles($id!==null?$id:null);
	} catch (\Exception $e) {
	    dd($e);
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
		->setFilterText()
		->setSuggestion();
	$grid->getColumn('name')->getEditableControl()->setRequired('Name is required.');
	$headerName = $grid->getColumn('name')->headerPrototype;
	$headerName->class[] = 'center';

	$grid->addColumnText('parents', 'Rodiče')
		->setSortable()
		->setFilterText()
		->setSuggestion();
	$grid->getColumn('parents')->setCustomRender(callback($this, 'roleParColToString'));

	$headerParent = $grid->getColumn('parents')->headerPrototype;
	$headerParent->class[] = 'center';

	$grid->addColumnDate('added', 'Přidáno')
		->setSortable();
	$headerAdded = $grid->getColumn('added')->headerPrototype;
	$headerAdded->class[] = 'center';

	$grid->addColumnText('note', 'Poznámka')
		->setSortable()
		->setTruncate(20)
		->setFilterText();
	$headerNote = $grid->getColumn('note')->headerPrototype;
	$headerNote->class[] = 'center';

	$grid->addActionHref('delete', '[Smaz]', 'deleteRole!')
		->setIcon('trash');
	$grid->addActionHref('edit', '[Uprav]', 'updateRole')
		->setIcon('pencil');

	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-roles " . date("Y-m-d H:i:s", time()));
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

    public function actionAddRule() {
	
    }

    public function actionUpdateRule($id) {
	if (!$id) {
	    $this->flashMessage("Identifier of updated rule has to be specified, '{$id}' given", self::FM_ERROR);
	    $this->redirect("default");
	}
	try {
	    $dbRule = $this->ruleService->getRule((integer) $id);
	    if ($dbRule !== null) {
		$form = $this->getComponent('updateRuleForm');
		$form->setDefaults($dbRule->toArray());
	    } else {
		$this->flashMessage("Rule with given id does not exist", self::FM_ERROR);
		// TODO LOG SECURITY VIOLANCE
	    }
	} catch (DataErrorException $e) {
	    dd($e);
	    // LOG 
	}
    }
    
    public function handleGetPrivileges($value) {
	$resource = $this->resourcesService->getResource($value);
	$privileges = $resource->getPrivileges();
	$this['addRuleForm']['privilege']
		->setPrompt("Vyber si")
		->setItems($privileges);
	$this->invalidateControl("privilegesSnippet");
    }
    
    public function handleDeleteRule($id) {
	if (!$id) {
	    $this->flashMessage("Identifier of rule has to be specified, '{$id}' given", self::FM_ERROR);
	    $this->redirect("default");
	}
	try {
	    $this->ruleService->deleteRule((integer) $id);
	} catch (DataErrorException $e) {
	    dd($e);
	}
	$this->redirect('this');
    }
    
    public function updateRule(ArrayHash $rule) {
	
	$role = new AclRule((array) $rule);
	try {
	    $this->ruleService->updateRule($role);
	} catch (DataErrorException $e) {
	    dd($e);
	    // LOG
	}
	$this->redirect('default');
    }
    
    public function createRule(ArrayHash $values) {
	$r = new AclRule((array) $values);
	try {
	    $this->ruleService->createRule($r);
	} catch (DataErrorException $e) {
	    // TODO LOG ??
	    dd(['Rule admin presenter create', $e]);
	}
	$this->redirect('default');
    }
    
    public function createComponentRulesGrid($name) {
	$grid = new Grid($this, $name);
	$grid->setModel($this->ruleService->getRulesDatasource());

	$grid->addColumnNumber('id', '#')
		->cellPrototype->class[] = 'center';
	$headerId = $grid->getColumn('id')->headerPrototype;
	$headerId->class[] = 'center';
	$headerId->rowspan = "2";
	$headerId->style['width'] = '0.1%';

	$grid->addColumnText('role', 'Role')
		->setSortable()
		->setFilterText()
		->setSuggestion();
	//$grid->getColumn('role')->setCustomRender(callback($this, 'roleParColToString'));

	$headerParent = $grid->getColumn('role')->headerPrototype;
	$headerParent->class[] = 'center';

	$grid->addColumnText('resource', 'Zdroj')
		->setSortable();
	$headerAdded = $grid->getColumn('resource')->headerPrototype;
	$headerAdded->class[] = 'center';

	$grid->addColumnText('privilege', 'Akce')
		->setSortable()
		->setTruncate(15)
		->setFilterText();
	$headerNote = $grid->getColumn('privilege')->headerPrototype;
	$headerNote->class[] = 'center';
	
	$grid->addColumnText('mode', 'Mód')
		->setSortable()
		->setTruncate(15)
		->setFilterText();
	$headerNote = $grid->getColumn('mode')->headerPrototype;
	$headerNote->class[] = 'center';

	$grid->addActionHref('delete', '[Smaz]', 'deleteRule!')
		->setIcon('trash');
	$grid->addActionHref('edit', '[Uprav]', 'updateRule')
		->setIcon('pencil');

	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-rules " . date("Y-m-d H:i:s", time()));
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
	$form = new AclRuleForm($this, $name);
	$form->setModes(AclMode::getOptions());
	$form->setResources($this->resourcesService->getSelectResources());
	$form->setPrivileges([]);//AclPrivilege::getOptions());
	try {
	    $roles = $this->roleService->getSelectRoles(); 
	} catch (\Exception $e) {
	    dd($e);
	}
	$form->setRoles($roles);
	return $form;
    }
    // </editor-fold>
    // <editor-fold desc="Administration of POSITIONS">
    
    public function actionAddPosition() {
	
    }
    
    public function renderAddPosition() {
	
    }
    
    public function createPosition(ArrayHash $values) {
	$p = new Position((array) $values);
	try {
	    $this->positionService->createPosition($p);
	} catch (DataErrorException $ex) {
	    // TODO LOG ??
	    dd(['Position admin presenter create', $ex]);
	}
	$this->redirect("default");
    }   
    
    public function actionEditPosition($id) {
	if (!is_numeric($id)) {
	    $this->flashMessage("Identifier of position has to be type of numeric, '{$id}' given", self::FM_ERROR);
	    $this->redirect("default");
	}
	try {
	    $dbPosition = $this->positionService->getPosition((integer) $id);
	    if ($dbPosition !== null) {
		$form = $this->getComponent("updatePositionForm");	
		$form->setDefaults($dbPosition->toArray());
	    } else {
		$this->flashMessage("Position with given id does not exist", self::FM_ERROR);
		// TODO LOG SECURITY VIOLANCE
	    }
	} catch (Exception $ex) {

	}
    }
    
    public function updatePosition(ArrayHash $values) {
	$pos = new Position((array) $values);
	try {
	    $this->positionService->updateRule($pos);
	} catch (DataErrorException $e) {
	    dd($e);
	    // LOG
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
		->setFilterText()
		->setSuggestion();
	
	$grid->addColumnText('group', 'Skupina')
		->setSortable()
		->setFilterText()
		->setSuggestion();
	
	$grid->addColumnText('role', 'Role')
		->setSortable()
		->setFilterText()
		->setSuggestion();
	//$grid->getColumn('role')->setCustomRender(callback($this, 'roleParColToString'));

	$headerOwner = $grid->getColumn('owner')->headerPrototype;
	$headerOwner->class[] = 'center';
	
	$headerGroup = $grid->getColumn('group')->headerPrototype;
	$headerGroup->class[] = 'center';
	
	$headerRole = $grid->getColumn('role')->headerPrototype;
	$headerRole->class[] = 'center';

	$grid->addActionHref('delete', '[Smaz]', 'deletePosition!')
		->setIcon('trash');
	$grid->addActionHref('edit', '[Uprav]', 'updatePosition')
		->setIcon('pencil');

	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-positions " . date("Y-m-d H:i:s", time()));
	
    }
    
    public function createComponentAddPositionForm($name) {
	$form = $this->preparePositionForm($name);
	$form->initialize();
	return $form;
    }
	
    public function createComponentEditPositionForm($name) {
	$form = $this->preparePositionForm($name);
	$form->setMode(FormMode::UPDATE_MODE);
	$form->initialize();
	return $form;
    }
    
    private function preparePositionForm($name) {
	$form = new PositionForm($this, $name);
	$roles = $this->roleService->getSelectRoles();
	$users = $this->userService->getSelectUsers();
	$groups = $this->sportGroupService->getSelectSportGroups();
	$form->setSportGroups($groups);
	$form->setRoles($roles);
	$form->setUsers($users);
	return $form;
    }
    
    public function handleDeletePosition($id) {
	if (!$id) {
	    $this->flashMessage("Identifier of position has to be type of numeric, '{$id}' given", self::FM_ERROR);
	    $this->redirect("default");
	}
	try {
	    $this->positionService->deletePosition((integer) $id);
	} catch (DataErrorException $e) {
	    dd($e);
	}
	$this->redirect('this');
    }
    // </editor-fold>
}
