
          <!-- Footer Start -->
     <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3 class="footer-title">HopOn</h3>
                    <p>
                        Your premier destination for booking bus tickets online.
                        Experience the beauty of the ride with ease.
                    </p>
                </div>
                <div class="footer-section">
                    <h3 class="footer-title">Quick Links</h3>
                    <div class="footer-links">
                        <a href="index.php">Home</a>
                    <a href="routes.php">Routes</a>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="mybookings.php">My Bookings</a>
                    <?php endif; ?>
                    </div>
                </div>
                <div class="footer-section">
                    <h3 class="footer-title">User Account</h3>
                    <div class="footer-links">
                        <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="logout.php">Logout</a>
                    <?php else: ?>
                        <a href="login.php">Login</a>
                        <a href="register.php">Register</a>
                    <?php endif; ?>
                    </div>
                </div>
                <div class="footer-section">
                    <h3 class="footer-title">Follow Us</h3>
                    <div class="social-links">
                        <a href=""><i class="fa-brands fa-facebook"></i></a>
                        <a href=""><i class="fa-brands fa-twitter"></i></a>
                        <a href=""><i class="fa-brands fa-instagram"></i></a>
                        <a href=""><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>

            </div>

            <div class="footer-bottom">
                <p>
                    &copy; 2025 HopOn. All Rights Reserved.
                </p>
            </div>

        </div>
    </footer>

     <script src="./assets/js/hopon.js"></script>
    
</body>
</html>