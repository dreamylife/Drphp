<?php
/**
 * DrPHP Action控制器基类 抽象类
 * @subpackage  Core
 * @author   Dreamy <dreamy1992@vip.qq.com>
 */
abstract class Controller{
	/**
     * 视图实例对象
     * @var view
     * @access protected
     */    
    protected $view     =  null;

    /**
     * 当前控制器名称
     * @var name
     * @access protected
     */      
    private   $name     =  '';

    /**
     * 控制器参数
     * @var config
     * @access protected
     */      
    protected $config   =   array();

    /**
    *数据库链接实例
    */
    protected $db      =  null; 

    /**
     * 构造方法 取得模板对象实例
     * @access public
     */
    public function __construct() {  
        //获取数据库引用
        $this->db = Database::getInstance() ; 
        //获取视图引用
        $this->view = View::getInstance() ;         
        //控制器初始化 对子类使用
        if(method_exists($this,'_init')){
        	$this->_init();
        }
    } 
    
    /**
     * 获取当前Controller名称
     * @access protected 
     */
    protected function getActionName() {
        if(empty($this->name)) {
            // 获取控制器名称
            $this->name    =   substr(get_class($this),0,-10);
        }
        return $this->name;
    }

    /**
     * 是否AJAX请求
     * @access protected
     * @return bool
     */
    protected function isAjax() {
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) ) {
            if('xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH']))
                return true;
        }
        //如果来的参数存在ajax 默认表示匹配为ajax提交方式   
        if(!empty($_POST['ajax']) || !empty($_GET['ajax'])){
        	// 判断Ajax方式提交
            return true;
        }
        return false;
    }

    /**
     * 操作错误跳转的快捷方法
     * @access protected
     * @param string $message 错误信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     */
    protected function error($message='',$jumpUrl='',$ajax=false) {
        $this->dispatchJump($message,0,$jumpUrl,$ajax);
    }

    /**
     * 操作成功跳转的快捷方法
     * @access protected
     * @param string $message 提示信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     */
    protected function success($message='',$jumpUrl='',$ajax=false) {
        $this->dispatchJump($message,1,$jumpUrl,$ajax);
    }

    /**
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type AJAX返回数据格式
     * @return void
     */
    protected function ajaxReturn($data,$type='') {
        if(empty($type)) $type  =  'JSON' ;
        switch (strtoupper($type)){
            case 'JSON' :
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode($data));
            case 'XML'  :
                // 返回xml格式数据
                header('Content-Type:text/xml; charset=utf-8');
                exit(xml_encode($data));
            case 'JSONP':
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                $handler  =   isset($_GET[C('VAR_JSONP_HANDLER')]) ? $_GET[C('VAR_JSONP_HANDLER')] : C('DEFAULT_JSONP_HANDLER');
                exit($handler.'('.json_encode($data).');');  
            case 'EVAL' :
                // 返回可执行的js脚本
                header('Content-Type:text/html; charset=utf-8');
                exit($data);            
            default     :
                //用于扩展其他返回格式数据
                //tag('ajax_return',$data);
        }
    }
    /**
     * 模板显示 调用内置的模板引擎显示方法，
     * @access protected
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @param string $charset 输出编码
     * @param string $contentType 输出类型
     * @param string $content 输出内容
     * @param string $prefix 模板缓存前缀
     * @return void
     */
    protected function display($templateFile='',$charset='',$contentType='',$content='',$prefix='') {
        $this->view->display($templateFile,$charset,$contentType,$content,$prefix);
    }

    /**
     * 析构方法
     * @access public
     */
    public function __destruct() {
        // 执行后续操作
        //tag('action_end');
    }

}
