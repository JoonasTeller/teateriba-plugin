<?php
/**
 * Plugin Name: Telleri Teateriba
 * Description: Kuvab kohandatava teateriba lehe ülaosas koos sulgemisvõimalusega.
 * Version: 1.1
 * Author: Joonas Teller
 */

if (!defined('ABSPATH')) exit;

// Lisa menüüvalik admin paneeli
add_action('admin_menu', 'teateriba_lisa_menuu');
function teateriba_lisa_menuu() {
    add_menu_page(
        'Teateriba Seaded',
        'Telleri teateriba',
        'manage_options',
        'teateriba-seaded',
        'teateriba_seaded_leht',
        'dashicons-megaphone'
    );
}

// Seadete lehe funktsionaalsus
function teateriba_seaded_leht() {
    if (isset($_POST['save_teateriba'])) {
        update_option('tr_tekst', sanitize_text_field($_POST['tr_tekst']));
        update_option('tr_taust', sanitize_hex_color($_POST['tr_taust']));
        update_option('tr_varv', sanitize_hex_color($_POST['tr_varv']));
        update_option('tr_ainult_avalehel', isset($_POST['tr_ainult_avalehel']) ? 1 : 0);
        update_option('tr_naita_peida', isset($_POST['tr_naita_peida']) ? 1 : 0);
        echo '<div class="updated"><p>Seaded edukalt salvestatud!</p></div>';
    }

    $tekst = get_option('tr_tekst', 'Tere tulemast meie lehele!');
    $taust = get_option('tr_taust', '#ff0000');
    $varv = get_option('tr_varv', '#ffffff');
    $ainult_avalehel = get_option('tr_ainult_avalehel', 0);
    $naita_peida = get_option('tr_naita_peida', 1);
    ?>
    <div class="wrap">
        <h1>Teateriba seadistamine</h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th>Teate tekst</th>
                    <td><input type="text" name="tr_tekst" value="<?php echo esc_attr($tekst); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th>Taustavärv</th>
                    <td><input type="color" name="tr_taust" value="<?php echo esc_attr($taust); ?>"></td>
                </tr>
                <tr>
                    <th>Tekstivärv</th>
                    <td><input type="color" name="tr_varv" value="<?php echo esc_attr($varv); ?>"></td>
                </tr>
                <tr>
                    <th>Kuvamise valikud</th>
                    <td>
                        <label><input type="checkbox" name="tr_ainult_avalehel" <?php checked($ainult_avalehel, 1); ?>> Kuva ainult avalehel</label><br>
                        <label><input type="checkbox" name="tr_naita_peida" <?php checked($naita_peida, 1); ?>> Luba kasutajal teade sulgeda (X nupp)</label>
                    </td>
                </tr>
            </table>
            <?php submit_button('Salvesta seaded', 'primary', 'save_teateriba'); ?>
        </form>
    </div>
    <?php
}

// Teateriba väljastamine veebilehel
add_action('wp_body_open', 'teateriba_kuva_frontend');
function teateriba_kuva_frontend() {
    $ainult_avalehel = get_option('tr_ainult_avalehel', 0);
    
    if ($ainult_avalehel && !is_front_page()) {
        return;
    }

    $tekst = get_option('tr_tekst');
    $taust = get_option('tr_taust', '#ff0000');
    $varv = get_option('tr_varv', '#ffffff');
    $naita_peida = get_option('tr_naita_peida', 1);

    if (empty($tekst)) return;
    ?>

    <style>
        #custom-teateriba {
            background-color: <?php echo esc_attr($taust); ?>;
            color: <?php echo esc_attr($varv); ?>;
            text-align: center;
            padding: 15px 50px;
            width: 100%;
            z-index: 9999;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            font-weight: 600;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            box-sizing: border-box;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        .admin-bar #custom-teateriba {
            top: 0;
        }
        .tr-tekst-area {
            max-width: 90%;
            line-height: 1.4;
        }
        .tr-sulge-nupp {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 28px;
            line-height: 1;
            color: <?php echo esc_attr($varv); ?>;
            opacity: 0.7;
            transition: opacity 0.2s;
            user-select: none;
        }
        .tr-sulge-nupp:hover {
            opacity: 1;
        }
    </style>

    <div id="custom-teateriba">
        <div class="tr-tekst-area">
            <?php echo esc_html($tekst); ?>
        </div>
        <?php if ($naita_peida) : ?>
            <div class="tr-sulge-nupp" onclick="document.getElementById('custom-teateriba').style.display='none';" title="Sulge teade">
                &times;
            </div>
        <?php endif; ?>
    </div>

    <?php
}
