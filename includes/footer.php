<footer class="bg-dark text-white py-5 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-code-square me-2"></i>Lab Software Engineering
                </h5>
                <p class="text-light"><?php echo htmlspecialchars(get_content('footer', 'description', 'Pusat keunggulan dalam pengembangan perangkat lunak dan penelitian teknologi informasi.')); ?></p>
            </div>
            <div class="col-md-4 mb-4">
                <h5 class="fw-bold mb-3">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>/views/tentang.php" class="text-light text-decoration-none">Tentang Kami</a></li>
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>/views/personil/" class="text-light text-decoration-none">Personil</a></li>
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>/views/blog/" class="text-light text-decoration-none">Blog</a></li>
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>/views/recruitment/" class="text-light text-decoration-none">Recruitment</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-4">
                <h5 class="fw-bold mb-3">Kontak & Media Sosial</h5>
                <p class="text-light mb-3">
                    <?php 
                    $email = get_content('footer', 'email', 'labse@university.ac.id');
                    $phone = get_content('footer', 'phone', '+62 21 1234 5678');
                    ?>
                    <i class="bi bi-envelope me-2"></i><a href="mailto:<?php echo htmlspecialchars($email); ?>" class="text-light text-decoration-none"><?php echo htmlspecialchars($email); ?></a><br>
                    <i class="bi bi-phone me-2"></i><a href="tel:<?php echo htmlspecialchars($phone); ?>" class="text-light text-decoration-none"><?php echo htmlspecialchars($phone); ?></a>
                </p>
                <div class="social-icons">
                    <?php 
                    $fb = get_content('footer', 'social_facebook', '');
                    $tw = get_content('footer', 'social_twitter', '');
                    $ig = get_content('footer', 'social_instagram', '');
                    $li = get_content('footer', 'social_linkedin', '');
                    $yt = get_content('footer', 'social_youtube', '');
                    
                    if (!empty($fb) && $fb !== '#') echo '<a href="' . htmlspecialchars($fb) . '" class="text-white me-3 fs-4" target="_blank"><i class="bi bi-facebook"></i></a>';
                    if (!empty($tw) && $tw !== '#') echo '<a href="' . htmlspecialchars($tw) . '" class="text-white me-3 fs-4" target="_blank"><i class="bi bi-twitter-x"></i></a>';
                    if (!empty($ig) && $ig !== '#') echo '<a href="' . htmlspecialchars($ig) . '" class="text-white me-3 fs-4" target="_blank"><i class="bi bi-instagram"></i></a>';
                    if (!empty($li) && $li !== '#') echo '<a href="' . htmlspecialchars($li) . '" class="text-white me-3 fs-4" target="_blank"><i class="bi bi-linkedin"></i></a>';
                    if (!empty($yt) && $yt !== '#') echo '<a href="' . htmlspecialchars($yt) . '" class="text-white fs-4" target="_blank"><i class="bi bi-youtube"></i></a>';
                    ?>
                </div>
            </div>
        </div>
        <hr class="border-secondary my-4">
        <div class="text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(get_content('footer', 'copyright', 'Lab Software Engineering. All rights reserved.')); ?></p>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- AOS Animation JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<!-- Custom JS -->
<script src="<?php echo BASE_URL; ?>/public/js/main.js"></script>

</body>
</html>