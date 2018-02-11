<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>@yield( 'title' )</title>
</head>
<body style="font-family: Arial;">
	<div style="width: 990px; margin: 0 auto;">
		<div style="padding: 10px 0; border-bottom: 1px solid #ccc; margin-bottom: 10px;">
			<div style="float: left; color: #2F354D; font-weight: bold;">
				TESTIO<span style="color: #9ED532;">.</span>
			</div>
			<div style="float: right;">
				<a href="{{ url('logout') }}">
					> Logout
				</a>
			</div>
			<div style="clear: both;"></div>
		</div>
		@yield( 'content' )
	</div>
</body>
</html>