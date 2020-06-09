<div class="buttons">
    <div class="right">
        <button id="sslczPayBtn" class="btn btn-primary"
            token="<?php echo $tran_id; ?>"
            postdata=""
            order="<?php echo $tran_id; ?>"
            endpoint="<?php echo $process_url; ?>"><?php echo $button_confirm; ?>
        </button>
    </div>
</div>
<?php if($api_type == "YES"){ ?>
    <script type="text/javascript">
    (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src="https://sandbox.sslcommerz.com/embed.min.js?" + Math.random().toString(36).substring(7);
        s1.charset='UTF-8';
        s0.parentNode.insertBefore(s1,s0);
    })();
</script>
<?php } elseif($api_type == "NO"){ ?>
    <script type="text/javascript">
        (function(){
            var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
            s1.async=true;
            s1.src="https://seamless-epay.sslcommerz.com/embed.min.js?" + Math.random().toString(36).substring(7);
            s1.charset='UTF-8';
            s0.parentNode.insertBefore(s1,s0);
        })();
    </script>
<?php } ?>