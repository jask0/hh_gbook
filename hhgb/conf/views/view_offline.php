<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <?=$l['admin_area']?>
            <small><?=$l['unpublished_msg']?></small>
	</h1>
    </div>
</div>
<?php
    if(!isset($_SESSION['username'])){
            die($l['prohibited_direct_access']);
    }
    
    $id=0;
    $get = filter_input_array(INPUT_GET);
    if(isset($get['id'])){
            $id = (int)$get['id'];
    }
        
    if(isset($_POST['submit'])){
        if(isset($_POST['loeschen']) and $_POST['loeschen'] == "1"){
            if ( $db->deletePost($id) )
            {
                echo '<p class="gruen">'.$l['msg_successful_deleted'].'</p>';
            } else {
                die($l['bad_query']);
            }
            echo '<hr>';
            }else{
                    $form['name'] = $_POST['name'];
                    $form['email'] = htmlentities($_POST['email']);
                    $form['homepage'] = htmlentities($_POST['homepage']);
                    $form['betreff'] = htmlentities($_POST['betreff']);
                    $form['bild_url'] = htmlentities($_POST['bild_url']);
                    $form['nachricht'] = str_replace('"','\"',str_replace("'", "\'",$_POST['nachricht']));
                    $form['kommentar'] = str_replace('"','\"',str_replace("'", "\'",$_POST['kommentar']));
                    $form['public'] = 0;
                    if(isset($_POST['public']) and $_POST['public'] == "1")
                            $form['public']=1;

                    if ( $db->updatePost($id, $form) ){
                        echo '<p class="gruen">'.$l['msg_successful_edited'].'</p>';
                    }else{
                        die($l['bad_query']);
                    }
                    echo '<hr>';
            }
    }
    $post_list = $db->readPosts('0');

    foreach ($post_list as $zeile)
    {	$zeile['public']=="1" ? $checked = "checked" : $checked = "";
            $zeile['admin'] = 1;
            //$zeile['datum'] = date_create($zeile['datum']);
            $gb->displayPost($zeile, '?page=edit&id='.$zeile['id']);
    }
?>