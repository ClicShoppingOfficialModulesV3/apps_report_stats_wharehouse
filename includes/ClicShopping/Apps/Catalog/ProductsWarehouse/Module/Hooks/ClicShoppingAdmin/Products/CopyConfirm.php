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

  class CopyConfirm implements \ClicShopping\OM\Modules\HooksInterface
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

      if (isset($_POST['products_id'])) {
        $current_products_id = HTML::sanitize($_POST['products_id']);

        if (isset($current_products_id) && isset($_GET['CopyConfirm'])) {
          $products = $this->app->db->prepare('select products_warehouse_time_replenishment,
                                                      products_warehouse,
                                                      products_warehouse_row,
                                                      products_warehouse_level_location
                                               from :table_products
                                               where products_id = :products_id
                                              ');
          $products->bindInt(':products_id', $current_products_id);
          $products->execute();

          $products_warehouse_time_replenishment = $products->value('products_warehouse_time_replenishment');
          $products_warehouse = $products->value('products_warehouse');
          $products_warehouse_row = $products->value('products_warehouse_row');
          $products_warehouse_level_location = $products->value('products_warehouse_level_location');

          $Qproducts = $this->app->db->prepare('select products_id 
                                                from :table_products                                            
                                                order by products_id desc
                                                limit 1 
                                               ');
          $Qproducts->execute();

          $id = $Qproducts->valueInt('products_id');

          $sql_data_array = ['products_warehouse_time_replenishment' => (int)$products_warehouse_time_replenishment,
            'products_warehouse' => (int)$products_warehouse,
            'products_warehouse_row' => (int)$products_warehouse_row,
            'products_warehouse_level_location' => (int)$products_warehouse_level_location,
          ];

          $this->app->db->save('products', $sql_data_array, ['products_id' => (int)$id]);
        }
      }
    }
  }