<?php
require_once '../../includes/config.php';
$page_title = 'Informasi Lainnya';
include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container text-center">
        <h1 data-aos="fade-down">Informasi Lainnya</h1>
        <p class="lead" data-aos="fade-up" data-aos-delay="100">Informasi tambahan tentang Lab Software Engineering</p>
    </div>
</div>

<!-- FAQ Section -->
<section class="content-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Frequently Asked Questions</h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item" data-aos="fade-up" data-aos-delay="100">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Bagaimana cara mendaftar di Lab Software Engineering?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Anda dapat mendaftar melalui halaman recruitment dengan mengisi formulir pendaftaran online. Setelah mendaftar, tim kami akan menghubungi Anda untuk proses seleksi lebih lanjut.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item" data-aos="fade-up" data-aos-delay="200">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Apakah ada biaya untuk bergabung?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Tidak ada biaya untuk bergabung dengan Lab SE. Semua kegiatan pembelajaran dan pelatihan disediakan secara gratis untuk mahasiswa yang terpilih.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item" data-aos="fade-up" data-aos-delay="300">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Apa saja persyaratan untuk bergabung?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Persyaratan utama adalah mahasiswa aktif dari jurusan terkait IT/komputer, memiliki passion di bidang software engineering, dan berkomitmen untuk aktif mengikuti kegiatan lab.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item" data-aos="fade-up" data-aos-delay="400">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Kegiatan apa saja yang ada di Lab SE?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Kami memiliki berbagai kegiatan seperti workshop, training, project-based learning, penelitian, dan kolaborasi dengan industri. Mahasiswa juga berkesempatan mengikuti kompetisi dan sertifikasi.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="content-section bg-light-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Hubungi Kami</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-geo-alt text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Alamat</h5>
                        <p class="text-muted">Gedung Fakultas Teknik Lantai 3<br>Universitas ABC<br>Jakarta, Indonesia</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-envelope text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Email</h5>
                        <p class="text-muted">labse@university.ac.id<br>info.labse@university.ac.id</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-phone text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Telepon</h5>
                        <p class="text-muted">+62 21 1234 5678<br>+62 812 3456 7890</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Operating Hours -->
<section class="content-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card" data-aos="zoom-in">
                    <div class="card-body p-5">
                        <h3 class="text-center mb-4">Jam Operasional</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="bi bi-calendar-week text-primary me-2"></i>Senin - Jumat</h5>
                                <p class="text-muted">08:00 - 17:00 WIB</p>
                            </div>
                            <div class="col-md-6">
                                <h5><i class="bi bi-calendar-check text-primary me-2"></i>Sabtu</h5>
                                <p class="text-muted">08:00 - 13:00 WIB</p>
                            </div>
                        </div>
                        <hr>
                        <p class="text-center text-muted mb-0"><i class="bi bi-info-circle me-2"></i>Tutup pada hari Minggu dan hari libur nasional</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
pg_close($conn);
include '../../includes/footer.php';
?>