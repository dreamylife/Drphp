<?php
/**
 * @subpackage  Core
 * @author Dreamy <dreamy1992@vip.qq.com>
 */
class View{
	
	/**
     * 模板输出变量
     * @var tVar
     * @access protected
     */       
    protected $tVar        =  array();

    /**
     *  保存类实例的静态成员变量
     */   
    private static $_instance ; 

    /**
    * 取得视图类实例 这里使用单例模式 
    */
    static public function getInstance(){
        if(!(self::$_instance instanceof self)){
            self::$_instance = new self;
        }
        return self::$_instance ;  
    }
    
	/**
     * 模板变量赋值
     * @access public
     * @param mixed $name
     * @param mixed $value
     */
    public function assign($name,$value=''){
        if(is_array($name)) {
            $this->tVar   =  array_merge($this->tVar,$name);
        }else {
            $this->tVar[$name] = $value;
        }
    }

    /**
     * 加载模板和页面输出 可以返回输出内容
     * @access public
     * @param string $templateFile 模板文件名
     * @param string $charset 模板输出字符集
     * @param string $contentType 输出类型
     * @param string $content 模板输出内容
     * @param string $prefix 模板缓存前缀
     * @return mixed
     */
    public function display($templateFile='',$charset='',$contentType='',$content='',$prefix='') {
        // 解析并获取模板内容
        $content = $this->fetch($templateFile,$content,$prefix);
        // 输出模板内容
        $this->render($content,$charset,$contentType); 
    }

    /*返回模版文件*/
    public static function getView($template, $ext = '.php') {
        if (!is_dir(TEMPLATE_PATH)) {
            drMsg('当前使用的模板已被删除或损坏，请登录后台更换其他模板。', BLOG_URL . 'admin/template.php');
        }
        return TEMPLATE_PATH . $template . $ext;
    }
    //输出模版内容
    public static function output() {
        $content = ob_get_clean();
        if (Option::get('isgzipenable') == 'y' && function_exists('ob_gzhandler')) {
            ob_start('ob_gzhandler');
        } else {
            ob_start();
        }
        echo $content;
        ob_end_flush();
        exit;
    }
    
}
