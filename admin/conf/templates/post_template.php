<!-- This is the Template to display posts, shoud be loaded by showPost() -->
<div class="row">
	<?php if ($data['public'] == 1 || isset($_SESSION['username']) || isset($_COOKIE['username'])) { ?>
		<div class="gb-overall col-md-12">
			<div class="panel <?=($data['public'] == 1) ? 'panel-primary custom-panel' : 'panel-danger'; ?>">
				<div class="panel-heading <?=($data['public'] == 1) ? 'custom-heading' : ''; ?>">
					<?php if(isset($_SESSION['username'])){ ?>
						<a class="btn btn-danger btn-xs pull-right" style="margin-left:5px;" href="<?php echo $edit; ?>"><i class="fa fa-pencil"></i> <?=$l['edit']?></a> 
					<?php } ?>
					<?php echo $l['at_date'].' '.date_format($data['datum'], 'd.m.y').' '.$l['at_time'].' '.date_format($data['datum'], 'H:m').' '.$l['a_clock'].' '.$l['wrote'].' '.$data['name'].':'; ?>
					<div class="pull-right">
						<?php if ($sgs['email'] && $data['email'] != '') { ?>
							<a href="mailto:<?php echo $data['email']; ?>" target="_blank" class="btn btn-default btn-xs" title="<?=$l['email']?>">
								<i class="fa fa-envelope-o"></i>
							</a>
						<?php }
						if ($sgs['homepage'] && $data['homepage'] != '') { ?>
							<a href="<?php echo $data['homepage']; ?>" target="_blank" class="btn btn-default btn-xs" title="<?=$l['homepage']?>">
								<i class="fa fa-home"></i>
							</a>
						<?php } ?>
					</div>
				</div>
				<div class="panel-body <?=($data['public'] == 1) ? 'custom-body' : ''; ?>">
					<?php if($sgs['image'] && $data['bild_url'] != ''){ ?>
						<div class="col-xs-6 col-md-3">
							<a href="<?php echo $data['bild_url']; ?>" class="thumbnail custom-thumbnail">
								<img src="<?php echo $data['bild_url']; ?>" alt="Thumbnail">
							</a>
						</div>
					<?php } ?>
						<h4><?php echo $data['betreff']; ?></h4>
						<?php echo nl2br($data['nachricht']); ?>
				</div>
				<?php if($data['kommentar'] != ''){ ?>
					<div class="panel-footer <?=($data['public'] == 1) ? 'custom-footer' : ''; ?>">
						<?php echo nl2br($data['kommentar']); ?>
					</div>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
</div><!-- END .row -->