<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 04.06.2018
 * Time: 23:58
 */

class SSCFModule
{
    public static function update()
    {
        if (!class_exists('ZipArchive')) {
            return;
        }
    
        if ($file = @file_get_contents('https://github.com/Seleda/ps__stopspamcontactform/releases')) {
            preg_match('#href="\/Seleda\/ps__stopspamcontactform\/releases\/tag\/(\d+\.\d+\.\d+)"#u', $file, $match);
        } else {
            return;
        }
    
        if (!isset($match[1]) && !$match[1]) {
            return;
        }
        
        if (version_compare($match[1], self::getVersion(), '<=')) {
            return;
        }
    
        if(!@copy(
            'https://github.com/Seleda/ps__stopspamcontactform/archive/master.zip',
            _PS_MODULE_DIR_.'./ps__stopspamcontactform/module.zip'
        )) {
            return;
        }
    
        $zip = new ZipArchive;
        if ($zip->open('test.zip') === true) {
            $zip->extractTo(_PS_MODULE_DIR_.'ps__stopspamcontactform/');
            $zip->close();
        }
        
        @unlink(_PS_MODULE_DIR_.'./ps__stopspamcontactform/module.zip');
    }
    
    public static function hookActionDispatcherBefore($params)
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            if (isset($params['route']) && $params['route'] == 'admin_module_catalog') {
                SSCFModule::update();
            }
        }
    }
    
    public static function hookActionDispatcher($params)
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
            if (isset($params['controller_class'])
                && $params['controller_class'] == 'AdminModulesController'
                && !Tools::getValue('module_name')
                && !Tools::getValue('configure')
                && !Tools::getValue('id_module')) {
                SSCFModule::update();
            }
        }
    }
    
    public static function getVersion()
    {
        require_once dirname(__FILE__).'/../ps__stopspamcontactform.php';
        $module = new Ps__stopspamcontactform();
        return $module->version;
    }
}