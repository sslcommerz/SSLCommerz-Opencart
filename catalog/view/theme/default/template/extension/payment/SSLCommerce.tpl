<form action="<?php echo $process_url; ?>" method="post">
  <input type="hidden" name="store_id" value="<?php echo $store_id; ?>" />
  <input type="hidden" name="tran_id" value="<?php echo $tran_id; ?>" />
  <input type="hidden" name="total_amount" value="<?php echo $total_amount; ?>" />
  <input type="hidden" name="cus_name" value="<?php echo $cus_name; ?>" />
  <input type="hidden" name="cus_add1" value="<?php echo $cus_add1; ?>" />
  <input type="hidden" name="cus_add2" value="<?php echo $cus_add2; ?>" />
  <input type="hidden" name="cus_city" value="<?php echo $cus_city; ?>" />
  <input type="hidden" name="cus_state" value="<?php echo $cus_state; ?>" />
  <input type="hidden" name="cus_postcode" value="<?php echo $cus_postcode; ?>" />
  <input type="hidden" name="cus_country" value="<?php echo $cus_country; ?>" />
  <input type="hidden" name="cus_phone" value="<?php echo $cus_phone; ?>" />
  <input type="hidden" name="cus_email" value="<?php echo $cus_email; ?>" />
  <input type="hidden" name="ship_name" value="<?php echo $ship_name; ?>" />
  <input type="hidden" name="ship_add1" value="<?php echo $ship_add1; ?>" />
  <input type="hidden" name="ship_add2" value="<?php echo $ship_add2; ?>" />
  <input type="hidden" name="ship_city" value="<?php echo $ship_city; ?>" />
  <input type="hidden" name="ship_state" value="<?php echo $ship_state; ?>" />
  <input type="hidden" name="ship_postcode" value="<?php echo $ship_postcode; ?>" />
  <input type="hidden" name="ship_country" value="<?php echo $ship_country; ?>" />
  <input type="hidden" name="currency" value="<?php echo $currency; ?>" />
  <input type="hidden" name="success_url" value="<?php echo $success_url; ?>" />
  <input type="hidden" name="fail_url" value="<?php echo $fail_url; ?>" />
  <input type="hidden" name="cancel_url" value="<?php echo $cancel_url; ?>" />
  
  
  <input type="hidden" name="value_a" value="<?php echo $detail1_text; ?>" />
  
  <input type="hidden" name="verify_sign" value="<?php echo $verify_sign; ?>" />
  <input type="hidden" name="verify_key" value="<?php echo $verify_key; ?>" />
  
  <div class="buttons">
    <div class="right">
      <input type="submit" value="<?php echo $button_confirm; ?>" class="btn btn-primary" />
    </div>
  </div>
</form>