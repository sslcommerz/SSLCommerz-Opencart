<form action="<?php echo $URL; ?>" method="post">

  <input type="hidden" name="store_id" value="<?php echo $merchant; ?>" />
  <input type="hidden" name="tran_id" value="<?php echo $trans_id; ?>" />
  <input type="hidden" name="total_amount" value="<?php echo $amount; ?>" />
  <input type="hidden" name="currency" value="<?php echo $currency; ?>" />
  
  <input type="hidden" name="success_url" value="<?php echo $callback; ?>" />
  <input type="hidden" name="cancel_url" value="<?php echo $callback; ?>" />
  <input type="hidden" name="fail_url" value="<?php echo $callback; ?>" />
  
  <input type="hidden" name="cus_name" value="<?php echo $bill_name; ?>" />
  <input type="hidden" name="cus_add1" value="<?php echo $bill_addr_1; ?>" />
  <input type="hidden" name="cus_add2" value="<?php echo $bill_addr_2; ?>" />
  <input type="hidden" name="cus_city" value="<?php echo $bill_city; ?>" />
  <input type="hidden" name="cus_state" value="<?php echo $bill_state; ?>" />
  <input type="hidden" name="cus_postcode" value="<?php echo $bill_post_code; ?>" />
  <input type="hidden" name="cus_country" value="<?php echo $bill_country; ?>" />
  <input type="hidden" name="cus_phone" value="<?php echo $bill_tel; ?>" />
  <input type="hidden" name="cus_email" value="<?php echo $bill_email; ?>" />
  
  <input type="hidden" name="ship_name" value="<?php echo $ship_name; ?>" />
  <input type="hidden" name="ship_add1" value="<?php echo $ship_addr_1; ?>" />
  <input type="hidden" name="ship_add2" value="<?php echo $ship_addr_2; ?>" />
  <input type="hidden" name="ship_city" value="<?php echo $ship_city; ?>" />
  <input type="hidden" name="ship_state" value="<?php echo $ship_state; ?>" />
  <input type="hidden" name="ship_postcode" value="<?php echo $ship_post_code; ?>" />
  <input type="hidden" name="ship_country" value="<?php echo $ship_country; ?>" />
  
  <input type="hidden" name="value_a" value="<?php echo $detail1_text; ?>" />
  <div class="buttons">
    <div class="right">
      <input type="submit" value="<?php echo $button_confirm; ?>" class="button" />
    </div>
  </div>
</form>
