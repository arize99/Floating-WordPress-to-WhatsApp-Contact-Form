<?php
/**
 * Plugin Name: WhatsApp Contact Form
 * Description: A simple contact form that sends data to WhatsApp.
 * Version: 1.1
 * Author: Arize Nnonyelu
 */

// Enqueue styles, scripts, and pass dynamic phone number
function wcf_enqueue_assets() {
    wp_enqueue_style( 'wcf-google-fonts', 'https://fonts.googleapis.com/css2?family=Montserrat:wght@100;900&display=swap', false );
    wp_enqueue_style( 'wcf-styles', plugin_dir_url( __FILE__ ) . 'wcf-styles.css' );
    wp_enqueue_script( 'wcf-script', plugin_dir_url( __FILE__ ) . 'wcf-script.js', array(), false, true );

    // Font Awesome for the WhatsApp icon
    wp_enqueue_style( 'wcf-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css' );

    // Get phone number from admin settings
    $phone_number = get_option( 'wcf_phone_number', '1234567890' );
    wp_localize_script( 'wcf-script', 'wcf_data', array(
        'phone_number' => $phone_number
    ));
}
add_action( 'wp_enqueue_scripts', 'wcf_enqueue_assets' );

// Automatically display the floating form on all pages
function wcf_display_floating_form() {
    ?>
    <!-- Floating WhatsApp Button -->
    <div class="wcf-floating-button">
        <i class="fab fa-whatsapp"></i>
    </div>

    <!-- Popup Modal -->
    <div class="wcf-popup">
        <div class="wcf-popup-content">
            <span class="wcf-close-btn">&times;</span>
            <form class="wcf-container">
                <h2>Contact us</h2>
                <p id="info">Fill in the form to leave us a message on WhatsApp, we will get back to you shortly.</p>
                <p><input id="wcf-name" type="text" placeholder="Full Name" required></p>
                <p><input id="wcf-email" type="email" placeholder="Email" required></p>
                <p><input id="wcf-tel" type="tel" placeholder="Tel" required></p>
                <p><textarea id="wcf-message" rows="5" placeholder="Your Message" required></textarea></p>
                <button type="button" onclick="sendToWhatsapp()">SUBMIT</button>
            </form>
        </div>
    </div>
    <?php
}
add_action( 'wp_footer', 'wcf_display_floating_form' );

// Add admin menu item for settings
function wcf_add_admin_menu() {
    add_menu_page(
        'WhatsApp Contact Form Settings', // Page title
        'WCF Settings', // Menu title
        'manage_options', // Capability
        'wcf_settings', // Menu slug
        'wcf_settings_page', // Function to display page content
        'dashicons-whatsapp', // Icon
        100 // Position
    );
}
add_action( 'admin_menu', 'wcf_add_admin_menu' );

// Register settings
function wcf_register_settings() {
    register_setting( 'wcf_settings_group', 'wcf_phone_number' );
}
add_action( 'admin_init', 'wcf_register_settings' );

// Settings page content
function wcf_settings_page() {
    ?>
    <div class="wrap">
        <h1>WhatsApp Contact Form Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'wcf_settings_group' );
            do_settings_sections( 'wcf_settings_group' );
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">WhatsApp Phone Number</th>
                    <td>
                        <input type="text" name="wcf_phone_number" value="<?php echo esc_attr( get_option('wcf_phone_number') ); ?>" placeholder="Enter phone number with country code" />
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Display a success message after updating the phone number
function wcf_admin_notice() {
    if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e( 'WhatsApp phone number updated successfully!', 'wcf' ); ?></p>
        </div>
        <?php
    }
}
add_action( 'admin_notices', 'wcf_admin_notice' );

// JavaScript function to handle WhatsApp data submission
function wcf_enqueue_script() {
    ?>
    <script type="text/javascript">
        // Toggle popup visibility
        document.addEventListener('DOMContentLoaded', function () {
            var whatsappButton = document.querySelector('.wcf-floating-button');
            var popup = document.querySelector('.wcf-popup');
            var closeBtn = document.querySelector('.wcf-close-btn');

            whatsappButton.addEventListener('click', function () {
                popup.style.display = 'block';
            });

            closeBtn.addEventListener('click', function () {
                popup.style.display = 'none';
            });

            window.addEventListener('click', function (e) {
                if (e.target == popup) {
                    popup.style.display = 'none';
                }
            });
        });

        // Send data to WhatsApp
        function sendToWhatsapp() {
            // Grab form data
            var name = document.getElementById('wcf-name').value;
            var email = document.getElementById('wcf-email').value;
            var tel = document.getElementById('wcf-tel').value;
            var message = document.getElementById('wcf-message').value;

            // Validate input fields
            if (!name || !email || !tel || !message) {
                alert('Please fill in all fields');
                return;
            }

            // Prepare WhatsApp message
            var whatsappMessage = 'Name: ' + name + '\nEmail: ' + email + '\nTel: ' + tel + '\nMessage: ' + message;

            // Use phone number from settings
            var whatsappUrl = 'https://wa.me/' + wcf_data.phone_number + '?text=' + encodeURIComponent(whatsappMessage);
            window.open(whatsappUrl, '_blank');
        }
    </script>
    <?php
}
add_action( 'wp_footer', 'wcf_enqueue_script' );
