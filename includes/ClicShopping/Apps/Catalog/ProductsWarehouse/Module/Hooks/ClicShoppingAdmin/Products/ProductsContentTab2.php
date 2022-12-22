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

  class ProductsContentTab2 implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('ProductsWarehouse')) {
        Registry::set('ProductsWarehouse', new ProductsWarehouseApp());
      }

      $this->app = Registry::get('ProductsWarehouse');
      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Products/page_content_tab_2');
    }


    public function display()
    {

      if (!defined('CLICSHOPPING_APP_PRODUCTS_WAREHOUSE_PW_STATUS') || CLICSHOPPING_APP_PRODUCTS_WAREHOUSE_PW_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['pID'])) {
        $products = $this->app->db->prepare('select products_warehouse_time_replenishment,
                                                    products_warehouse,
                                                    products_warehouse_row,
                                                    products_warehouse_level_location
                                             from :table_products
                                             where products_id = :products_id
                                            ');
        $products->bindInt(':products_id', HTML::sanitize($_GET['pID']));
        $products->execute();

        $products_warehouse_time_replenishment = $products->value('products_warehouse_time_replenishment');
        $products_warehouse = $products->value('products_warehouse');
        $products_warehouse_row = $products->value('products_warehouse_row');
        $products_warehouse_level_location = $products->value('products_warehouse_level_location');
      }

        $content = '<div class="mainTitle">' . $this->app->getDef('text_warehouse') . '</div>';
        $content .= '<div class="adminformTitle">';
        $content .= '<div class="row">';
        $content .= '<div class="col-md-5">';
        $content .= '<div class="form-group row">';
        $content .= '<label for="' . $this->app->getDef('text_products_time_replenishment') . '" class="col-5 col-form-label">' . $this->app->getDef('text_products_time_replenishment') . '</label>';
        $content .= '<div class="col-md-5">';
        $content .= HTML::inputField('products_warehouse_time_replenishment', $products_warehouse_time_replenishment ?? null, 'id="products_warehouse_time_replenishment" size="15"');
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '<div class="col-md-5">';
        $content .= '<div class="form-group row">';
        $content .= '<label for="' . $this->app->getDef('text_products_warehouse') . '" class="col-5 col-form-label">' . $this->app->getDef('text_products_warehouse') . '</label>';
        $content .= '<div class="col-md-5">';
        $content .= HTML::inputField('products_warehouse', $products_warehouse ?? null, 'id="products_warehouse" size="15"');
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';

        $content .= '<div class="row">';
        $content .= '<div class="col-md-5">';
        $content .= '<div class="form-group row">';
        $content .= '<label for="' . $this->app->getDef('text_products_warehouse_row') . '" class="col-5 col-form-label">' . $this->app->getDef('text_products_warehouse_row') . '</label>';
        $content .= '<div class="col-md-5">';
        $content .= HTML::inputField('products_warehouse_row', $products_warehouse_row ?? null, 'id="products_warehouse_row" size="15"');
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= ' <div class="col-md-5">';
        $content .= '<div class="form-group row">';
        $content .= '<label for="' . $this->app->getDef('text_products_warehouse_level_location') . '" class="col-5 col-form-label">' . $this->app->getDef('text_products_warehouse_level_location') . '</label>';
        $content .= '<div class="col-md-5">';
        $content .= HTML::inputField('products_warehouse_level_location', $products_warehouse_level_location ?? null, 'id="products_warehouse_level_location" size="15"');
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';

        $output = <<<EOD
  <!-- ######################## -->
  <!--  Start Warehouse Hooks      -->
  <!-- ######################## -->
  <script>
  $('#tab2ContentRow7').prepend(
      '{$content}'
  );
  </script>
  
  <!-- ######################## -->
  <!--  End Warehouse App      -->
  <!-- ######################## -->
  
  EOD;
        return $output;
      }
  }