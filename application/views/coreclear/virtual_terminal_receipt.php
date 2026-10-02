<!DOCTYPE html>
<head>

</head>
<body>
Date: <?php echo date(get_date_format().' '.get_time_format(), strtotime($cc_response['timestamp']))?><br />
Customer Name: <?php echo $cc_request['cardholderName']?><br />
Amount: <?php echo to_currency($cc_request['amount'])?><br />
Card: <?php echo $cc_response['maskedPan']?><br />

</body>
</html>