<?php

/**
 * the purpose of this file is to simply contain any action/filter hook callbacks etc for specific aspects of EE
 * related to caffeinated (regular) use.  Before putting any code in here, First be certain that it isn't better to
 * define and use the hook in a specific caffeinated/whatever class or file.
 */

use EventEspresso\caffeinated\core\domain\services\pue\PueLicensingManager;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\interfaces\InterminableInterface;
use EventEspresso\core\services\database\TableAnalysis;
use EventEspresso\core\services\loaders\LoaderInterface;


/**
 * EE_Brewing_Regular class.  Just a wrapper to help namespace activity for the functionality of this file.
 *
 * @package        Event Espresso
 * @subpackage     /caffeinated/brewing_regular.php
 * @author         Darren Ethier
 */
class EE_Brewing_Regular extends EE_BASE implements InterminableInterface
{
    /**
     * @var EE_Dependency_Map
     */
    protected $dependency_map;

    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * @var TableAnalysis
     */
    protected $_table_analysis;


    /**
     * EE_Brewing_Regular constructor.
     *
     * @param EE_Dependency_Map $dependency_map
     * @param LoaderInterface   $loader
     * @param TableAnalysis     $table_analysis
     */
    public function __construct(
        EE_Dependency_Map $dependency_map,
        LoaderInterface $loader,
        TableAnalysis $table_analysis
    ) {
        $this->dependency_map = $dependency_map;
        $this->loader         = $loader;
        $this->_table_analysis = $table_analysis;
        if (defined('EE_CAFF_PATH')) {
            // defined some new constants related to caffeinated folder
            define('EE_CAF_URL', EE_PLUGIN_DIR_URL . 'caffeinated/');
            define('EE_CAF_CORE', EE_CAFF_PATH . 'core/');
            define('EE_CAF_LIBRARIES', EE_CAF_CORE . 'libraries/');
            define('EE_CAF_PAYMENT_METHODS', EE_CAFF_PATH . 'payment_methods/');
        }
    }


    /**
     * @throws EE_Error
     */
    public function caffeinated()
    {
        $this->setInitializationHooks();
        $this->setApiRegistrationHooks();
        $this->setSwitchHooks();
        $this->setDefaultFilterHooks();
        // caffeinated constructed
        do_action('AHEE__EE_Brewing_Regular__construct__complete');
    }


    public function initializePUE()
    {
        $this->dependency_map->registerDependencies(
            PueLicensingManager::class,
            [
                'EE_Dependency_Map'                          => EE_Dependency_Map::load_from_cache,
                'EventEspresso\core\services\loaders\Loader' => EE_Dependency_Map::load_from_cache,
            ]
        );
        /** @var PueLicensingManager $pue_manager */
        $pue_manager = $this->loader->getShared(PueLicensingManager::class);
        $pue_manager->registerDependencies();
        $pue_manager->registerHooks();
    }


    /**
     * Various hooks used for extending features via registration of modules or extensions.
     *
     * @throws EE_Error
     */
    private function setApiRegistrationHooks()
    {
        add_filter(
            'FHEE__EE_Config__register_modules__modules_to_register',
            [$this, 'caffeinated_modules_to_register']
        );
        add_filter('FHEE__EE_Registry__load_helper__helper_paths', [$this, 'caf_helper_paths'], 10);
        add_filter(
            'AHEE__EE_System__load_core_configuration__complete',
            function () {
                EE_Register_Payment_Method::register(
                    'caffeinated_payment_methods',
                    [
                        'payment_method_paths' => glob(EE_CAF_PAYMENT_METHODS . '*', GLOB_ONLYDIR),
                    ]
                );
            }
        );
    }


    /**
     * Various hooks used for modifying initialization or activation processes.
     */
    private function setInitializationHooks()
    {
        // activation
        add_action('AHEE__EEH_Activation__initialize_db_content', [$this, 'initialize_caf_db_content']);
        // load caff init
        add_action('AHEE__EE_System__set_hooks_for_core', [$this, 'caffeinated_init']);
        // load caff scripts
        add_action('wp_enqueue_scripts', [$this, 'enqueue_caffeinated_scripts'], 10);
    }


