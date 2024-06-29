<!doctype html>
<html lang="zxx">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>aranaz</title>
    <link rel="icon" href="img/favicon.png">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/nice-select.css">
    <link rel="stylesheet" href="css/all.css">
    <link rel="stylesheet" href="css/flaticon.css">
    <link rel="stylesheet" href="css/themify-icons.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/slick.css">
    <link rel="stylesheet" href="css/price_rangs.css">
    <link rel="stylesheet" href="css/style.css">
  </head>

  <body>
    <?php session_start();
    include("header.php");
    include("config.php");

    //$booking_id['booking_id'] = 0;
    $total_quantity = 0;
    date_default_timezone_set("Asia/Kolkata");
    $from_date = date("Y-m-d h:i:s");
    $date = date_create($from_date);

    $booking_query_stmt = "INSERT INTO bookings VALUES ('',1,'" . $_SESSION['user_id'] . "',now(),now(),'" . $_SESSION['grand_total'] . "','0','0','" . $_SESSION['grand_total'] . "','success')";
    $booking_query = mysqli_query($dbh, $booking_query_stmt);

    if ($booking_query) {
      $booking_id_stmt = "SELECT booking_id FROM bookings WHERE user_id = '" . $_SESSION['user_id'] . "' ORDER BY booking_id DESC LIMIT 1";
      $booking_id_query = mysqli_query($dbh, $booking_id_stmt);
      $booking_id = mysqli_fetch_assoc($booking_id_query);
      $_SESSION['booking_id'] = $booking_id['booking_id'];
      
      foreach ($_SESSION['cart'] as $product) {
        date_add($date, date_interval_create_from_date_string($product['no_of_months'] . " months"));
        $to_date = date_format($date, "Y-m-d h:i:s");

        $bpm_query_stmt = "INSERT INTO booking_product_map VALUES ('','" . $booking_id['booking_id'] . "','" . $product['product_id'] . "','" . $product['quantity'] . "', '" . $from_date . "', '" . $to_date . "', '" . $product['total_price'] . "', '', '" . $product['total_price'] . "')";
        $bpm_query = mysqli_query($dbh, $bpm_query_stmt);

        if ($bpm_query) {
          continue;
        } else {
          echo "<script>alert('Something Went Wrong in bpm_query');</script>";
        }
      }
    } else {
      echo "<script>alert('Something Went Wrong in booking_query');</script>";
    }

    $order_details_stmt = "SELECT * FROM bookings WHERE booking_id = '" . $_SESSION['booking_id'] . "'";

    $order_details_query = mysqli_query($dbh, $order_details_stmt);
    $order_details = mysqli_fetch_assoc($order_details_query);

    $order_date = date_format(date_create($order_details['booking_date']), "d-m-Y");

    $products_stmt = "SELECT booking_id,bpm.product_id,qty,from_date,to_date,total,p.name FROM booking_product_map bpm, products p WHERE booking_id = '" . $order_details['booking_id'] . "' AND bpm.product_id = p.product_id";
    $products_query = mysqli_query($dbh, $products_stmt);
    $products = mysqli_fetch_all($products_query, MYSQLI_ASSOC);
    //unset($_SESSION['cart']);
 
    ?>
    <section class="breadcrumb breadcrumb_bg">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-8">
            <div class="breadcrumb_iner">
              <div class="breadcrumb_iner_item">
                <h2>Order Confirmation</h2>
                <p>Home <span>-</span> Order Confirmation</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="confirmation_part padding_top">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <div class="confirmation_tittle">
              <span>Thank you. Your order has been received.</span>
            </div>
          </div>
          <div class="col-lg-6 col-lx-4">
            <div class="single_confirmation_details">
              <h4>order info</h4>
              <ul>
                <li>
                  <p>order number</p><span>: <?php echo $order_details['booking_id']; ?></span>
                </li>
                <li>
                  <p>date</p><span>: <?php echo $order_date; ?></span>
                </li>
                <li>
                  <p>total</p><span>: &#8377;<?php echo $order_details['grand_total']; ?></span>
                </li>
                <li>
                  <p>Payment method</p><span>: Online</span>
                </li>
              </ul>
            </div>
          </div>
          <div class="col-lg-6 col-lx-4">
            <div class="single_confirmation_details">
              <h4>Billing Address</h4>
              <ul>
                <li>
                  <p>Address</p><span>: <?php echo $_SESSION['address']; ?></span>
                </li>
                <li>
                  <p>City</p><span>: <?php echo $_SESSION['city']; ?></span>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <div class="order_details_iner">
              <h3>Order Details</h3>
              <table class="table table-borderless">
                <thead>
                  <tr>
                    <th scope="col" colspan="2">Product</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">From</th>
                    <th scope="col">To</th>
                    <th scope="col">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <?php for ($i = 0; $i < mysqli_num_rows($products_query); $i++) { ?>
                    <tr>
                      <th colspan="2"><span><?php echo $products[$i]['name']; ?></span></th>
                      <th>x <?php echo $products[$i]['qty']; ?></th>
                      <th><?php echo date_format(date_create($products[$i]['from_date']), "d-m-Y"); ?></th>
                      <th><?php echo date_format(date_create($products[$i]['to_date']), "d-m-Y"); ?></th>
                      <th> <span>&#8377;<?php echo $products[$i]['total']; ?></span></th>
                    </tr>
                  <?php
                    $total_quantity += $products[$i]['qty'];
                  } ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th scope="col">Quantity:</th>
                    <th scope="col" colspan="3"><?php echo $total_quantity; ?></th>
                    <th scope="col">Total:</th>
                    <th scope="col">&#8377;<?php echo $order_details['grand_total']; ?></th>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <?php include("footer.php") ?>
   <script src="js/jquery-1.12.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.magnific-popup.js"></script>
    <script src="js/swiper.min.js"></script>
    <script src="js/masonry.pkgd.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.nice-select.min.js"></script>
    <script src="js/slick.min.js"></script>
    <script src="js/jquery.counterup.min.js"></script>
    <script src="js/waypoints.min.js"></script>
    <script src="js/contact.js"></script>
    <script src="js/jquery.ajaxchimp.min.js"></script>
    <script src="js/jquery.form.js"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script src="js/mail-script.js"></script>
    <script src="js/stellar.js"></script>
    <script src="js/price_rangs.js"></script>
    <script src="js/custom.js"></script>
  </body>

</html>