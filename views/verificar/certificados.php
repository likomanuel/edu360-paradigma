<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="login-wrapper" style="min-height: 80vh; background: #000; padding: 60px 20px;">
    <div class="container" style="max-width: 1100px;">
        
        <div class="results-header" style="margin-bottom: 40px; text-align: left;">
            <h2 style="font-size: 2rem; color: var(--primary-blue); margin-bottom: 10px;">
                <i class="fas fa-search"></i> Explorador de Certificados
            </h2>
            <p style="color: #666;">Resultados para el usuario: <span style="color: #eee; font-family: monospace;"><?= htmlspecialchars($_GET['q'] ?? '') ?></span></p>
        </div>

        <?php if ($user): ?>
            <div class="user-info-card" style="background: rgba(255,255,255,0.03); padding: 30px; border-radius: 15px; border: 1px solid rgba(0,168,232,0.2); margin-bottom: 30px; display: flex; align-items: center; gap: 30px;">
                <div class="user-avatar" style="width: 80px; height: 80px; background: linear-gradient(45deg, var(--primary-blue), #003366); border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 2rem; color: white;">
                    <i class="fas fa-user-check"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.5rem; color: #fff;"><?= htmlspecialchars($user['nombre_completo']) ?></h3>
                    <p style="margin: 5px 0 0; color: #888;">ID Hash: <span style="color: var(--primary-blue); font-family: monospace;"><?= htmlspecialchars($user['hash_identidad']) ?></span></p>
                    <p style="margin: 5px 0 0; color: #888;">Email: <?= htmlspecialchars($user['email_verificado']) ?></p>
                </div>
            </div>

            <div class="table-container" style="background: rgba(10,10,10,0.8); border: 1px solid rgba(255,255,255,0.05); border-radius: 15px; overflow: hidden; backdrop-filter: blur(10px);">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background: rgba(0,168,232,0.1); border-bottom: 1px solid rgba(0,168,232,0.2);">
                            <th style="padding: 20px; color: var(--primary-blue); font-size: 0.8rem; text-transform: uppercase;">HASH CERTIFICADO</th>
                            <th style="padding: 20px; color: var(--primary-blue); font-size: 0.8rem; text-transform: uppercase;">MÓDULO / ARTEFACTO</th>
                            <th style="padding: 20px; color: var(--primary-blue); font-size: 0.8rem; text-transform: uppercase;">FECHA DE EMISIÓN</th>
                            <th style="padding: 20px; color: var(--primary-blue); font-size: 0.8rem; text-transform: uppercase; text-align: center;">ACCIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($certificates)): ?>
                            <tr>
                                <td colspan="4" style="padding: 40px; text-align: center; color: #666;">
                                    No se encontraron certificados acuñados para este evolucionador.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($certificates as $cert): ?>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: background 0.3s;" onmouseover="this.style.background='rgba(0,168,232,0.05)'" onmouseout="this.style.background='transparent'">
                                    <td style="padding: 20px;">
                                        <div style="font-family: monospace; color: #ccc; font-size: 0.9rem;">
                                            <i class="fas fa-file-signature" style="color: var(--primary-blue); margin-right: 10px;"></i>
                                            <?= substr(str_replace(['Certificado_', '.png'], '', $cert['name']), 0, 20) ?>...
                                        </div>
                                    </td>
                                    <td style="padding: 20px;">
                                        <div style="color: #eee; font-weight: 600;">Logo de Dominio</div>
                                        <div style="font-size: 0.8rem; color: #666;">Protocolo SRAA</div>
                                    </td>
                                    <td style="padding: 20px;">
                                        <div style="color: #888; font-size: 0.9rem;">
                                            <i class="far fa-clock"></i> <?= $cert['date'] ?>
                                        </div>
                                    </td>
                                    <td style="padding: 20px; text-align: center;">
                                        <a href="<?= $cert['url'] ?>" target="_blank" class="btn" style="padding: 10px 20px; background: rgba(0,168,232,0.2); border: 1px solid var(--primary-blue); color: var(--primary-blue); border-radius: 8px; font-size: 0.8rem; text-decoration: none; font-weight: bold; transition: all 0.3s;">
                                            VER TRANSACCIÓN
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <div class="not-found" style="text-align: center; padding: 100px 20px; background: rgba(255,255,255,0.02); border-radius: 20px; border: 1px dashed rgba(255,255,255,0.1);">
                <i class="fas fa-search-minus" style="font-size: 4rem; color: #444; margin-bottom: 20px;"></i>
                <h3 style="color: #eee; font-size: 1.5rem;">Evolucionador No Encontrado</h3>
                <p style="color: #666; margin-bottom: 30px;">El hash de identidad o email ingresado no corresponde a ningún usuario registrado en la red.</p>
                <a href="<?= base_url('verificar') ?>" class="btn-main" style="text-decoration: none;">VOLVER A BUSCAR</a>
            </div>
        <?php endif; ?>

        <div style="margin-top: 40px; text-align: center;">
            <a href="<?= base_url('verificar') ?>" style="color: #555; text-decoration: none; font-size: 0.9rem;">
                <i class="fas fa-arrow-left"></i> Nueva búsqueda
            </a>
        </div>
    </div>
</main>

<style>
    .btn:hover {
        background: var(--primary-blue) !important;
        color: white !important;
        box-shadow: 0 0 15px var(--primary-blue);
    }
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
