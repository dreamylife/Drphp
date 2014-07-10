<?php
/**
 * 数据库操作路由
 *
 * @copyright (c) Drphp All Rights Reserved
 */

class Database{

    private static $dbtype ; 
    private static $dbconf ; 

    /**
    *获取数据库连接实例
    */
    public static function getInstance() { 

        $dbconf = C('db') ; 
        self::$dbtype = $dbconf['default'] ; //数据库连接类型 
        self::$dbconf = $dbconf['connections'][self::$dbtype] ; //数据库配置参数
        switch ( self::$dbtype ) {
            case 'mysqli':
                return MySqlii::getInstance(self::$dbconf);
                break;
            case 'mysql':
            default : 
                return MySql::getInstance(self::$dbconf);
                break;
        }
    }

}
