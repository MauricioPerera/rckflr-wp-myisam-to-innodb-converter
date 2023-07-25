<?php
/*
Plugin Name: MyISAM to InnoDB Converter
Plugin URI: https://rckflr.party/
Description: Un plugin para convertir tablas de MyISAM a InnoDB. Permite seleccionar tablas específicas, respaldarlas y convertirlas a InnoDB. Muestra el progreso de la conversión y bloquea la interacción del usuario durante el proceso.
Version: 1.0
Author: Mauricio Perera
Author URI: https://www.linkedin.com/in/mauricioperera/
Donate link: https://www.buymeacoffee.com/rckflr
*/

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class rckflr_MyISAM_To_InnoDB_Table extends WP_List_Table {
    function __construct() {
        parent::__construct(array(
            'singular' => 'Tabla',
            'plural' => 'Tablas',
            'ajax' => false
        ));
    }

    function prepare_items() {
        global $wpdb;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $tables = $wpdb->get_results("SHOW TABLE STATUS WHERE Engine != 'InnoDB'", ARRAY_A);

        // Convertir el tamaño de los datos a MB
        foreach ($tables as &$table) {
            $table['Data_length'] = round($table['Data_length'] / 1048576, 2);
        }

        $this->items = $tables;
    }

    function get_columns() {
        return array(
            'cb' => '<input type="checkbox" />',
            'Name' => 'Nombre',
            'Engine' => 'Motor',
            'Rows' => 'Número de Registros',
            'Data_length' => 'Peso (MB)'
        );
    }

    function column_cb($item) {
        return sprintf('<input type="checkbox" name="table[]" value="%s" />', $item['Name']);
    }

    function column_default($item, $column_name) {
        return $item[$column_name];
    }
}

add_action('admin_menu', 'rckflr_myisam_to_innodb_converter_menu');

function rckflr_myisam_to_innodb_converter_menu() {
    add_options_page(
        'MyISAM to InnoDB Converter',
        'MyISAM to InnoDB Converter',
        'manage_options',
        'myisam-to-innodb-converter',
        'rckflr_myisam_to_innodb_converter_options'
    );
}

function rckflr_myisam_to_innodb_converter_options() {
    if (!current_user_can('manage_options')) {
        wp_die(__('No tienes suficientes permisos para acceder a esta página.'));
    }

    $tableList = new rckflr_MyISAM_To_InnoDB_Table();
    $tableList->prepare_items();

    echo '<div class="wrap">';
    echo '<h1>MyISAM to InnoDB Converter</h1>';
    echo '<form id="myisam-to-innodb-converter-form" method="post">';
    echo '<input type="hidden" name="page" value="myisam-to-innodb-converter" />';
    $tableList->display();
    echo '<input type="submit" name="backup" value="Respaldar" class="button button-secondary" />';
    echo '<input type="submit" name="convert" value="Convertir a InnoDB" class="button button-primary" />';
    echo '</form>';
    echo '<div id="progress" style="display: none;"><div id="progress-bar" style="background: #0073aa; height: 20px; width: 0;"></div></div>';
    echo '<div id="loader" style="display: none;"><img src="/wp-admin/images/spinner.gif"></div>';
    echo '</div>';
}

add_action('admin_footer', 'rckflr_myisam_to_innodb_converter_scripts');

function rckflr_myisam_to_innodb_converter_scripts() {
    ?>
    <script type="text/javascript">
    jQuery('#myisam-to-innodb-converter-form').on('submit', function(e) {
        e.preventDefault();

        var tables = jQuery('input[name="table[]"]:checked').map(function() {
            return this.value;
        }).get();

        var total = tables.length;
        var count = 0;

        jQuery('#progress').show();
        jQuery('#loader').show();

        function convertNext() {
            if (tables.length == 0) {
                location.reload();
                return;
            }

            var table = tables.shift();

            jQuery.post(ajaxurl, {
                action: 'rckflr_convert_table',
                table: table,
                _ajax_nonce: '<?php echo wp_create_nonce('rckflr_convert_table'); ?>'
            }, function(response) {
                count++;
                var progress = count / total * 100;
                jQuery('#progress-bar').css('width', progress + '%');

                convertNext();
            });
        }

        if (jQuery(this).find('input[name="backup"]').is(':focus')) {
            // Respaldar las tablas
            jQuery.post(ajaxurl, {
                action: 'rckflr_backup_tables',
                tables: tables,
                _ajax_nonce: '<?php echo wp_create_nonce('rckflr_backup_tables'); ?>'
            }, function(response) {
                alert(response);
            });
        } else {
            // Convertir las tablas
            convertNext();
        }
    });
    </script>
    <?php
}

add_action('wp_ajax_rckflr_convert_table', 'rckflr_myisam_to_innodb_converter_ajax');

function rckflr_myisam_to_innodb_converter_ajax() {
    check_ajax_referer('rckflr_convert_table');

    global $wpdb;

    $table = $_POST['table'];

    // Convertir la tabla a InnoDB
    $wpdb->query("ALTER TABLE $table ENGINE=InnoDB");

    // Optimizar la tabla
    $wpdb->query("OPTIMIZE TABLE $table");

    echo "La tabla $table ha sido convertida a InnoDB y optimizada.";

    wp_die();
}

add_action('wp_ajax_rckflr_backup_tables', 'rckflr_myisam_to_innodb_converter_backup_ajax');

function rckflr_myisam_to_innodb_converter_backup_ajax() {
    check_ajax_referer('rckflr_backup_tables');

    global $wpdb;

    $tables = $_POST['tables'];

    foreach ($tables as $table) {
        // Crear una copia de seguridad de la tabla
        $wpdb->query("CREATE TABLE {$table}_backup AS SELECT * FROM $table");
    }

    echo "Se han creado copias de seguridad de las tablas seleccionadas.";

    wp_die();
}
?>
