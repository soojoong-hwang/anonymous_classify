<?php
    if(!defined("__XE__")) exit();

	if($called_position == 'after_module_proc' && $this->act == "procBoardInsertDocument") {
		$module = Context::get('module');
		if(!$module) $module = $this->module;
		if($module != 'board') return;

		if($this->module_info->use_anonymous == 'Y') {
				$logged_info = Context::get('logged_info');

				$args->document_srl = $this->get('document_srl');
				$args->nick_name = iconv("euc-kr","UTF-8","익명_").substr(md5($args->document_srl.$addon_info->security_code.$logged_info->member_srl),0,6);
				executeQuery('addons.anonymous_classify.updatedocuments', $args);
		}
	}

	if($called_position == 'after_module_proc' && $this->act == "procBoardInsertComment") {
		if($this->module_info->use_anonymous == 'Y') {
				$args->document_srl = Context::get('document_srl');
				$args->comment_srl = $this->get('comment_srl');

				$oDocumentModel = &getModel('document');
				$oDocument = $oDocumentModel->getDocument($args->document_srl);
				$oget_member_srl = $oDocument->get('member_srl');

				$logged_info = Context::get('logged_info');
				$check = $oget_member_srl != -1*$logged_info->member_srl ? true : false;

				if(!$logged_info->member_srl && !$oget_member_srl) {
					//'회원 글작성자'와 '비회원 댓글작성자'의 ip가 우연히 같을 경우 '글쓴이'로 표시하지 않도록 함
					$logged_info->member_srl = $_SERVER['REMOTE_ADDR']; //방금 댓글 쓴 사용자의 ip
					$oget_member_srl = $oDocument->get('ipaddress'); //글을 쓴 사용자의 ip

					$check = $oget_member_srl == $logged_info->member_srl ? false : true;
				}

				if($check) {
					$args->nick_name = iconv("euc-kr","UTF-8","익명_").substr(md5($args->document_srl.$addon_info->security_code.$logged_info->member_srl),0,6);
				} else {$args->nick_name = iconv("euc-kr","UTF-8","글쓴이"); }
				executeQuery('addons.anonymous_classify.updatecomments', $args);
		}
	}
?>