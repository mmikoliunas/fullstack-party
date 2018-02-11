<?php

class PagesController extends BaseController {
	
	public function logout() {
		
		Session::forget( 'github_access_token' );
			
		return Redirect::to( '/' );
	}
	
	public function showIndex() {
		
		$github_settings = Config::get( 'constants.github_settings' );
		$github_login_url = ( !empty( $github_settings['client_id'] ) ) ? 'https://github.com/login/oauth/authorize?scope=user:email&client_id=' . $github_settings['client_id'] : '';
		
		return View::make('index', [ 'github_login_url' => $github_login_url ] );
	}
	
	public function showMain() {
		
		$page = Input::get( 'p' );
		$page = ( !empty( $page ) && is_numeric( $page ) ) ? (int)$page : 1;
		$type = Input::get( 't' );
		$type = ( !empty( $type ) && in_array( $type, array( 'open', 'closed' ) ) ) ? $type : 'open';
		$github_access_token = Session::get( 'github_access_token' );
		
		if ( empty( $github_access_token ) ) {
			
			return Redirect::to( '/' );
		}
		
		$oGit = new GitHelper();
		$issues = $oGit->getIssues( $type, $page );
		$issues['current_type'] = $type;
		
		return View::make( 'main', $issues );
	}
	
	public function processGithubCallback() {
		
		$code = Input::get( 'code' );
		$oGit = new GitHelper();
		$github_access_token = $oGit->gitLogin( $code );
		
		if ( !empty( $github_access_token ) ) {
			
			Session::put( 'github_access_token', $github_access_token );
			
			return Redirect::to( '/main' );
		}
		
		return $this->showIndex();
	}
	
	public function showIssue() {
		
		$issue_id = Input::get( 'id' );
		$issue_id = ( !empty( $issue_id ) && is_numeric( $issue_id ) ) ? (int)$issue_id : 0;
		$oGit = new GitHelper();
		$issue = $oGit->getIssue( $issue_id );
		
		if ( empty( $issue ) ) {
			
			return Redirect::to( '/main' );
		}
		
		return View::make( 'issue', [ 'issue' => $issue ] );
	}
}
