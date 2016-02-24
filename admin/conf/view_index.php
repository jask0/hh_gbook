<div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">
						Administration
						<small>Einstellungen</small>
					</h1>
				</div>
			</div>
	<div class="raw">
		<div class="col-md-12">
		<?php if($_POST) {echo $info;} ?>
	<form class="form-horizontal" action="<?php echo basename(__FILE__); ?>" method="post" autocomplete="off">
		<div class="form-group">
			<label for="gb_title" class="col-sm-4 control-label hh_form">G&auml;stebuch Titel</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" id="gb_title" name="gb_title" placeholder="G&auml;stebuch" value="<?php echo $sgs['title']; ?>">
			</div>
		</div>
		<div class="form-group">
			<label for="show_email" class="col-sm-4 control-label hh_form">E-Mail Sichtbarkeit</label>
			<div class="col-sm-8">
				<select class="form-control" id="show_email" name="show_email">
					<option value="1" <?php ($sgs['email']) ? print 'selected' : print''; ?>>Sichtbar</option>
					<option value="0" <?php ($sgs['email']) ? print '' : print 'selected'; ?>>Unsichtbar</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="show_homepage" class="col-sm-4 control-label hh_form">Homepage Sichtbarkeit</label>
			<div class="col-sm-8">
				<select class="form-control" id="show_homepage" name="show_homepage">
					<option value="1" <?php ($sgs['homepage']) ? print 'selected' : print''; ?>>Sichtbar</option>
					<option value="0" <?php ($sgs['homepage']) ? print '' : print 'selected'; ?>>Unsichtbar</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="show_image" class="col-sm-4 control-label hh_form">Bild Sichtbarkeit</label>
			<div class="col-sm-8">
				<select class="form-control" id="show_image" name="show_image">
					<option value="1" <?php ($sgs['image']) ? print 'selected' : print''; ?>>Sichtbar</option>
					<option value="0" <?php ($sgs['image']) ? print '' : print 'selected'; ?>>Unsichtbar</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="show_subject" class="col-sm-4 control-label hh_form">Betreff Sichtbarkeit</label>
			<div class="col-sm-8">
				<select class="form-control" id="show_subject" name="show_subject">
					<option value="1" <?php ($sgs['subject']) ? print 'selected' : print''; ?>>Sichtbar</option>
					<option value="0" <?php ($sgs['subject']) ? print '' : print 'selected'; ?>>Unsichtbar</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="beitraege_pro_seite" class="col-sm-4 control-label hh_form">Beitraege pro Seite</label>
			<div class="col-sm-8">
				<input type="number" min="5" step="5" class="form-control" id="beitraege_pro_seite" name="beitraege_pro_seite" placeholder="5" value="<?php echo $sgs['posts']; ?>">
			</div>
		</div>
		<div class="form-group">
			<label for="public" class="col-sm-4 control-label hh_form">Beit&auml;ge sofort ver&ouml;ffentlichen</label>
			<div class="col-sm-8">
				<select class="form-control" id="public" name="public">
					<option value="1" <?php ($sgs['public']) ? print 'selected' : print''; ?>>Ja</option>
					<option value="0" <?php ($sgs['public']) ? print '' : print 'selected'; ?>>Nein</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="hinweis" class="col-sm-4 control-label hh_form">Hinweis</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" id="hinweis" name="msg" placeholder="Hinweis" value="<?php echo $sgs['msg']; ?>">
			</div>
		</div>
		<div class="form-group">
			<label for="fehler" class="col-sm-4 control-label hh_form">Fehler Meldung</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" id="fehler" name="fehler" placeholder="Fehler" value="<?php echo $sgs['error']; ?>">
			</div>
		</div>
		<div class="form-group">
			<label for="uname" class="col-sm-4 control-label hh_form">Benutzername</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" id="uname" name="uname" placeholder="Benutzername" value="<?php echo $sgs['username']; ?>">
			</div>
		</div>
		<div class="form-group">
			<label for="pword" class="col-sm-4 control-label hh_form">Passwort</label>
			<div class="col-sm-8">
				<input type="password" name="password" value="" style="display: none" />
				<input type="password" class="form-control" id="pword" name="pword" placeholder="Passwort" autocomplete="off">
			</div>
		</div>
		<div class="form-group">
			<label for="pword_v" class="col-sm-4 control-label hh_form">Passwort (wiederhollen)</label>
			<div class="col-sm-8">
				<input type="password" class="form-control" id="pword_v" name="pword_v" placeholder="Passwort" value="" disabled>
				<p>Nur beim &auml;ndern des Passwortes n&ouml;tig</p>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-4 col-sm-8">
				<button type="submit" class="btn btn-success" name="submit">Speichern</button>
			</div>
		</div>
	</form>
</div>
</div> <!-- END .raw -->