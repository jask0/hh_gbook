<?php
$count = $db->getPostCountbyCondition("WHERE public*1 in (0,1)");

if($_POST && $_POST['beitraege_pro_seite'] >0){
	$gb = array();
	$id = $gbs['id'];
	$gb[$id] = array();
	//change gb configuration
	$gb[$id]['id'] = $id;
	$gb[$id]['title'] = $_POST['gb_title'];
	$gb[$id]['language'] = $_POST['language'];
	$gb[$id]['posts'] = $_POST['beitraege_pro_seite'];
	$gb[$id]['public'] = $_POST['public'];

	//write gb configuration
	$fp = fopen('conf/gb.json', 'w');
	fwrite($fp, json_encode($gb));
	fclose($fp);
		
	$info = '<p class="alert alert-success">'.$l['settings_successful_saved'].'</p>';
	$gbs = getGBsettings();
}
?>
<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">
			<?=$l['admin_area']?>
			<small><?=$l['home']?></small>
		</h1>
		<?php
			if ($user['password'] == sha1('123456'+$db->getSalt())){
				echo '<p class="alert alert-danger">'.$l['std_pwd_danger'].'</p>';
			}
		?>
	</div>
</div><!-- END .row -->
<div class="row">
	<div class="col-lg-4 col-md-4">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<div class="row">
					<div class="col-xs-3">
						<i class="fa fa-comments fa-5x"></i>
					</div>
					<div class="col-xs-9 text-right">
						<div class="huge"><?=$count?></div>
						<div><?=$l['post_count']?></div>
					</div>
				</div>
			</div>
			<a href="../">
				<div class="panel-footer">
					<span class="pull-left"><?=$l['to_gb']?></span>
					<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
					<div class="clearfix"></div>
				</div>
			</a>
		</div>
	</div>
	<div class="col-lg-4 col-md-4">
		<div class="panel panel-green">
			<div class="panel-heading">
				<div class="row">
					<div class="col-xs-3">
						<i class="fa fa-tasks fa-5x"></i>
					</div>
					<div class="col-xs-9 text-right">
						<div class="huge"><?=$gbs['posts_on_page']?></div>
						<div><?=$l['msgs_per_page']?></div>
					</div>
				</div>
			</div>
			<a href="?page=settings">
				<div class="panel-footer">
					<span class="pull-left"><?=$l['change']?></span>
					<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
					<div class="clearfix"></div>
				</div>
			</a>
		</div>
	</div>
	<div class="col-lg-4 col-md-4">
		<div class="panel panel-yellow">
			<div class="panel-heading">
				<div class="row">
					<div class="col-xs-3">
						<i class="fa fa-ban fa-5x"></i>
					</div>
					<div class="col-xs-9 text-right">
						<div class="huge"><?=$db->getPostCountbyCondition('WHERE public=0')?></div>
						<div><?=$l['unpublished_msg']?></div>
					</div>
				</div>
			</div>
			<a href="?page=offline">
				<div class="panel-footer">
					<span class="pull-left"><?=$l['see_all']?></span>
					<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
					<div class="clearfix"></div>
				</div>
			</a>
		</div>
	</div>
</div><!-- END .row -->
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="list-group">
			<span class="list-group-item active">
				<h4 class="list-group-item-heading"><?=$l['3_new_msg']?></h4>
				<p class="list-group-item-text"></p>
			</span>
			<?php
				$abfrage_antwort = $db->getLastPosts();
				
				while ($zeile = mysqli_fetch_assoc($abfrage_antwort))
				{
					$zeile['datum'] = date_create($zeile['datum']);
			?>
			<a href="?page=edit&id=<?php echo $zeile['id']; ?>" class="list-group-item">
				<h4 class="list-group-item-heading"><?php echo '('.date_format($zeile['datum'], 'd.m.y H:i').') '.$zeile['name'].':'; ?></h4>
				<p class="list-group-item-text"><?php echo substr($zeile['nachricht'],0,240).'...'; ?></p>
			</a>
			<?php	}
				
			?>
		</div>
	</div>
</div> <!-- END .row -->
