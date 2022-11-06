<?php
    if(!defined("__XE__")) exit();

	if($called_position == 'after_module_proc' && $this->act == "procBoardInsertDocument") {
		$module = Context::get('module');
		if(!$module) $module = $this->module;
		if($module != 'board') return;

		if($this->module_info->use_anonymous == 'Y') {
				$logged_info = Context::get('logged_info');

				$args->document_srl = $this->get('document_srl');
				$args->nick_name = iconv("euc-kr","UTF-8","�͸�_").substr(md5($args->document_srl.$addon_info->security_code.$logged_info->member_srl),0,6);
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
					//'ȸ�� ���ۼ���'�� '��ȸ�� ����ۼ���'�� ip�� �쿬�� ���� ��� '�۾���'�� ǥ������ �ʵ��� ��
					$logged_info->member_srl = $_SERVER['REMOTE_ADDR']; //��� ��� �� ������� ip
					$oget_member_srl = $oDocument->get('ipaddress'); //���� �� ������� ip

					$check = $oget_member_srl == $logged_info->member_srl ? false : true;
				}

				if($check) {
					$args->nick_name = iconv("euc-kr","UTF-8","�͸�_").substr(md5($args->document_srl.$addon_info->security_code.$logged_info->member_srl),0,6);
				} else {$args->nick_name = iconv("euc-kr","UTF-8","�۾���"); }
				executeQuery('addons.anonymous_classify.updatecomments', $args);
		}
	}
?>