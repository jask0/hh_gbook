<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <?=$l['admin_area']?>
            <small><?=$l['edit']?></small>
        </h1>
    </div>
</div>
<?php
    if(!isset($_SESSION['username'])){
            die($l['prohibited_direct_access']);
    }
    $id = (int)$_GET['id'];

    if(isset($_POST['submit'])){
        if(isset($_POST['loeschen']) and $_POST['loeschen'] == "1"){	
            if ( $db->deletePost($id) )
            {
                echo '<p class="alert alert-success">'.$l['msg_successful_deleted'].'</p>';
            }else{
            die($l['bad_query']);
            }
        }else{
            $form = array();
            $post = filter_input_array(INPUT_POST);
            $form['datum'] = $post['old_datum'];
            $form['name'] = $post['name'];
            $form['email'] = $post['email'];
            $form['homepage'] = $post['homepage'];
            $form['betreff'] = $post['betreff'];
            $form['bild_url'] = $post['bild_url'];
            $form['nachricht'] = str_replace('"','\"',str_replace("'", "\'",$post['nachricht']));
            $form['kommentar'] = str_replace('"','\"',str_replace("'", "\'",$post['kommentar']));
            $form['public'] = 0;
            if(isset($_POST['public']) and $_POST['public'] == "1")
                $form['public']=1;

            if ( $db->updatePost($id,$form) )
            {
                echo '<p class="alert alert-success">'.$l['msg_successful_edited'].'</p>';
            }else{
                die($l['bad_query']);
            }
        }
    }
    $post_list = $db->readPost($id);

    foreach ($post_list as $zeile){
        $zeile['public']=="1" ? $checked = "checked" : $checked = "";
        $zeile['admin'] = 1;
        $gb->displayForm($zeile, '?page=edit&id='.$id);
    }

?>