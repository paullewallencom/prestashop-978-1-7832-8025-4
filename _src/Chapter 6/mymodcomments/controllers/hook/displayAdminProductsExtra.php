<?php

class MyModCommentsDisplayAdminProductsExtraController
{
	public function __construct($module, $file, $path)
	{
		$this->file = $file;
		$this->module = $module;
		$this->context = Context::getContext();
		$this->_path = $path;
	}

	public function run()
	{
		// Get number of comments
		$id_product = (int)Tools::getValue('id_product');
		$nb_comments = MyModComment::getProductNbComments((int)$id_product);

		// Init
		$page = 1;
		$nb_per_page = 10;
		$nb_pages = ceil($nb_comments / $nb_per_page);
		if (Tools::getIsset('page'))
			$page = (int)Tools::getValue('page');
		$limit_start = ($page - 1) * $nb_per_page;
		$limit_end = $nb_per_page;

		// Build actions url
		$ajax_action_url = $this->context->link->getAdminLink('AdminModules', true);
		$ajax_action_url = str_replace('index.php', 'ajax-tab.php', $ajax_action_url);
		$action_url = $this->context->link->getAdminLink('AdminMyModComments', true);

		// Get comments
		$comments = MyModComment::getProductComments((int)$id_product, (int)$limit_start, (int)$limit_end);

		// Assign comments and product object
		$this->context->smarty->assign('page', $page);
		$this->context->smarty->assign('nb_pages', $nb_pages);
		$this->context->smarty->assign('comments', $comments);
		$this->context->smarty->assign('action_url', $action_url);
		$this->context->smarty->assign('ajax_action_url', $ajax_action_url);
		$this->context->smarty->assign('pc_base_dir', __PS_BASE_URI__.'modules/'.$this->module->name.'/');

		return $this->module->display($this->file, 'displayAdminProductsExtra.tpl');
	}
}