<?php 
// La configuración ahora se maneja vía src/helpers.php cargado en index.php
?>
                    <!-- Footer -->
<footer>
    <div class="footer-blur"></div>
    <div class="footer-content">
        <div class="footer-section main-info">
            <div class="footer-logo">
                <img src="<?php echo img('favicon/university/android-chrome-192x192.png') ?>" alt="Logo EDU360" width="45">
                <h4>Paradigma EDU360</h4>
            </div>
            <p>Transformamos el futuro de la educación integrando neurociencia y tecnología de vanguardia para potenciar el talento humano en el mundo hispano.</p>
            <div class="social-links">
                <a href="https://www.instagram.com/edu360_global?igsh=ZTg1bm54aGw2NzU2" target="_blank" title="Instagram" class="social-btn instagram">
                    <i class="ri-instagram-line"></i>
                </a>
                <a href="https://www.facebook.com/share/17myYfTZFq/" target="_blank" title="Facebook" class="social-btn facebook">
                    <i class="ri-facebook-circle-fill"></i>
                </a>
                <a href="https://maps.app.goo.gl/xdKYE4dWPMnwijcPA?g_st=aw" target="_blank" title="Google Maps" class="social-btn maps">
                    <i class="ri-map-pin-2-fill"></i>
                </a>
            </div>
        </div>
        
        <div class="footer-section">
            <h4>Explorar</h4>
            <ul class="footer-links">
                <li><a href="<?php echo base_url('/quienes-somos'); ?>"><i class="ri-arrow-right-s-line"></i> ¿Quiénes Somos?</a></li>
                <li><a href="<?php echo base_url('/que-hacemos'); ?>"><i class="ri-arrow-right-s-line"></i> ¿Qué Hacemos?</a></li>
                <li><a href="<?php echo base_url('/como-lo-hacemos'); ?>"><i class="ri-arrow-right-s-line"></i> ¿Cómo lo Hacemos?</a></li>
                <li><a href="<?php echo base_url('/contacto'); ?>"><i class="ri-arrow-right-s-line"></i> Contáctanos</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Contacto Directo</h4>
            <div class="contact-info">
                <a href="mailto:president@edu360global.org" class="contact-item">
                    <i class="ri-mail-send-line"></i>
                    <span>president@edu360global.org</span>
                </a>
                <a href="tel:+18137908821" class="contact-item">
                    <i class="ri-phone-line"></i>
                    <span>+1 813 790 8821</span>
                </a>
                <div class="contact-item technical">
                    <i class="ri-cpu-line"></i>
                    <span>Protocolo: Kérnel v10</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        <div class="copyright">
            <p>&copy; <span id="year"></span> EDU360 Global. La soberanía del conocimiento empieza aquí.</p>
        </div>
    </div>
    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</footer>

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
                <!-- / Layout page -->
            </div>
            <!-- Overlay -->
            <div class="layout-overlay layout-menu-toggle"></div>

            <!-- Drag Target Area To SlideIn Menu On Small Screens -->
            <div class="drag-target"></div>
        </div>
        <!-- / Layout wrapper -->

        <!-- Core JS -->

        <!-- build:js assets/vendor/js/theme.js  -->

        <script src="<?php echo js('jquery-3.5.1.js'); ?>"></script>
        <script src="<?php echo js('sweetalert2.js'); ?>"></script>
    </body>
</html>
