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

  namespace ClicShopping\Apps\Catalog\ProductsWarehouse\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Catalog\ProductsWarehouse\ProductsWarehouse;

  class Home extends \ClicShopping\OM\PagesAbstract
  {
    public mixed $app;

    protected function init()
    {
      $CLICSHOPPING_ProductsWarehouse = new ProductsWarehouse();
      Registry::set('ProductsWarehouse', $CLICSHOPPING_ProductsWarehouse);

      $this->app = Registry::get('ProductsWarehouse');

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
