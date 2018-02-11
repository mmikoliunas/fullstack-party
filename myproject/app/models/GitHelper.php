<?php

use GuzzleHttp\Client;
use Carbon\Carbon;

class GitHelper {
	
	public function __construct() {
		
		$this->github_settings = Config::get( 'constants.github_settings' );
		$this->guzzle_client = new Client();
	}
	
	protected function makeApiCall( $endpoint = '', $method = 'GET', $params = array() ) {
		
		if ( $endpoint == 'auth' ) {
			$endpoint = 'https://github.com/login/oauth/access_token';
		} else {
			
			$endpoint = 'https://api.github.com/' . $endpoint;
			$endpoint .= ( stripos( $endpoint, '?' ) === false ) ? '?' : '&';
			$endpoint .= 'client_id=' . $this->github_settings['client_id']. '&client_secret=' . $this->github_settings['client_secret'];
		}
		
		$result = $this->guzzle_client->request( $method, $endpoint, $params );
		$result = @json_decode( $result->getBody(), true );
		$result = ( is_array( $result ) ) ? $result : array();
		
		return $result;
	}
	
	public function gitLogin( $code = '' ) {
		
		$params = [
			'headers' => [
				'Accept' => 'application/json'
			],
			'form_params' => [
				'client_id' => $this->github_settings['client_id'],
				'client_secret' => $this->github_settings['client_secret'],
				'code' => $code,
				'accept' => 'application/json'
			]
		];
		
		$result = $this->makeApiCall( 'auth', 'POST', $params );
		
		return ( !empty( $result['access_token'] ) ) ? $result['access_token'] : false;
	}
	
	public function getIssuesInfo() {
		
		$open_issues = $this->makeApiCall( 'search/issues?q=repo:' . $this->github_settings['repo'] . '+is:issue+is:open' );
		$closed_issues = $this->makeApiCall( 'search/issues?q=repo:' . $this->github_settings['repo'] . '+is:issue+is:closed' );  
		
		$info = array(
			'open_count' => ( !empty( $open_issues ) ) ? $open_issues['total_count'] : 0,
			'closed_count' => ( !empty( $closed_issues ) ) ? $closed_issues['total_count'] : 0,
		);
		
		return $info;
	}
	
	public function getIssues( $type = 'open', $page = 0 ) {
		
		$ret_val = array(
			'open_count' => 0,
			'closed_count' => 0,
			'issues' => array(),
			'paging' => array(),
			'current_page' => 0
		);
		
		$issues_info = $this->getIssuesInfo();
		$page = ( !empty( $page ) && is_numeric( $page ) ) ? (int)$page : 1;
		$items_count = ( $type == 'open' ) ? $issues_info['open_count'] : $issues_info['closed_count'];
		$max_pages = ceil( $items_count / $this->github_settings['items_on_page'] );
		$page = min( $page, $max_pages );
		
		$issues = $this->makeApiCall( 'repos/' . $this->github_settings['repo'] . '/issues?state=' . $type . '&page=' . $page . '&per_page=' . $this->github_settings['items_on_page'] );
		
		$offset = ( $page - 1 ) * $this->github_settings['items_on_page'];
		$ret_val['issues'] = $issues;
		$ret_val['paging'] = $this->makePaging( $page, $issues_info['open_count'], $this->github_settings['items_on_page'] );
		$ret_val['current_page'] = $page;
		$ret_val['open_count'] = $issues_info['open_count'];
		$ret_val['closed_count'] = $issues_info['closed_count'];
		
		foreach ( $ret_val['issues'] as $key => $issue ) {
			
			if ( $issue['state'] == 'closed' ) {
				
				$ret_val['issues'][ $key ]['_closed_time_ago_'] = Carbon::parse( $issue['closed_at'] )->diffForHumans();
			} else {
				
				$ret_val['issues'][ $key ]['_created_time_ago_'] = Carbon::parse( $issue['created_at'] )->diffForHumans();
			}
		}
		
		return $ret_val;
	}
	
	public function getIssue( $issue_id = 0 ) {
		
		$issue = $this->makeApiCall( 'repos/' . $this->github_settings['repo'] . '/issues/' . $issue_id );
		
		if ( empty( $issue ) ) {
			
			return $issue;
		}
		
		if ( $issue['state'] == 'closed' ) {
			
			$issue['_closed_time_ago_'] = Carbon::parse( $issue['closed_at'] )->diffForHumans();
		} else {
			
			$issue['_created_time_ago_'] = Carbon::parse( $issue['created_at'] )->diffForHumans();
		}
		
		$issue['_comments_'] = $this->makeApiCall( 'repos/' . $this->github_settings['repo'] . '/issues/' . $issue_id . '/comments' );
		
		foreach ( $issue['_comments_'] as $key => $comment ) {
			
			$issue['_comments_'][ $key ]['_created_time_ago_'] = Carbon::parse( $comment['created_at'] )->diffForHumans();
		}
		
		return $issue;
	}
	
	protected function makePaging( $page, $items_count, $items_on_page ) {
		
		$pages_count = ceil( $items_count / $items_on_page );
		$pages = array();
		
		for ( $i = 1; $i <= $pages_count; $i++ ) {
			
			if ( $i <= 1 ) {
				
				$pages[ $i ] = array(
					'page' => $i,
					'is_page' => true
				);
			}
			
			$page_from_selected = ( $page - 2 ) < 0 ? 0 : ( $page - 2 );
			$page_to_selected = ( $page + 2 ) > $pages_count ? $pages_count : ( $page + 2 );
			
			if ( ( $page_from_selected - 1 ) > 0 && ( $page_from_selected - 1 ) == $i && $i > 1  ) {
				
				$pages[ $i ] = array(
					'is_page' => false
				);
			}
			
			if ( $page_to_selected >= $i && $page_from_selected <= $i ) {
				
				$pages[ $i ] = array(
					'page' => $i,
					'is_page' => true
				);
			}
			
			if ( ( $page_to_selected + 1 ) < $pages_count && ( $page_to_selected + 1 ) == $i ) {
				
				$pages[ $i ] = array(
					'is_page' => false
				);
			}
			
			if ( ( $pages_count - $i ) < 1 ) {
				
				$pages[ $i ] = array(
					'page' => $i,
					'is_page' => true
				);
			}
		}
		
		return $pages;
	}
}