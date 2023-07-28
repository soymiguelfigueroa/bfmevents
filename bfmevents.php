<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

include_once(_PS_MODULE_DIR_.'bfmevents/loader.php');

class BfmEvents extends Module
{
    public function __construct()
    {
        $this->name = 'bfmevents';
        $this->tab = 'administration';
        $this->version = '1.1.2';
        $this->author = 'Miguel Figueroa';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '8.99.99',
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Bfm Events');
        $this->description = $this->l('This module let you save and show events');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        $this->table_widthout_prefix = 'bfmevents';
        $this->tablename = _DB_PREFIX_ . $this->table_widthout_prefix;
        $this->table_fields = [
            'name',
            'image',
            'since',
            'until',
            'location',
            'description',
            'active',
        ];
    }

    public function install()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `".$this->tablename."` ( `id_event` INT NOT NULL AUTO_INCREMENT, `name` VARCHAR(255) NOT NULL, `image` VARCHAR(255) NOT NULL, `since` DATETIME NOT NULL, `until` DATETIME NOT NULL, `location` VARCHAR(255) NOT NULL, `description` TEXT NOT NULL, `active` TINYINT NOT NULL, `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(`id_event`) ) ENGINE = InnoDB;";

        Db::getInstance()->execute($sql);
        
        return parent::install()
            && $this->registerHook('moduleRoutes')
            && $this->registerHook('actionFrontControllerSetMedia');
    }

    public function uninstall()
    {
        Db::getInstance()->execute("DROP TABLE IF EXISTS `".$this->tablename."`");

        $this->delTree(dirname(__FILE__) . '/assets/img/');
        
        return parent::uninstall()
            && $this->unregisterHook('moduleRoutes')
            && $this->unregisterHook('actionFrontControllerSetMedia');
    }

    public function getContent()
    {
        $html = '';
        $events = [];

        $events_filters = '';

        if (Tools::isSubmit('add_event')) {
            return $this->renderAddEventForm();
        } elseif (Tools::isSubmit('submit_add_event')) {
            $html .= $this->processAddEventForm();
        } elseif (Tools::isSubmit('viewbfmevents')) {
            return $this->renderShowEvent();
        } elseif (Tools::isSubmit('updatebfmevents')) {
            return $this->renderUpdateEventForm();
        } elseif (Tools::isSubmit('submit_update_event')) {
            $html .= $this->processUpdateEventForm();
        } elseif (Tools::isSubmit('deletebfmevents')) {
            $html .= $this->processDeleteEventForm();
        } elseif (Tools::isSubmit('submitFilterbfmevents') && !Tools::isSubmit('submitResetbfmevents')) {
            $events_filters = $this->getEventsFilters();
        }
        
        $html .= $this->renderEventsTable($events_filters);

        return $html;
    }

    public function hookModuleRoutes()
    {
        return [
            'module-bfmevents-list' => [
                'rule' => 'bfmevents/list',
                'keywords' => [],
                'controller' => 'list',
                'params' => [
                    'fc' => 'module',
                    'module' => 'bfmevents',
                ]
            ],
            'module-bfmevents-show' => [
                'rule' => 'bfmevents/show/{id_event}',
                'keywords' => [
                    'id_event' => [
                        'regexp' => '[0-9]*',
                        'param' => 'id_event'
                    ]
                ],
                'controller' => 'show',
                'params' => [
                    'fc' => 'module',
                    'module' => 'bfmevents',
                ]
            ]
        ];
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            'bfmevents-style',
            'modules/' . $this->name . '/views/css/bfmevents.css',
            [
                'media' => 'all',
                'priority' => 1000,
            ]
        );
    }

    private function getModuleConfigurationPageLink()
    {
        return AdminController::$currentIndex.'&configure='.$this->name;
    }

    private function getEventFieldsValue($event = [])
    {
        $fields_value = [
            'id_event' => '',
            'name' => '',
            'image' => '',
            'since' => '',
            'until' => '',
            'location' => '',
            'description' => '',
            'active' => false,
        ];

        if (!empty($event)) {
            foreach ($event as $event_field => $event_value) {
                foreach ($fields_value as $fields_value_field => $fields_value_value) {
                    if ($event_field == $fields_value_field) {
                        $fields_value[$event_field] = $event_value;
                    }
                }
            }
        }

        return $fields_value;
    }

    private function getEventForm($update = false, $image_required = true)
    {
        $title = $update == false ? 'New event' : 'Edit event';
        $icon = $update == false ? 'icon-plus' : 'icon-pencil';
        $form_submit_name = $update == false ? 'submit_add_event' : 'submit_update_event';
        
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l($title),
                    'icon' => $icon
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Name'),
                        'name' => 'name',
                        'required' => true,
                    ],
                    [
                        'type' => 'file',
                        'label' => $this->l('Image'),
                        'name' => 'image',
                        'required' => $image_required,
                    ],
                    [
                        'type' => 'datetime',
                        'label' => $this->l('Since'),
                        'name' => 'since',
                        'required' => true,
                    ],
                    [
                        'type' => 'datetime',
                        'label' => $this->l('Until'),
                        'name' => 'until',
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Location'),
                        'name' => 'location',
                        'required' => true,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Description'),
                        'name' => 'description',
                        'required' => true,
                    ],
                    [
                        'type' => 'switch',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                            ]
                        ],
                        'label' => $this->l('Active'),
                        'name' => 'active',
                        'required' => true,
                    ],
                ],
                'submit' => [
                    'name' => $form_submit_name,
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    private function getEventHelperForm($fields_value, $update = false)
    {
        $helper = new HelperForm();
        $helper->table = $this->table;
        $helper->submit_action = !$update ? 'add-event' : 'update-event';
        $helper->currentIndex = $this->getModuleConfigurationPageLink();
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $fields_value,
        ];
        $helper->show_cancel_button = true;

        return $helper;
    }

    private function renderEventsTable($where = '')
    {
        $fields = '`id_event`, `name`, `active`';
        
        $events = Event::getAll($fields, $where);

        $fields_display = array(
            'id_event' => array(
                'title' => $this->l('Id'),
                'type' => 'text',
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'type' => 'text',
            ),
            'active' => array(
                'title' => $this->l('Active'),
                'type' => 'bool',
            ),
        );

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->actions = [
            'view',
            'edit',
            'delete',
        ];
        $helper->simple_header = false;
        $helper->show_toolbar = true;
        $helper->toolbar_btn['new'] = array(
            'href' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&module_name=' . $this->name . '&add_event',
            'desc' => $this->l('Add New Event'),
        );
        $helper->module = $this;
        $helper->listTotal = count($events);
        $helper->identifier = 'id_event';
        $helper->no_link = true;
        $helper->title = $this->l('Events');
        $helper->table = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = $this->getModuleConfigurationPageLink();

        return $helper->generateList($events, $fields_display);
    }

    private function renderAddEventForm()
    {
        $fields_value = $this->getEventFieldsValue();

        $form = $this->getEventForm();

        $helper = $this->getEventHelperForm($fields_value);

        return $helper->generateForm([$form]);
    }

    private function renderShowEvent()
    {
        $event = Event::getEvent();

        $this->context->smarty->assign([
            'event' => $event,
            'url_admin' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name,
            'module_image_dir' => Event::getModuleImageDir(false, true),
        ]);

        return $this->context->smarty->fetch($this->local_path.'views/templates/admin/event.tpl');
    }

    private function renderUpdateEventForm()
    {
        $event = Event::getEvent();

        $fields_value = $this->getEventFieldsValue($event);

        $form = $this->getEventForm(true, false);
        $form['form']['input'][] = [
            'type' => 'hidden',
            'name' => 'id_event',
        ];

        $helper = $this->getEventHelperForm($fields_value, true);

        return $helper->generateForm([$form]);
    }

    private function processAddEventForm()
    {
        $values = Tools::getAllValues();

        $data = [];

        foreach ($values as $key => $value) {
            if (in_array($key, $this->table_fields)) {
                $data[$key] = $value;
            }
        }

        $data['image'] = $this->uploadImage();

        if (Db::getInstance()->insert($this->table_widthout_prefix, $data)) {
            $this->redirectToModuleMainPage();
        } else {
            return $this->displayError($this->l('There were an error trying to proccess the form'));
        }
    }

    private function processUpdateEventForm()
    {
        $values = Tools::getAllValues();

        $data = [];

        foreach ($values as $key => $value) {
            if (in_array($key, $this->table_fields)) {
                $data[$key] = $value;
            }
        }

        if (!empty($data['image'])) {
            $this->deleteImage();
            
            $data['image'] = $this->uploadImage();
        } else {
            unset($data['image']);
        }

        $where = '`id_event` = ' . $values['id_event'];

        if (Db::getInstance()->update($this->table_widthout_prefix, $data, $where)) {
            $this->redirectToModuleMainPage();
        } else {
            return $this->displayError($this->l('There were an error trying to proccess the form'));
        }
    }

    private function processDeleteEventForm()
    {
        $values = Tools::getAllValues();

        $where = '`id_event` = ' . $values['id_event'];

        $this->deleteImage();

        if (Db::getInstance()->delete($this->table_widthout_prefix, $where)) {
            $this->redirectToModuleMainPage();
        } else {
            return $this->displayError($this->l('There were an error trying to proccess the form'));
        }
    }

    private function redirectToModuleMainPage()
    {
        Tools::redirectAdmin($this->getModuleConfigurationPageLink() . '&token=' . Tools::getAdminTokenLite('AdminModules'));
    }

    private function uploadImage()
    {
        // Saving event image
        $target_dir = Event::getModuleImageDir();
        $file_image_name_explode = explode('.', basename($_FILES['image']["name"]));
        $filename = uniqid() . '.' . $file_image_name_explode[1];
        $target_file = $target_dir . $filename;
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES['image']["tmp_name"]);
        if(!$check) {
            return $this->displayError($this->l('Sorry, file is not an valid image.'));
        }
        // Allow certain file formats
        if($imageFileType != "jpg" 
            && $imageFileType != "png" 
            && $imageFileType != "jpeg" 
            && $imageFileType != "gif" ) {
            return $this->displayError($this->l('Sorry, only JPG, JPEG, PNG & GIF files are allowed.'));
        }
        // Check if $uploadOk is set to 0 by an error
        try {
            $file_uploaded = move_uploaded_file($_FILES['image']["tmp_name"], $target_file);
        } catch (\Throwable $th) {
            return $this->displayError($this->l('Sorry, there was an error uploading your file.'));
        }

        return $filename;
    }

    private function deleteImage()
    {
        $event = Event::getEvent();

        $image = Event::getModuleImageDir() . $event['image'];

        if (file_exists($image)) {
            unlink($image);

            return true;
        }

        return false;
    }

    private function delTree($dir) 
    {
        $files = array_diff(scandir($dir), array('.','..'));

        foreach ($files as $file) {
            if ($file == 'index.php')
                continue;

            (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }
    }

    private function getEventsFilters()
    {
        $bfmeventsFilter_id_event = Tools::getValue('bfmeventsFilter_id_event');
        $bfmeventsFilter_name = Tools::getValue('bfmeventsFilter_name');
        $bfmeventsFilter_active = Tools::getValue('bfmeventsFilter_active');
  
        $fields = '';
        $where = '';
        $order_by = '';

        if ($bfmeventsFilter_id_event)
            $where .= "`id_event` = $bfmeventsFilter_id_event ";

        if ($bfmeventsFilter_name) {
            if (!empty($where))
                $where .= 'AND ';
            $where .= "`name` LIKE '%$bfmeventsFilter_name%' ";
        }

        if ($bfmeventsFilter_active === '1' || $bfmeventsFilter_active === '0') {
            if (!empty($where))
                $where .= 'AND ';
            $where .= "`active` = " . (int) $bfmeventsFilter_active;
        }

        if (!empty($where)) {
            $where = 'WHERE ' . $where;
        }

        return $where;
    }
}
