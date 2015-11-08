<html>
	<head>
		<title>Sign in</title>
		<link rel="icon" href="../resources/images/favicon.png" type="image/gif">
		<link href="../resources/css/style.css" type="text/css" rel="stylesheet"/>
	</head>
	<body>
		<div id="login">
			<div class="start-form-container">
				<form id="loginForm" name="loginForm" method="POST">
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
						<label for="nickname">Nickname</label><input type="text" id="nickname" name="nickname"/>
					</div>
					<div class="form-group">
						<label for="password">Password</label><input type="password" id="password" name="password"/>
					</div>
					<div class="form-group" style="min-height: 41px"> <!-- min-height needed because of button animation -->
						<button class="button button-small grey" type="submit">Submit</button>
					</div>
				</form>
			</div>
			<a href="<?php echo $registerUrl ?>"><button class="round-big">Register</button></a>
		</div>
	</body>
</html>