<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>My project - login</title>
</head>
<body style="font-family: Arial;">
	<div style="position: absolute; top: 50%; margin-top: -15px; left: 50%; margin-left: -100px; padding: 10px; border: 1px solid #ccc; width: 200px; text-align: center;">
	@if ( !empty( $github_login_url ) )
		<a href="{{ $github_login_url }}" title="GITHUB Login">
			GITHUB Login
		</a>
	@else
		Sorry, no login available.
	@endif
	</div>
</body>
</html>
