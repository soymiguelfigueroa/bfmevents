<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once(_PS_MODULE_DIR_.'bfmevents/loader.php');

class BfmeventsShowModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        
        $event = Event::getEvent();
        $event['image'] = Event::getModuleImageDir(false, true) . $event['image'];
        $event['google_calendar_link'] = Event::getGoogleCalendarLink($event);

        $this->context->smarty->assign([
            'event' => $event,
        ]);

        $this->setTemplate('module:bfmevents/views/templates/front/show.tpl');
    }
}