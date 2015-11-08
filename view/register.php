<html>
	<head>
		<title>Register</title>
		<link rel="icon" href="../resources/images/favicon.png" type="image/gif"> 
		<link href="../resources/css/style.css" type="text/css" rel="stylesheet"/>
	</head>
	<body>
		<div id="register">
			<div class="start-form-container">
				<form id="registerForm" name="registerForm" method="POST">
					<div class="form-errors <?php if(isset($formErrors) && count($formErrors) > 0) echo 'show'; ?>">
						<?php 
							if(isset($formErrors) && count($formErrors) > 0){
								echo '<ul>';
								foreach($formErrors as $error){
									echo '<li>' . $error . '</li>';
								}
								echo '</ul>';
							}
						?>
					</div>
					<div class="form-group">
						<label for="nickname">Nickname</label>
						<input type="text" id="nickname" name="nickname" <?php if($nickname != null) echo 'value="' . $nickname . '"'; ?>/>
					</div>
					<div class="form-group">
						<label for="pass_first">Password</label><input type="password" id="pass_first" name="pass_first"/>
					</div>
					<div class="form-group">
						<label for="pass_second">Repeat password</label><input type="password" id="pass_second" name="pass_second"/>
					</div>
					<div class="form-group" style="min-height: 41px"> <!-- min-height needed because of button animation -->
						<button class="button button-small grey" type="submit">Submit</button>
					</div>
				</form>
			</div>
			<a href="<?php echo $loginUrl ?>"><button class="round-big">Login</button></a>
		</div>
	</body>
</html>	