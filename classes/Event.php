<?php

abstract class Event
{
    public const TABLENAME = 'bfmevents';
    public const NAME = 'bfmevents';
    
    public static function getAll($fields = '', $where = '', $order_by = '')
    {
        $sql = 'SELECT ';

        if (!empty($fields))
            $sql .= $fields;
        else
            $sql .= '*';

        $sql .= ' FROM `'. _DB_PREFIX_ . Event::TABLENAME .'` ';
        
        if (!empty($where))
            $sql .= $where . ' ';
        
        if (!empty($order_by))
            $sql .= $order_by . ' ';

        return Db::getInstance()->executeS($sql);
    }

    public static function getEvent()
    {
        $id_event = Tools::getValue('id_event');

        $sql = 'SELECT * FROM `'. _DB_PREFIX_ . Event::TABLENAME .'` WHERE `id_event` = ' . $id_event;

        return Db::getInstance()->getRow($sql);
    }

    public static function getModuleImageDir($include_ps_module_dir = true, $include_module_dir = false)
    {
        $dir = Event::NAME . "/assets/img/";

        if ($include_ps_module_dir) {
            $dir = _PS_MODULE_DIR_ . $dir;
        }
        
        if ($include_module_dir) {
            $dir = _MODULE_DIR_ . $dir;
        }
        
        return $dir;
    }

    public static function getGoogleCalendarLink($event)
    {
        $link = 'https://www.google.com/calendar/event?action=TEMPLATE';
        $link .= '&text=' . str_replace(' ', '+', $event['name']);
        $link .= '&dates=' . SELF::getGoogleCalendarDate($event['since']) . '/' . SELF::getGoogleCalendarDate($event['until']);
        $link .= '&ctz=America/Winnipeg';
        $link .= '&details=' . str_replace(' ', '+', $event['description']);
        $link .= '&location=' . str_replace(' ', '+', $event['location']);

        return $link;
    }

    public static function getGoogleCalendarDate($date)
    {
        $date_object = date_create($date);

        $googleCalendarDate = date_format($date_object, "Ymd");
        $googleCalendarDate .= 'T';
        $googleCalendarDate .= date_format($date_object, "His");

        return $googleCalendarDate;
    }
}