    /**
     * Various hooks used for switch (on/off) type filters.
     */
    private function setSwitchHooks()
    {
        // remove the "powered by" credit link from receipts and invoices
        add_filter('FHEE_EE_Html_messenger__add_powered_by_credit_link_to_receipt_and_invoice', '__return_false');
        // seeing how this is caf, which isn't put on WordPress.org, we can have affiliate links without a disclaimer
        add_filter('FHEE__ee_show_affiliate_links', '__return_false');
    }


    /**
     * Various filters for affecting default configuration values in the caffeinated
     * context.
     */
    private function setDefaultFilterHooks()
    {
        add_filter(
            'FHEE__EE_Admin_Config__show_reg_footer__default',
            '__return_true'
        );
    }


    /**
     * callback for the FHEE__EE_Registry__load_helper__helper_paths filter to add the caffeinated paths
     *
     * @param array $paths original helper paths array
     * @return array             new array of paths
     */
    public function caf_helper_paths(array $paths): array
    {
        $paths[] = EE_CAF_CORE . 'helpers/';
        return $paths;
    }


    /**
     * Upon brand-new activation, if this is a new activation of CAF, we want to add
     * some global prices that will show off EE4's capabilities. However, if they're upgrading
     * from 3.1, or simply EE4.x decaf, we assume they don't want us to suddenly introduce these extra prices.
     * This action should only be called when EE 4.x.0.P is initially activated.
     * Right now the only CAF content are these global prices. If there's more in the future, then
     * we should probably create a caf file to contain it all instead just a function like this.
     * Right now, we ASSUME the only price types in the system are default ones
     *
     * @global wpdb $wpdb
     */
    public function initialize_caf_db_content()
    {
        global $wpdb;
        // use same method of getting creator id as the version introducing the change
        $default_creator_id = apply_filters('FHEE__EE_DMS_Core_4_5_0__get_default_creator_id', get_current_user_id());
        $price_type_table   = $wpdb->prefix . "esp_price_type";
        $price_table        = $wpdb->prefix . "esp_price";
        if ($this->_get_table_analysis()->tableExists($price_type_table)) {
            $SQL                  =
                'SELECT COUNT(PRT_ID) FROM ' . $price_type_table . ' WHERE PBT_ID=4';// include trashed price types
            $tax_price_type_count = $wpdb->get_var($SQL);
            if ($tax_price_type_count <= 1) {
                $wpdb->insert(
                    $price_type_table,
                    [
                        'PRT_name'       => esc_html__("Regional Tax", "event_espresso"),
                        'PBT_ID'         => 4,
                        'PRT_is_percent' => true,
                        'PRT_order'      => 60,
                        'PRT_deleted'    => false,
                        'PRT_wp_user'    => $default_creator_id,
                    ],
                    [
                        '%s',// PRT_name
                        '%d',// PBT_id
                        '%d',// PRT_is_percent
                        '%d',// PRT_order
                        '%d',// PRT_deleted
                        '%d', // PRT_wp_user
                    ]
                );
                // federal tax
                $result = $wpdb->insert(
                    $price_type_table,
                    [
                        'PRT_name'       => esc_html__("Federal Tax", "event_espresso"),
                        'PBT_ID'         => 4,
                        'PRT_is_percent' => true,
                        'PRT_order'      => 70,
                        'PRT_deleted'    => false,
                        'PRT_wp_user'    => $default_creator_id,
                    ],
                    [
                        '%s',// PRT_name
                        '%d',// PBT_id
                        '%d',// PRT_is_percent
                        '%d',// PRT_order
                        '%d',// PRT_deleted
                        '%d' // PRT_wp_user
                    ]
                );
                if ($result) {
                    $wpdb->insert(
                        $price_table,
                        [
                            'PRT_ID'         => $wpdb->insert_id,
                            'PRC_amount'     => 15.00,
                            'PRC_name'       => esc_html__("Sales Tax", "event_espresso"),
                            'PRC_desc'       => '',
                            'PRC_is_default' => true,
                            'PRC_overrides'  => null,
                            'PRC_deleted'    => false,
                            'PRC_order'      => 50,
                            'PRC_parent'     => null,
                            'PRC_wp_user'    => $default_creator_id,
                        ],
                        [
                            '%d',// PRT_id
                            '%f',// PRC_amount
                            '%s',// PRC_name
                            '%s',// PRC_desc
                            '%d',// PRC_is_default
                            '%d',// PRC_overrides
                            '%d',// PRC_deleted
                            '%d',// PRC_order
                            '%d',// PRC_parent
                            '%d' // PRC_wp_user
                        ]
                    );
                }
            }
        }
    }


