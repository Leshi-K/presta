<?php
if (!defined('_PS_VERSION_'))
	exit;

require(dirname(__FILE__).'/feedback.class.php');

class Feedback extends Module
{

    public function __construct()
	{
		$this->name = 'Feedback';
		$this->tab = 'FakeFeedback';
		$this->version = '1.0.0';
		$this->author = 'Leshi';
		$this->need_instance = 0;
		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Мой модуль');
		$this->description = $this->l('Мой тестовый модуль.');
	}

	public function install()
	{
		return parent::install() && $this->registerHook('displayHome')  && $this->installDb();
	}

    public function uninstall()
    {
        return parent::uninstall() && $this->uninstallDB();
    }

	public function installDb()
    {
        return (Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'feedback` (
			`id_feedback` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(128) NOT NULL,
            `title` VARCHAR(200) NOT NULL,
            `text` VARCHAR(1000) NOT NULL,
            `date` TIMESTAMP NULL DEFAULT NULL
		) ENGINE = '._MYSQL_ENGINE_.' CHARACTER SET utf8 COLLATE utf8_general_ci;'));
    }

	public function installDb()
    {
        return (Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `ps_feedback` (
			`id_feedback` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(128) NOT NULL,
            `title` VARCHAR(200) NOT NULL,
            `text` VARCHAR(1000) NOT NULL,
            `date` TIMESTAMP NULL DEFAULT NULL
		) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;'));
    }

    protected function uninstallDb()
    {
        Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'feedback`');
        return true;
    }

    //start_helper
    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Добавить отзыв'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Имя'),
                        'name' => 'name',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Заголовок'),
                        'name' => 'title',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Коммент'),
                        'name' => 'feedback',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Сохранить'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPWCOPYRIGHT';
        $helper->currentIndex = $this->context->link->getAdminLink(
            'AdminModules',
            false
        ).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            // 'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getContent()
    {
        $data = array(
            "name"  => Tools::getValue('name'),
            "title" => Tools::getValue('title'),
            "text"  => Tools::getValue('text'),
        );

        Feedback::insertFeedback($data);

        return $this->renderForm();
    }
    //end_helper

	public function hookdisplayHome($params)
    {

        $feedback = Feedback::getRandomFeedback();

        $this->context->smarty->assign(array(
            'feedback' => $feedback
        ));

        return $this->display(__FILE__, '/views/templates/front/index.tpl');
    }


}