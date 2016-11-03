<?php
if(strpos($_SERVER['REQUEST_URI'], 'hhgb/') !== false){
    require_once 'conf/config.php';
} else {
    require_once 'hhgb/conf/config.php';
}
if(!isset($dbc)){
    if(strpos($_SERVER['REQUEST_URI'], 'hhgb/') !== false){
		header('Location: install.php');
	} else {
		header('Location: hhgb/install.php');
	}
}
if(strpos($_SERVER['REQUEST_URI'], 'hhgb/') !== false){ 
    require_once 'conf/functions.php';
    require_once 'conf/db.php';
} else {
    require_once 'hhgb/conf/functions.php';
    require_once 'hhgb/conf/db.php';
}


class GB {
    private $gb;
    private $user;
    private $db;
    private $system;
    private $lang;
    private $css;
    public $path;
    public $smilie;

    
    /*
     * CONSTRUCTOR METHOD
     */
    public function __construct() {
        $this->db = new DB;
        $this->gb = $this->db->getGbConfig();
        $this->user = $this->db->getUserConfig();
        $this->system = $this->db->getSystemConfig();
        $this->setLanguage($this->db->getValue('gb_language'));
        $this->setSmilies();
        $this->setCSS();
        $this->setPath();
    }
    
    
    /*
     * PUBLIC METHODS
     */
    public function setPath($p="") {
        $this->path = $p;
    }

    public function setCSS($css=array()){
        if(count($css) == 0){
        $this->css = json_decode($this->gb['custom_css'], true);
        } else {
            $this->db->updateConfigTable($css);
            $this->css = json_decode($this->gb['custom_css'], true);
        }
    }

    public function setSmilies($update=array()){
        if(count($update) == 0){
            $this->smilie  = json_decode($this->gb['smilies'], true);
        } else {
            $this->db->updateConfigTable($update);
            $this->css = json_decode($this->gb['smilies'], true);
        }
    }

    public function setLanguage($language=''){
        if ($language != '') {
            $this->lang = $this->loadLanguage($language);
        } else {
            $this->lang = $this->loadLanguage($this->db['gb_language']);
        }
    }
    
    public function getSalt(){
        return $this->db->getSalt();
    }

    public function getGbSettings($commit=''){
        if($commit == ''){
            return $this->gb;
        } else {
            return $this->db->getGbConfig();
        }
    }
    
    public function getUserSettings($commit=''){
        if($commit == ''){
            return $this->user;
        } else {
            return $this->user->getUserConfig();
        }
    }
    
    public function getSystemSettings($commit=''){
        if($commit == ''){
            return $this->system;
        } else {
            return $this->system->getUserConfig();
        }
    }
    
    public function getLanguage($lang=''){
        if($lang==''){
            return $this->lang;
        }else {
            return $this->loadLanguage($lang);
        }
    }
    
    public function getDB(){
        return $this->db;
    }
    
    public function getCustomCss(){
        return json_decode($this->gb['custom_css'], true);
    }
    
    /*load meta data like css an javascript
    # @param:
    # 		no param
    # @info:
    #		custom css is here
    */
    public function includeMeta(){
        $path = $this->path;
        echo sprintf(file_get_contents('hhgb/conf/templates/meta.html'), $path, $path, $path, $path, $path);

        if ($this->gb["use_custom_css"] == 1){
            echo "\n".'<link rel="stylesheet" href="'.$path.'hhgb/conf/css/gb.custom.css">'."\n";
        }
    }   
    
    /* display submit form and save post to database
    # @param:
    # 		$file ->  	url to the file where the GB is displayed
    */
    public function showGBookForm($filename){
        $post = filter_input_array(INPUT_POST);
        if(!isset($post['submit'])){
            $this->showForm(getFormDefault(), $filename);
        } else {
            if($_SESSION['captcha_code'] == md5($post['captcha']) or $this->db->getValue('captcha_on','0') == '0'){
                if($post['name'] == "" || $post['nachricht'] == ""){
                    $_POST['info_msg'] = '<p class="alert alert-danger">'. $this->lang['error'].'<br>'. $this->lang['error_msg_dont_send'] . '</p>';
                    $this->showForm($post, $filename);
                } else {
                    if ($this->sendPost($post)) {
                        $_POST['info_msg'] = '<p class="alert alert-success">'.$this->lang['msg_successful_send'].'</p>';
                        if($this->user['mail_msg'] == 1){
                            mail($this->user['email'], $this->lang['new_gb_msg'], $form['nachricht']);
                        }
                        $this->showForm(getFormDefault(), $filename);
                    } else {
                        $post['info_msg'] = '<p class="alert alert-danger">'.$this->lang['error_msg_dont_send'].'</p>';
                    }
                }
            } else {
                    $_POST['info_msg'] = '<p class="alert alert-danger">'.$this->lang['error_wrong_captcha'].'</p>';
                    $this->showForm($post, $filename);
            }
        }
    }
    /* display posts from database to website
    # @param:
    # 		no param
    */
    public function showGBookPosts(){
        $get = filter_input_array(INPUT_GET);
        $page = 1;
        if(isset($get['page'])){
            $page = (int)$get['page'];
        }
        $offset = ($page - 1) * $this->gb['posts_on_page'];
        $post_list = $this->db->readPostsForPage($this->gb['posts_on_page'], $offset, $this->isLoggedIn());
        foreach ($post_list as $post) {
            $this->showPost($post);
        }
    }
    
