<!DOCTYPE html>
<html lang="en">
<head>
 <title>Bootstrap Example</title>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
 <form method="post" class="form-inline" action="dataform.php">
   <div class="form-group">
<input type=“name” class="form-control" id=“name” placeholder="Enter name” name=“name”>
</div>
<div class="form-group">
<input  class="form-control" id="email" placeholder="Enter email" name="email">
   </div>
<div class="form-group">
<input type=“phone” class="form-control" id=“phone” placeholder="Enter phone” name=“phone”>
 </div>
<div class="form-group">
<input type="hidden" class="form-control" id=“product_id”  name=“product_id”>
</div>
<button type="submit" class="btn btn-default">Submit</button>
</form>
</div>
</body>
</html>