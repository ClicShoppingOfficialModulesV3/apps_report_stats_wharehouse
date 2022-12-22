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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Language = Registry::get('Language');

  $CLICSHOPPING_ProductsWarehouse = Registry::get('ProductsWarehouse');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

  // get list of order status
  $orders_statuses = [];
  $orders_status_array = [];

  $QordersStatus = $CLICSHOPPING_ProductsWarehouse->db->prepare('select orders_status_id,
                                                                        orders_status_name
                                                                 from :table_orders_status
                                                                 where language_id = :language_id
                                                                 order by orders_status_id
                                                                ');

  $QordersStatus->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
  $QordersStatus->execute();

  while ($QordersStatus->fetch()) {
    $orders_statuses[] = ['id' => $QordersStatus->valueInt('orders_status_id'),
      'text' => $QordersStatus->value('orders_status_name')
    ];
    $orders_status_array[$QordersStatus->valueInt('orders_status_id')] = $QordersStatus->value('orders_status_name');
  }
?>

<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/warehouse.png', $CLICSHOPPING_ProductsWarehouse->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-5 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_ProductsWarehouse->getDef('heading_title'); ?></span>
          <span class="col-md-5 text-end">
<?php
  echo HTML::form('status', $CLICSHOPPING_ProductsWarehouse->link('ProductsWarehouse'), 'post', 'class="form-inline"', ['session_id' => true]);
  echo HTML::selectMenu('status', array_merge(array(array('id' => '0', 'text' => $CLICSHOPPING_ProductsWarehouse->getDef('title_status'))), $orders_statuses), '', 'onchange="this.form.submit();"');

?>
              </span>
          <span class="col-md-1">
<?php
  if (isset($_POST['status'])) {
    echo HTML::button($CLICSHOPPING_ProductsWarehouse->getDef('button_reset'), $CLICSHOPPING_ProductsWarehouse->link('ProductsWarehouse&page=' . $page), null, 'warning');
  }
?>
              </span>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <td>
      <table class="table table-sm table-hover table-striped">
        <thead>
        <tr class="dataTableHeadingRow">
          <th></th>
          <th width="130"></th>
          <th
            class="text-center"><?php echo $CLICSHOPPING_ProductsWarehouse->getDef('table_heading_order_id'); ?></th>
          <th
            class="text-center"><?php echo $CLICSHOPPING_ProductsWarehouse->getDef('table_heading_warehouse'); ?></th>
          <th
            class="text-center"><?php echo $CLICSHOPPING_ProductsWarehouse->getDef('table_heading_warehouse_row'); ?></th>
          <th
            class="text-center"><?php echo $CLICSHOPPING_ProductsWarehouse->getDef('table_heading_warehouse_level'); ?></th>
          <th class="text-center"><?php echo $CLICSHOPPING_ProductsWarehouse->getDef('table_heading_model'); ?></th>
          <th
            class="text-center"><?php echo $CLICSHOPPING_ProductsWarehouse->getDef('table_heading_products_name'); ?></th>
          <th
            class="text-center"><?php echo $CLICSHOPPING_ProductsWarehouse->getDef('table_heading_packaging'); ?></th>
          <th
            class="text-center"><?php echo $CLICSHOPPING_ProductsWarehouse->getDef('table_heading_order_id'); ?></th>
          <th
            class="text-center"><?php echo $CLICSHOPPING_ProductsWarehouse->getDef('table_heading_customers_id'); ?></th>
          <th
            class="text-center"><?php echo $CLICSHOPPING_ProductsWarehouse->getDef('table_heading_customers_name'); ?></th>
          <th
            class="text-center"><?php echo $CLICSHOPPING_ProductsWarehouse->getDef('table_heading_customers_phone'); ?></th>
          <th
            class="text-center"><?php echo $CLICSHOPPING_ProductsWarehouse->getDef('table_heading_qty_left'); ?></th>
          <th
            class="text-center"><?php echo $CLICSHOPPING_ProductsWarehouse->getDef('table_heading_warehouse_time_replenishment'); ?></th>
          <th class="text-center"><?php echo $CLICSHOPPING_ProductsWarehouse->getDef('text_action'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
          if (isset($_POST['status'])) {
            $status = HTML::sanitize($_POST['status']);
          } else {
            $status = 0;
          }

          if ($status > 0) {

            $Qproducts = $CLICSHOPPING_ProductsWarehouse->db->prepare('select  SQL_CALC_FOUND_ROWS  op.products_id,
                                                                                                  op.orders_products_id,
                                                                                                  op.orders_id,
                                                                                                  op.products_model,
                                                                                                  op.products_name,
                                                                                                  op.products_quantity as customers_order_quantity,
                                                                                                  o.orders_id,
                                                                                                  o.customers_id,
                                                                                                  o.customers_name,
                                                                                                  o.customers_telephone,
                                                                                                  o.orders_status,
                                                                                                  p.products_quantity,
                                                                                                  p.products_warehouse_time_replenishment,
                                                                                                  p.products_warehouse,
                                                                                                  p.products_warehouse_row,
                                                                                                  p.products_warehouse_level_location,
                                                                                                  p.products_packaging,
                                                                                                  p.products_image
                                                                    from  :table_orders_products op left join :table_products p on (op.products_id = p.products_id),
                                                                         :table_orders o
                                                                    where  op.orders_id = o.orders_id
                                                                    and o.orders_status = :status
                                                                    order by o.orders_id, o.date_purchased DESC
                                                                    limit :page_set_offset,
                                                                          :page_set_max_results
                                                                    ');

            $Qproducts->bindInt(':status', $status);
            $Qproducts->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
            $Qproducts->execute();

          } else {

            $Qproducts = $CLICSHOPPING_ProductsWarehouse->db->prepare('select  SQL_CALC_FOUND_ROWS  op.products_id,
                                                                                                  op.orders_products_id,
                                                                                                  op.orders_id,
                                                                                                  op.products_model,
                                                                                                  op.products_name,
                                                                                                  op.products_quantity as customers_order_quantity,
                                                                                                  o.orders_id,
                                                                                                  o.customers_id,
                                                                                                  o.customers_name,
                                                                                                  o.customers_telephone,
                                                                                                  o.orders_status,
                                                                                                  p.products_quantity,
                                                                                                  p.products_warehouse_time_replenishment,
                                                                                                  p.products_warehouse,
                                                                                                  p.products_warehouse_row,
                                                                                                  p.products_warehouse_level_location,
                                                                                                  p.products_packaging,
                                                                                                  p.products_image
                                                                    from  :table_orders_products op left join :table_products p on (op.products_id = p.products_id),
                                                                          :table_orders o
                                                                    where  op.orders_id = o.orders_id
                                                                    and o.orders_status = 1
                                                                    order by o.orders_id, o.date_purchased DESC
                                                                    limit :page_set_offset,
                                                                          :page_set_max_results
                                                                    ');

            $Qproducts->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
            $Qproducts->execute();
          }

          $listingTotalRow = $Qproducts->getPageSetTotalRows();

          if ($listingTotalRow > 0) {
            $rows = 0;

            while ($Qproducts->fetch()) {
              $rows++;

              if (strlen($rows) < 2) {
                $rows = '0' . $rows;
              }

              if ($Qproducts->valueInt('products_packaging') == 0) $products_packaging = '';
              if ($Qproducts->valueInt('products_packaging') == 1) $products_packaging = $CLICSHOPPING_ProductsWarehouse->getDef('text_products_packaging_new');
              if ($Qproducts->valueInt('products_packaging') == 2) $products_packaging = $CLICSHOPPING_ProductsWarehouse->getDef('text_products_packaging_repackaged');
              if ($Qproducts->valueInt('products_packaging') == 3) $products_packaging = $CLICSHOPPING_ProductsWarehouse->getDef('text_products_used');
              ?>
              <tr>
                <td scope="row"
                    width="50px"><?php echo HTML::link(CLICSHOPPING::link(null, 'A&Catalog\Products&Preview&pID=' . $Qproducts->valueInt('products_id') . '?page=' . $page), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/preview.gif', $CLICSHOPPING_ProductsWarehouse->getDef('text_preview'))); ?></td>
                <td><?php echo HTML::image($CLICSHOPPING_Template->getDirectoryShopTemplateImages() . $Qproducts->value('products_image'), $Qproducts->value('products_name'), (int)SMALL_IMAGE_WIDTH_ADMIN, (int)SMALL_IMAGE_HEIGHT_ADMIN); ?></td>
                <td class="text-end"><?php echo $Qproducts->valueInt('customers_order_quantity'); ?></td>
                <td class="text-end"><?php echo $Qproducts->value('products_warehouse'); ?></td>
                <td class="text-end"><?php echo $Qproducts->value('products_warehouse_row'); ?></td>
                <td class="text-end"><?php echo $Qproducts->value('products_warehouse_level_location'); ?></td>
                <td class="text-end"><?php echo $Qproducts->value('products_model'); ?></td>
                <td class="text-end"><?php echo $Qproducts->value('products_name'); ?></td>
                <td class="text-end"><?php echo $products_packaging; ?></td>
                <td class="text-end"><?php echo $Qproducts->valueInt('orders_id'); ?></td>
                <td class="text-end"><?php echo $Qproducts->valueInt('customers_id'); ?></td>
                <td class="text-end"><?php echo $Qproducts->value('customers_name'); ?></td>
                <td class="text-center"><?php echo $Qproducts->value('customers_telephone'); ?></td>
                <td class="text-center"><?php echo $Qproducts->valueInt('products_quantity'); ?></td>
                <td
                  class="text-center"><?php echo $Qproducts->value('products_warehouse_time_replenishment'); ?></td>
                <td class="text-end" width="75">
                  <?php
                    echo HTML::link(CLICSHOPPING::link(null, 'A&Customers\Customers&Edit&cID=' . $Qproducts->valueInt('customers_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/client_b2b.gif', $CLICSHOPPING_ProductsWarehouse->getDef('icon_edit_customer')));
                    echo '&nbsp;';
                    echo HTML::link(CLICSHOPPING::link(null, 'A&Orders\Orders&Edit&oID=' . $Qproducts->valueInt('orders_id')), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/order.gif', $CLICSHOPPING_ProductsWarehouse->getDef('icon_edit_order')));
                    echo '&nbsp;';
                    echo HTML::link(CLICSHOPPING::link(null, 'A&Catalog\Products&Products&pID=' . $Qproducts->valueInt('products_id') . '&action=new_product'), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_ProductsWarehouse->getDef('icon_edit')));
                  ?>
                </td>
              </tr>
              <?php
            }
          } // end $listingTotalRow
        ?>
        </tbody>
      </table>
    </td>
  </table>
  <?php
    if ($listingTotalRow > 0) {
      ?>
      <div class="row">
        <div class="col-md-12">
          <div
            class="col-md-6 float-start pagenumber hidden-xs TextDisplayNumberOfLink"><?php echo $Qproducts->getPageSetLabel($CLICSHOPPING_ProductsWarehouse->getDef('text_display_number_of_link')); ?></div>
          <div
            class="float-end text-end"> <?php echo $Qproducts->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y'))); ?></div>
        </div>
      </div>
      <?php
    } // end $listingTotalRow
  ?>
</div>