    /**
     *    caffeinated_modules_to_register
     *
     * @access public
     * @param array $modules_to_register
     * @return array
     */
    public function caffeinated_modules_to_register(array $modules_to_register = []): array
    {
        if (is_readable(EE_CAFF_PATH . 'modules')) {
            $caffeinated_modules_to_register = glob(EE_CAFF_PATH . 'modules/*', GLOB_ONLYDIR);
            if (is_array($caffeinated_modules_to_register) && ! empty($caffeinated_modules_to_register)) {
                $modules_to_register = array_merge($modules_to_register, $caffeinated_modules_to_register);
            }
        }
        return $modules_to_register;
    }


    /**
     * @throws EE_Error
     * @throws InvalidArgumentException
     * @throws ReflectionException
     * @throws InvalidDataTypeException
     * @throws InvalidInterfaceException
     */
    public function caffeinated_init()
    {
        // Custom Post Type hooks
        add_filter(
            'FHEE__EventEspresso_core_domain_entities_custom_post_types_TaxonomyDefinitions__getTaxonomies',
            [$this, 'filter_taxonomies']
        );
        add_filter(
            'FHEE__EventEspresso_core_domain_entities_custom_post_types_CustomPostTypeDefinitions__getCustomPostTypes',
            [$this, 'filter_cpts']
        );
        add_filter(
            'FHEE__EE_Admin__get_extra_nav_menu_pages_items',
            [$this, 'nav_metabox_items']
        );
        EE_Registry::instance()->load_file(
            EE_CAFF_PATH,
            'EE_Caf_Messages',
            'class',
            [],
            false
        );
        // caffeinated_init__complete hook
        do_action('AHEE__EE_Brewing_Regular__caffeinated_init__complete');
    }


    public function enqueue_caffeinated_scripts()
    {
        // sound of crickets...
    }


    /**
     * callbacks below here
     *
     * @param array $taxonomy_array
     * @return array
     */
    public function filter_taxonomies(array $taxonomy_array): array
    {
        $taxonomy_array['espresso_venue_categories']['args']['show_in_nav_menus'] = true;
        return $taxonomy_array;
    }


    /**
     * @param array $cpt_array
     * @return array
     */
    public function filter_cpts(array $cpt_array): array
    {
        $cpt_array['espresso_venues']['args']['show_in_nav_menus'] = true;
        return $cpt_array;
    }


    /**
     * @param array $menu_items
     * @return array
     */
    public function nav_metabox_items(array $menu_items): array
    {
        $menu_items[] = [
            'title'       => esc_html__('Venue List', 'event_espresso'),
            'url'         => get_post_type_archive_link('espresso_venues'),
            'description' => esc_html__('Archive page for all venues.', 'event_espresso'),
        ];
        return $menu_items;
    }


    /**
     * Gets the injected table analyzer, or throws an exception
     *
     * @return TableAnalysis
     */
    protected function _get_table_analysis(): TableAnalysis
    {
        return $this->_table_analysis;
    }
}
