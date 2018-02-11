@extends( 'layouts.app' )
@section( 'title', 'My project - Issue #' . $issue['number'] )
@section( 'content' )
	<div style="background-color: #ddd; padding: 10px 5px; margin-bottom: 10px;">
		<a href="{{ URL::previous() }}" title="Back to issues">< Back to issues</a>
	</div>
	<div>
		<div style="padding-bottom: 10px;">
			<div style="padding-bottom: 10px; font-weight: 16px; font-weight: bold;">
				{{ $issue['title'] }}
			</div>
			<div>
				<div style="float: left; padding-right: 10px; width: 100px;">
					@if ( $issue['state'] == 'open' )
						<span style="color: green; font-weight: bold;">[OPEN]</span>
					@else
						<span style="color: red; font-weight: bold;">[CLOSED]</span>
					@endif
				</div>
				<div style="float: left; padding-right: 10px;">
					@if ( !empty( $issue['_closed_time_ago_'] ) )
						#{{ $issue['number'] }} closed {{ $issue['_closed_time_ago_'] }} by <a href="{{ $issue['user']['html_url'] }}" target="_blank">{{ $issue['user']['login'] }}</a>.
					@else
						#{{ $issue['number'] }} opened {{ $issue['_created_time_ago_'] }} by <a href="{{ $issue['user']['html_url'] }}" target="_blank">{{ $issue['user']['login'] }}</a>.
					@endif
				</div>
				<div style="clear: both;"></div>
			</div>
		</div>
		<div>
			<div style="padding: 5px; border: 1px solid #ccc; background-color: #ddd; margin-bottom: 5px;">
				<div style="float: left; width: 70px;">
					<img src="{{ $issue['user']['avatar_url'] }}" alt="{{ $issue['user']['login'] }}" style="width: 50px; height: 50px;" />
				</div>
				<div style="float: right; width: 908px; overflow: auto;">
					<div style="padding-bottom: 10px; margin-bottom: 10px; border-bottom: 1px solid #ccc;">
						<a href="{{ $issue['user']['html_url'] }}" target="_blank">{{ $issue['user']['login'] }}</a> commented {{ $issue['_created_time_ago_'] }}
					</div>
					{{ nl2br( $issue['body'] ) }}
				</div>
				<div style="clear: both;"></div>
			</div>
			@if ( !empty( $issue['_comments_'] ) )
				@foreach ( $issue['_comments_'] as $comment )
					<div style="padding: 5px; border: 1px solid #ccc; background-color: #ddd; margin-bottom: 5px;">
						<div style="float: left; width: 70px;">
							<img src="{{ $comment['user']['avatar_url'] }}" alt="{{ $comment['user']['login'] }}" style="width: 50px; height: 50px;" />
						</div>
						<div style="float: right; width: 908px; overflow: auto;">
							<div style="padding-bottom: 10px; margin-bottom: 10px; border-bottom: 1px solid #ccc;">
								<a href="{{ $comment['user']['html_url'] }}" target="_blank">{{ $comment['user']['login'] }}</a> commented {{ $comment['_created_time_ago_'] }}
							</div>
							{{ nl2br( $comment['body'] ) }}
						</div>
						<div style="clear: both;"></div>
					</div>
				@endforeach
			@else
				<div style="padding-top: 10px; text-align: center;">
					There is no comments.
				</div>
			@endif
		</div>
	</div>
@endsection