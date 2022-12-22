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

  namespace ClicShopping\Apps\Catalog\ProductsWarehouse\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Catalog\ProductsWarehouse\ProductsWarehouse as ProductsWarehouseApp;

  class CloneProducts implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('ProductsWarehouse')) {
        Registry::set('ProductsWarehouse', new ProductsWarehouseApp());
      }

      $this->app = Registry::get('ProductsWarehouse');
    }

    public function execute()
    {
      if (!defined('CLICSHOPPING_APP_PRODUCTS_WAREHOUSE_PW_STATUS') || CLICSHOPPING_APP_PRODUCTS_WAREHOUSE_PW_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['Update']) && isset($_POST['clone_categories_id_to'])) {
        $Qproducts = $this->app->db->prepare('select *
                                              from :table_products
                                              where products_id = :products_id
                                             ');
        $Qproducts->bindInt(':products_id', $_GET['pID']);

        $Qproducts->execute();

        $sql_array = ['products_warehouse_time_replenishment' => $Qproducts->value('products_warehouse_time_replenishment'),
          'products_warehouse' => $Qproducts->value('products_warehouse'),
          'products_warehouse_row' => $Qproducts->value('products_warehouse_row'),
          'products_warehouse_level_location' => $Qproducts->value('products_warehouse_level_location')
        ];
        $insert_array = ['products_id' => HTML::sanitize($_POST['clone_products_id'])];

        $this->app->db->save('products', $sql_array, $insert_array);
      }
    }
  }