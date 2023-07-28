<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once(_PS_MODULE_DIR_.'bfmevents/loader.php');

class BfmeventsListModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $where = 'WHERE active=1';
        $order_by = 'ORDER BY since DESC';
        
        $events = Event::getAll($fields = '', $where, $order_by);

        $this->context->smarty->assign([
            'events' => $events,
            'module_image_dir' => Event::getModuleImageDir(false, true),
        ]);

        $this->setTemplate('module:bfmevents/views/templates/front/list.tpl');
    }
}