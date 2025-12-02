<?php
require_once 'auth_check.php';
require_once '../core/database.php';

$page_title = 'Kelola Landing Page';
include 'includes/admin_header.php';
include 'includes/admin_sidebar.php';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $success_msg = '';
    $error_msg = '';
    
    // Handle File Upload (Logo)
    if (isset($_FILES['navbar_logo_file']) && $_FILES['navbar_logo_file']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['navbar_logo_file']['name'];
        $filetype = $_FILES['navbar_logo_file']['type'];
        $filesize = $_FILES['navbar_logo_file']['size'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            $error_msg = "Format file tidak diizinkan. Gunakan JPG, PNG, GIF, atau WEBP.";
        } else {
            // Create directory if not exists
            $upload_dir = '../public/img/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $new_filename = 'logo-' . time() . '.' . $ext;
            $destination = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['navbar_logo_file']['tmp_name'], $destination)) {
                // Update database with new path
                $logo_path = '/public/img/' . $new_filename;
                $query = "UPDATE landing_page_content SET content_value = $1, updated_at = NOW() 
                          WHERE section_name = 'navbar' AND key_name = 'logo_path'";
                pg_query_params($conn, $query, array($logo_path));
            } else {
                $error_msg = "Gagal mengupload file.";
            }
        }
    }
    
    // Handle Hero Image Upload
    if (isset($_FILES['hero_image_file']) && $_FILES['hero_image_file']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $filename = $_FILES['hero_image_file']['name'];
        $filetype = $_FILES['hero_image_file']['type'];
        $filesize = $_FILES['hero_image_file']['size'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            $error_msg = "Format file tidak diizinkan untuk Hero Image. Gunakan JPG, PNG, GIF, SVG, atau WEBP.";
        } else {
            // Create directory if not exists
            $upload_dir = '../public/img/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $new_filename = 'hero-' . time() . '.' . $ext;
            $destination = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['hero_image_file']['tmp_name'], $destination)) {
                // Update database with new path
                $image_path = '/public/img/' . $new_filename;
                $query = "UPDATE landing_page_content SET content_value = $1, updated_at = NOW() 
                          WHERE section_name = 'hero' AND key_name = 'image_path'";
                $update_result = pg_query_params($conn, $query, array($image_path));
                
                // If no row was updated, insert new row
                if ($update_result && pg_affected_rows($update_result) == 0) {
                    $query = "INSERT INTO landing_page_content (section_name, key_name, content_value, content_type) 
                              VALUES ('hero', 'image_path', $1, 'image')";
                    pg_query_params($conn, $query, array($image_path));
                }
            } else {
                $error_msg = "Gagal mengupload Hero Image.";
            }
        }
    }

    foreach ($_POST as $key => $value) {
        if (strpos($key, 'content_') === 0) {
            // Format: content_{section}_{key}
            // Need to handle social keys carefully as they have underscores
            // e.g. content_footer_social_facebook -> section=footer, key=social_facebook
            
            $parts = explode('_', $key, 3);
            if (count($parts) >= 3) {
                $section = $parts[1];
                $key_name = $parts[2];
                $content = trim($value);
                
                // Update database
                $query = "UPDATE landing_page_content SET content_value = $1, updated_at = NOW() 
                          WHERE section_name = $2 AND key_name = $3";
                $result = pg_query_params($conn, $query, array($content, $section, $key_name));
                
                if (!$result) {
                    $error_msg = "Gagal mengupdate $section - $key_name";
                }
            }
        }
    }
    
    if (empty($error_msg)) {
        $success_msg = "Konten berhasil diperbarui!";
    }
}

// Fetch all content
$content_map = [];
$query = "SELECT * FROM landing_page_content";
$result = pg_query($conn, $query);
while ($row = pg_fetch_assoc($result)) {
    $content_map[$row['section_name']][$row['key_name']] = $row['content_value'];
}

// Helper function to get content safely
function getContent($section, $key, $map) {
    return isset($map[$section][$key]) ? htmlspecialchars($map[$section][$key]) : '';
}
?>

