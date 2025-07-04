<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

include 'components/wishlist_cart.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Shop</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <!-- Embedded CSS for Filters and Categories -->
   <style>
      .filters-container {
         display: flex;
         justify-content: space-between;
         margin-bottom: 20px;
      }
      .filter-group {
         display: flex;
         align-items: center;
      }
      .filter-group label {
         margin-right: 10px;
         font-weight: bold;
      }
      .filter-group select, .filter-group input {
         padding: 5px;
         border: 1px solid #ccc;
         border-radius: 4px;
      }
      .categories {
         display: flex;
         gap: 10px;
         margin-bottom: 2rem;
      }
      .category-btn {
         padding: 10px 15px;
         background-color: #f4f4f4;
         border: 1px solid #ddd;
         border-radius: 4px;
         cursor: pointer;
         transition: background-color 0.3s ease;
      }
      .category-btn.active {
         background-color: #007bff;
         color: white;
         border-color: #007bff;
      }
      .category-btn:hover {
         background-color: #0056b3;
         color: white;
      }
   </style>

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="products">

   <h1 class="heading">Latest Products.</h1>

   <!-- Filters Section -->
   <div class="filters-container">
      <div class="filter-group">
         <label for="gender">Gender:</label>
         <select id="gender" name="gender">
            <option value="">All</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
         </select>
      </div>
      <div class="filter-group">
         <label for="size">Size:</label>
         <select id="size" name="size">
            <option value="">All</option>
            <option value="S">S</option>
            <option value="M">M</option>
            <option value="L">L</option>
            <option value="XL">XL</option>
         </select>
      </div>
      <div class="filter-group">
         <label for="price-min">Price Range:</label>
         <input type="number" id="price-min" name="price_min" placeholder="Min" min="0">
         <input type="number" id="price-max" name="price_max" placeholder="Max" min="0">
      </div>
   </div>

   <!-- Categories Section -->
   <div class="categories">
      <button class="category-btn" data-category="all">All</button>
      <button class="category-btn" data-category="male">Male</button>
      <button class="category-btn" data-category="female">Female</button>
      <button class="category-btn" data-category="accessories">Accessories</button>
   </div>

   <div class="box-container">

   <?php
     // Fetch products from the database with optional filtering
     $sql = "SELECT * FROM `products` WHERE 1=1";

     if(isset($_GET['gender']) && $_GET['gender'] != '') {
        $sql .= " AND gender = :gender";
     }
     if(isset($_GET['clothing_type']) && $_GET['clothing_type'] != '') {
        $sql .= " AND category = :category";
     }
     if(isset($_GET['size']) && $_GET['size'] != '') {
        $sql .= " AND size = :size";
     }
     if(isset($_GET['price_min']) && $_GET['price_min'] != '') {
        $sql .= " AND price >= :price_min";
     }
     if(isset($_GET['price_max']) && $_GET['price_max'] != '') {
        $sql .= " AND price <= :price_max";
     }

     $select_products = $conn->prepare($sql);

     if(isset($_GET['gender']) && $_GET['gender'] != '') {
        $select_products->bindParam(':gender', $_GET['gender']);
     }
     if(isset($_GET['clothing_type']) && $_GET['clothing_type'] != '') {
        $select_products->bindParam(':category', $_GET['clothing_type']);
     }
     if(isset($_GET['size']) && $_GET['size'] != '') {
        $select_products->bindParam(':size', $_GET['size']);
     }
     if(isset($_GET['price_min']) && $_GET['price_min'] != '') {
        $select_products->bindParam(':price_min', $_GET['price_min']);
     }
     if(isset($_GET['price_max']) && $_GET['price_max'] != '') {
        $select_products->bindParam(':price_max', $_GET['price_max']);
     }

     $select_products->execute();

     if($select_products->rowCount() > 0){
      while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="post" class="box">
      <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
      <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
      <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
      <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
      <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
      <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>
      <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="">
      <div class="name"><?= $fetch_product['name']; ?></div>
      <div class="flex">
         <div class="price"><span>Rs.</span><?= $fetch_product['price']; ?><span>/-</span></div>
         <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="1">
      </div>
      <input type="submit" value="add to cart" class="btn" name="add_to_cart">
   </form>
   <?php
      }
   }else{
      echo '<p class="empty">no products found!</p>';
   }
   ?>

   </div>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

<!-- JavaScript for Category Buttons -->
<script>
   document.querySelectorAll('.category-btn').forEach(button => {
      button.addEventListener('click', () => {
         document.querySelector('.category-btn.active').classList.remove('active');
         button.classList.add('active');

         const category = button.getAttribute('data-category');
         const url = new URL(window.location.href);
         if(category === 'all') {
            url.searchParams.delete('category');
         } else {
            url.searchParams.set('category', category);
         }
         window.location.href = url.toString();
      });
   });
</script>

</body>
</html>