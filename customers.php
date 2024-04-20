<?php
    session_start();
    include ('navigation.php');

    $date= date('Y-m-d', strtotime('-7 days'));
    //die ($date);
    $conn=connect();
    $id= $_SESSION['userid'];
    $sq= "SELECT * FROM users_info WHERE id='$id'";
    $thisUser= mysqli_fetch_assoc($conn->query($sq));

    $sql= "SELECT * from products WHERE updated_at>'$date'";
    $prod= $conn->query($sql);

    $sql= "SELECT COUNT(*) as products FROM products";
    $total_products= mysqli_fetch_assoc($conn->query($sql));

    $sql= "SELECT SUM(bought) as total_bought FROM products";
    $total_bought= mysqli_fetch_assoc($conn->query($sql));

    $sql= "SELECT SUM(sold) as total_sold FROM products";
    $total_sold= mysqli_fetch_assoc($conn->query($sql));

    $stock_available= $total_bought['total_bought']-$total_sold['total_sold'];

    $m='';

    if(isset($_POST['submit'])) { // Ensure both name and userId are set
        $name = $_POST['name'];
        $userId = $_POST['userId']; // Corrected variable name to match the HTML form field name
        $cssq = "INSERT INTO customer_info(customer_id, Name, product_id, payment) VALUES('$userId', '$name', NULL, NULL)";
        /** @var TYPE_NAME $cssq */
        if ($conn->query($cssq) === true) {
            header('Location: customers.php');
            exit; // After redirection, exit to prevent further execution
        } else {
            $m = 'Connection not established!';
        }
    }

    $cssq= "SELECT * from customer_info";
    $res= $conn->query($cssq);

?>




<html>

    <head>
        <title>Dashboard</title>
        <link rel="stylesheet" type="text/css" href="css/dashboard.css">
    </head>
    <body>
        <div class="row" style="padding-top: 40px;">
            <div class="leftcolumn">
                <div class="row">
                    <section style="padding-left: 20px; padding-right: 20px;">
                        <div class="col-sm-3">
                            <div class="card card-green">
                                <h3>Total Products </h3>
                                <h2 style="color: #282828; text-align: center;"><?php echo $total_products['products'] ?></h2>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="card card-yellow" >
                                <h3>Products Bought </h3>
                                <h2 style="color: #282828; text-align: center;"><?php echo $total_bought['total_bought'] ?></h2>
                            </div>
                        </div>
                        <div class="col-sm-3 " >
                            <div class="card card-blue" >
                                <h3>Products Sold </h3>
                                <h2 style="color: #282828; text-align: center;"><?php echo $total_sold['total_sold'] ?></h2>
                            </div>
                        </div>
                        <div class="col-sm-3" >
                            <div class="card card-red" >
                                <h3>Available Stock </h3>
                                <h2 style="color: #282828; text-align: center;"><?php echo $stock_available ?></h2>
                            </div>
                        </div>
                    </section>
                </div><div class="card">
                    <div class="table_container">
                        <div class="row justify-content-center"> <!-- Center the form horizontally -->
                            <div class="col-md-6"> <!-- Specify the width of the form -->
                                <h1>Add Customer</h1>
                                <form method="POST" action="customers.php">
                                    <span><?php if($m!='') echo $m; ?></span>
                                    <div class="form-group">
                                        <label for="name">Name:</label>
                                        <input type="text" id="name" name="name" class="form-control form-control-sm" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="userId">User ID:</label>
                                        <input type="text" id="userId" name="userId" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="form-group text-center"> <!-- Center the button -->
                                        <input type="submit" class="btn btn-success btn-sm" value="Submit" name="submit">
                                    </div>
                                </form>
                            </div>
                            <h1 style="text-align: center;">Customers</h1>
                            <div class="table-responsive">
                                <table class="table table-dark" id="table" data-toggle="table" data-search="true" data-filter-control="true" data-show-export="true" data-click-to-select="true" data-toolbar="#toolbar">
                                    <thead class="thead-light">
                                    <tr>
                                        <th data-field="date" data-filter-control="select" data-sortable="true">Name</th>
                                        <th data-field="examen" data-filter-control="select" data-sortable="true">ID</th>
                                        <th data-field="note" data-sortable="true">Products</th>
                                        <th data-field="note" data-sortable="true">Quantity</th>
                                        <th data-field="note" data-sortable="true">Payment</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if(mysqli_num_rows($res)>0) {
                                        while ($row = mysqli_fetch_assoc($res)) {

                                            echo '<tr>';
                                            echo '<td>'. $row['Name'].'</td>';
                                            echo '<td>'. $row['customer_id'].'</td>';
                                            $productId = $row['product_id'];
                                            $productNameQuery = "SELECT name FROM products WHERE id = '$productId' LIMIT 1";
                                            $productNameResult = mysqli_query($conn, $productNameQuery);
                                            if($productNameResult && mysqli_num_rows($productNameResult) > 0) {
                                                $productNameRow = mysqli_fetch_assoc($productNameResult);
                                                $productName = $productNameRow['name'];
                                                echo '<td>'. $productName .'</td>';
                                            } else {
                                                echo '<td>Unknown Product</td>'; // If product not found
                                            }
                                            echo '<td>'. $row['quantity'].'</td>';
                                            echo '<td>'. $row['payment'].'</td>';
                                            echo '</tr>';
                                        }
                                    }
                                    ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="rightcolumn">
                <div class="card  text-center" >
                    <h2>About User</h2>
                    <p ><h4 style="color:red"><?php if($thisUser['is_admin']==1) {echo"Admin";}?></h4></p>
                    <div style="height:100px;"><img src="<?php echo $thisUser['avatar']; ?>" height="100px;" width="100px;" class="img-circle" alt="Please Select your avatar"></div>
                    <p><h4><?php echo $thisUser['name'];  ?></h4> is working here since <h4><?php echo date('F j, Y', strtotime($thisUser['created_at'])); ?></h4></p>
                </div>
                <div class="card text-center">
                    <h2>Owners Info</h2>
                    <p>Some text..</p>
                </div>
            </div>
        </div>

        <div class="footer">

            All rights reserved @<a href="https://www.facebook.com/ARIF.FUAD.399/">Md. Arif Fuad Akash</a>
        </div>

    </body>
</html>