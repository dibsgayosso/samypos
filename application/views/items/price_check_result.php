<?php $this->load->view("partial/header_standalone"); ?>
<style>
    /* Custom styles */
    .header {
      background-color: #489ee7;
      color: white;
      text-align: center;
      padding: 10px;
    }
    .container {
      margin-top: 40px;
    }
    .result-container {
      background-color: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
      text-align: center;
      margin-top: 20px;
    }
    .item-image {
      max-width: 100%;
      height: auto;
      max-height: 400px;
      margin-bottom: 20px;
    }
    .item-details {
      background-color: #333;
      color: white;
      padding: 20px;
      border-radius: 10px;
      margin-top: 20px;
      text-align: left;
    }
    .item-details h3 {
      margin-top: 0;
    }
    .price {
      font-size: 64px;
      font-weight: bold;
      margin-bottom: 20px;
    }
    .back-button {
      font-size: 18px;
      background-color: #489ee7;
      border: none;
      padding: 10px 20px;
      border-radius: 10px;
      cursor: pointer;
      margin-top: 20px;
      text-align: left;
      color: white;
      text-align: left; /* Added for left alignment */
    }
    .stock-status {
      font-size: 24px;
      font-weight: bold;
    }
    .in-stock {
      color: green;
    }
    .out-of-stock {
      color: red;
    }
  </style>
 <!-- Header -->
  <div class="header">
    <h1><?php echo lang('common_price_lookup');?></h1>
  </div>

  <!-- Main Content -->
  <div class="container">
      <a href="<?php echo current_url(); ?>" class="back-button btn">&lt; <?php echo lang('common_back');?></a>
    <div class="result-container">
      <div class="price"><?php echo to_currency($item_price);?></div>
	  <?php?>
      <img src="<?php echo $item_image_src;?>" alt="Item Image" class="item-image">
	  
	  <?php if ($in_stock === TRUE) { ?>
      <div class="stock-status in-stock"><?php echo lang('common_in_stock')?></div>
	 <?php } elseif($in_stock === FALSE) {?>
      <div class="stock-status out-of-stock"><?php echo lang('common_out_stock')?></div>
	  <?php } ?>
	  
      <div class="item-details">
        <h3><?php echo lang('common_item_name');?></h3>
        <p><?php echo $item_name;?></p>
        <h3><?php echo lang('common_category');?></h3>
        <p><?php echo $category;?></p>
        <h3><?php echo lang('common_description');?></h3>
        <p><?php echo $item_description;?></p>
      </div>
    </div>
  </div>
  
<?php $this->load->view("partial/footer_standalone"); ?>


<script>
//refresh
setTimeout(function()
{
	window.location.href = window.location.href;
},30000);
</script>