    public function displayPost($data, $edit){
        $this->showPost($data, $edit);
    }
    
    public function displayForm($data, $edit){
        $this->showForm($data, $edit);
    }
    
    /*pruf is the admin loged in
    # @param:
    # 		no param, use the session privateiable or cookie
    */
    public function isLoggedIn(){
            if(isset($_SESSION['username']) && $_SESSION['username'] == $this->user['user']){
                    return true; //' WHERE public*1 in (0,1) '
            } else {
                    return false; //' WHERE public = 1 ';
            }
    }
    
    /*
     * PRIVATE METHODS
     */
    private function addSmilieBbCode($msg){
        foreach ($this->smilie['list'] as $key => $value) {
            $new_msg = str_replace(':'.$value.':', '[smillie]'.$this->path.'hhgb/smilies/'.$this->smilie['folder'].'/'.$value.'[/smillie]', $msg);
        }
        $new_msg = str_replace("'", "\'", str_replace('"', '\"',$new_msg));
        return $new_msg;
    }

    private function sendPost($post){
        $form = getFormDefault();
        $form['name'] = fixUmlautEntry($post['name']);
        
        $form['email'] = (isset($post['email'])) ? securityCheck($post['email']) : '';
        $form['homepage'] = (isset($post['homepage'])) ? securityCheck($post['homepage']) : '';
        $form['betreff'] = (isset($post['betreff'])) ? fixUmlautEntry($post['betreff']) : '';
        $form['bild_url'] = (isset($post['bild_url'])) ? securityCheck($post['bild_url']) : '';
        
        $form['nachricht'] = fixUmlautEntry($this->scaleImages($post['nachricht']));
        $form['nachricht'] = $this->addSmilieBbCode($form['nachricht']);
        
        if ($this->db->insertInToPosts($form)) {
            return true;
        } else { 
            return false; 
        }
    }

    public function getLanguageFileList(){
	$page = scandir("conf/lang/");
	$file_list = array();
	foreach ($page as $key => $value){
		if(isFileExtension($value,'php')){
                    echo $value;
                    $lang = explode('.php', $value);
                    array_push($file_list, $lang[0]);
		}
	}
	return $file_list;
    }
    
    private function fallBackLanguage($l_new){
        $position = testPosition($_SERVER['REQUEST_URI']);
	include($this->path.$position."conf/lang/en.php");
	foreach($l as $key => $value){
		if (!array_key_exists($key, $l_new)) {
			$l_new[$key] = $value;
		}
	}
	return $l_new;
    }
    
    private function loadLanguage($filename){
        $position = testPosition($_SERVER['REQUEST_URI']);
        @include ($this->path.$position.'conf/lang/'.$filename.'.php');
        if (!isset($l)){
            $l = array();
        }
        return $this->fallBackLanguage($l);
    }
    
    /*scale all images in $msg if gbc[scale] is active
    # @param:
    # 		$msg, string text with html image tags
    */
    private function scaleImages($msg){
        if ($this->gb['scale_image'] == "1"){
            return str_replace('<img', '<img class="img-responsive img-thumbnail"',$msg);
        }
    }    

    /* display submit form
    # @param:
    # 		$data ->  	posted data array to fill the value fields
    #		$action ->	url for the action atributt
    */
    private function showForm($data, $action) {
        $path = $this->path;
        $gbs = $this->gb;
        $smilie = $this->smilie;
        $l = $this->lang;
        $form = getFormDefault();
        $data['action'] = $action;
        include('conf/templates/form_template.php');
    }
    
    /*display of one post
    # @param:
    # 		$data ->  	posted data array to fill the value fields
    */
    private function showPost($data, $edit=0) {
        $path = $this->path;
        $gbs = $this->gb;
        $smilie = $this->smilie;
        $l = $this->lang;
        $user = $this->user;
        $form = getFormDefault();
        if(is_numeric($edit)){
            $edit='hhgb/index.php?page=edit&id='.$data['id'];
        }
        include('conf/templates/post_template.php');
    }
    
    /* display page navigation of GB
    # @param:
    # 		no param needed
    */
    public function showGBpageNavi(){
        $gbs = $this->getGbSettings(); 
        $count = $this->db->getPostCountbyCondition("WHERE public = 1");
        $p=1;
        $get = filter_input_array(INPUT_GET);
        if(isset($get['page']) && ((int)$get['page'] > 1)){
                $p = ((int)$get['page'])-1;
        }
        echo '<div class="row"><div class="col-md-12">
                        <center><nav><ul class="pagination pagination-custom">
                                <li><a href="?page='.$p.'" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
        $x = 0;
        $last_page = (($count/$gbs['posts_on_page'])+1);
        for ($x = 1; $x < $last_page; $x++){
                        $active ='';
                        $sr_info='';
                        if(isset($get['page']) && $get['page'] == $x) {
                                $active =' class="active active-custom"';
                                $sr_info=' <span class="sr-only">(current)</span>';
                        }
                        $nav = '<li'.$active.'><a href="?page='.$x.'">'.$x.$sr_info.'</a></li>';
                        echo $nav;
                }
        if(isset($get['page']) && (((int)$get['page']) < ($x-1))){
                $p = (int)$get['page']+1;
        } elseif(!isset($get['page'])) {
                if($last_page < 2)
                        $p = 1;
                else
                        $p = 2;
        } else {
                $p = $x-1;
        }
        echo '<li><a href="?page='.$p.'" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li></ul></nav></center></div></div>';
    }
}
