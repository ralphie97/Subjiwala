<?php  
error_reporting(E_ALL);
ini_set('display_errors', '1');
include "storescripts/connect_to_mysql.php"; 
?>
<?php 
if(isset($_POST['cid']) && isset($_POST['pid'])){
	$cid = $_POST['cid'];
	$pid = $_POST['pid'];
	$sql = mysql_query("SELECT * FROM customer_cart WHERE customerid='$cid' LIMIT 1");
	$count = mysql_num_rows($sql);
	$prodquery = mysql_query("SELECT * FROM products WHERE id='$pid' LIMIT 1");
	while($row = mysql_fetch_array($prodquery)){
		$product_name = $row["product_name"];
		$details = $row["details"];
		$price = $row["price"];
		$date_added = $row["date_added"];
	}
	$quantity=1;	
	if($count==0){
		$ssql = mysql_query("INSERT INTO customer_cart (productid, customerid, product_name, details, price, quantity, date_added) VALUES('$pid','$cid','$product_name','$details','$price','$quantity',now())") or die (mysql_error());
	} else {
		$already = mysql_query("SELECT * FROM customer_cart WHERE productid='$pid' AND customerid='$cid' LIMIT 1");
		$acount = mysql_num_rows($already);
		if($acount!=0){
			while($row = mysql_fetch_array($already)){
				$aquantity = $row["quantity"];
				}
			$aquantity=$aquantity+1;
			$ssql = mysql_query("UPDATE customer_cart SET quantity='$aquantity' WHERE productid='$pid' AND customerid='$cid' ") or die (mysql_error());
		} else {
			$ssql = mysql_query("INSERT INTO customer_cart (productid, customerid, product_name, details, price, quantity, date_added) 
        	VALUES('$pid','$cid','$product_name','$details','$price','$quantity',now())") or die (mysql_error());
		}	
	}
	header("location: cart.php"); 
    exit();
}

?>
<?php
session_start();
if (isset($_GET['cmd']) && $_GET['cmd'] == "emptycart" && isset($_SESSION["id"])) {
		$cid = $_SESSION["id"];
		$sql = mysql_query("SELECT * FROM customer_cart WHERE customerid='$cid' LIMIT 1");
		$count = mysql_num_rows($sql);
		if($count!=0){
			$sql = mysql_query("DELETE FROM customer_cart WHERE customerid='$cid'") or die(mysql_error());
		}
}
?>

<?php
if (isset($_POST['item_to_adjust']) && $_POST['item_to_adjust'] != "" && isset($_SESSION["id"])) {
	$cid = $_SESSION["id"];
	$item_to_adjust = $_POST['item_to_adjust'];
	$quantity = $_POST['quantity'];
	$quantity = preg_replace('#[^0-9]#i', '', $quantity); 
	if ($quantity >= 100) { $quantity = 99; }
	if ($quantity < 1) { $quantity = 1; }
	if ($quantity == "") { $quantity = 1; }
	$ssql = mysql_query("UPDATE customer_cart SET quantity='$quantity' WHERE productid='$item_to_adjust' AND customerid='$cid' ") or die (mysql_error());
}
?>

<?php
if (isset($_POST['index_to_remove']) && $_POST['index_to_remove'] != ""  && isset($_SESSION["id"])) {
	$cid = $_SESSION["id"];
	$toremove = $_POST['index_to_remove'];
	$sql = mysql_query("DELETE FROM customer_cart WHERE customerid='$cid' AND productid='$toremove'") or die(mysql_error());
}
?>

<?php
$cartOutput = "";
$cartTotal = "";
if(isset($_SESSION["id"])){
	$cid=$_SESSION["id"];
	$sql = mysql_query("SELECT * FROM customer_cart WHERE customerid='$cid'") or die(mysql_error());
	$count = mysql_num_rows($sql);	
	if($count==0){
		$cartOutput = "<h2 align='center'>Your shopping cart is empty</h2>";
	}
	else {
		$i=0;
		while ($lists = mysql_fetch_array($sql)) {
				$item_id = $lists["productid"];
				$sqls = mysql_query("SELECT * FROM products WHERE id='$item_id' LIMIT 1") or die(mysql_error());
				$list = mysql_fetch_array($sqls);
				$product_name = $list["product_name"];
				$price = $list["price"];
				$details = $list["details"];
				$quantity = $lists["quantity"];
				$pricetotal = $price * $quantity;
				$cartTotal = $pricetotal + $cartTotal;
				$cartOutput .= '<table width="100%" align="center" ><tr>';
		$cartOutput .= '<td width="20" ><br /><img src="inventory_images/' . $item_id . '.jpg" alt="' . $product_name. '" width="100" height="100"  /><a href="product.php?id=' . $item_id . '"></br>' . $product_name . '</a></td>';
        $cartOutput .= '<td width="20%" align="center">Quantity </br>'.$quantity.'</td>';	
		$cartOutput .= '<td width="20%"align="center">price</br>' . $price . '</td>';
		
		$cartOutput .= '<td align="center">total price</br>' . $pricetotal . '</td>';
		
		$cartOutput .= '</tr></table>';
		}
		$cartTotal = "<div style='font-size:15px; ' align='center'> ".$cartTotal."</div>";

	}
}
?>
<?php 
include "storescripts/connect_to_mysql.php"; 
$dyn_table = "";
$sql = mysql_query("SELECT * FROM products ORDER BY price DESC LIMIT 3");
$i = 0;
$productCount = mysql_num_rows($sql); 
if ($productCount > 0) {
	$i = 0;
$dyn_table = '<table align="center"  cellpadding="10" table width="100%">';
while($row = mysql_fetch_array($sql)){ 
    
    $id = $row["id"];
    $product_name = $row["product_name"];
	$price = $row["price"];
	$date_added = strftime("%b %d, %Y", strtotime($row["date_added"]));
    if ($i % 3 == 0) { 
        
		
					
		$dyn_table .= '<div class="col-md-3 text-center">
						<div class="product-entry"><tr>
						<td width="20%" height="10" valign="top"><a href="product.php?id=' . $id . '"><img style="border:#666 0px solid;" src="inventory_images/' . $id . '.jpg" alt="' . $product_name . '" width="250" height="320"  /></a>
						</br>' . $product_name . '<br/>
						$' . $price . '<br/>
						<a href="product.php?id=' . $id . '">View Details</a>
						</td>';
    } else {
        $dyn_table .= '<td width="20%" valign="top"><a href="product.php?id=' . $id . '"><img style="border:#666 0px solid;" src="inventory_images/' . $id . '.jpg" alt="' . $product_name . '" width="250" height="320"  /></a>
						</br>' . $product_name . '<br/>
						$' . $price . '<br/>
						 <a href="product.php?id=' . $id . '">View Details</a>
						</td></div></div>';
    }
	
    $i++;
	if ($i%3 == 0)
		$dyn_table .= '</tr>';
}
$dyn_table .= '</table';	
	
	
}
mysql_close();
?>
<!DOCTYPE HTML>
<html>
	<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>SabjiWala-Checkout</title>
	
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<meta name="author" content="" />

  <!-- Facebook and Twitter integration -->
	<meta property="og:title" content=""/>
	<meta property="og:image" content=""/>
	<meta property="og:url" content=""/>
	<meta property="og:site_name" content=""/>
	<meta property="og:description" content=""/>
	<meta name="twitter:title" content="" />
	<meta name="twitter:image" content="" />
	<meta name="twitter:url" content="" />
	<meta name="twitter:card" content="" />

	<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700" rel="stylesheet">
	
	<!-- Animate.css -->
	<link rel="stylesheet" href="css/animate.css">
	<!-- Icomoon Icon Fonts-->
	<link rel="stylesheet" href="css/icomoon.css">
	<!-- Bootstrap  -->
	<link rel="stylesheet" href="css/bootstrap.css">

	<!-- Magnific Popup -->
	<link rel="stylesheet" href="css/magnific-popup.css">

	<!-- Flexslider  -->
	<link rel="stylesheet" href="css/flexslider.css">

	<!-- Owl Carousel -->
	<link rel="stylesheet" href="css/owl.carousel.min.css">
	<link rel="stylesheet" href="css/owl.theme.default.min.css">
	
	<!-- Date Picker -->
	<link rel="stylesheet" href="css/bootstrap-datepicker.css">
	<!-- Flaticons  -->
	<link rel="stylesheet" href="fonts/flaticon/font/flaticon.css">

	<!-- Theme style  -->
	<link rel="stylesheet" href="css/style.css">

	<!-- Modernizr JS -->
	<script src="js/modernizr-2.6.2.min.js"></script>
	<!-- FOR IE9 below -->
	<!--[if lt IE 9]>
	<script src="js/respond.min.js"></script>
	<![endif]-->

	</head>
	<body>
		
	<div class="colorlib-loader"></div>

	<div id="page">
		<nav class="colorlib-nav" role="navigation">
			<div class="top-menu">
				<div class="container">
					<div class="row">
						<div class="col-xs-2">
							<div id="colorlib-logo"><a href="http://localhost/store/index.php">SabjiWala.com</a></div>
						</div>
						<div class="col-xs-10 text-right menu-1">
							<ul>
								<li><a href="http://localhost/store/index.php">Home</a></li> &nbsp; &middot; &nbsp; <li><a href="http://localhost/store/list_all_products.php">Products</a></li> &nbsp; &middot; &nbsp; <li><a href="http://localhost/store/help.php">Help</a></li> &nbsp; &middot; &nbsp; <li><a href="http://localhost/store/contact.php">Contact</a></li>
								<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

session_start();
if(!isset($_SESSION['user']))
{
 ?><li><a href="http://localhost/store/user_registration.php">Register</a></li><?php } ?>
								
								<li><?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

session_start();
if(!isset($_SESSION['user']))
{
 ?>
<a href="http://localhost/store/user_login.php">Login</a> | <a href="http://localhost/store/forgetpass.php">Reset Your Password</a></li><?php } ?>
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

session_start();
if(isset($_SESSION['user']))
{
 ?>
 You are logged in as: <?php echo $_SESSION["user"] ?> <a href="http://localhost/store/logout.php">Logout</a> | <a href="http://localhost/store/cart.php">Your Cart</a> | <a href="http://localhost/store/user_profile.php">Edit profile</a></td>
<?php } ?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</nav>
		<?php 
  	if (isset($_GET['success'])){
			echo "Successful Registration.";
	}
  	if (isset($_GET['userloginsuccess'])){
			echo "Successful Login.";
	}
	if (isset($_GET['resetsuccess'])){
			echo "Password Successfully Changed.";
	}
  ?>
		<aside id="colorlib-hero" class="breadcrumbs">
			<div class="flexslider">
				<ul class="slides">
			   	<li style="background-image: url(images/cover-img-1.jpg);">
			   		<div class="overlay"></div>
			   		<div class="container-fluid">
			   			<div class="row">
				   			<div class="col-md-6 col-md-offset-3 col-sm-12 col-xs-12 slider-text">
				   				<div class="slider-text-inner text-center">
				   					<h1>Checkout</h1>
				   					<h2 class="bread"><span><a href="index.php">Home</a></span> <span><a href="cart.html">Shopping Cart</a></span> <span>Checkout</span></h2>
				   				</div>
				   			</div>
				   		</div>
			   		</div>
			   	</li>
			  	</ul>
		  	</div>
		</aside>

		<div class="colorlib-shop">
			<div class="container">
				<div class="row row-pb-md">
					<div class="col-md-10 col-md-offset-1">
						<div class="process-wrap">
							<div class="process text-center active">
								<p><span>01</span></p>
								<h3>Shopping Cart</h3>
							</div>
							<div class="process text-center active">
								<p><span>02</span></p>
								<h3>Checkout</h3>
							</div>
							<div class="process text-center">
								<p><span>03</span></p>
								<h3>Order Complete</h3>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-5" >
						<div class="cart-detail">
							<h2>Cart Total</h2>
							<ul>
								
								
								
									
										 <?php echo $cartOutput; ?>
										
								
								
								<li><span>Shipping</span> <span>$0.00</span></li>
								<li><span>Order Total</span> <span><?php echo $cartTotal; ?></span></li>
							</ul>
						</div>
					</div>
					<div class="col-md-5">
						
						<div class="cart-detail">
							<h2>Payment Method</h2>
							<div class="form-group">
								<div class="col-md-12">
									<div class="radio">
									   <label><input type="radio" name="optradio">Cash on delivey</label>
									</div>
								</div>
							</div>
							
						<div class="row">
							<div class="col-md-5">
								<p><a href="#" class="btn btn-primary">Place an order</a></p>
							</div>
						</div>
					  </div>
				</div>
			</div>
		</div>

		<div class="colorlib-shop">
			<div class="container">
				<div class="row">
					<div class="col-md-6 col-md-offset-3 text-center colorlib-heading">
						<h2><span>Recommended Products</span></h2>
						<p>We love to tell our successful far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
					</div>
				</div>
				<div class="row">
					<?php echo $dyn_table; ?>
				</div>
			</div>
		</div>

</br></br>
		<div id="colorlib-subscribe">
			<div class="overlay"></div>
			<div class="container">
				<div class="row">
					<div class="col-md-8 col-md-offset-2">
						<div class="col-md-6 text-center">
							<h2><i class="icon-paperplane"></i>Sign Up for a Newsletter</h2>
						</div>
						<div class="col-md-6">
							<form class="form-inline qbstp-header-subscribe">
								<div class="row">
									<div class="col-md-12 col-md-offset-0">
										<div class="form-group">
											<input type="text" class="form-control" id="email" placeholder="Enter your email">
											<button type="submit" class="btn btn-primary">Subscribe</button>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>

		<footer id="colorlib-footer" role="contentinfo">
			<div class="container">
				<div class="row row-pb-md">
					<div class="col-md-3 colorlib-widget">
						<h4>About Store</h4>
						<p>Facilis ipsum reprehenderit nemo molestias. Aut cum mollitia reprehenderit. Eos cumque dicta adipisci architecto culpa amet.</p>
						<p>
							<ul class="colorlib-social-icons">
								<li><a href="#"><i class="icon-twitter"></i></a></li>
								<li><a href="#"><i class="icon-facebook"></i></a></li>
								<li><a href="#"><i class="icon-linkedin"></i></a></li>
								<li><a href="#"><i class="icon-dribbble"></i></a></li>
							</ul>
						</p>
					</div>
					<div class="col-md-2 colorlib-widget">
						<h4>Customer Care</h4>
						<p>
							<ul class="colorlib-footer-links">
								<li><a href="#">Contact</a></li>
								<li><a href="#">Returns/Exchange</a></li>
								<li><a href="#">Gift Voucher</a></li>
								<li><a href="#">Wishlist</a></li>
								<li><a href="#">Special</a></li>
								<li><a href="#">Customer Services</a></li>
								<li><a href="#">Site maps</a></li>
							</ul>
						</p>
					</div>
					<div class="col-md-2 colorlib-widget">
						<h4>Information</h4>
						<p>
							<ul class="colorlib-footer-links">
								<li><a href="#">About us</a></li>
								<li><a href="#">Delivery Information</a></li>
								<li><a href="#">Privacy Policy</a></li>
								<li><a href="#">Support</a></li>
								<li><a href="#">Order Tracking</a></li>
							</ul>
						</p>
					</div>

					<div class="col-md-2">
						<h4>News</h4>
						<ul class="colorlib-footer-links">
							<li><a href="blog.html">Blog</a></li>
							<li><a href="#">Press</a></li>
							<li><a href="#">Exhibitions</a></li>
						</ul>
					</div>

					<div class="col-md-3">
						<h4>Contact Information</h4>
						<ul class="colorlib-footer-links">
							<li>291 South 21th Street, <br> Suite 721 New York NY 10016</li>
							<li><a href="tel://1234567920">+ 1235 2355 98</a></li>
							<li><a href="mailto:info@yoursite.com">info@yoursite.com</a></li>
							<li><a href="#">yoursite.com</a></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="copy">
				<div class="row">
					<div class="col-md-12 text-center">
						<p>
							
							<span class="block"><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="icon-heart2" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></span> 
							<span class="block">Demo Images: <a href="http://unsplash.co/" target="_blank">Unsplash</a> , <a href="http://pexels.com/" target="_blank">Pexels.com</a></span>
						</p>
					</div>
				</div>
			</div>
		</footer>
	</div>

	<div class="gototop js-top">
		<a href="#" class="js-gotop"><i class="icon-arrow-up2"></i></a>
	</div>
	
	<!-- jQuery -->
	<script src="js/jquery.min.js"></script>
	<!-- jQuery Easing -->
	<script src="js/jquery.easing.1.3.js"></script>
	<!-- Bootstrap -->
	<script src="js/bootstrap.min.js"></script>
	<!-- Waypoints -->
	<script src="js/jquery.waypoints.min.js"></script>
	<!-- Flexslider -->
	<script src="js/jquery.flexslider-min.js"></script>
	<!-- Owl carousel -->
	<script src="js/owl.carousel.min.js"></script>
	<!-- Magnific Popup -->
	<script src="js/jquery.magnific-popup.min.js"></script>
	<script src="js/magnific-popup-options.js"></script>
	<!-- Date Picker -->
	<script src="js/bootstrap-datepicker.js"></script>
	<!-- Stellar Parallax -->
	<script src="js/jquery.stellar.min.js"></script>
	<!-- Main -->
	<script src="js/main.js"></script>
	</script>

	</body>
</html>
