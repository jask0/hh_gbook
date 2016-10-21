<?php
session_start();
if(!isset($_SESSION['username'])){
		header('Location: login.php');
}
require 'gb.php';
$gb = new GB;
$db = $gb->getDB();
$l = $gb->getLanguage();
$gbs = $gb->getGbSettings();
$user = $gb->getUserSettings();
$unpublic_count = $db->getCountOfUnpablicPosts();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title><?=$l['gb']?> : <?=$gbs['gb_title']?></title>
	<!-- Font-Awesome CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<!-- Bootstrap Core CSS -->
	<link href="conf/css/bootstrap.min.css" rel="stylesheet">

	<!-- Custom CSS -->
	<link href="conf/css/sb-admin.css" rel="stylesheet">
	<style>
		.top-nav > li > a.new_msg {
			color: red;
		}
		
		.top-nav > li > a.new_msg:hover {
			color:white;
		}
	</style>
	<?=(isset($_GET['page']) && $_GET['page'] == 'custom') ? '<link rel="stylesheet" href="conf/css/gb.custom.css">':''?>
	<?=(isset($_GET['page']) && $_GET['page'] == 'custom') ? '<script src="conf/js/jscolor.js"></script>':''?>
</head>

<body>
<div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only"><?=$l['toggle_navigation']?></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="../"><i class="fa fa-fw fa-desktop"></i> <?=$l['to_gb']?></a>
            </div>
            <!-- Top Menu Items -->
            <ul class="nav navbar-right top-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-envelope"></i> <b class="caret"></b></a>
                    <ul class="dropdown-menu message-dropdown">
			<li class="message-footer">
                            <a href="#"><strong><?=$l['3_new_msg']?></strong></a>
                        </li>
			<?php
			$abfrage_antwort = $db->getLastPosts();
						
			while ($zeile = mysqli_fetch_assoc($abfrage_antwort))
			{?>
			<li class="message-preview">
                            <a href="?page=edit&id=<?php echo $zeile['id']; ?>" title="<?=$l['3_new_msg']?>">
				<div class="media">
				<?php if($zeile['bild_url'] != '') {; ?>
                                    <span class="pull-left">
					<img class="media-object" style="width:64px;height:64px;" src="<?php echo $zeile['bild_url']; ?>" alt="">
                                    </span>
				<?php } ?>
                                    <div class="media-body">
                                        <h5 class="media-heading">
                                            <strong><?php echo $zeile['name']; ?></strong>
                                        </h5>
                                        <p class="small text-muted"><i class="fa fa-clock-o"></i> <?php echo $zeile['datum']; ?></p>
                                        <p><?php echo substr($zeile['nachricht'],0,200).'...'; ?></p>
                                    </div>
				</div>
                            </a>
			</li>
			<?php	}
                            mysqli_free_result( $abfrage_antwort );
			?>
                        
                       
                        <li class="message-footer">
                            <a href="../"><?=$l['read_all']?></a>
                        </li>
                    </ul>
                </li>
		<?php 
		if($unpublic_count > 0){
                    $new_msg=1;
		} else{
                    $new_msg=0;
		}?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle <?php $new_msg ? print 'new_msg': print '';?>" data-toggle="dropdown" title="<?php echo $unpublic_count;?> <?=$l['unpublished_msg']?>">
                        <i class="fa fa-bell" ></i> <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu alert-dropdown">
                        <li class="message-footer">
                            <a href="#"><strong><?=$l['unpublished_msg']?></strong></a>
                        </li>
			<li class="divider"></li>
                        <?php
			$abfrage_antwort = $db->getLastPosts();
						
			while ($zeile = mysqli_fetch_assoc($abfrage_antwort))
                            {?>	
                            <li>
                                <a href="?page=edit&id=<?php echo $zeile['id']; ?>">
                                    <?php echo $zeile['name']; ?> 
                                    <span class="label label-warning" style="float:right;"><?=$l['edit']?></span>
                                </a>
                            </li>
			<?php	}
				mysqli_free_result( $abfrage_antwort );
			?>
                        <li class="divider message-footer"></li>
                        <li>
                            <a href="?page=offline&id=0"><?=$l['see_all']?></a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo $db->getUserConfig()['user']; ?> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="index.php?page=profile"><i class="fa fa-fw fa-user"></i> <?=$l['profile']?></a>
                        </li>
                        <li>
                            <a href="?page=offline&id=0"><i class="fa fa-fw fa-envelope"></i> <?php echo $unpublic_count; ?> <?=$l['new_msg']?></a>
                        </li>
                        <li>
                            <a href="index.php?page=settings"><i class="fa fa-fw fa-gear"></i> <?=$l['settings']?></a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="login.php?logout=1"><i class="fa fa-fw fa-power-off"></i> <?=$l['logout']?></a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav side-nav">
					<li>
                        <a href="index.php"><i class="fa fa-fw fa-home"></i> <?=$l['home']?></a>
                    </li>
					<li>
                        <a href="?page=settings"><i class="fa fa-fw fa-wrench"></i> <?=$l['settings']?></a>
                    </li>
                    <li>
                        <a href="?page=custom"><i class="fa fa-fw fa-css3"></i> <?=$l['custom_css']?></a>
                    </li>
					<li>
                        <a href="?page=smilies"><i class="fa fa-fw fa-smile-o"></i> <?=$l['smilies']?></a>
                    </li>
					<!--
					<li>
                        <a href="../"><i class="fa fa-fw fa-desktop"></i> <?=$l['to_gb']?></a>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-fw fa-edit"></i> Neues GB anlegen</a>
                    </li>
					<li>
                        <a href="index.php?page=profile"><i class="fa fa-fw fa-user"></i> <?=$l['profile']?></a>
                    </li>
					-->
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </nav>

        <div id="page-wrapper">

            <div class="container-fluid">
			<?php
			if(isset($_GET['page'])){
				include('conf/views/view_'.$_GET['page'].'.php');
			} else {
				include('conf/views/view_index.php');
			}
			?>
	
			</div>
            <!-- /.container-fluid -->

        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->
    <!-- jQuery -->
    <script src="conf/js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="conf/js/bootstrap.min.js"></script>
</body>

</html>