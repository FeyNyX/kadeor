<?php
if (!defined('_PS_VERSION_'))
    exit;

class Queue extends Module
{
    public function __construct()
    {
        $this->name = 'queue';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Wojciech Lenartowicz';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Queue');
        $this->description = $this->l('Queue for unavailable products.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('QUEUE'))
            $this->warning = $this->l('No name provided');
    }

    public function install()
    {
        if (!parent::install() ||
            !$this->registerHook('rightColumnProduct') ||
            !Configuration::updateValue('QUEUE', 'clients queue')
        )
            return false;

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
            !Configuration::deleteByName('QUEUE')
        )
            return false;

        return true;
    }

    public function hookDisplayRightColumnProduct($params)
    {
        $this->context->smarty->assign(
            array(
                'queue_name' => Configuration::get('QUEUE'),
                'queue_link' => $this->context->link->getModuleLink('queue', 'display')
            )
        );
        return $this->display(__FILE__, 'queue.tpl');
    }

    /**
     * Podpunkt 2:
     * - wysłać zapytanie do bazy poprzez przesłanie danych z formularza do akcji kontrolera odpowiedzialnej za zapisanie ich w bazie (AJAXem)
     * - po otrzymaniu pozytywnej odpowiedzi odpytać bazę (analogicznie jak wyżej) o inne wpisy (count ilości wpisów dotyczących id tego produktu) AJAXem
     * - po odebraniu odpowiedzi wyświetlić JS'em komunikat o miejscu w kolejce
     */

    /**
     * Podpunkt 3 (konfiguracja) może zostać wykonany według http://doc.prestashop.com/display/PS16/Adding+a+configuration+page
     */
}