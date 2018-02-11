@extends( 'layouts.app' )
@section( 'title', 'My project - List of issues' )
@section( 'content' )
	<div style="text-align: center; background-color: #ddd; padding: 10px 0; margin-bottom: 10px;">
		@if ( $current_type == 'closed' )
			<a href="{{ url('/main?t=open' ) }}" title="Open issues">Open: {{ $open_count }}</a>
		@else
			Open: {{ $open_count }}
		@endif
		|
		@if ( $current_type == 'open' )
			<a href="{{ url('/main?t=closed' ) }}" title="Closed issues">Closed: {{ $closed_count }}</a>
		@else
			Closed: {{ $closed_count }}
		@endif
	</div>
	@if ( !empty( $issues ) )
		<div>
			@foreach ( $issues as $key => $issue )
				<div style="
					padding-bottom: 10px; 
					@if ( $key+1 != count( $issues ) )
						margin-bottom: 10px; border-bottom: 1px solid #ccc;
					@endif
				">
					<div style="float: left; width: 800px;">
						<div style="padding-bottom: 5px;">
							<a href="{{ url('issue') . '/?id=' . $issue['number'] }}" title="{{ htmlspecialchars( $issue['title'] ) }}">{{$issue['title']}}</a>
							@foreach ( $issue['labels'] as $label )
								<div style="@if ( !empty( $label['color'] ) )background-color: #{{$label['color']}}; @endif padding: 3px; display: inline-block;">{{ $label['name'] }}</div>
							@endforeach
						</div>
						<div style="color: #bbb;">
							@if ( !empty( $issue['_closed_time_ago_'] ) )
								#{{ $issue['number'] }} closed {{ $issue['_closed_time_ago_'] }} by <a href="{{ $issue['user']['html_url'] }}" target="_blank">{{ $issue['user']['login'] }}</a>.
							@else
								#{{ $issue['number'] }} opened {{ $issue['_created_time_ago_'] }} by <a href="{{ $issue['user']['html_url'] }}" target="_blank">{{ $issue['user']['login'] }}</a>.
							@endif
						</div>
					</div>
					<div style="float: right; width: 150px;">
						@if ( !empty( $issue['comments'] ) )
							Comments: {{ $issue['comments'] }}
						@endif
					</div>
					<div style="clear: both;"></div>
				</div>
			@endforeach
		</div>
		<div style="text-align: center; padding-top: 10px;">
			@foreach ( $paging as $page )
				<div style="display: inline-block;">
					@if ( !empty( $page['is_page'] ) )
						@if ( $current_page == $page['page'] )
							<strong>{{ $page['page'] }}</strong>
						@else
							<a href="{{ url('main') . '/?p=' . $page['page'] . '&t=' . $current_type }}" title="">{{ $page['page'] }}</a>
						@endif
					@else
						...
					@endif
				</div>
			@endforeach
		</div>
	@else
		<div style="padding-top: 10px; text-align: center;">
			There is no issues.
		</div>
	@endif
@endsection