<div class="admin-content">
    <div class="admin-topbar">
        <div>
            <h4 class="mb-0">Kelola Landing Page</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Landing Page</li>
                </ol>
            </nav>
        </div>
    </div>

    <?php if (isset($success_msg) && !empty($success_msg)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?php echo $success_msg; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if (isset($error_msg) && !empty($error_msg)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error_msg; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <ul class="nav nav-tabs card-header-tabs" id="landingTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="hero-tab" data-bs-toggle="tab" data-bs-target="#hero" type="button" role="tab">Hero Section</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="about-tab" data-bs-toggle="tab" data-bs-target="#about" type="button" role="tab">About Section</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="navbar-tab" data-bs-toggle="tab" data-bs-target="#navbar" type="button" role="tab">Navbar</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="footer-tab" data-bs-toggle="tab" data-bs-target="#footer" type="button" role="tab">Footer</button>
                </li>
            </ul>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="tab-content" id="landingTabsContent">
                    
                    <!-- HERO SECTION -->
                    <div class="tab-pane fade show active" id="hero" role="tabpanel">
                        <h5 class="mb-4 text-primary">Hero Section Settings</h5>
                        
                        <!-- Hero Image -->
                        <div class="mb-4 p-3 bg-light rounded">
                            <label class="form-label fw-bold"><i class="bi bi-image me-2"></i>Hero Image</label>
                            <div class="d-flex align-items-center mb-3">
                                <?php 
                                $hero_image = getContent('hero', 'image_path', $content_map);
                                if (empty($hero_image)) {
                                    $hero_image = '/public/img/logo-se.png';
                                }
                                ?>
                                <img src="<?php echo BASE_URL . $hero_image; ?>" alt="Hero Image" style="max-height: 150px; margin-right: 15px; border: 2px solid #ddd; padding: 10px; border-radius: 10px; background: white;">
                                <div class="flex-grow-1">
                                    <input type="file" class="form-control" name="hero_image_file" accept="image/*">
                                    <div class="form-text mt-2">
                                        <i class="bi bi-info-circle me-1"></i>Upload gambar untuk hero section (kanan halaman depan).<br>
                                        Format: JPG, PNG, SVG, WEBP. Ukuran rekomendasi: 450x450px atau lebih.
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Main Title</label>
                            <input type="text" class="form-control" name="content_hero_title" value="<?php echo getContent('hero', 'title', $content_map); ?>" required>
                            <div class="form-text">Judul utama yang tampil besar di halaman depan.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Subtitle</label>
                            <textarea class="form-control" name="content_hero_subtitle" rows="3" required><?php echo getContent('hero', 'subtitle', $content_map); ?></textarea>
                            <div class="form-text">Deskripsi singkat di bawah judul utama.</div>
                        </div>
                    </div>

                    <!-- ABOUT SECTION -->
                    <div class="tab-pane fade" id="about" role="tabpanel">
                        <h5 class="mb-4 text-primary">About Section Settings</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Section Title</label>
                                <input type="text" class="form-control" name="content_about_title" value="<?php echo getContent('about', 'title', $content_map); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Section Subtitle</label>
                                <input type="text" class="form-control" name="content_about_subtitle" value="<?php echo getContent('about', 'subtitle', $content_map); ?>" required>
                            </div>
                        </div>
                        <hr>
                        <h6 class="mb-3">Card 1 (Left)</h6>
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="content_about_card1_title" value="<?php echo getContent('about', 'card1_title', $content_map); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="content_about_card1_desc" rows="2" required><?php echo getContent('about', 'card1_desc', $content_map); ?></textarea>
                        </div>
                        <hr>
                        <h6 class="mb-3">Card 2 (Center)</h6>
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="content_about_card2_title" value="<?php echo getContent('about', 'card2_title', $content_map); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="content_about_card2_desc" rows="2" required><?php echo getContent('about', 'card2_desc', $content_map); ?></textarea>
                        </div>
                        <hr>
                        <h6 class="mb-3">Card 3 (Right)</h6>
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="content_about_card3_title" value="<?php echo getContent('about', 'card3_title', $content_map); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="content_about_card3_desc" rows="2" required><?php echo getContent('about', 'card3_desc', $content_map); ?></textarea>
                        </div>
                    </div>

                    <!-- NAVBAR SECTION -->
                    <div class="tab-pane fade" id="navbar" role="tabpanel">
                        <h5 class="mb-4 text-primary">Navbar Settings</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Logo Website</label>
                            <div class="d-flex align-items-center mb-2">
                                <img src="<?php echo BASE_URL . getContent('navbar', 'logo_path', $content_map); ?>" alt="Current Logo" style="height: 50px; margin-right: 15px; border: 1px solid #ddd; padding: 5px; border-radius: 5px;">
                                <input type="file" class="form-control" name="navbar_logo_file" accept="image/*">
                            </div>
                            <div class="form-text">Upload gambar baru untuk mengganti logo. Format: JPG, PNG, WEBP.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Brand Title</label>
                            <input type="text" class="form-control" name="content_navbar_brand_title" value="<?php echo getContent('navbar', 'brand_title', $content_map); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Brand Subtitle</label>
                            <input type="text" class="form-control" name="content_navbar_brand_subtitle" value="<?php echo getContent('navbar', 'brand_subtitle', $content_map); ?>" required>
                        </div>
                    </div>

                    <!-- FOOTER SECTION -->
                    <div class="tab-pane fade" id="footer" role="tabpanel">
                        <h5 class="mb-4 text-primary">Footer Settings</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Footer Description</label>
                            <textarea class="form-control" name="content_footer_description" rows="3" required><?php echo getContent('footer', 'description', $content_map); ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email Contact</label>
                                <input type="email" class="form-control" name="content_footer_email" value="<?php echo getContent('footer', 'email', $content_map); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Phone Contact</label>
                                <input type="text" class="form-control" name="content_footer_phone" value="<?php echo getContent('footer', 'phone', $content_map); ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Copyright Text</label>
                            <input type="text" class="form-control" name="content_footer_copyright" value="<?php echo getContent('footer', 'copyright', $content_map); ?>" required>
                        </div>
                        
                        <hr>
                        <h6 class="mb-3">Social Media Links (Kosongkan jika tidak ingin ditampilkan)</h6>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-facebook me-2"></i>Facebook</label>
                            <input type="text" class="form-control" name="content_footer_social_facebook" value="<?php echo getContent('footer', 'social_facebook', $content_map); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-twitter me-2"></i>Twitter / X</label>
                            <input type="text" class="form-control" name="content_footer_social_twitter" value="<?php echo getContent('footer', 'social_twitter', $content_map); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-instagram me-2"></i>Instagram</label>
                            <input type="text" class="form-control" name="content_footer_social_instagram" value="<?php echo getContent('footer', 'social_instagram', $content_map); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-linkedin me-2"></i>LinkedIn</label>
                            <input type="text" class="form-control" name="content_footer_social_linkedin" value="<?php echo getContent('footer', 'social_linkedin', $content_map); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-youtube me-2"></i>YouTube</label>
                            <input type="text" class="form-control" name="content_footer_social_youtube" value="<?php echo getContent('footer', 'social_youtube', $content_map); ?>">
                        </div>
                    </div>

                </div>
                
                <div class="mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>
