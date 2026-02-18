<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="explorer-wrapper" style="min-height: 90vh; background: radial-gradient(circle at 50% 50%, #0a1118 0%, #000 100%); padding: 60px 20px; color: #fff; font-family: 'Inter', sans-serif;">
    <div class="container" style="max-width: 1100px; margin: 0 auto;">
        
        <!-- Encabezado con Animación -->
        <div class="results-header" style="margin-bottom: 50px; text-align: left; animation: fadeInDown 0.8s ease-out;">
            <h2 style="font-size: clamp(1.5rem, 5vw, 2.5rem); color: var(--primary-blue); margin-bottom: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; display: flex; align-items: center; gap: 15px;">
                <i class="fas fa-microchip" style="filter: drop-shadow(0 0 10px var(--primary-blue));"></i> Explorador de Certificados
            </h2>
            <div style="display: flex; align-items: center; gap: 10px; color: #888; font-size: 0.9rem;">
                <span style="width: 8px; height: 8px; background: #0f0; border-radius: 50%; box-shadow: 0 0 10px #0f0; display: inline-block;"></span>
                Resultados para: <span style="color: #eee; font-family: 'Fira Code', monospace; background: rgba(255,255,255,0.05); padding: 2px 8px; border-radius: 4px;"><?= htmlspecialchars($_GET['q'] ?? '') ?></span>
            </div>
        </div>

        <?php if ($user): ?>
            <!-- Perfil Hero -->
            <div class="user-profile-hero" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(0, 168, 232, 0.2); border-radius: 25px; padding: 40px; margin-bottom: 50px; display: flex; align-items: center; gap: 40px; position: relative; overflow: hidden; animation: zoomIn 0.6s ease-out;">
                <!-- Fondo decorativo -->
                <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: radial-gradient(circle, rgba(0, 168, 232, 0.1) 0%, transparent 70%); border-radius: 50%;"></div>
                
                <div class="profile-avatar-wrapper" style="position: relative;">
                    <div class="user-avatar" style="width: 120px; height: 120px; background: linear-gradient(45deg, var(--primary-blue), #003366); border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 3rem; color: white; overflow: hidden; border: 4px solid rgba(255,255,255,0.1); box-shadow: 0 0 30px rgba(0, 168, 232, 0.3);">
                        <?php if(!empty($user['foto'])): ?>
                            <img src="<?= base_url('public/users/'.$user['hash_identidad'].'/perfil/'.$user['foto']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <i class="fas fa-user-astronaut"></i>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="user-details">
                    <h3 style="margin: 0; font-size: clamp(1.2rem, 4vw, 2rem); color: #fff; font-weight: 700; letter-spacing: -0.5px;"><?= htmlspecialchars($user['nombre_completo']) ?></h3>
                    <div style="margin-top: 15px; display: flex; flex-wrap: wrap; gap: 20px;">
                        <div style="display: flex; flex-direction: column;">
                            <span style="font-size: 0.7rem; color: #666; text-transform: uppercase; font-weight: 600; letter-spacing: 1px;">ID Hash Paradigma</span>
                            <span style="color: var(--primary-blue); font-family: 'Fira Code', monospace; font-size: 0.85rem;"><?= htmlspecialchars($user['hash_identidad']) ?></span>
                        </div>
                        <div style="display: flex; flex-direction: column;">
                            <span style="font-size: 0.7rem; color: #666; text-transform: uppercase; font-weight: 600; letter-spacing: 1px;">Protocolo de Red</span>
                            <span style="color: #eee; font-size: 0.85rem;"><?= htmlspecialchars($user['email_verificado']) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Listado de Certificados -->
            <div class="certificates-section">
                <h4 style="font-size: 1.1rem; color: #eee; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; font-weight: 600;">
                    <i class="fas fa-th-large" style="color: var(--primary-blue);"></i> Activos Acuñados
                </h4>

                <div class="certificates-grid">
                    <?php if (empty($certificates)): ?>
                        <div style="grid-column: 1 / -1; background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1); border-radius: 15px; padding: 60px; text-align: center; color: #666;">
                            <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.5;"></i>
                            <p style="font-style: italic;">No se han encontrado registros transaccionales para este evolucionador.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($certificates as $cert): ?>
                            <div class="cert-card" style="background: rgba(20,20,20,0.6); border: 1px solid rgba(255,255,255,0.05); border-radius: 20px; padding: 25px; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); position: relative; overflow: hidden; backdrop-filter: blur(10px); display: flex; flex-direction: column; gap: 20px;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <div class="cert-thumbnail" style="width: 80px; height: 50px; background: #000; border-radius: 8px; overflow: hidden; border: 1px solid rgba(0, 168, 232, 0.3); box-shadow: 0 4px 10px rgba(0,0,0,0.5);">
                                        <img src="<?= $cert['url'] ?>" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.8; transition: 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">
                                    </div>
                                    <div style="background: rgba(0, 255, 136, 0.15); color: #0f8; font-size: 0.65rem; padding: 4px 10px; border-radius: 20px; font-weight: 700; letter-spacing: 0.5px;">SRAA VERIFIED</div>
                                </div>
                                
                                <div>
                                    <h5 style="margin: 0; font-size: 1rem; color: #fff; font-weight: 600;"><?= htmlspecialchars($cert['achievement']) ?></h5>
                                    <p style="margin: 5px 0 0; font-size: 0.8rem; color: #666;">ID Transacción: <?= substr(str_replace(['Certificado_', '.png'], '', $cert['name']), 0, 15) ?>...</p>
                                </div>

                                <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: rgba(0,0,0,0.3); border-radius: 10px;">
                                    <i class="far fa-calendar-alt" style="color: #444; font-size: 0.8rem;"></i>
                                    <span style="font-size: 0.75rem; color: #888;">Emitido: <?= date('d M, Y', strtotime($cert['date'])) ?></span>
                                </div>

                                <a href="<?= $cert['url'] ?>" target="_blank" class="glow-button" style="text-align: center; background: linear-gradient(90deg, var(--primary-blue), #0056b3); color: #fff; padding: 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 700; text-decoration: none; transition: 0.3s; box-shadow: 0 4px 15px rgba(0, 168, 232, 0.2);">
                                    VER TRANSACCIÓN <i class="fas fa-external-link-alt" style="margin-left: 5px; font-size: 0.7rem;"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        <?php else: ?>
            <!-- Fallback No Encontrado -->
            <div class="not-found-card" style="text-align: center; padding: 80px 40px; background: rgba(255,255,255,0.02); backdrop-filter: blur(15px); border-radius: 30px; border: 2px dashed rgba(255,255,255,0.05); animation: shake 0.5s ease-in-out;">
                <div style="width: 100px; height: 100px; background: rgba(255, 100, 100, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px; font-size: 3rem; color: #ff6464;">
                    <i class="fas fa-user-slash"></i>
                </div>
                <h3 style="color: #fff; font-size: 1.8rem; margin-bottom: 15px;">Evolucionador No Localizado</h3>
                <p style="color: #888; max-width: 500px; margin: 0 auto 40px; line-height: 1.6;">El sistema no ha podido encontrar registros vinculados a esta identidad. Por favor verifica que el Hash o Email sean correctos.</p>
                <a href="<?= base_url('verificar') ?>" class="btn-main" style="display: inline-block; padding: 15px 40px; background: #fff; color: #000; text-decoration: none; border-radius: 15px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s;">
                    NUEVA BÚSQUEDA
                </a>
            </div>
        <?php endif; ?>

        <div style="margin-top: 60px; text-align: center;">
            <a href="<?= base_url('verificar') ?>" style="color: #666; text-decoration: none; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px; transition: 0.3s;" onmouseover="this.style.color='var(--primary-blue)'" onmouseout="this.style.color='#666'">
                <i class="fas fa-arrow-left"></i> Volver al Portal de Verificación
            </a>
        </div>
    </div>
</main>

<style>
    /* Grid Responsivo */
    .certificates-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
        animation: fadeInUp 0.8s ease-out;
    }

    /* Animaciones */
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes zoomIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }

    /* Efectos de Hover */
    .cert-card:hover {
        transform: translateY(-10px);
        border-color: var(--primary-blue);
        box-shadow: 0 15px 40px rgba(0, 168, 232, 0.15);
        background: rgba(255,255,255,0.04);
    }

    .glow-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 168, 232, 0.4) !important;
        filter: brightness(1.1);
    }

    /* Ajustes para Mobile */
    @media (max-width: 768px) {
        .user-profile-hero {
            flex-direction: column;
            text-align: center;
            padding: 30px 20px;
            gap: 20px;
        }
        .user-details {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .user-details div {
            align-items: center;
            text-align: center;
        }
        .results-header {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .explorer-wrapper {
            padding: 40px 15px;
        }
        .certificates-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
