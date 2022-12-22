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

  class Update implements \ClicShopping\OM\Modules\HooksInterface
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

      if (isset($_GET['Update'])) {
        if (isset($_GET['pID'])) {
          $id = HTML::sanitize($_GET['pID']);

          if (isset($_POST['products_warehouse_time_replenishment'])) {
            $products_warehouse_time_replenishment = HTML::sanitize($_POST['products_warehouse_time_replenishment']);
          } else {
            $products_warehouse_time_replenishment = '';
          }

          if (isset($_POST['products_warehouse'])) {
            $products_warehouse = HTML::sanitize($_POST['products_warehouse']);
          } else {
            $products_warehouse= '';
          }

          if (isset($_POST['products_warehouse_row'])) {
            $products_warehouse_row = HTML::sanitize($_POST['products_warehouse_row']);
          } else {
            $products_warehouse_row = '';
          }

          if (isset($_POST['products_warehouse_level_location'])) {
            $products_warehouse_level_location = HTML::sanitize($_POST['products_warehouse_level_location']);
          } else {
            $products_warehouse_level_location ='';
          }

          $sql_data_array = ['products_warehouse_time_replenishment' => $products_warehouse_time_replenishment,
            'products_warehouse' => $products_warehouse,
            'products_warehouse_row' => $products_warehouse_row,
            'products_warehouse_level_location' => $products_warehouse_level_location,
          ];

          $this->app->db->save('products', $sql_data_array, ['products_id' => (int)$id]);
        }
      }
    }
  }