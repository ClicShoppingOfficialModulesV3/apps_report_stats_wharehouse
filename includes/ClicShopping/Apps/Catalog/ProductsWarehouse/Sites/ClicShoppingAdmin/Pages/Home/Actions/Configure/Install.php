<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Catalog\ProductsWarehouse\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;

  use ClicShopping\OM\Cache;
  use ClicShopping\OM\CLICSHOPPING;

  class Install extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_ProductsWarehouse = Registry::get('ProductsWarehouse');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_ProductsWarehouse->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('ProductsWarehouseAdminConfig' . $current_module);
      $m->install();

      static::installDb();
      static::installDbMenuAdministration();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_ProductsWarehouse->getDef('alert_module_install_success'), 'success', 'ProductsWarehouse');

      $CLICSHOPPING_ProductsWarehouse->redirect('Configure&module=' . $current_module);
    }

    private static function installDb()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->query("show columns from :table_products like 'products_warehouse_time_replenishment'");

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
ALTER TABLE :table_products ADD products_warehouse_time_replenishment VARCHAR(255) NULL AFTER products_type;
ALTER TABLE :table_products ADD products_warehouse VARCHAR(255) NULL AFTER products_warehouse_time_replenishment;
ALTER TABLE :table_products ADD products_warehouse_row VARCHAR(255) NULL AFTER products_warehouse;
ALTER TABLE :table_products ADD products_warehouse_level_location VARCHAR(255) NULL AFTER products_warehouse_row;
EOD;

        $CLICSHOPPING_Db->exec($sql);
      }
    }

    private static function installDbMenuAdministration()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_ProductsWarehouse = Registry::get('ProductsWarehouse');
      $CLICSHOPPING_Language = Registry::get('Language');
      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_report_products_warehouse']);

      if ($Qcheck->fetch() === false) {

        $sql_data_array = ['sort_order' => 5,
          'link' => 'index.php?A&Catalog\ProductsWarehouse&ProductsWarehouse',
          'image' => 'warehouse.png',
          'b2b_menu' => 0,
          'access' => 0,
          'app_code' => 'app_report_products_warehouse'
        ];

        $insert_sql_data = ['parent_id' => 107];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_ProductsWarehouse->getDef('title_menu')];

          $insert_sql_data = ['id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);
        }

        Cache::clear('menu-administrator');
      }
    }
  }
